/**
 * FullCalendar npm packages (v5)
 */
import FullCalendar from '@fullcalendar/react';
import dayGridPlugin  from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import allLocales     from '@fullcalendar/core/locales-all';
import '@fullcalendar/daygrid/main.css';
import '@fullcalendar/timegrid/main.css';

/**
 * WordPress dependencies
 */
import { useState, useCallback, useRef, useEffect } from '@wordpress/element';
import { FormTokenField, SelectControl, Spinner } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

// ── Constants ─────────────────────────────────────────────────────────────────

const EXCLUDED_STATUSES = [ 'wc-cancelled', 'wc-refunded', 'wc-failed' ];

const VIEW_OPTIONS = [
	{ value: 'order',   label: 'Orders'   },
	{ value: 'product', label: 'Products' },
];

// ── Helpers ───────────────────────────────────────────────────────────────────

const calData = () => window.orddd_calendar_js || {};

const buildStatusOptions = () =>
	Object.entries( calData().order_statuses || {} ).map( ( [ value, label ] ) => ( { value, label } ) );

const buildShippingOptions = () =>
	( calData().shipping_methods || [] ).map( ( m ) => ( { value: m.method_key, label: m.title } ) );

const buildEventUrl = ( { view, statuses, shipping } ) => {
	const base     = calData().pluginurl || '';
	const params   = new URLSearchParams();
	params.set( 'eventType',     view );
	params.set( 'orderStatus',   statuses.map( ( s ) => s.value ).join( ',' ) );
	params.set( 'orderShipping', shipping.map( ( s ) => s.value ).join( ',' ) );
	return `${ base }&${ params.toString() }`;
};

const buildExportUrl = ( baseUrl, { view, statuses, shipping }, dateRange ) => {
	const params = new URLSearchParams();
	params.set( 'eventType',     view );
	params.set( 'orderStatus',   statuses.map( ( s ) => s.value ).join( ',' ) );
	params.set( 'orderShipping', shipping.map( ( s ) => s.value ).join( ',' ) );
	if ( dateRange.start ) params.set( 'start', dateRange.start );
	if ( dateRange.end )   params.set( 'end',   dateRange.end );
	return `${ baseUrl }&${ params.toString() }`;
};

const parseHolidays = () => {
	const str = calData().orddd_holidays || '';
	if ( ! str ) return [];
	try {
		// eslint-disable-next-line no-eval
		const parsed = eval( '[' + str + ']' );
		return Array.isArray( parsed ) ? parsed : [];
	} catch {
		return [];
	}
};

/**
 * Returns a change handler that maps selected label strings back to
 * { value, label } objects using the provided options array.
 */
const makeTokenChangeHandler = ( options, setState ) => ( newLabels ) => {
	const selected = newLabels
		.map( ( lbl ) => options.find( ( o ) => o.label === lbl ) )
		.filter( Boolean );
	setState( selected );
};

// ── OrderCalendar ─────────────────────────────────────────────────────────────

const OrderCalendar = () => {
	const statusOptions   = buildStatusOptions();
	const shippingOptions = buildShippingOptions();

	const defaultStatuses = statusOptions.filter( ( o ) => ! EXCLUDED_STATUSES.includes( o.value ) );

	// Filter state
	const [ view,     setView     ] = useState( 'order' );
	const [ statuses, setStatuses ] = useState( defaultStatuses );
	const [ shipping, setShipping ] = useState( [] );

	// Applied filters (what the calendar actually uses)
	const [ appliedFilters, setAppliedFilters ] = useState( {
		view: 'order', statuses: defaultStatuses, shipping: [],
	} );

	const [ eventUrl,  setEventUrl  ] = useState( () => buildEventUrl( {
		view: 'order', statuses: defaultStatuses, shipping: [],
	} ) );
	const [ isLoading, setIsLoading ] = useState( false );
	const [ dateRange, setDateRange ] = useState( { start: '', end: '' } );

	const calendarRef = useRef( null );


	// ── Callbacks ─────────────────────────────────────────────────────────────

	const handleLoading = useCallback( ( bool ) => setIsLoading( bool ), [] );

	const handleDatesSet = useCallback( ( { startStr, endStr } ) => {
		setDateRange( { start: startStr.slice( 0, 10 ), end: endStr.slice( 0, 10 ) } );
	}, [] );

	const handleEventDidMount = useCallback( ( info ) => {
		const jQuery = window.jQuery;
		if ( ! jQuery || ! jQuery( info.el ).qtip ) return;
		jQuery( info.el ).qtip( {
			content: {
				text:   'Loading…',
				button: 'Close',
				ajax:   {
					url:  calData().ajax_url,
					type: 'POST',
					data: {
						action:            'orddd_order_calendar_content',
						order_id:          info.event.id,
						event_product_id:  info.event.extendedProps.event_product_id  || '',
						product_name:      info.event.extendedProps.product_name      || '',
						event_product_qty: info.event.extendedProps.event_product_qty || '',
						event_value:       info.event.extendedProps.value             || '',
						event_date:        info.event.extendedProps.delivery_date     || '',
						event_timeslot:    info.event.extendedProps.time_slot         || '',
						event_type:        appliedFilters.view,
						security:          calData().security                         || '',
					},
				},
			},
			show:     { event: 'click', solo: true },
			position: { my: 'bottom right', at: 'top right' },
			hide:     'unfocus',
			style:    { classes: 'qtip-light qtip-shadow' },
		} );
	}, [ appliedFilters.view ] );

	const handleDayCellDidMount = useCallback( ( args ) => {
		const holidays = parseHolidays();
		const color    = calData().orddd_holiday_color;
		if ( ! holidays.length || ! color ) return;
		const d         = args.date;
		const m         = d.getMonth() + 1;
		const fullDate  = `${ m }-${ d.getDate() }-${ d.getFullYear() }`;
		const shortDate = `${ m }-${ d.getDate() }`;
		if ( holidays.includes( fullDate ) || holidays.includes( shortDate ) ) {
			args.el.style.background = color;
		}
	}, [] );

	// ── Actions ───────────────────────────────────────────────────────────────

	const applyFilters = () => {
		const filters = { view, statuses, shipping };
		setAppliedFilters( filters );
		setEventUrl( buildEventUrl( filters ) );
	};

	const handlePrint = () => {
		const url = buildExportUrl( calData().print_url || '', appliedFilters, dateRange );
		window.open( url, '_blank' );
	};

	const handleCsv = () => {
		const url = buildExportUrl( calData().csv_url || '', appliedFilters, dateRange );
		window.location.href = url;
	};

	const [ gcalMessage,    setGcalMessage    ] = useState( '' );
	const [ gcalExporting,  setGcalExporting  ] = useState( false );

	const postAjaxAction = ( action ) => {
		const body = new FormData();
		body.append( 'action', action );
		return fetch( calData().ajax_url, { method: 'POST', body } );
	};

	const handleGcalExport = () => {
		const ordersCount = calData().gcal_orders_count || 0;
		setGcalMessage( `Total orders to export ${ ordersCount } ... ` );
		setGcalExporting( true );
		postAjaxAction( 'orddd_admin_delivery_calendar_events' )
			.then( () => {
				setGcalMessage( __( 'All events are added to the Google calendar. You can re-export the events to your calendar if you wish to add the events again with new settings.', 'order-delivery-date' ) );
			} )
			.finally( () => setGcalExporting( false ) );
	};

	const handleGcalReExport = () => {
		setGcalMessage( __( 'Re-exporting Orders …', 'order-delivery-date' ) );
		setGcalExporting( true );
		postAjaxAction( 'orddd_export_orders_again' )
			.then( () => {
				setGcalMessage( __( 'All events are re-exported to the Google calendar.', 'order-delivery-date' ) );
			} )
			.finally( () => setGcalExporting( false ) );
	};

	// ── Render ────────────────────────────────────────────────────────────────

	return (
		<div className="orddd-page-wrap" style={ {maxWidth: '1220px'}}>
			<div className="hol-page-header">
				<div className="hol-page-header-text">
					<h1 className="hol-title">{ __( 'Order Calendar', 'order-delivery-date' ) }</h1>
					<p className="hol-subtitle">
						{ __( 'View all orders placed on their delivery date using the filters below.', 'order-delivery-date' ) }
					</p>
				</div>
			</div>

			{ /* Filter toolbar */ }
			<div className="orddd-cal-filters">
				<div className="orddd-cal-filters-row">

					{ /* Row 1: filter controls */ }
					<div className="orddd-cal-filters-row__filters">

						{ /* Select a view */ }
						<div className="orddd-cal-filter-group">
							<SelectControl
								label={ __( 'Select a view:', 'order-delivery-date' ) }
								value={ view }
								options={ VIEW_OPTIONS }
								onChange={ setView }
								__nextHasNoMarginBottom
							/>
						</div>

						{ /* Select status */ }
						<div className="orddd-cal-filter-group">
							<FormTokenField
								label={ __( 'Select status:', 'order-delivery-date' ) }
								value={ statuses.map( ( s ) => s.label ) }
								suggestions={ statusOptions.map( ( o ) => o.label ) }
								onChange={ makeTokenChangeHandler( statusOptions, setStatuses ) }
								__experimentalShowHowTo={ false }
								__experimentalExpandOnFocus
								__nextHasNoMarginBottom
							/>
						</div>

						{ /* Select shipping method */ }
						<div className="orddd-cal-filter-group">
							<FormTokenField
								label={ __( 'Select shipping method:', 'order-delivery-date' ) }
								value={ shipping.map( ( s ) => s.label ) }
								suggestions={ shippingOptions.map( ( o ) => o.label ) }
								onChange={ makeTokenChangeHandler( shippingOptions, setShipping ) }
								__experimentalShowHowTo={ false }
								__experimentalExpandOnFocus
								__nextHasNoMarginBottom
							/>
						</div>


					</div>

					{ /* Row 2: action buttons */ }
					<div className="orddd-cal-filters-row__actions">
						<button className="orddd-cal-btn orddd-cal-btn--apply" onClick={ applyFilters }>
							{ __( 'Apply filters', 'order-delivery-date' ) }
						</button>
						<div style={ { display: 'flex', gap: '4px'}}>
							<button className="orddd-cal-btn orddd-cal-btn--export" onClick={ handlePrint }>
									<span className="dashicons dashicons-printer" />
									{ __( 'Print', 'order-delivery-date' ) }
								</button>
								<button className="orddd-cal-btn orddd-cal-btn--export" onClick={ handleCsv }>
									<span className="dashicons dashicons-media-spreadsheet" />
									{ __( 'CSV', 'order-delivery-date' ) }
								</button>
						</div>
					</div>
				</div>

				
			</div>

			<div className="orddd-cal-calendar-wrap">
				{ isLoading && (
					<div className="orddd-cal-loader-overlay">
						<Spinner />
					</div>
				) }
				<FullCalendar
				ref={ calendarRef }
				plugins={ [ dayGridPlugin, timeGridPlugin ] }
				headerToolbar={ {
					left:   'prev,today,next',
					center: 'title',
					right:  'dayGridMonth,timeGridWeek,timeGridDay',
				} }
				locales={ allLocales }
				locale={ calData().calendar_language || 'en' }
				firstDay={ calData().first_day_of_week ?? 0 }
				selectable={ false }
				navLinks={ true }
				dayMaxEvents={ true }
				events={ eventUrl }
				loading={ handleLoading }
				datesSet={ handleDatesSet }
				eventDidMount={ handleEventDidMount }
				dayCellDidMount={ handleDayCellDidMount }
				/>
			</div>

			{ /* Bottom notes + Google Calendar export */ }
			<div className="orddd-cal-footer">
				<p className="orddd-cal-footer__note">
					{ __( '* Orders that are imported from the ICS feed URL will not be displayed on the calendar under "Products" view until the products are added for those orders. Such orders will however continue to show on the calendar in the "Orders" view.', 'order-delivery-date' ) }
				</p>

				{ calData().gcal_export_enabled && (
					<>
						<hr className="orddd-cal-footer__divider" />
						<p className="orddd-cal-footer__note">
							{ __( '* To export all orders to your Google calendar, please click the "Export to Google Calendar" button.', 'order-delivery-date' ) }
							{ calData().gcal_has_exported && (
								<>{ ' ' }{ __( 'If you have already exported the orders once & wish to re-export all orders again, please click on the "Re-export to Google Calendar" button.', 'order-delivery-date' ) }</>
							) }
						</p>
						<div className="orddd-cal-footer__gcal-actions">
							<button
								className="orddd-cal-btn orddd-cal-btn--apply"
								onClick={ handleGcalExport }
								disabled={ gcalExporting }
							>
								{ __( 'Export to Google Calendar', 'order-delivery-date' ) }
							</button>
							{ calData().gcal_has_exported && (
								<button
									className="orddd-cal-btn orddd-cal-btn--export"
									onClick={ handleGcalReExport }
									disabled={ gcalExporting }
								>
									{ __( 'Re-export to Google Calendar', 'order-delivery-date' ) }
								</button>
							) }
						</div>
						{ gcalMessage && (
							<p className="orddd-cal-footer__gcal-message">{ gcalMessage }</p>
						) }
					</>
				) }
			</div>
		</div>
	);
};

export default OrderCalendar;

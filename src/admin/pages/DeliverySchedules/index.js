/**
 * Delivery Schedules — list page (Lite).
 *
 * Displays only the Default Schedule with expandable details.
 * Edit → /edit-schedule/0
 */

import { useState, useEffect, useCallback } from '@wordpress/element';
import { Spinner, Notice } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { useNavigate } from 'react-router-dom';
import { __, _n, sprintf } from '@wordpress/i18n';
import { Icon } from '@wordpress/components';
import { lock, starFilled } from '@wordpress/icons';

import { WEEK_DAYS } from '../EditSchedule/constants';

const upgradeUrl =
	window.ordddAdminData?.upgradeUrl ??
	'https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/';

const trialUrl = 'https://www.tychesoftwares.com/products/woocommerce-order-delivery-date-pro-plugin-trial/?utm_source=wprepo&utm_medium=litesettings&utm_campaign=OrderDeliveryDateLite';

// ── Pure helpers ──────────────────────────────────────────────────────────────

/**
 * Convert 24-hour hours + minutes to "H:MM AM/PM" format.
 * Returns null if the hours value is absent or zero (no cutoff configured).
 */
const fmt12 = ( hours, minutes ) => {
	const h = parseInt( hours, 10 );
	if ( ! hours || isNaN( h ) || h === 0 ) return null;
	const m    = String( minutes || '00' ).padStart( 2, '0' );
	const ampm = h >= 12 ? 'PM' : 'AM';
	const h12  = h % 12 || 12;
	return `${ h12 }:${ m } ${ ampm }`;
};

/**
 * Convert 24-hour hours + minutes to "HH:MM" format.
 * Returns null if the hours value is absent or zero (no cutoff configured).
 */
const fmt24 = ( hours, minutes ) => {
	const h = parseInt( hours, 10 );
	if ( ! hours || isNaN( h ) || h === 0 ) return null;
	const m = String( minutes || '00' ).padStart( 2, '0' );
	return `${ String( h ).padStart( 2, '0' ) }:${ m }`;
};

/** Format cutoff time according to the given time format ('2' = 24h, else 12h). */
const fmtTime = ( hours, minutes, timeFormat ) =>
	timeFormat === '2' ? fmt24( hours, minutes ) : fmt12( hours, minutes );

/** Format a time slot's time field (object or legacy string) into a display string. */
const formatSlotTime = ( time ) => {
	if ( ! time ) return '';
	if ( typeof time === 'object' ) return `${ time.from_time } - ${ time.to_time }`;
	return time;
};

/** Return unique time-slot objects (deduplicated by time string) — used for summary counts. Excludes blocked slots. */
const getUniqueSlots = ( timeSlots ) => {
	if ( ! Array.isArray( timeSlots ) ) return [];
	const seen = new Set();
	return timeSlots.filter( ( s ) => {
		const key = formatSlotTime( s.time );
		if ( ! key || s.blocked || seen.has( key ) ) return false;
		seen.add( key );
		return true;
	} );
};

/** Return all non-empty, non-blocked time-slot entries (preserves per-day rows). */
const getAllSlots = ( timeSlots ) => {
	if ( ! Array.isArray( timeSlots ) ) return [];
	return timeSlots.filter( ( s ) => s.time && ! s.blocked );
};

const WEEKDAY_LABELS = Object.fromEntries( WEEK_DAYS.map( ( d ) => [ d.key, d.label ] ) );

/** Convert a delivery_days value to a human-readable label. */
const getDayLabel = ( day ) => WEEKDAY_LABELS[ day ] || day || '—';

/** Checkout option label for the day-summary. */
const getCheckoutLabel = ( schedule ) => {
	const opt = schedule.orddd_delivery_checkout_options
		|| schedule.orddd_pickup_checkout_options
		|| '';
	return opt === 'text_block' ? __( 'Estimated', 'order-delivery-date' ) : __( 'Calendar', 'order-delivery-date' );
};

/** Count enabled weekdays. */
const countEnabledDays = ( weekdays ) => {
	if ( ! weekdays || typeof weekdays !== 'object' ) return 0;
	return WEEK_DAYS.filter( ( d ) => weekdays[ d.key ]?.enable === 'checked' ).length;
};



// ── DayPills ──────────────────────────────────────────────────────────────────

const DayPills = ( { weekdays } ) => (
	<div className="ds-day-pills">
		{ WEEK_DAYS.map( ( d ) => {
			const active = weekdays?.[ d.key ]?.enable === 'checked';
			return (
				<span
					key={ d.key }
					className={ `ds-day-pill${ active ? ' ds-day-pill--active' : '' }` }
				>
					{ d.label.slice( 0, 2 ) }
				</span>
			);
		} ) }
	</div>
);

// ── CutoffRow ─────────────────────────────────────────────────────────────────

const CutoffRow = ( { label, value, charge, chargeType } ) => {
	const hasCharge = charge !== '' && charge !== null && charge !== undefined;
	const chargeStr = hasCharge
		? ( chargeType === 'percent' ? `${ charge }%` : `$${ charge }` )
		: null;
	return (
		<div className="ds-cutoff-row">
			<span className="ds-cutoff-label">{ `${ label }: ${ value }` }</span>
			{ chargeStr && (
				<>
					<span className="ds-cutoff-divider">|</span>
					<span className="ds-cutoff-charge">{ chargeStr }</span>
				</>
			) }
		</div>
	);
};

// ── ExpandPanel ───────────────────────────────────────────────────────────────

const ExpandPanel = ( { schedule, isEstimated = false } ) => {
	const slots = getAllSlots( schedule.time_slots );

	const [ holidays,     setHolidays     ] = useState( null ); // null = loading
	const [ holidayError, setHolidayError ] = useState( false );
	useEffect( () => {
		apiFetch( { path: '/orddd/v1/holidays' } )
			.then( ( data ) => {
				if ( ! Array.isArray( data ) ) { setHolidays( [] ); return; }
				const match = data.find(
					( item ) => String( item.delivery_schedule_id ) === String( schedule.delivery_schedule_id )
				);
				setHolidays( match?.holidays || [] );
			} )
			.catch( () => {
				setHolidayError( true );
				setHolidays( [] );
			} );
	}, [] ); // eslint-disable-line react-hooks/exhaustive-deps

	if ( isEstimated ) {
		return null;
	}

	return (
		<div className="ds-expand-panel">

			{ /* ── Time Slots section ── */ }
			{ schedule.orddd_lite_enable_time_slot === 'on' && (
				<div className="ds-expand-section">
					<div className="ds-expand-section-header">
						<span className="ds-expand-section-title">
							{ slots.length > 0
								? sprintf( __( 'TIME SLOTS (%d)', 'order-delivery-date' ), slots.length )
								: __( 'TIME SLOTS', 'order-delivery-date' ) }
						</span>
					</div>
					{ slots.length > 0 ? (
						<div className="ds-table-scroll">
							<table className="ds-slots-table">
								<thead>
									<tr>
										<th>{ __( 'Day', 'order-delivery-date' ) }</th>
										<th>{ __( 'Slot', 'order-delivery-date' ) }</th>
										<th>{ __( 'Max Orders', 'order-delivery-date' ) }</th>
										<th>{ __( 'Charges', 'order-delivery-date' ) }</th>
									</tr>
								</thead>
								<tbody>
									{ slots.map( ( slot, i ) => (
										<tr key={ i }>
											<td>{ getDayLabel( slot.delivery_days ) }</td>
											<td>{ formatSlotTime( slot.time ) }</td>
											<td>{ slot.max_orders || '—' }</td>
											<td>{ slot.charges ? `${ slot.charges_label ? slot.charges_label + ': ' : '' }${ slot.charges }${ slot.charges_type === 'percent' ? '%' : '' }` : '—' }</td>
										</tr>
									) ) }
								</tbody>
							</table>
						</div>
					) : (
						<p className="ds-expand-empty">{ __( 'No time slots configured.', 'order-delivery-date' ) }</p>
					) }
				</div>
			) }

			{ /* ── Delivery Days section ── */ }
			{ ( () => {
				const globalLockout = schedule.orddd_lite_lockout_date_after_orders || '';
				const enabledWeekdays = WEEK_DAYS
					.filter( ( d ) => schedule.weekdays?.[ d.key ]?.enable === 'checked' )
					.map( ( d ) => {
						const wd = schedule.weekdays[ d.key ];
						return {
							label:      d.label,
							max_orders: wd.max_orders || globalLockout || '—',
							type:       'weekday',
						};
					} );
				const rows = enabledWeekdays;
				if ( rows.length === 0 ) return null;
				return (
					<div className="ds-expand-section">
						<div className="ds-expand-section-header">
							<span className="ds-expand-section-title">
								{ sprintf( __( 'DELIVERY DAYS (%d)', 'order-delivery-date' ), rows.length ) }
							</span>
						</div>
						<div className="ds-table-scroll">
							<table className="ds-slots-table">
								<thead>
									<tr>
										<th>{ __( 'Day / Date', 'order-delivery-date' ) }</th>
										<th>{ __( 'Max Orders', 'order-delivery-date' ) }</th>
									</tr>
								</thead>
								<tbody>
									{ rows.map( ( row, i ) => (
										<tr key={ i }>
											<td>{ row.label }</td>
											<td>{ row.max_orders }</td>
										</tr>
									) ) }
								</tbody>
							</table>
						</div>
					</div>
				);
			} )() }

			{ /* ── Holidays section ── */ }
			<div className="ds-expand-section">
				<div className="ds-expand-section-header">
					<span className="ds-expand-section-title">{ __( 'HOLIDAYS', 'order-delivery-date' ) }</span>
				</div>
				{ holidays === null ? (
					<Spinner />
				) : holidayError ? (
					<p className="ds-expand-empty">{ __( 'Could not load holidays.', 'order-delivery-date' ) }</p>
				) : holidays.length > 0 ? (
					<div className="ds-table-scroll">
						<table className="ds-slots-table">
							<thead>
								<tr>
									<th>{ __( 'Name', 'order-delivery-date' ) }</th>
									<th>{ __( 'Date', 'order-delivery-date' ) }</th>
									<th>{ __( 'Type', 'order-delivery-date' ) }</th>
								</tr>
							</thead>
							<tbody>
								{ holidays.map( ( h, i ) => (
									<tr key={ i }>
										<td>{ h.holiday_name || '—' }</td>
										<td>{ h.holiday_date }</td>
										<td style={ { textTransform: 'capitalize' } }>{ h.holiday_type }</td>
									</tr>
								) ) }
							</tbody>
						</table>
					</div>
				) : (
					<p className="ds-expand-empty">{ __( 'No holidays configured.', 'order-delivery-date' ) }</p>
				) }
			</div>

		</div>
	);
};

// ── ScheduleRow ───────────────────────────────────────────────────────────────

const ScheduleRow = ( { schedule, timeFormat, onEdit, defaultExpanded = false } ) => {
	const [ expanded, setExpanded ] = useState( defaultExpanded );

	const isEnabled  = schedule.status !== 'off' && schedule.status !== 'inactive';
	const dayCount   = countEnabledDays( schedule.weekdays );
	const numDates   = schedule.number_of_dates || '30';
	const checkout   = getCheckoutLabel( schedule );
	const minTime    = schedule.minimum_delivery_time;
	const sameDay    = fmtTime( schedule.same_day?.after_hours, schedule.same_day?.after_minutes, timeFormat );
	const nextDay    = fmtTime( schedule.next_day?.after_hours, schedule.next_day?.after_minutes, timeFormat );
	const sameDayCharge     = schedule.same_day?.additional_charges;
	const sameDayChargeType = schedule.same_day?.charges_type;
	const nextDayCharge     = schedule.next_day?.additional_charges;
	const nextDayChargeType = schedule.next_day?.charges_type;
	const slots      = getUniqueSlots( schedule.time_slots );
	const hasSlots          = schedule.orddd_lite_enable_time_slot === 'on' && slots.length > 0;
	const holidayCount      = Array.isArray( schedule.holidays ) ? schedule.holidays.filter( ( h ) => h.holiday_date ).length : 0;
	const hasCutoffs  = minTime || sameDay || nextDay;
	const isEstimated = ( schedule.orddd_delivery_checkout_options || schedule.orddd_pickup_checkout_options ) === 'text_block';
	return (
		<div className={ `ds-row${ ! isEnabled ? ' ds-row--disabled' : '' }` }>

			{ /* ── Main row ── */ }
			<div className="ds-row-main">

				<div className="ds-row-cell ds-row-cell--checkbox" />

				{ /* Expand / collapse arrow */ }
				<div className="ds-row-cell ds-row-cell--expand">
					{ ! isEstimated && (
						<button
							type="button"
							className={ `ds-expand-btn${ expanded ? ' ds-expand-btn--open' : '' }` }
							onClick={ () => setExpanded( ( o ) => ! o ) }
							aria-label={ __( 'Toggle row details', 'order-delivery-date' ) }
						>
							<svg width="10" height="10" viewBox="0 0 10 10" fill="none">
								{ expanded ? (
									<path d="M1 3L5 7L9 3" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
								) : (
									<path d="M3 1L7 5L3 9" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
								) }
							</svg>
						</button>
					) }
				</div>

				<div className="ds-row-cell ds-row-cell--status" />

				{ /* Schedule info */ }
				<div className="ds-row-cell ds-row-cell--schedule">
					<div className="ds-schedule-default-title">
						<span className="ds-schedule-default-name">{ __( 'Default Schedule', 'order-delivery-date' ) }</span>
						<span className="ds-badge ds-badge--default">{ __( 'DEFAULT', 'order-delivery-date' ) }</span>
					</div>
					<div className="ds-schedule-info">
						<div className="ds-schedule-info-item">
							<span className="ds-schedule-info-label">{ __( 'Delivery Date:', 'order-delivery-date' ) }</span>
							<span className={ `ds-schedule-info-status${ isEnabled ? ' ds-schedule-info-status--enabled' : ' ds-schedule-info-status--disabled' }` }>
								{ isEnabled ? __( 'Enabled', 'order-delivery-date' ) : __( 'Disabled', 'order-delivery-date' ) }
							</span>
						</div>
					</div>
				</div>

				{ /* Delivery days */ }
				<div className="ds-row-cell ds-row-cell--days">
					{ isEstimated ? (
						<div className="ds-day-summary" style={ { display: 'flex', flexDirection: 'column', gap: '2px' } }>
						<span style={ { fontWeight: '600' }} >{ __( 'Estimated Delivery Date', 'order-delivery-date' ) }</span>
						{ ( schedule.orddd_min_between_days || schedule.orddd_max_between_days ) && (
							<span>{ sprintf( __( 'Range: %s - %s Days', 'order-delivery-date' ), schedule.orddd_min_between_days || '—', schedule.orddd_max_between_days || '—' ) }</span>
						) }
					</div>
					) : (
						<>
							<DayPills weekdays={ schedule.weekdays } />
							<div className="ds-day-summary">
								<button
									type="button"
									className="ds-inline-link"
									onClick={ () => setExpanded( true ) }
								>
									{ sprintf( _n( '%d day', '%d days', dayCount, 'order-delivery-date' ), dayCount ) }
								</button>
								{ ' · ' }{ checkout }
								{ numDates ? ' · ' + sprintf( _n( '%d day ahead', '%d days ahead', parseInt( numDates ), 'order-delivery-date' ), parseInt( numDates ) ) : '' }
							</div>
						</>
					) }
				</div>

				{ /* Cutoffs */ }
				<div className="ds-row-cell ds-row-cell--cutoffs">
					{ hasCutoffs ? (
						<>
							{ sameDay && (
									<CutoffRow
										label={ __( 'Same day', 'order-delivery-date' ) }
										value={ sameDay }
										charge={ sameDayCharge }
										chargeType={ sameDayChargeType }
									/>
								) }
								{ sameDay && nextDay && <hr className="ds-cutoff-separator" /> }
								{ nextDay && (
									<CutoffRow
										label={ __( 'Next day', 'order-delivery-date' ) }
										value={ nextDay }
										charge={ nextDayCharge }
										chargeType={ nextDayChargeType }
									/>
								) }

								{ ( sameDay || nextDay ) && minTime && parseFloat (minTime) > 0 && <hr className="ds-cutoff-separator" /> }
								{ minTime && parseFloat (minTime) > 0 && (
								<CutoffRow
									label={ __( 'Minimum Delivery Time', 'order-delivery-date' ) }
									value={ sprintf( _n( '%s hour', '%s hours', parseFloat( minTime ), 'order-delivery-date' ), parseFloat( minTime ) ) }
								/>
							) }
						</>
					) : (
						<span className="ds-empty-dash">—</span>
					) }
				</div>

				{ /* Time Slots & Holidays */ }
				<div className="ds-row-cell ds-row-cell--slots">
				{ isEstimated ? null : <>
					{ /* Slots */ }
					<div className="ds-slots-section">
						{ hasSlots ? (
							<span className="ds-slots-text" onClick={ () => setExpanded( true ) } role="button" tabIndex={ 0 }>
								{ sprintf( _n( '%d slot', '%d slots', slots.length, 'order-delivery-date' ), slots.length ) }
							</span>
						) : (
							<span className="ds-no-value">{ __( 'No slots', 'order-delivery-date' ) }</span>
						) }
					</div>

					<div className="ds-slots-divider" />

					{ /* Holidays */ }
					<div className="ds-holidays-section">
						{ holidayCount === 0 ? (
							<span className="ds-no-value">{ __( 'No holidays', 'order-delivery-date' ) }</span>
						) : (
							<span className="ds-holidays-custom-text" onClick={ () => setExpanded( true ) } role="button" tabIndex={ 0 }>
								{ sprintf( _n( '%d holiday', '%d holidays', holidayCount, 'order-delivery-date' ), holidayCount ) }
							</span>
						) }
					</div>
				</> }
				</div>

				{ /* Actions */ }
				<div className="ds-row-cell ds-row-cell--actions">
					<button
						type="button"
						className="ds-action-btn ds-action-btn--edit"
						title={ __( 'Edit', 'order-delivery-date' ) }
						onClick={ () => onEdit( schedule ) }
					>
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none">
							<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
							<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
						</svg>
					</button>
				</div>

			</div>

			{ /* ── Expand panel ── */ }
			{ expanded && <ExpandPanel schedule={ schedule } isEstimated={ isEstimated } /> }

		</div>
	);
};

// ── DeliverySchedules page ────────────────────────────────────────────────────

const DeliverySchedules = () => {
	const [ schedules,  setSchedules  ] = useState( [] );
	const [ loading,    setLoading    ] = useState( true );
	const [ notice,     setNotice     ] = useState( null );
	const [ timeFormat, setTimeFormat ] = useState( '1' ); // '1' = 12h, '2' = 24h
	const navigate = useNavigate();

	const load = useCallback( async () => {
		setLoading( true );
		try {
			const data = await apiFetch( { path: '/orddd/v1/delivery_schedule' } );
			const mapped = Array.isArray( data ) ? data.map( ( s ) => ( {
				...s,
				status: s.orddd_shipping_settings_status ?? 'on',
			} ) ) : [];
			const defaultSchedule = mapped.find( ( s ) => String( s.delivery_schedule_id ) === '0' );
			if ( defaultSchedule?.appearance?.time_format ) {
				setTimeFormat( defaultSchedule.appearance.time_format );
			}
			setSchedules( defaultSchedule ? [ defaultSchedule ] : [] );
		} catch {
			setNotice( { type: 'error', message: __( 'Could not load delivery schedules. Please refresh the page.', 'order-delivery-date' ) } );
		} finally {
			setLoading( false );
		}
	}, [] );

	useEffect( () => { load(); }, [ load ] );

	const handleEdit = ( schedule ) => {
		navigate( `/edit-schedule/${ schedule.delivery_schedule_id }` );
	};

	// ── Render ──────────────────────────────────────────────────────────────────

	if ( loading ) {
		return <div className="ds-loading"><Spinner /></div>;
	}

	return (
		<div className="orddd-page-wrap ds-page">

			{ notice && (
				<Notice
					status={ notice.type }
					isDismissible
					onRemove={ () => setNotice( null ) }
				>
					{ notice.message }
				</Notice>
			) }

			{ /* ── Page header ── */ }
			<div className="ds-page-header">
				<h1 className="ds-page-title">{ __( 'Delivery & Pickup Schedules', 'order-delivery-date' ) }</h1>
				<div className="ds-page-header-actions">
					<a
						href={ upgradeUrl }
						target="_blank"
						rel="noopener noreferrer"
						className="ds-add-btn ds-add-btn--locked"
					>
						<Icon icon={ lock } size={ 14 } />
						{ __( '+ Add Schedule', 'order-delivery-date' ) }
					</a>
				</div>
			</div>

			{ /* ── Upsell banner ── */ }
			<div className="ds-info-banner ds-info-banner--upsell">
				<Icon icon={ starFilled } size={ 16 } className="ds-info-icon" />
				<span>
					{ __( 'Create custom schedules per shipping method, product, or location with ', 'order-delivery-date' ) }
					<a href={ trialUrl } target="_blank" rel="noopener noreferrer">
						{ __( 'Order Delivery Date Pro', 'order-delivery-date' ) }
					</a>
					{ __( '.', 'order-delivery-date' ) }
				</span>
			</div>

			{ /* ── Table ── */ }
			<div className="ds-table">

				{ /* Column headers */ }
				<div className="ds-table-header">
					<div className="ds-header-cell ds-header-cell--checkbox" />
					<div className="ds-header-cell ds-header-cell--expand" />
					<div className="ds-header-cell ds-header-cell--status" />
					<div className="ds-header-cell ds-header-cell--schedule">{ __( 'Schedule', 'order-delivery-date' ) }</div>
					<div className="ds-header-cell ds-header-cell--days">{ __( 'Delivery Days', 'order-delivery-date' ) }</div>
					<div className="ds-header-cell ds-header-cell--cutoffs">{ __( 'Cutoffs', 'order-delivery-date' ) }</div>
					<div className="ds-header-cell ds-header-cell--slots">{ __( 'Time Slots/Holidays', 'order-delivery-date' ) }</div>
					<div className="ds-header-cell ds-header-cell--actions">{ __( 'Actions', 'order-delivery-date' ) }</div>
				</div>

				{ schedules.length === 0 ? (
					<div className="ds-empty">{ __( 'No delivery schedules found.', 'order-delivery-date' ) }</div>
				) : (
					schedules.map( ( schedule ) => (
						<ScheduleRow
							key={ schedule.delivery_schedule_id }
							schedule={ schedule }
							timeFormat={ timeFormat }
							onEdit={ handleEdit }
							defaultExpanded={ schedules.length === 1 }
						/>
					) )
				) }

			</div>
		</div>
	);
};

export default DeliverySchedules;

/**
 * WordPress dependencies
 */
import { useState, useEffect, useRef } from '@wordpress/element';
import {
	ToggleControl,
	TextControl,
	SelectControl,
	Notice,
	Spinner,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';

/**
 * External dependencies
 */
import { useForm, Controller, useWatch } from 'react-hook-form';

/**
 * Internal dependencies
 */
import SettingsPanel, { AccordionCard, SettingRow } from '../../../components/SettingsPanel';

// ── Option lists (sourced from orddd-lite-config.php via ordddAdminData) ──────

// Formats a Date using jQuery UI datepicker format tokens.
const DAY_NAMES_SHORT   = [ 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' ];
const DAY_NAMES_LONG    = [ 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ];
const MONTH_NAMES_SHORT = [ 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' ];
const MONTH_NAMES_LONG  = [ 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' ];

const formatJQueryUIDate = ( date, format ) => {
	const map = {
		DD: DAY_NAMES_LONG[ date.getDay() ],
		D:  DAY_NAMES_SHORT[ date.getDay() ],
		MM: MONTH_NAMES_LONG[ date.getMonth() ],
		M:  MONTH_NAMES_SHORT[ date.getMonth() ],
		dd: String( date.getDate() ).padStart( 2, '0' ),
		d:  String( date.getDate() ),
		mm: String( date.getMonth() + 1 ).padStart( 2, '0' ),
		m:  String( date.getMonth() + 1 ),
		yy: String( date.getFullYear() ),
		y:  String( date.getFullYear() ).slice( -2 ),
	};
	return format.replace( /DD|D|MM|M|dd|d|mm|m|yy|y/g, ( token ) => map[ token ] );
};

const TODAY     = new Date();
const adminData = window.ordddAdminData ?? {};

const DATE_FORMAT_OPTIONS = Object.keys( adminData.date_formats ?? {} )
	.map( ( fmt ) => ( { value: fmt, label: formatJQueryUIDate( TODAY, fmt ) } ) );

const TIME_FORMAT_OPTIONS = Object.entries( adminData.time_formats ?? {} )
	.map( ( [ value, label ] ) => ( { value, label } ) );

const START_OF_WEEK_OPTIONS = Object.entries( adminData.days ?? {} )
	.map( ( [ value, label ] ) => ( { value, label } ) );

const NUMBER_OF_MONTHS_OPTIONS = Object.entries( adminData.number_of_months ?? {} )
	.map( ( [ value ] ) => ( { value: String( value ), label: String( value ) } ) );

const CALENDAR_THEME_OPTIONS = Object.entries( adminData.calendar_themes ?? {} )
	.map( ( [ value, label ] ) => ( { value, label } ) );

const LANGUAGE_OPTIONS = Object.entries( adminData.languages ?? {} )
	.map( ( [ value, label ] ) => ( { value, label } ) );

// ── Default values ─────────────────────────────────────────────────────────────

const DEFAULT_VALUES = {
	orddd_lite_language_selected:          'en-GB',
	orddd_lite_delivery_date_format:       'd MM, yy',
	orddd_lite_delivery_time_format:       '1',
	orddd_lite_start_of_week:              '0',
	orddd_lite_number_of_months:           '1',
	orddd_lite_calendar_theme:             'smoothness',
	orddd_lite_available_dates_color:      '#77c04b',
	orddd_lite_booked_dates_color:         '#f85959',
	orddd_lite_holiday_color:              '#ff0000',
	orddd_lite_show_partially_booked_dates: false,
};

const upgradeUrl =
	window.ordddAdminData?.upgradeUrl ??
	'https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/';

// ── LockedToggle ──────────────────────────────────────────────────────────────

const LockedToggle = () => (
	<div className="orddd-locked-toggle">
		<span className="orddd-locked-toggle__badge">
			{ __( 'Pro', 'order-delivery-date' ) }
		</span>
		<a
			href={ upgradeUrl }
			target="_blank"
			rel="noopener noreferrer"
			className="orddd-locked-toggle__link"
		>
			{ __( 'Upgrade to Pro', 'order-delivery-date' ) }
		</a>
	</div>
);

// ── ColorInput ────────────────────────────────────────────────────────────────

const ColorInput = ( { value, onChange } ) => (
	<div className="ap-color-input">
		<input
			type="color"
			className="ap-color-swatch"
			value={ value }
			onChange={ ( e ) => onChange( e.target.value ) }
		/>
		<TextControl
			className="ap-color-text"
			value={ value }
			onChange={ onChange }
			placeholder="#000000"
			__nextHasNoMarginBottom
		/>
	</div>
);

// ── CalendarAppearance ────────────────────────────────────────────────────────

const CalendarAppearance = () => {
	const [ loading, setLoading ] = useState( true );
	const [ saving,  setSaving  ] = useState( false );
	const [ notice,  setNotice  ] = useState( null );

	const { control, handleSubmit, reset } = useForm( { defaultValues: DEFAULT_VALUES } );

	// Watch theme to update the inline preview.
	const selectedTheme = useWatch( { control, name: 'orddd_lite_calendar_theme' } );

	// Inline datepicker preview.
	const previewRef     = useRef( null );
	const themeLinkRef   = useRef( null );
	const pluginUrl      = ( window.ordddAdminData || {} ).plugin_url || '';

	// Swap theme CSS whenever the selected theme changes.
	useEffect( () => {
		if ( ! pluginUrl || ! selectedTheme ) return;

		const href = `${ pluginUrl }css/themes/${ selectedTheme ?? 'smoothness' }/jquery-ui.css`;

		if ( ! themeLinkRef.current ) {
			const link = document.createElement( 'link' );
			link.rel  = 'stylesheet';
			link.id   = 'orddd-theme-preview-css';
			link.href = href;
			document.head.appendChild( link );
			themeLinkRef.current = link;
		} else {
			themeLinkRef.current.href = href;
		}

		return () => {
			// Leave the link in the DOM while the component is alive; remove on unmount.
		};
	}, [ selectedTheme, pluginUrl ] );

	// Remove the injected link when the component unmounts.
	useEffect( () => {
		return () => {
			if ( themeLinkRef.current ) {
				themeLinkRef.current.remove();
				themeLinkRef.current = null;
			}
		};
	}, [] );

	// Initialise / re-initialise inline jQuery UI datepicker.
	// Depends on `loading` so it runs once the spinner clears and the ref mounts.
	useEffect( () => {
		if ( loading ) return;
		const el = previewRef.current;
		if ( ! el || ! window.jQuery ) return;
		const $ = window.jQuery;
		$( el ).datepicker( 'destroy' );
		$( el ).datepicker( { inline: true, showOtherMonths: true } );
	}, [ loading, selectedTheme ] );

	// ── Fetch ──────────────────────────────────────────────────────────────────

	useEffect( () => {
		apiFetch( { path: '/orddd/v1/settings/appearance/calendar' } )
			.then( ( data ) => {
				const ca = data?.data ?? data ?? {};
				reset( {
					orddd_lite_language_selected:           ca.orddd_lite_language_selected                    ?? 'en-GB',
					orddd_lite_delivery_date_format:        ca.orddd_lite_delivery_date_format                 ?? 'd MM, yy',
					orddd_lite_delivery_time_format:        ca.orddd_lite_delivery_time_format                 ?? '1',
					orddd_lite_start_of_week:               String( ca.orddd_lite_start_of_week               ?? '0' ),
					orddd_lite_number_of_months:            String( ca.orddd_lite_number_of_months            ?? '1' ),
					orddd_lite_calendar_theme:              ca.orddd_lite_calendar_theme                       ?? 'smoothness',
					orddd_lite_available_dates_color:       ca.orddd_lite_available_dates_color                ?? '#77c04b',
					orddd_lite_booked_dates_color:          ca.orddd_booked_color                              ?? '#f85959',
					orddd_lite_holiday_color:               ca.orddd_lite_holiday_color                        ?? '#ff0000',
					orddd_lite_show_partially_booked_dates: ca.orddd_lite_show_partially_booked_dates          === 'on',
				} );
			} )
			.catch( () => setNotice( { type: 'error', message: __( 'Could not load settings. Please refresh and try again.', 'order-delivery-date' ) } ) )
			.finally( () => setLoading( false ) );
	}, [] );

	// ── Save ───────────────────────────────────────────────────────────────────

	const onSubmit = async ( values ) => {
		setSaving( true );
		setNotice( null );

		try {
			await apiFetch( {
				path:   '/orddd/v1/settings/appearance/calendar',
				method: 'POST',
				data:   {
					orddd_lite_language_selected:           values.orddd_lite_language_selected,
					orddd_lite_delivery_date_format:        values.orddd_lite_delivery_date_format,
					orddd_lite_delivery_time_format:        values.orddd_lite_delivery_time_format,
					orddd_lite_start_of_week:               values.orddd_lite_start_of_week,
					orddd_lite_number_of_months:            values.orddd_lite_number_of_months,
					orddd_lite_calendar_theme:              values.orddd_lite_calendar_theme,
					orddd_lite_available_dates_color:       values.orddd_lite_available_dates_color,
					orddd_booked_color:                     values.orddd_lite_booked_dates_color,
					orddd_lite_holiday_color:               values.orddd_lite_holiday_color,
					orddd_lite_show_partially_booked_dates: values.orddd_lite_show_partially_booked_dates ? 'on' : '',
				},
			} );

		} catch ( err ) {
			setNotice( {
				type:    'error',
				message: err?.message ?? __( 'Failed to save settings. Please try again.', 'order-delivery-date' ),
			} );
			throw err;
		} finally {
			setSaving( false );
		}
	};

	// ── Loading ───────────────────────────────────────────────────────────────

	if ( loading ) {
		return <div className="bd-loading"><Spinner /></div>;
	}

	// ── Render ────────────────────────────────────────────────────────────────

	return (
		<div className="orddd-page-wrap fa-page">
			{ notice && (
				<Notice status={ notice.type } isDismissible onRemove={ () => setNotice( null ) }>
					{ notice.message }
				</Notice>
			) }

			<SettingsPanel
				title={ __( 'Calendar Appearance Settings', 'order-delivery-date' ) }
				subtitle={ __( "Fit your needs by tailoring the calendar appearance from top to bottom (or let's talk calendar - from Jan through Dec!)", 'order-delivery-date' ) }
				noCard
				onSave={ handleSubmit( onSubmit ) }
				isSaving={ saving }
			>
				<AccordionCard title={ __( 'Calendar Appearance', 'order-delivery-date' ) }>
					<SettingRow
						label={ __( 'Calendar Language:', 'order-delivery-date' ) }
						tooltip={ __( 'The language used for month names, day names, and other calendar labels.', 'order-delivery-date' ) }
					>
						<Controller
							name="orddd_lite_language_selected"
							control={ control }
							render={ ( { field } ) => (
								<SelectControl
									value={ field.value }
									options={ LANGUAGE_OPTIONS }
									onChange={ field.onChange }
									__nextHasNoMarginBottom
								/>
							) }
						/>
					</SettingRow>

					<SettingRow
						label={ __( 'Date Format:', 'order-delivery-date' ) }
						tooltip={ __( 'How the selected delivery date is formatted in the order field and confirmation.', 'order-delivery-date' ) }
					>
						<Controller
							name="orddd_lite_delivery_date_format"
							control={ control }
							render={ ( { field } ) => (
								<SelectControl
									value={ field.value }
									options={ DATE_FORMAT_OPTIONS }
									onChange={ field.onChange }
									__nextHasNoMarginBottom
								/>
							) }
						/>
					</SettingRow>

					<SettingRow
						label={ __( 'Time Format:', 'order-delivery-date' ) }
						tooltip={ __( 'Whether time slots are shown in 12-hour (AM/PM) or 24-hour format.', 'order-delivery-date' ) }
					>
						<Controller
							name="orddd_lite_delivery_time_format"
							control={ control }
							render={ ( { field } ) => (
								<SelectControl
									value={ field.value }
									options={ TIME_FORMAT_OPTIONS }
									onChange={ field.onChange }
									__nextHasNoMarginBottom
								/>
							) }
						/>
					</SettingRow>

					<SettingRow
						label={ __( 'First Day of Week:', 'order-delivery-date' ) }
						tooltip={ __( 'Choose which day the calendar week starts on.', 'order-delivery-date' ) }
					>
						<Controller
							name="orddd_lite_start_of_week"
							control={ control }
							render={ ( { field } ) => (
								<SelectControl
									value={ field.value }
									options={ START_OF_WEEK_OPTIONS }
									onChange={ field.onChange }
									__nextHasNoMarginBottom
								/>
							) }
						/>
					</SettingRow>

					<SettingRow
						label={ __( 'Number of Months:', 'order-delivery-date' ) }
						tooltip={ __( 'How many calendar months to show at once on the checkout page.', 'order-delivery-date' ) }
					>
						<Controller
							name="orddd_lite_number_of_months"
							control={ control }
							render={ ( { field } ) => (
								<SelectControl
									value={ field.value }
									options={ NUMBER_OF_MONTHS_OPTIONS }
									onChange={ field.onChange }
									__nextHasNoMarginBottom
								/>
							) }
						/>
					</SettingRow>

					<SettingRow
						label={ __( 'Theme:', 'order-delivery-date' ) }
						tooltip={ __( 'The visual theme applied to the jQuery UI datepicker calendar widget.', 'order-delivery-date' ) }
					>
						<div>
							<Controller
								name="orddd_lite_calendar_theme"
								control={ control }
								render={ ( { field } ) => (
									<SelectControl
										value={ field.value }
										options={ CALENDAR_THEME_OPTIONS }
										onChange={ field.onChange }
										__nextHasNoMarginBottom
									/>
								) }
							/>
							<div className="orddd-theme-preview">
								<span className="orddd-theme-preview-label">{ __( 'Preview Theme:', 'order-delivery-date' ) }</span>
								<div ref={ previewRef } className="orddd-theme-preview-calendar" />
							</div>
						</div>
					</SettingRow>

					<SettingRow
						label={ __( 'Display availability on date:', 'order-delivery-date' ) }
						tooltip={ __( 'Show the availability widget that displays delivery dates on the frontend. Available in Pro.', 'order-delivery-date' ) }
					>
						<LockedToggle />
					</SettingRow>

				</AccordionCard>

				<AccordionCard title={ __( 'Color Code Your Calendar', 'order-delivery-date' ) }>
					<SettingRow
						label={ __( 'Show partially booked dates:', 'order-delivery-date' ) }
						tooltip={ __( 'Highlight dates that still have some delivery slots available on the calendar.', 'order-delivery-date' ) }
					>
						<Controller
							name="orddd_lite_show_partially_booked_dates"
							control={ control }
							render={ ( { field } ) => (
								<ToggleControl
									checked={ field.value }
									onChange={ field.onChange }
									__nextHasNoMarginBottom
								/>
							) }
						/>
					</SettingRow>

					<SettingRow
						label={ __( 'Available dates:', 'order-delivery-date' ) }
						tooltip={ __( 'Colour used to highlight dates that are open for delivery.', 'order-delivery-date' ) }
					>
						<Controller
							name="orddd_lite_available_dates_color"
							control={ control }
							render={ ( { field } ) => (
								<ColorInput value={ field.value } onChange={ field.onChange } />
							) }
						/>
					</SettingRow>

					<SettingRow
						label={ __( 'Booked dates:', 'order-delivery-date' ) }
						tooltip={ __( 'Colour used to indicate fully booked dates.', 'order-delivery-date' ) }
					>
						<Controller
							name="orddd_lite_booked_dates_color"
							control={ control }
							render={ ( { field } ) => (
								<ColorInput value={ field.value } onChange={ field.onChange } />
							) }
						/>
					</SettingRow>

					<SettingRow
						label={ __( 'Holidays:', 'order-delivery-date' ) }
						tooltip={ __( 'Colour used to mark holiday dates when delivery is unavailable.', 'order-delivery-date' ) }
					>
						<Controller
							name="orddd_lite_holiday_color"
							control={ control }
							render={ ( { field } ) => (
								<ColorInput value={ field.value } onChange={ field.onChange } />
							) }
						/>
					</SettingRow>

					<SettingRow
						label={ __( 'Cut-off time dates:', 'order-delivery-date' ) }
						tooltip={ __( 'Colour used to indicate dates that are past the cut-off time for ordering. Available in Pro.', 'order-delivery-date' ) }
					>
						<LockedToggle />
					</SettingRow>

				</AccordionCard>
			</SettingsPanel>
		</div>
	);
};

export default CalendarAppearance;

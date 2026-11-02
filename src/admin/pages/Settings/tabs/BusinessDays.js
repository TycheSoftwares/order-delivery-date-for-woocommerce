/**
 * WordPress dependencies
 */
import { useState, useEffect } from '@wordpress/element';
import { ToggleControl, FormTokenField, TextControl, Notice, Spinner } from '@wordpress/components';
import TimeInput from '../../../components/ui/TimeInput';
import apiFetch from '@wordpress/api-fetch';
import { __ } from '@wordpress/i18n';
import { UpgradePrompt } from '../../../components/ProNotice';

/**
 * External dependencies
 */
import { useForm, Controller } from 'react-hook-form';

/**
 * Internal dependencies
 */
import SettingsPanel, { SettingRow } from '../../../components/SettingsPanel';

// ── Constants ────────────────────────────────────────────────────────────────

// ── Time helpers ──────────────────────────────────────────────────────────────

/** "08:00 AM" / "07:15 PM"  →  "08:00" / "19:15" */
function to24Hour( timeStr ) {
	if ( ! timeStr ) return '';
	const match = timeStr.match( /^(\d{1,2}):(\d{2})\s*(AM|PM)$/i );
	if ( ! match ) return timeStr;
	let [ , h, m, meridiem ] = match;
	h = parseInt( h, 10 );
	if ( meridiem.toUpperCase() === 'AM' ) {
		if ( h === 12 ) h = 0;
	} else {
		if ( h !== 12 ) h += 12;
	}
	return `${ String( h ).padStart( 2, '0' ) }:${ m }`;
}

/** "08:00" / "19:15"  →  "08:00 AM" / "07:15 PM" */
function to12Hour( timeStr ) {
	if ( ! timeStr ) return '';
	const match = timeStr.match( /^(\d{1,2}):(\d{2})$/ );
	if ( ! match ) return timeStr;
	let [ , h, m ] = match;
	h = parseInt( h, 10 );
	const meridiem = h < 12 ? 'AM' : 'PM';
	if ( h === 0 ) h = 12;
	else if ( h > 12 ) h -= 12;
	return `${ String( h ).padStart( 2, '0' ) }:${ m } ${ meridiem }`;
}

const DAY_OPTIONS = [
	{ key: 'orddd_shipping_day_0', label: 'Sunday'    },
	{ key: 'orddd_shipping_day_1', label: 'Monday'    },
	{ key: 'orddd_shipping_day_2', label: 'Tuesday'   },
	{ key: 'orddd_shipping_day_3', label: 'Wednesday' },
	{ key: 'orddd_shipping_day_4', label: 'Thursday'  },
	{ key: 'orddd_shipping_day_5', label: 'Friday'    },
	{ key: 'orddd_shipping_day_6', label: 'Saturday'  },
];

const DAY_LABELS = DAY_OPTIONS.map( ( d ) => d.label );
const labelToKey = ( label ) => DAY_OPTIONS.find( ( d ) => d.label === label )?.key;

const DEFAULT_VALUES = {
	enableShippingDays: false,
	selectedDays:       [],
	openingTime:        '',
	closingTime:        '',
};

// ── Component ─────────────────────────────────────────────────────────────────

const BusinessDays = ( { locked = false } ) => {
	const [ loading, setLoading ] = useState( ! locked );
	const [ saving,  setSaving  ] = useState( false );
	const [ error,   setError   ] = useState( null );

	const { control, handleSubmit, reset } = useForm( { defaultValues: DEFAULT_VALUES } );

	// ── Fetch ──────────────────────────────────────────────────────────────────

	useEffect( () => {
		if ( locked ) return;

		apiFetch( { path: '/orddd/v1/settings' } )
			.then( ( data ) => {
				const bd = data?.business_days ?? {};
				reset( {
					enableShippingDays: bd.orddd_enable_shipping_days === 'on',
					selectedDays:       DAY_OPTIONS
						.filter( ( d ) => bd.business_days?.[ d.key ]?.enabled === 'checked' )
						.map( ( d ) => d.label ),
					openingTime:        to24Hour( bd.orddd_business_opening_time ?? '' ),
					closingTime:        to24Hour( bd.orddd_business_closing_time ?? '' ),
				} );
			} )
			.catch( () => {
				setError( __( 'Could not load settings. Please refresh and try again.', 'order-delivery-date' ) );
			} )
			.finally( () => setLoading( false ) );
	}, [] );

	// ── Save ───────────────────────────────────────────────────────────────────

	const onSubmit = async ( values ) => {
		setSaving( true );
		setError( null );

		try {
			await apiFetch( {
				path:   '/orddd/v1/settings',
				method: 'POST',
				data:   {
					key:                         'business_days',
					orddd_enable_shipping_days:  values.enableShippingDays ? 'on' : '',
					shipping_days:               values.selectedDays.map( labelToKey ).filter( Boolean ).join( ',' ),
					orddd_business_opening_time: to12Hour( values.openingTime ),
					orddd_business_closing_time: to12Hour( values.closingTime ),
				},
			} );
		} catch ( err ) {
			setError( err?.message ?? __( 'Failed to save settings. Please try again.', 'order-delivery-date' ) );
		} finally {
			setSaving( false );
		}
	};

	// ── Loading state ─────────────────────────────────────────────────────────

	if ( loading ) {
		return (
			<div className="bd-loading">
				<Spinner />
			</div>
		);
	}

	// ── Render ────────────────────────────────────────────────────────────────

	return (
		<div className="orddd-page-wrap">
			{ locked && <UpgradePrompt message={ __( 'Unlock Business Days configuration by upgrading to Pro.', 'order-delivery-date' ) } /> }
			{ error && ! locked && (
				<Notice
					status="error"
					isDismissible
					onRemove={ () => setError( null ) }
				>
					{ error }
				</Notice>
			) }

			<div className={ locked ? 'orddd-locked-panel' : undefined }>
			<SettingsPanel
				title={ __( 'Business Days and Hours', 'order-delivery-date' ) }
				subtitle={
					<>
						{ __( 'Business days a.k.a. working days and hours of your own company play a major role in correct delivery calculation. Eager for the deets?', 'order-delivery-date' ) }{ ' ' }
						<a
							href="https://www.tychesoftwares.com/docs/docs/order-delivery-date-pro-for-woocommerce/setup-shipping-days/?utm_source=userwebsite&utm_medium=link&utm_campaign=OrderDeliveryDateProSetting"
							target="_blank"
							rel="noreferrer"
						>
							{ __( 'this post', 'order-delivery-date' ) }
						</a>
						{ ' ' }{ __( 'to find out more.', 'order-delivery-date' ) }
					</>
				}
				onSave={ locked ? undefined : handleSubmit( onSubmit ) }
				isSaving={ saving }
			>
				{ /* ── Calculate by working days ──────────────────── */ }
				<SettingRow
					label={ __( 'Calculate delivery by working days:', 'order-delivery-date' ) }
					tooltip={ __( 'Calculate Minimum Delivery Time, Same Day cut-off and Next Day cut-off based on the business days selected.', 'order-delivery-date' ) }
				>
					<Controller
						name="enableShippingDays"
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

				{ /* ── Business days multi-select ──────────────────── */ }
				<SettingRow
					label={ __( 'Business Days:', 'order-delivery-date' ) }
					tooltip={ __( 'Business days of your store.', 'order-delivery-date' ) }
				>
					<Controller
							name="selectedDays"
							control={ control }
							render={ ( { field } ) => (
								<FormTokenField
									label={ __( 'Select \'Weekdays\' or \'Business Days\'', 'order-delivery-date' ) }
									value={ field.value }
									suggestions={ DAY_LABELS }
									onChange={ field.onChange }
									placeholder={ __( 'Select Weekdays', 'order-delivery-date' ) }
									__experimentalShowHowTo={ false }
									__experimentalExpandOnFocus
									__nextHasNoMarginBottom
								/>
							) }
						/>
				</SettingRow>

				{ /* ── Opening time ─────────────────────────────────── */ }
				<SettingRow
					label={ __( 'Opening Time:', 'order-delivery-date' ) }
					tooltip={ __( 'What time does your business start delivering orders?', 'order-delivery-date' ) }
				>
					<Controller
						name="openingTime"
						control={ control }
						render={ ( { field } ) => (
							<TimeInput
								value={ field.value }
								onChange={ field.onChange }
							/>
						) }
					/>
				</SettingRow>

				{ /* ── Closing time ─────────────────────────────────── */ }
				<SettingRow
					label={ __( 'Closing Time:', 'order-delivery-date' ) }
					tooltip={ __( 'What time does your business stop delivering orders?', 'order-delivery-date' ) }
				>
					<Controller
						name="closingTime"
						control={ control }
						render={ ( { field } ) => (
							<TimeInput
								value={ field.value }
								onChange={ field.onChange }
							/>
						) }
					/>
				</SettingRow>

			</SettingsPanel>
			</div>
		</div>
	);
};

export default BusinessDays;

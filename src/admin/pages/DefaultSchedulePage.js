/**
 * DefaultSchedulePage – edit form for the Default Schedule.
 * Lite settings are fully functional; Pro-only settings are shown locked.
 */

import { useForm, Controller } from 'react-hook-form';
import { useEffect, useState } from '@wordpress/element';
import { useNavigate } from 'react-router-dom';
import { CheckboxControl, TextControl, ToggleControl, Spinner, Notice } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { apiGet, apiPost } from '../api';
import SettingsPanel, { AccordionCard, SettingRow } from '../components/SettingsPanel';
import TimeSlotsTable from '../tables/TimeSlotsTable';

const upgradeUrl =
	window.ordddAdminData?.upgradeUrl ??
	'https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/';

const weekdayLabels = window.ordddAdminData?.weekdays ?? {};

// ── LockedToggle ───────────────────────────────────────────────────────────────

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

// ── Component ──────────────────────────────────────────────────────────────────

export default function DefaultSchedulePage() {
	const navigate = useNavigate();

	const [ loading,   setLoading   ] = useState( true );
	const [ saving,    setSaving    ] = useState( false );
	const [ notice,    setNotice    ] = useState( null );
	const [ timeSlots, setTimeSlots ] = useState( [] );

	const { control, handleSubmit, reset, watch } = useForm( {
		defaultValues: {
			orddd_lite_enable_delivery_date:                    false,
			weekdays: {
				orddd_lite_weekday_0: false,
				orddd_lite_weekday_1: false,
				orddd_lite_weekday_2: false,
				orddd_lite_weekday_3: false,
				orddd_lite_weekday_4: false,
				orddd_lite_weekday_5: false,
				orddd_lite_weekday_6: false,
			},
			orddd_lite_minimumOrderDays:                        '0',
			orddd_lite_number_of_dates:                        '10',
			orddd_lite_date_field_mandatory:                   false,
			orddd_lite_lockout_date_after_orders:              '0',
			orddd_lockout_date_quantity_based:                 false,
			orddd_lite_calculate_min_time_disabled_days:       false,
			orddd_lite_enable_time_slot:                       false,
			orddd_lite_time_slot_mandatory:                    false,
			orddd_lite_time_slot_asap:                         false,
			orddd_lite_auto_populate_first_available_time_slot: false,
		},
	} );

	const enableTimeSlot = watch( 'orddd_lite_enable_time_slot' );

	useEffect( () => {
		apiGet( '/settings' )
			.then( ( data ) => {
				const ds = data?.default_schedule ?? {};

				const weekdaysBool = {};
				const rawWeekdays  = ds.weekdays ?? {};
				for ( let i = 0; i <= 6; i++ ) {
					const key = `orddd_lite_weekday_${ i }`;
					weekdaysBool[ key ] = rawWeekdays[ key ] === 'checked';
				}

				reset( {
					orddd_lite_enable_delivery_date:                    ds.orddd_lite_enable_delivery_date === 'on',
					weekdays:                                            weekdaysBool,
					orddd_lite_minimumOrderDays:                        ds.orddd_lite_minimumOrderDays ?? '0',
					orddd_lite_number_of_dates:                        ds.orddd_lite_number_of_dates ?? '10',
					orddd_lite_date_field_mandatory:                   ds.orddd_lite_date_field_mandatory === 'on',
					orddd_lite_lockout_date_after_orders:              ds.orddd_lite_lockout_date_after_orders ?? '0',
					orddd_lockout_date_quantity_based:                 ds.orddd_lockout_date_quantity_based === 'on',
					orddd_lite_calculate_min_time_disabled_days:       ds.orddd_lite_calculate_min_time_disabled_days === 'on',
					orddd_lite_enable_time_slot:                       ds.orddd_lite_enable_time_slot === 'on',
					orddd_lite_time_slot_mandatory:                    ds.orddd_lite_time_slot_mandatory === 'on',
					orddd_lite_time_slot_asap:                         ds.orddd_lite_time_slot_asap === 'on',
					orddd_lite_auto_populate_first_available_time_slot: ds.orddd_lite_auto_populate_first_available_time_slot === 'on',
				} );

				setTimeSlots( ds.time_slots ?? [] );
			} )
			.catch( () => {
				setNotice( { type: 'error', message: __( 'Could not load settings. Please refresh and try again.', 'order-delivery-date' ) } );
			} )
			.finally( () => setLoading( false ) );
	}, [] );

	const onSubmit = async ( values ) => {
		setSaving( true );
		setNotice( null );

		const weekdaysMapped = {};
		for ( const [ key, val ] of Object.entries( values.weekdays ) ) {
			weekdaysMapped[ key ] = val ? 'checked' : '';
		}

		try {
			await apiPost( '/settings', {
				key:                                               'default_schedule',
				orddd_lite_enable_delivery_date:                  values.orddd_lite_enable_delivery_date ? 'on' : '',
				weekdays:                                         weekdaysMapped,
				orddd_lite_minimumOrderDays:                      values.orddd_lite_minimumOrderDays,
				orddd_lite_number_of_dates:                       values.orddd_lite_number_of_dates,
				orddd_lite_date_field_mandatory:                  values.orddd_lite_date_field_mandatory ? 'on' : '',
				orddd_lite_lockout_date_after_orders:             values.orddd_lite_lockout_date_after_orders,
				orddd_lockout_date_quantity_based:                values.orddd_lockout_date_quantity_based ? 'on' : '',
				orddd_lite_calculate_min_time_disabled_days:      values.orddd_lite_calculate_min_time_disabled_days ? 'on' : '',
				orddd_lite_enable_time_slot:                      values.orddd_lite_enable_time_slot ? 'on' : '',
				orddd_lite_time_slot_mandatory:                   values.orddd_lite_time_slot_mandatory ? 'on' : '',
				orddd_lite_time_slot_asap:                        values.orddd_lite_time_slot_asap ? 'on' : '',
				orddd_lite_auto_populate_first_available_time_slot: values.orddd_lite_auto_populate_first_available_time_slot ? 'on' : '',
				time_slots:                                       timeSlots,
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

	if ( loading ) {
		return <div className="bd-loading"><Spinner /></div>;
	}

	return (
		<div className="orddd-page-wrap">
			{ notice && (
				<Notice status={ notice.type } isDismissible onRemove={ () => setNotice( null ) }>
					{ notice.message }
				</Notice>
			) }

			<SettingsPanel
				title={ __( 'Default Schedule', 'order-delivery-date' ) }
				subtitle={ __( 'Configure delivery days, timing, and time slots for your store.', 'order-delivery-date' ) }
				onSave={ handleSubmit( onSubmit ) }
				isSaving={ saving }
				onBack={ () => navigate( '/delivery-schedule' ) }
				backLabel={ __( '← Delivery Schedules', 'order-delivery-date' ) }
				noCard
			>

				{ /* ── Delivery Settings ─────────────────────────────────────────── */ }
				<AccordionCard title={ __( 'Delivery Settings', 'order-delivery-date' ) }>

					<SettingRow
						label={ __( 'Enable Delivery Date on Checkout:', 'order-delivery-date' ) }
						tooltip={ __( 'Show the delivery date picker on the checkout page.', 'order-delivery-date' ) }
					>
						<Controller
							name="orddd_lite_enable_delivery_date"
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
						label={ __( 'Delivery Days:', 'order-delivery-date' ) }
						tooltip={ __( 'Select the days of the week on which delivery is available.', 'order-delivery-date' ) }
					>
						<div style={ { display: 'flex', flexWrap: 'wrap', gap: '8px 20px' } }>
							{ Object.entries( weekdayLabels ).map( ( [ key, label ] ) => (
								<Controller
									key={ key }
									name={ `weekdays.${ key }` }
									control={ control }
									render={ ( { field } ) => (
										<CheckboxControl
											label={ label }
											checked={ field.value }
											onChange={ field.onChange }
											__nextHasNoMarginBottom
										/>
									) }
								/>
							) ) }
						</div>
					</SettingRow>

					<SettingRow
						label={ __( 'Minimum Order Days:', 'order-delivery-date' ) }
						tooltip={ __( 'Minimum number of days required before the delivery date.', 'order-delivery-date' ) }
					>
						<Controller
							name="orddd_lite_minimumOrderDays"
							control={ control }
							render={ ( { field } ) => (
								<TextControl
									type="number"
									min="0"
									value={ field.value }
									onChange={ field.onChange }
									style={ { width: '100px' } }
									__nextHasNoMarginBottom
								/>
							) }
						/>
					</SettingRow>

					<SettingRow
						label={ __( 'Number of Dates to Show:', 'order-delivery-date' ) }
						tooltip={ __( 'How many upcoming delivery dates to display to the customer.', 'order-delivery-date' ) }
					>
						<Controller
							name="orddd_lite_number_of_dates"
							control={ control }
							render={ ( { field } ) => (
								<TextControl
									type="number"
									min="1"
									value={ field.value }
									onChange={ field.onChange }
									style={ { width: '100px' } }
									__nextHasNoMarginBottom
								/>
							) }
						/>
					</SettingRow>

					<SettingRow
						label={ __( 'Delivery Date is Mandatory:', 'order-delivery-date' ) }
						tooltip={ __( 'Require customers to select a delivery date before placing an order.', 'order-delivery-date' ) }
					>
						<Controller
							name="orddd_lite_date_field_mandatory"
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
						label={ __( 'Lockout After N Orders:', 'order-delivery-date' ) }
						tooltip={ __( 'Disable a date after this many orders. Set to 0 for unlimited.', 'order-delivery-date' ) }
					>
						<Controller
							name="orddd_lite_lockout_date_after_orders"
							control={ control }
							render={ ( { field } ) => (
								<TextControl
									type="number"
									min="0"
									value={ field.value }
									onChange={ field.onChange }
									style={ { width: '100px' } }
									__nextHasNoMarginBottom
								/>
							) }
						/>
					</SettingRow>

					<SettingRow
						label={ __( 'Lockout Based on Product Quantity:', 'order-delivery-date' ) }
						tooltip={ __( 'When enabled, lockout counts product quantities instead of number of orders.', 'order-delivery-date' ) }
					>
						<Controller
							name="orddd_lockout_date_quantity_based"
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
						label={ __( 'Skip Disabled Days for Minimum Time:', 'order-delivery-date' ) }
						tooltip={ __( 'When calculating minimum delivery time, skip days that are disabled for delivery.', 'order-delivery-date' ) }
					>
						<Controller
							name="orddd_lite_calculate_min_time_disabled_days"
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

				</AccordionCard>

				{ /* ── Same Day & Next Day (Pro) ──────────────────────────────────── */ }
				<AccordionCard title={ __( 'Same Day & Next Day Delivery', 'order-delivery-date' ) } defaultOpen={ false }>

					<SettingRow
						label={ __( 'Cut-off Time for Same Day Delivery:', 'order-delivery-date' ) }
						tooltip={ __( 'Orders placed before this time can select same-day delivery.', 'order-delivery-date' ) }
					>
						<LockedToggle />
					</SettingRow>

					<SettingRow
						label={ __( 'Additional Charges for Same Day Delivery:', 'order-delivery-date' ) }
						tooltip={ __( 'Extra fee applied when a customer selects same-day delivery.', 'order-delivery-date' ) }
					>
						<LockedToggle />
					</SettingRow>

					<SettingRow
						label={ __( 'Cut-off Time for Next Day Delivery:', 'order-delivery-date' ) }
						tooltip={ __( 'Orders placed before this time can select next-day delivery.', 'order-delivery-date' ) }
					>
						<LockedToggle />
					</SettingRow>

					<SettingRow
						label={ __( 'Additional Charges for Next Day Delivery:', 'order-delivery-date' ) }
						tooltip={ __( 'Extra fee applied when a customer selects next-day delivery.', 'order-delivery-date' ) }
					>
						<LockedToggle />
					</SettingRow>

				</AccordionCard>

				{ /* ── Delivery Charges (Pro) ──────────────────────────────────────── */ }
				<AccordionCard title={ __( 'Delivery Charges', 'order-delivery-date' ) } defaultOpen={ false }>

					<SettingRow
						label={ __( 'Delivery Charges Label:', 'order-delivery-date' ) }
						tooltip={ __( 'Custom label shown to customers for the delivery charge at checkout.', 'order-delivery-date' ) }
					>
						<LockedToggle />
					</SettingRow>

					<SettingRow
						label={ __( 'Minimum Order Amount to Waive Charges:', 'order-delivery-date' ) }
						tooltip={ __( 'Delivery charges are waived when the order total exceeds this amount.', 'order-delivery-date' ) }
					>
						<LockedToggle />
					</SettingRow>

					<SettingRow
						label={ __( 'Rush Delivery Charges Based on Number of Days:', 'order-delivery-date' ) }
						tooltip={ __( 'Charge more for deliveries sooner than a set number of days out.', 'order-delivery-date' ) }
					>
						<LockedToggle />
					</SettingRow>

					<SettingRow
						label={ __( 'Enable Tax on Delivery Charges:', 'order-delivery-date' ) }
						tooltip={ __( 'Apply tax calculation to delivery charges.', 'order-delivery-date' ) }
					>
						<LockedToggle />
					</SettingRow>

				</AccordionCard>

				{ /* ── Schedule Conditions (Pro) ───────────────────────────────────── */ }
				<AccordionCard title={ __( 'Schedule Conditions', 'order-delivery-date' ) } defaultOpen={ false }>

					<SettingRow
						label={ __( 'Apply Schedule Based On:', 'order-delivery-date' ) }
						tooltip={ __( 'Restrict this schedule to specific shipping methods or product categories.', 'order-delivery-date' ) }
					>
						<LockedToggle />
					</SettingRow>

					<SettingRow
						label={ __( 'Shipping Methods:', 'order-delivery-date' ) }
						tooltip={ __( 'Select which shipping methods this schedule applies to.', 'order-delivery-date' ) }
					>
						<LockedToggle />
					</SettingRow>

					<SettingRow
						label={ __( 'Product Categories:', 'order-delivery-date' ) }
						tooltip={ __( 'Select which product categories this schedule applies to.', 'order-delivery-date' ) }
					>
						<LockedToggle />
					</SettingRow>

					<SettingRow
						label={ __( 'Allow Customers to Reschedule:', 'order-delivery-date' ) }
						tooltip={ __( 'Let customers change their delivery or pickup date and time after placing an order.', 'order-delivery-date' ) }
					>
						<LockedToggle />
					</SettingRow>

				</AccordionCard>

				{ /* ── Time Slots ──────────────────────────────────────────────────── */ }
				<AccordionCard title={ __( 'Time Slots', 'order-delivery-date' ) }>

					<SettingRow
						label={ __( 'Enable Time Slots:', 'order-delivery-date' ) }
						tooltip={ __( 'Let customers choose a delivery time slot at checkout.', 'order-delivery-date' ) }
					>
						<Controller
							name="orddd_lite_enable_time_slot"
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

					{ enableTimeSlot && (
						<>
							<SettingRow
								label={ __( 'Time Slot is Mandatory:', 'order-delivery-date' ) }
								tooltip={ __( 'Require customers to select a time slot before placing an order.', 'order-delivery-date' ) }
							>
								<Controller
									name="orddd_lite_time_slot_mandatory"
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
								label={ __( 'Show "As Soon As Possible" Option:', 'order-delivery-date' ) }
								tooltip={ __( 'Add an ASAP option to the time slot selector.', 'order-delivery-date' ) }
							>
								<Controller
									name="orddd_lite_time_slot_asap"
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
								label={ __( 'Auto-populate First Available Slot:', 'order-delivery-date' ) }
								tooltip={ __( 'Automatically pre-select the first available time slot for the customer.', 'order-delivery-date' ) }
							>
								<Controller
									name="orddd_lite_auto_populate_first_available_time_slot"
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

							<SettingRow label={ __( 'Time Slot Table:', 'order-delivery-date' ) }>
								<TimeSlotsTable
									slots={ timeSlots }
									onChange={ setTimeSlots }
								/>
							</SettingRow>
						</>
					) }

				</AccordionCard>

			</SettingsPanel>
		</div>
	);
}

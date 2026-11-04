/**
 * Edit / Add Delivery Schedule
 *
 * Routes:
 *   /edit-schedule/new  → create a new schedule
 *   /edit-schedule/:id  → edit an existing schedule
 */

import { useState, useEffect } from '@wordpress/element';
import { Spinner, Notice, Button } from '@wordpress/components';
import apiFetch    from '@wordpress/api-fetch';
import { useNavigate, useParams } from 'react-router-dom';
import { useForm, useWatch } from 'react-hook-form';
import { __ } from '@wordpress/i18n';

import SettingsPanel, { AccordionCard } from '../../components/SettingsPanel';

import { DEFAULT_DELIVERY_DAYS, EMPTY_FORM, getScheduleLabels } from './constants';

import ScheduleBasedOnSection from './sections/ScheduleBasedOn';
import Panel1DateSettings     from './sections/dateSettings';
import Panel2TimeSlot         from './sections/TimeSlot';
import Panel3DeliverySchedule from './sections/DeliverySchedule';
import Panel4AdditionalCharges from './sections/AdditionalCharges';
import Panel5WeekdaySchedule  from './sections/WeekdaySchedule';
import PanelTimeSlotCutoffs   from './sections/TimeSlotCutoffs';

// ── Main: EditSchedule ────────────────────────────────────────────────────────

const EditSchedule = () => {
	const { id }    = useParams();
	const navigate  = useNavigate();
	const isNew     = id === 'new';
	const isDefault = id === '0';

	const [ loading,              setLoading              ] = useState( ! isNew );
	const [ saving,               setSaving               ] = useState( false );
	const [ notice,               setNotice               ] = useState( null );
	const [ scheduleTitle,        setScheduleTitle        ] = useState( '' );
	const [ deliveryDays,         setDeliveryDays         ] = useState( DEFAULT_DELIVERY_DAYS );
	const [ timeSlots,            setTimeSlots            ] = useState( [] );
	const [ weekdayBasedSchedule, setWeekdayBasedSchedule ] = useState( [] );
	const [ chargesRows,          setChargesRows          ] = useState( [] );
	const [ tsCutoffRows,         setTsCutoffRows         ] = useState( [] );

	const { watch, control, setValue, reset, handleSubmit } = useForm( { defaultValues: EMPTY_FORM } );

	const form            = watch();
	const checkoutOption  = useWatch( { control, name: 'orddd_lite_delivery_checkout_options' } );
	const isEstimatedText = checkoutOption === 'text_block';
	const scheduleType    = form.schedule_type || 'delivery';
	const labels          = getScheduleLabels( scheduleType );

	// ── Load ──────────────────────────────────────────────────────────────────

	useEffect( () => {
		if ( isNew ) return;

		setLoading( true );
		apiFetch( { path: `/orddd/v1/delivery_schedule/${ id }` } )
			.then( ( data ) => {
				if ( id === '0' ) {
					setScheduleTitle( __( 'Edit Default Schedule', 'order-delivery-date' ) );
				} else if ( Array.isArray( data.title ) && data.title.length > 0 ) {
					//setScheduleTitle( data.title[ 0 ].title_full || __( 'Unnamed Schedule', 'order-delivery-date' ) );
					setScheduleTitle( __( 'Edit Custom Schedule', 'order-delivery-date' ) );
				}

				// Fallback to 'shipping_methods' when the stored value is empty or a blank array (e.g. imported settings).
				const basedOn = ( Array.isArray( data.delivery_settings_based_on )
					? data.delivery_settings_based_on[ 0 ]
					: data.delivery_settings_based_on
				) || 'shipping_methods';
				
				const defaulScheduleData = {
					// ── Keys match the Lite WP option names directly ──────────────
					orddd_lite_enable_delivery_date:        data.orddd_lite_enable_delivery_date        || '',
					orddd_lite_delivery_checkout_options:   data.orddd_lite_delivery_checkout_options   || 'delivery_calendar',
					orddd_lite_lockout_date_after_orders:   data.orddd_lite_lockout_date_after_orders   || '',
					orddd_lite_date_field_mandatory:        data.orddd_lite_date_field_mandatory        || '',
					orddd_lite_number_of_dates:             data.orddd_lite_number_of_dates             || '30',
					orddd_lite_delivery_date_field_label:   data.orddd_lite_delivery_date_field_label   || '',
					orddd_lite_delivery_date_field_note:    data.orddd_lite_delivery_date_field_note    || '',
					orddd_lite_minimumOrderDays:                        data.orddd_lite_minimumOrderDays                        || '',
					orddd_lite_calculate_min_time_disabled_days:        data.orddd_lite_calculate_min_time_disabled_days        || '',
					orddd_lite_enable_time_slot:            data.orddd_lite_enable_time_slot            || '',
					orddd_lite_time_slot_mandatory:         data.orddd_lite_time_slot_mandatory         || '',
					orddd_lite_time_slot_asap:              data.orddd_lite_time_slot_asap              || '',
					orddd_lite_delivery_timeslot_field_label: data.orddd_lite_delivery_timeslot_field_label || '',
					// ── Complex / nested ──────────────────────────────────────────
					delivery_type:      data.delivery_type  || { weekdays: 'on', specific_dates: '' },
					orddd_min_between_days: data.orddd_min_between_days || '',
					orddd_max_between_days: data.orddd_max_between_days || '',
					time_settings:      data.time_settings  || { enable: '', from_hours: '', to_hours: '' },
					same_day:           data.same_day       || { after_hours: '', after_minutes: '00', additional_charges: '', charges_type: 'fixed' },
					next_day:           data.next_day       || { after_hours: '', after_minutes: '00', additional_charges: '', charges_type: 'fixed' },
				}

				const customScheduleData = {
					schedule_type:                              data.schedule_type                              || 'delivery',
					delivery_settings_based_on:                 basedOn,
					shipping_methods:                           data.shipping_methods                           || [],
					product_categories:                         data.product_categories                         || [],
					shipping_methods_for_categories:            data.shipping_methods_for_categories            || [],
					products:                                   data.products                                   || [],
					shipping_methods_for_products:              data.shipping_methods_for_products              || [],
					product_tags:                               data.product_tags                                 || [],
					shipping_methods_for_product_tags:          data.shipping_methods_for_product_tags              || [],
					orddd_locations:                            data.orddd_locations                            || [],
					categories_for_pickup_locations:            data.categories_for_pickup_locations            || [],
					order_type:                                 data.order_type                                 || [],
					user_role:                                  data.user_role                                  || [],
					enable_shipping_based_delivery:             data.enable_shipping_based_delivery             || '',
					orddd_shipping_settings_status:             data.orddd_shipping_settings_status             || '',
					orddd_delivery_checkout_options:            data.orddd_delivery_checkout_options            || 'delivery_calendar',
					orddd_enable_specific_delivery_dates:       data.orddd_enable_specific_delivery_dates          || '',
					delivery_type:                              data.delivery_type                              || { weekdays: 'on', specific_dates: '' },
					date_lockout:                               data.date_lockout                               || '',
					date_mandatory_field:                       data.date_mandatory_field                       || '',
					number_of_dates:                            data.number_of_dates                            || '30',
					orddd_shipping_based_delivery_date_field_label:     data.orddd_shipping_based_delivery_date_field_label     || '',
					orddd_shipping_based_delivery_date_field_note_text: data.orddd_shipping_based_delivery_date_field_note_text || '',
					minimum_delivery_time:                      data.minimum_delivery_time                      || '',
					minimum_pickup_time:                        data.minimum_pickup_time                  || '',
					orddd_min_between_days:                     data.orddd_min_between_days                     || '',
					orddd_max_between_days:                     data.orddd_max_between_days                     || '',
					enable_time_slot:                           data.enable_time_slot                           || '',
					timeslot_mandatory_field:                   data.timeslot_mandatory_field                   || '',
					timeslot_asap_option:                       data.timeslot_asap_option                       || '',
					orddd_asap_charges:                         data.orddd_asap_charges                         || '',
					orddd_asap_charges_type:                    data.orddd_asap_charges_type                    || 'fixed',
					orddd_asap_charges_label:                   data.orddd_asap_charges_label                   || '',
					orddd_shipping_based_delivery_timeslot_field_label: data.orddd_shipping_based_delivery_timeslot_field_label || '',
					time_settings:                              data.time_settings                              || { enable: '', from_hours: '', to_hours: '' },
					orddd_priority_for_delivery_schedule:       data.orddd_priority_for_delivery_schedule       || '',
					same_day:                                   data.same_day                                   || { after_hours: '', after_minutes: '00', additional_charges: '', charges_type: 'fixed' },
					next_day:                                   data.next_day                                   || { after_hours: '', after_minutes: '00', additional_charges: '', charges_type: 'fixed' },
					max_order_total:          					data.max_order_total            || '',
					orddd_delivery_charges_based_on_days_enable: data.orddd_delivery_charges_based_on_days_enable || '',
					orddd_additional_settings_based_on_days:    data.orddd_additional_settings_based_on_days           || '',
					weekday_based_schedule_enable:              data.weekday_based_schedule_enable              || '',
				}

				if ( String( id ) === '0' ) {
					reset( defaulScheduleData );
				} else {
					reset( customScheduleData );
				}

				// Build weekday rows.
				const weekdayRows = DEFAULT_DELIVERY_DAYS.map( ( d ) => ( {
					...d,
					...( data.weekdays?.[ d.key ] ? {
						enable:             data.weekdays[ d.key ].enable ?? d.enable,
						additional_charges: data.weekdays[ d.key ].additional_charges || '',
						charges_type:       data.weekdays[ d.key ].charges_type       || 'fixed',
						charges_label:      data.weekdays[ d.key ].charges_label      || 'Delivery Charges',
						max_orders:         data.weekdays[ d.key ].max_orders         || '',
					} : {} ),
				} ) );

				// Append saved specific dates (returned as `data.specific_dates` by the API).
				const specRows = Array.isArray( data.specific_dates )
					? data.specific_dates.map( ( s, i ) => ( {
						key:                `spec_${ s.date ?? i }`,
						name:               s.date ?? '',
						delivery_type:      s.date ?? '',
						date_from:          s.date ?? '',
						date_to:            '',
						range_type:         'individual',
						additional_charges: s.additional_charges ?? '',
						charges_type:       s.charges_type       ?? 'fixed',
						charges_label:      s.charges_label      ?? '',
						max_orders:         s.max_orders         ?? '',
						edit:               false,
						isSpecific:         true,
					} ) )
					: [];

				setDeliveryDays( [ ...weekdayRows, ...specRows ] );

				if ( Array.isArray( data.time_slots ) ) {
					setTimeSlots( data.time_slots.map( ( s ) => ( { ...s, edit: false, check: false } ) ) );
				}
				const toArray = ( val ) =>
					Array.isArray( val ) ? val : ( val && typeof val === 'object' ? Object.values( val ) : [] );

				const weekdayScheduleData = toArray( data.weekday_based_schedule );
				if ( weekdayScheduleData.length > 0 ) {
					setWeekdayBasedSchedule( weekdayScheduleData.map( ( r ) => ( { ...r, edit: false } ) ) );
				}
				if ( Array.isArray( data.orddd_additional_settings_based_on_days ) ) {
					setChargesRows( data.orddd_additional_settings_based_on_days );
				}
				const tsCutoffData = toArray( data.time_slot_cutoffs );
				if ( tsCutoffData.length > 0 ) {
					setTsCutoffRows( tsCutoffData.map( ( r ) => ( { ...r, edit: false } ) ) );
				}
			} )
			.catch( () => setNotice( { type: 'error', message: __( 'Could not load schedule data.', 'order-delivery-date' ) } ) )
			.finally( () => setLoading( false ) );
	}, [ id, isNew ] ); // eslint-disable-line react-hooks/exhaustive-deps

	// ── Save ──────────────────────────────────────────────────────────────────

	const onSubmit = async ( values ) => {
		setSaving( true );
		setNotice( null );

		const weekdaysObj    = {};
		const specificDatesArr = [];

		deliveryDays.forEach( ( d ) => {
			if ( d.isSpecific ) {
				// Specific date row — send as a separate array entry.
				specificDatesArr.push( {
					delivery_type:      d.range_type === 'date_range' ? ( d.date_from ?? '' ) : ( d.delivery_type ?? d.delivery_dates?.[ 0 ] ?? d.name ?? d.date ?? '' ),
					date_to:            d.date_to            ?? '',
					range_type:         d.range_type         ?? 'individual',
					additional_charges: d.additional_charges ?? '',
					charges_type:       d.charges_type       ?? 'fixed',
					charges_label:      d.charges_label      ?? '',
					max_orders:         d.max_orders         ?? '',
				} );
			} else {
				weekdaysObj[ d.key ] = {
					enable:             d.enable,
					additional_charges: d.additional_charges,
					charges_type:       d.charges_type,
					charges_label:      d.charges_label,
					max_orders:         d.max_orders,
				};
			}
		} );

		const payload = {
			shipping_settings: {
				...values,
				weekdays:                       weekdaysObj,
				specific_dates:                 specificDatesArr,
				time_slots:                     timeSlots,
				weekday_based_schedule:         weekdayBasedSchedule,
				orddd_additional_settings_based_on_days: chargesRows,
				time_slot_cutoffs:              tsCutoffRows,
			},
		};

		try {
			if ( isNew ) {
				await apiFetch( { path: '/orddd/v1/delivery_schedule', method: 'POST', data: payload } );
				setNotice( { type: 'success', message: __( 'Schedule created successfully.', 'order-delivery-date' ) } );
				setTimeout( () => navigate( '/delivery-schedule' ), 1200 );
			} else {
				await apiFetch( { path: `/orddd/v1/delivery_schedule/${ id }`, method: 'PUT', data: payload } );
				setNotice( { type: 'success', message: __( 'Schedule saved successfully.', 'order-delivery-date' ) } );
			}
		} catch ( e ) {
			setNotice( { type: 'error', message: e?.message || __( 'Failed to save schedule. Please try again.', 'order-delivery-date' ) } );
		} finally {
			setSaving( false );
		}
	};

	// ── Render ────────────────────────────────────────────────────────────────

	if ( loading ) {
		return (
			<div className="osp-wrap" style={ { display: 'flex', justifyContent: 'center', padding: '60px' } }>
				<Spinner />
			</div>
		);
	}

	const pageTitle = isNew ? __( 'Add New Schedule', 'order-delivery-date' ) : ( scheduleTitle || __( 'Edit Schedule', 'order-delivery-date' ) );

	return (
		<div className="orddd-page-wrap">

			<div style={ { marginBottom: '8px', paddingTop: '30px' } }>
				<Button
					variant="link"
					style={ { color: '#41278d', padding: 0, fontSize: '15px', textDecoration: 'none' } }
					onClick={ () => navigate( '/delivery-schedule' ) }
				>
					{ __( '← Back to Schedules', 'order-delivery-date' ) }
				</Button>
			</div>

			{ notice && (
				<Notice
					status={ notice.type }
					isDismissible
					onRemove={ () => setNotice( null ) }
					style={ { marginBottom: '16px' } }
				>
					{ notice.message }
				</Notice>
			) }

			<SettingsPanel
				title={ pageTitle }
				onSave={ handleSubmit( onSubmit ) }
				isSaving={ saving }
				noCard
				onBack={ () => navigate( '/delivery-schedule' ) }
				backLabel={ __( '← Back to Schedules', 'order-delivery-date' ) }
			>
				{ ! isDefault && (
					<AccordionCard title={ __( 'Schedule Settings', 'order-delivery-date' ) } defaultOpen>
						<ScheduleBasedOnSection control={ control } />
					</AccordionCard>
				) }

				<AccordionCard title={ __( 'Date Settings', 'order-delivery-date' ) } defaultOpen={ isDefault }>
					<Panel1DateSettings
						form={ form }
						control={ control }
						deliveryDays={ deliveryDays }
						setDeliveryDays={ setDeliveryDays }
						scheduleID={ id }
						labels={ labels }
					/>
				</AccordionCard>

				{ ! isEstimatedText && (
					<AccordionCard title={ __( 'Time Slot', 'order-delivery-date' ) } defaultOpen={ false }>
						<Panel2TimeSlot
							form={ form }
							control={ control }
							timeSlots={ timeSlots }
							setTimeSlots={ setTimeSlots }
							deliveryDays={ deliveryDays }
							scheduleID={ id }
							labels={ labels }
						/>
					</AccordionCard>
				) }

				{ ! isEstimatedText && (
					<AccordionCard title={ labels.delivery_schedule() } defaultOpen={ false }>
						<Panel3DeliverySchedule form={ form } control={ control } setValue={ setValue } labels={ labels } />
					</AccordionCard>
				) }

				{ ! isEstimatedText && (
					<AccordionCard title={ __( 'Additional Charges', 'order-delivery-date' ) } defaultOpen={ false } badge={ __( 'Pro', 'order-delivery-date' ) }>
						<Panel4AdditionalCharges
							form={ form }
							control={ control }
							chargesRows={ chargesRows }
							setChargesRows={ setChargesRows }
							scheduleID={ id }
							labels={ labels }
						/>
					</AccordionCard>
				) }

				{ ! isEstimatedText && (
					<AccordionCard title={ __( 'Weekday / Specific Dates', 'order-delivery-date' ) } defaultOpen={ false } badge={ __( 'Pro', 'order-delivery-date' ) }>
						<Panel5WeekdaySchedule
							form={ form }
							control={ control }
							weekdayBasedSchedule={ weekdayBasedSchedule }
							setWeekdayBasedSchedule={ setWeekdayBasedSchedule }
							labels={ labels }
						/>
					</AccordionCard>
				) }

				{ ! isEstimatedText && (
					<AccordionCard title={ __( 'Time Slot Cutoffs', 'order-delivery-date' ) } defaultOpen={ false } badge={ __( 'Pro', 'order-delivery-date' ) }>
						<PanelTimeSlotCutoffs
							timeSlots={ timeSlots }
							tsCutoffRows={ tsCutoffRows }
							setTsCutoffRows={ setTsCutoffRows }
						/>
					</AccordionCard>
				) }

			</SettingsPanel>
		</div>
	);
};

export default EditSchedule;

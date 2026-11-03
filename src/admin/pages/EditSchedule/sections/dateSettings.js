import { useState } from '@wordpress/element';
import { Controller } from 'react-hook-form';
import { __ } from '@wordpress/i18n';
import {
	ToggleControl,
	TextControl,
	TextareaControl,
	SelectControl,
	CardBody,
	CardDivider,
	RadioControl,
	Button,
	__experimentalNumberControl as NumberControl,
	__experimentalInputControl as InputControl
} from '@wordpress/components';

import { SettingRow }        from '../../../components/SettingsPanel';
import { ProFieldNotice }    from '../../../components/ProNotice';
import DataTable              from '../../../components/DataTable';
import SettingsFormCard, {
	SettingsFormRow,
	DatePickerField,
	MultiDatePickerField,
	SettingsFormSaveBtn,
}                             from '../../../components/SettingsForm';

import {
	CHARGES_TYPE_OPTIONS,
	SPEC_DATE_RANGE_OPTIONS,
	SPEC_DATE_EMPTY,
}                             from '../constants';
import InlineRadios           from '../../../components/ui/InlineRadios';

// ── AddSpecificDatesForm ──────────────────────────────────────────────────────
// Uses its own local state (passed as form/setForm props) — no RHF here.

const AddSpecificDatesForm = ( { form, setForm, onSave, labels } ) => {
	const set     = ( key ) => ( value ) => setForm( ( p ) => ( { ...p, [ key ]: value } ) );
	const isRange = form.range_type === 'date_range';

	return (
		<SettingsFormCard title={ __( 'Add Specific Dates', 'order-delivery-date' ) }>

			<SettingsFormRow
				label={ __( 'Range Type:', 'order-delivery-date' ) }
				tooltip={ __( 'Choose Individual to pick a single date, or Date Range to select a from/to span.', 'order-delivery-date' ) }
			>
				<RadioControl
					selected={ form.range_type }
					options={ SPEC_DATE_RANGE_OPTIONS }
					onChange={ set( 'range_type' ) }
					__nextHasNoMarginBottom
				/>
			</SettingsFormRow>

			<SettingsFormRow
				label={ isRange ? __( 'Specific Dates From/To:', 'order-delivery-date' ) : labels.time_slot_dates() }
				tooltip={ isRange
					? __( 'Select the start and end dates for the delivery date range.', 'order-delivery-date' )
					: __( 'Pick the specific delivery date.', 'order-delivery-date' ) }
			>
				{ isRange ? (
					<div style={ { display: 'flex', alignItems: 'center', gap: '8px' } }>
						<DatePickerField value={ form.date_from } onChange={ set( 'date_from' ) } placeholder={ __( 'From date', 'order-delivery-date' ) } />
						<span style={ { fontSize: '13px', color: '#1d2327' } }>{ __( 'To', 'order-delivery-date' ) }</span>
						<DatePickerField value={ form.date_to } onChange={ set( 'date_to' ) } placeholder={ __( 'To date', 'order-delivery-date' ) } />
					</div>
				) : (
					<MultiDatePickerField value={ form.delivery_dates } onChange={ set( 'delivery_dates' ) } />
				) }
			</SettingsFormRow>

			<SettingsFormRow
				label={ __( 'Maximum Order Deliveries:', 'order-delivery-date' ) }
				tooltip={ __( 'Maximum number of orders allowed on these specific delivery dates. Leave blank for unlimited.', 'order-delivery-date' ) }
			>
				<NumberControl
					min={ 0 }
					value={ form.max_orders }
					onChange={ set( 'max_orders' ) }
					__nextHasNoMarginBottom
				/>
			</SettingsFormRow>

			<SettingsFormRow
				label={ labels.specific_dates_charges() }
				tooltip={ __( 'Add extra charges applied when customers select these specific delivery dates.', 'order-delivery-date' ) }
			>
				<div className="sf-inline-fields">
					<div className="sf-inline-field">
						<NumberControl
							label={ __( 'Charges', 'order-delivery-date' ) }
							min={ 0 }
							value={ form.additional_charges }
							onChange={ set( 'additional_charges' ) }
							__nextHasNoMarginBottom
						/>
					</div>
					<div className="sf-inline-field">
						<SelectControl 
							label={ __( 'Charges Type', 'order-delivery-date' ) }
							className="sf-select"
							value={ form.charges_type } options={ CHARGES_TYPE_OPTIONS }
							onChange={ set( 'charges_type' ) } __nextHasNoMarginBottom
						/>
					</div>
					<div className="sf-inline-field">
						<InputControl
							label={ __( 'Charges Label', 'order-delivery-date' ) }
							placeholder={ __( 'Charges Label', 'order-delivery-date' ) }
							value={ form.charges_label }
							onChange={ set( 'charges_label' ) }
							__nextHasNoMarginBottom
						/>
					</div>
				</div>
			</SettingsFormRow>
			<SettingsFormRow>
				<SettingsFormSaveBtn onClick={ onSave } />

			</SettingsFormRow>

		</SettingsFormCard>
	);
};

const upgradeUrl =
	window.ordddAdminData?.upgradeUrl ??
	'https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/';

const ProColLabel = ( { label } ) => (
	<span style={ { display: 'inline-flex', alignItems: 'center', gap: '4px' } }>
		{ label }
		<span className="orddd-pro-field__badge">{ __( 'Pro', 'order-delivery-date' ) }</span>
	</span>
);

// ── Panel1DateSettings ────────────────────────────────────────────────────────

const Panel1DateSettings = ( { form, control, deliveryDays, setDeliveryDays, scheduleID, labels } ) => {
	const isCalendar      = form.orddd_lite_delivery_checkout_options === 'delivery_calendar';
	const isDefault       = String( scheduleID ) === '0';
	const daysOn          = form.delivery_type?.weekdays === 'on';
	const enableSpecificDates = isDefault
		? form.orddd_lite_enable_delivery_date === 'on'
		: form.delivery_type?.specific_dates === 'on';

	const [ showSpecDates, setShowSpecDates ] = useState( false );
	const [ newSpecDate,   setNewSpecDate   ] = useState( SPEC_DATE_EMPTY );

	// ── Weekday-table helpers ─────────────────────────────────────────────────

	const updateDay = ( key, field, val ) =>
		setDeliveryDays( ( p ) => p.map( ( r ) => r.key === key ? { ...r, [ field ]: val } : r ) );

	const setDayEdit = ( key, editing ) =>
		setDeliveryDays( ( p ) => p.map( ( r ) => r.key === key ? { ...r, edit: editing } : r ) );

	const toggleDay = ( key ) =>
		setDeliveryDays( ( p ) => p.map( ( r ) =>
			r.key === key ? { ...r, enable: r.enable === 'checked' ? '' : 'checked' } : r
		) );

	const deleteSpecDate = ( key ) =>
		setDeliveryDays( ( p ) => p.filter( ( r ) => r.key !== key ) );


	const DAY_COLS = [
		{ key: 'name',               label: labels.delivery_days_table() },
		{ key: 'additional_charges', label: <ProColLabel label={ __( 'Additional Charges', 'order-delivery-date' ) } /> },
		{ key: 'charges_type',       label: <ProColLabel label={ __( 'Charges Type',       'order-delivery-date' ) } /> },
		{ key: 'charges_label',      label: <ProColLabel label={ __( 'Charges Label',      'order-delivery-date' ) } /> },
		{ key: 'max_orders',         label: <ProColLabel label={ __( 'Maximum Orders',     'order-delivery-date' ) } /> },
	];

	const PRO_COLS = new Set( [ 'additional_charges', 'charges_type', 'charges_label', 'max_orders' ] );

	const renderDayCell = ( row, colKey ) => {
		const dayDisabled = row.enable !== 'checked';
		if ( colKey === 'name' ) {
			return (
				<span style={ { color: dayDisabled ? '#a0a0a0' : '#1d2327', fontWeight: dayDisabled ? 400 : 500 } }>
					{ row.name }
				</span>
			);
		}
		if ( PRO_COLS.has( colKey ) ) {
			return <span style={ { color: '#ccc' } }>—</span>;
		}
		return <span>{ row[ colKey ] || '—' }</span>;
	};

	const renderDayActions = ( row ) => (
		<Button variant="link" style={ { color: '#41278d', padding: 0, minHeight: 0, height: 'auto', fontSize: '13px' } }
			onClick={ () => toggleDay( row.key ) }>
			{ row.enable === 'checked' ? __( 'Disable', 'order-delivery-date' ) : __( 'Enable', 'order-delivery-date' ) }
		</Button>
	);

	const saveSpecDate = () => {
		const isRange  = newSpecDate.range_type === 'date_range';
		const hasValue = isRange
			? ( newSpecDate.date_from && newSpecDate.date_to )
			: newSpecDate.delivery_dates.length > 0;
		if ( ! hasValue ) return;

		if ( isRange ) {
			const from  = new Date( newSpecDate.date_from + 'T00:00:00' );
			const to    = new Date( newSpecDate.date_to   + 'T00:00:00' );
			const now   = Date.now();
			const toYMD = ( d ) => `${ d.getFullYear() }-${ String( d.getMonth() + 1 ).padStart( 2, '0' ) }-${ String( d.getDate() ).padStart( 2, '0' ) }`;
			const dates = [];
			for ( let d = new Date( from ); d <= to; d.setDate( d.getDate() + 1 ) ) {
				dates.push( toYMD( d ) );
			}
			const newRows = dates.map( ( date, i ) => ( {
				key:            `spec_${ now + i }`,
				name:           date,
				...newSpecDate,
				delivery_type:  date,
				delivery_dates: [ date ],
				range_type:     'individual',
				edit:           false,
				isSpecific:     true,
			} ) );
			setDeliveryDays( ( p ) => [ ...p, ...newRows ] );
		} else {
			const now = Date.now();
			const newRows = newSpecDate.delivery_dates.map( ( date, i ) => ( {
				key:                `spec_${ now + i }`,
				name:               date,
				...newSpecDate,
				delivery_dates:     [ date ],
				edit:               false,
				isSpecific:         true,
			} ) );
			setDeliveryDays( ( p ) => [ ...p, ...newRows ] );
		}

		setNewSpecDate( SPEC_DATE_EMPTY );
		setShowSpecDates( false );
	};

	return (
		<>

			<SettingRow label={ labels.enable() }
				tooltip={ __( 'Enable Delivery Date capture on the checkout page for this delivery schedule.', 'order-delivery-date' ) }>
				<Controller name={ String( scheduleID ) === '0' ? 'orddd_lite_enable_delivery_date' : 'enable_shipping_based_delivery' } control={ control }
					render={ ( { field } ) => (
						<ToggleControl checked={ field.value === 'on' }
							onChange={ ( v ) => field.onChange( v ? 'on' : '' ) } __nextHasNoMarginBottom />
					) }
				/>
			</SettingRow>
			<CardDivider />

			<SettingRow label={ labels.checkout_options() }
				tooltip={ __( 'Choose the delivery date option to be displayed on the checkout page.', 'order-delivery-date' ) }>
				<Controller name="orddd_lite_delivery_checkout_options" control={ control }
					render={ ( { field } ) => (
						<InlineRadios
							name="es_checkout_option"
							value={ field.value || 'delivery_calendar' }
							onChange={ field.onChange }
							options={ [
								{ value: 'delivery_calendar', label: __( 'Calendar',                'order-delivery-date' ) },
								{ value: 'text_block',         label: labels.estimated_date(), locked: true },
							] }
						/>
					) }
				/>
			</SettingRow>
			<CardDivider />

			{ isCalendar && (
				<>
					<SettingRow label={ labels.weekdays_dates() }
						tooltip={ __( 'Select delivery days & specific dates and add additional charges & lockout for the date (if any).', 'order-delivery-date' ) }>
						<Controller name="delivery_type.weekdays" control={ control }
							render={ ( { field } ) => (
								<ToggleControl checked={ field.value === 'on' }
									onChange={ ( v ) => field.onChange( v ? 'on' : '' ) } __nextHasNoMarginBottom />
							) }
						/>
					</SettingRow>

					{ daysOn && (
						<CardBody className="es-table-cardBody">
							<DataTable
								columns={ DAY_COLS }
								rows={ deliveryDays.filter( ( r ) => ! r.isSpecific ) }
								rowKey="key"
								renderCell={ renderDayCell }
								renderActions={ renderDayActions }
								showCheckboxes={ false }
								emptyMessage=""
							/>
						</CardBody>
					) }
					<CardDivider />

					<SettingRow label={ labels.specific_dates() }
						tooltip={ __( 'Enable this option to choose specific delivery dates on the checkout page.', 'order-delivery-date' ) }>
						<ProFieldNotice />
					</SettingRow>
					<CardDivider />

					<SettingRow label={ __( 'Add the maximum number of deliveries you can manage:', 'order-delivery-date' ) }
						tooltip={ __( 'A date will become unavailable for further deliveries once these many orders are placed.', 'order-delivery-date' ) }>
						<Controller name="orddd_lite_lockout_date_after_orders" control={ control }
							render={ ( { field } ) => (
								<TextControl type="number" min={ 0 } placeholder={ __( 'Maximum Order', 'order-delivery-date' ) }
									value={ field.value } onChange={ field.onChange } __nextHasNoMarginBottom />
							) }
						/>
					</SettingRow>
					<CardDivider />

					<SettingRow label={ __( 'Mandatory field?:', 'order-delivery-date' ) }
						tooltip={ __( 'Selection of delivery date on the checkout page will become mandatory.', 'order-delivery-date' ) }>
						<Controller name="orddd_lite_date_field_mandatory" control={ control }
							render={ ( { field } ) => (
								<ToggleControl checked={ field.value === 'checked' }
									onChange={ ( v ) => field.onChange( v ? 'checked' : '' ) } __nextHasNoMarginBottom />
							) }
						/>
					</SettingRow>
					<CardDivider />

					<SettingRow label={ __( 'Number of dates to choose:', 'order-delivery-date' ) } tooltip={ __( 'Number of dates available for delivery.', 'order-delivery-date' ) }>
						<Controller name="orddd_lite_number_of_dates" control={ control }
							render={ ( { field } ) => (
								<TextControl type="number" min={ 1 } placeholder="30"
									value={ field.value } onChange={ field.onChange } __nextHasNoMarginBottom />
							) }
						/>
					</SettingRow>
					<CardDivider />

					<SettingRow label={ __( 'Date Field Label:', 'order-delivery-date' ) }
						tooltip={ __( 'Choose the label that is to be displayed for the field on checkout page.', 'order-delivery-date' ) }>
						<Controller name={ isDefault ? 'orddd_lite_delivery_date_field_label' : 'orddd_shipping_based_delivery_date_field_label' } control={ control }
							render={ ( { field } ) => (
								<TextControl placeholder={ __( 'Delivery/Pick Up Date', 'order-delivery-date' ) }
									value={ field.value } onChange={ field.onChange } __nextHasNoMarginBottom />
							) }
						/>
					</SettingRow>
					<CardDivider />

					<SettingRow label={ __( 'Field Note Text:', 'order-delivery-date' ) }
						tooltip={ __( 'Choose the note to be displayed below the delivery date field on checkout page.', 'order-delivery-date' ) }>
						<ProFieldNotice />
					</SettingRow>
				</>
			) }

			{ ! isCalendar && (
				<>
					<SettingRow label={ labels.mdt() }
						tooltip={ __( 'Minimum number of hours required to prepare for delivery.', 'order-delivery-date' ) }>
						<Controller name="orddd_lite_minimumOrderDays" control={ control }
							render={ ( { field } ) => (
								<TextControl type="number" min={ 0 } step={ 0.25 } placeholder={ __( 'e.g. 2', 'order-delivery-date' ) }
									value={ field.value } onChange={ field.onChange } __nextHasNoMarginBottom />
							) }
						/>
					</SettingRow>
					<CardDivider />

				</>
			) }
		</>
	);
};

export default Panel1DateSettings;

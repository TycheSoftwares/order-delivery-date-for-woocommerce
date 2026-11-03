import { useState } from '@wordpress/element';
import { Controller } from 'react-hook-form';
import {
	ToggleControl,
	TextControl,
	SelectControl,
	CardBody,
	CardDivider,
	RadioControl,
	Button,
	FormTokenField,
	__experimentalNumberControl as NumberControl,
	__experimentalInputControl as InputControl

} from '@wordpress/components';

import { __ } from '@wordpress/i18n';

import { SettingRow }      from '../../../components/SettingsPanel';
import DataTable            from '../../../components/DataTable';
import SettingsFormCard, {
	SettingsFormRow,
	SettingsFormSaveBtn,
	MultiSelectDropdown,
}                           from '../../../components/SettingsForm';

import {
	CHARGES_TYPE_OPTIONS,
	TIME_SLOT_FOR_OPTIONS,
	INDIVIDUAL_SLOT_EMPTY,
	BULK_SLOT_EMPTY,
	WEEK_DAYS,
}                           from '../constants';
import TimeInput            from '../../../components/ui/TimeInput';
import InlineRadios         from '../../../components/ui/InlineRadios';

const upgradeUrl =
	window.ordddAdminData?.upgradeUrl ??
	'https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/';

const ProColLabel = ( { label } ) => (
	<span style={ { display: 'inline-flex', alignItems: 'center', gap: '4px' } }>
		{ label }
		<span className="orddd-pro-field__badge">{ __( 'Pro', 'order-delivery-date' ) }</span>
	</span>
);

// ── AddIndividualTimeSlotsForm ────────────────────────────────────────────────
// Uses its own local state — no RHF here.

export const AddIndividualTimeSlotsForm = ( { form, setForm, onSave, weekdayOptions, specificDateOptions } ) => {
	const set     = ( key ) => ( value ) => setForm( ( p ) => ( { ...p, [ key ]: value } ) );
	const isDates = form.time_slot_for === 'specific_dates';

	const updateRange = ( idx, field, val ) =>
		setForm( ( p ) => ( {
			...p,
			timeRanges: p.timeRanges.map( ( r, i ) => i === idx ? { ...r, [ field ]: val } : r ),
		} ) );

	const addRange = () =>
		setForm( ( p ) => ( { ...p, timeRanges: [ ...p.timeRanges, { from_time: '', to_time: '' } ] } ) );

	const removeRange = ( idx ) =>
		setForm( ( p ) => ( {
			...p,
			timeRanges: p.timeRanges.length > 1 ? p.timeRanges.filter( ( _, i ) => i !== idx ) : p.timeRanges,
		} ) );

	return (
		<SettingsFormCard title="Add Individual Time Slots">

			<SettingsFormRow label="Time Slot For:"
				tooltip="Choose whether this time slot applies to recurring weekdays or specific dates.">
			<InlineRadios
				name="individual_time_slot_for"
				value={ form.time_slot_for }
				onChange={ set( 'time_slot_for' ) }
				options={ [
					{ value: 'weekdays',       label: __( 'Weekdays',       'order-delivery-date' ) },
					{ value: 'specific_dates', label: __( 'Specific Dates', 'order-delivery-date' ), locked: true },
				] }
			/>

			</SettingsFormRow>

			<SettingsFormRow label="Select Delivery Days:"
				tooltip="Select the weekdays or dates for which these time slots apply.">
				<FormTokenField
					value={ form.delivery_days }
					suggestions={ isDates ? specificDateOptions.map( ( d ) => d.label ) : weekdayOptions.map( ( d ) => d.label ) }
					onChange={ set( 'delivery_days' ) }
					help={''}
					label={ isDates ? 'Select dates' : 'Select weekdays' }
					__nextHasNoMarginBottom
					__experimentalExpandOnFocus
				/>
			</SettingsFormRow>

			<SettingsFormRow label="Time From/To:"
				tooltip="Set the start and end time for each time slot.">
				<div style={ { display: 'flex', flexDirection: 'column', gap: '6px' } }>
					{ form.timeRanges.map( ( range, idx ) => (
						<div key={ idx } className="sf-time-range-row">
							<TimeInput value={ range.from_time } onChange={ ( v ) => updateRange( idx, 'from_time', v ) } />
							<span className="sf-time-range-sep">To</span>
							<TimeInput value={ range.to_time } onChange={ ( v ) => updateRange( idx, 'to_time', v ) } />
							<button type="button" className="sf-delete-link" onClick={ () => removeRange( idx ) }>Delete</button>
						</div>
					) ) }
					<button type="button" className="sf-add-link" onClick={ addRange }>+ Add Another Slot</button>
				</div>
			</SettingsFormRow>

			<SettingsFormRow label="Maximum Order Deliveries Per Time Slot (based on per order):"
				tooltip="Set the maximum number of orders allowed per time slot.">
				<NumberControl
					min={ 0 }
					value={ form.max_orders }
					onChange={ ( v ) => set( 'max_orders' )( v ) }
				/>
			</SettingsFormRow>

			<SettingsFormRow label="Additional Charges for Time Slot and Delivery Charges label:"
				tooltip="Add extra charges applied when customers select this time slot.">
				<div className="sf-inline-fields">
					<div className="sf-inline-field">
						<NumberControl
							label="Charges"
							placeholder='Charges'
							min={ 0 }
							value={ form.charges }
							onChange={ ( v ) => set( 'charges' )( v ) }
						/>
					</div>
					<div className="sf-inline-field">
						<span className="sf-inline-field__label">Charges Type</span>
						<SelectControl className="sf-select" value={ form.charges_type }
							options={ [ { value: 'fixed', label: 'Fixed' } ] } onChange={ set( 'charges_type' ) } __nextHasNoMarginBottom />
					</div>
					<div className="sf-inline-field">
						<InputControl
							label="Time Slot Charges Label"
							placeholder='Time slot Charges'
							min={ 0 }
							value={ form.charges_label }
							onChange={ ( v ) => set( 'charges_label' )( v ) }
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

// ── AddBulkTimeSlotsForm ──────────────────────────────────────────────────────
// Uses its own local state — no RHF here.

export const AddBulkTimeSlotsForm = ( { form, setForm, onSave, weekdayOptions, specificDateOptions } ) => {
	const set     = ( key ) => ( value ) => setForm( ( p ) => ( { ...p, [ key ]: value } ) );
	const isDates = form.time_slot_for === 'specific_dates';

	return (
		<SettingsFormCard title="Add Bulk Time Slots">

			<SettingsFormRow label="Time Slot For:"
				tooltip="Choose whether these bulk time slots apply to recurring weekdays or specific dates.">
				<InlineRadios
					name="bulk_time_slot_for"
					value={ form.time_slot_for }
					onChange={ set( 'time_slot_for' ) }
					options={ [
						{ value: 'weekdays',       label: __( 'Weekdays',       'order-delivery-date' ) },
						{ value: 'specific_dates', label: __( 'Specific Dates', 'order-delivery-date' ), locked: true },
					] }
				/>

			</SettingsFormRow>

			<SettingsFormRow label="Select Delivery Days:"
				tooltip="Select the weekdays or dates for which these time slots apply."
				>
				<FormTokenField
					value={ form.delivery_days }
					suggestions={ isDates ? specificDateOptions.map( ( d ) => d.label ) : weekdayOptions.map( ( d ) => d.label ) }
					onChange={ set( 'delivery_days' ) }
					help={''}
					label={ isDates ? 'Select dates' : 'Select weekdays' }
					__nextHasNoMarginBottom
					__experimentalExpandOnFocus
				/>
			</SettingsFormRow>

			<SettingsFormRow label="Time Slot Duration (in minutes):"
				tooltip="Duration of each generated time slot in minutes.">
				<NumberControl
					min={ 1 }
					value={ form.duration }
					onChange={ ( v ) => set( 'duration' )( v ) }
				/>
			</SettingsFormRow>

			<SettingsFormRow label="Interval Between Time Slots (in minutes):"
				tooltip="Gap between the end of one slot and the start of the next." >
				<NumberControl
					min={ 0 }
					value={ form.interval }
					onChange={ ( v ) => set( 'interval' )( v ) }
				/>
			</SettingsFormRow>

			<SettingsFormRow label="Time From/To:"
				tooltip="Overall time window within which slots will be generated.">
				<div className="sf-time-range-row">
					<TimeInput value={ form.from_time } onChange={ set( 'from_time' ) } />
					<span className="sf-time-range-sep">To</span>
					<TimeInput value={ form.to_time } onChange={ set( 'to_time' ) } />
				</div>
			</SettingsFormRow>

			<SettingsFormRow label="Maximum Order Deliveries Per Time Slot (based on per order):"
				tooltip="Set the maximum number of orders allowed per generated time slot." >
				<NumberControl
					min={ 0 }
					value={ form.max_orders }
					onChange={ ( v ) => set( 'max_orders' )( v ) }
				/>
			</SettingsFormRow>

			<SettingsFormRow label="Additional Charges for Time Slot and Delivery Charges label:"
				tooltip="Add extra charges applied when customers select any of these generated time slots.">
				<div className="sf-inline-fields">
					<div className="sf-inline-field">
						<NumberControl
							label="Charges"
							placeholder='Charges'
							min={ 0 }
							value={ form.charges }
							onChange={ ( v ) => set( 'charges' )( v ) }
						/>
					</div>
					<div className="sf-inline-field">
						<SelectControl 
							className="sf-select" 
							label="Charges Type"
							value={ form.charges_type }
							options={ [ { value: 'fixed', label: 'Fixed' } ] } 
							onChange={ set( 'charges_type' ) } 
							__nextHasNoMarginBottom />
					</div>
					<div className="sf-inline-field">
						<InputControl
							label="Time Slot Charges Label"
							placeholder='Time slot Charges'
							value={ form.charges_label }
							onChange={ ( v ) => set( 'charges_label' )( v ) }
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

// ── Panel2TimeSlot ────────────────────────────────────────────────────────────

const Panel2TimeSlot = ( { form, control, timeSlots, setTimeSlots, deliveryDays = [], scheduleID, labels } ) => {
	const [ slotMode, setSlotMode ] = useState( null );
	const [ newSlot,  setNewSlot  ] = useState( INDIVIDUAL_SLOT_EMPTY );
	const [ bulkSlot, setBulkSlot ] = useState( BULK_SLOT_EMPTY );
	const isDefault                 = String( scheduleID ) === '0';

	// Derive options from the delivery days table
	const weekdayOptions = [
		{ value: 'all', label: 'All' },
		...deliveryDays
			.filter( ( d ) => ! d.isSpecific && d.enable === 'checked' )
			.map( ( d ) => ( { value: d.key, label: d.name } ) ),
	];

	const specificDateOptions = deliveryDays
		.filter( ( d ) => d.isSpecific )
		.map( ( d ) => ( { value: d.name, label: d.name } ) );

	// FormTokenField stores labels, not values — map back to option values first.
	const labelToValue = ( label ) =>
		weekdayOptions.find( ( o ) => o.label === label )?.value ?? label;

	const expandDays = ( days ) => {
		const values = days.map( labelToValue );
		if ( values.includes( 'all' ) ) {
			return deliveryDays
				.filter( ( d ) => ! d.isSpecific && d.enable === 'checked' )
				.map( ( d ) => d.key );
		}
		return values;
	};

	const updateSlot  = ( idx, field, val ) =>
		setTimeSlots( ( p ) => p.map( ( r, i ) => i === idx ? { ...r, [ field ]: val } : r ) );
	const deleteSlot  = ( idx ) => setTimeSlots( ( p ) => p.filter( ( _, i ) => i !== idx ) );
	const blockSlot   = ( idx ) => setTimeSlots( ( p ) => p.map( ( r, i ) => i === idx ? { ...r, blocked: ! r.blocked } : r ) );
	const setSlotEdit = ( idx, v ) => setTimeSlots( ( p ) => p.map( ( r, i ) => i === idx ? { ...r, edit: v } : r ) );

	const saveIndividualSlot = () => {
		const validRanges = newSlot.timeRanges.filter( ( r ) => r.from_time && r.to_time );
		if ( ! validRanges.length ) return;
		const days    = expandDays( newSlot.delivery_days );
		const entries = ( days.length ? days : [ '' ] ).flatMap( ( day ) =>
			validRanges.map( ( range ) => ( {
				delivery_days: day, time_slot_for: newSlot.time_slot_for,
				time: { from_time: range.from_time, to_time: range.to_time },
				max_orders: newSlot.max_orders, charges: newSlot.charges,
				charges_type: newSlot.charges_type, charges_label: newSlot.charges_label,
				blocked: false, edit: false, check: false,
			} ) )
		);
		setTimeSlots( ( p ) => [ ...p, ...entries ] );
		setNewSlot( INDIVIDUAL_SLOT_EMPTY );
		setSlotMode( null );
	};

	const saveBulkSlots = () => {
		if ( ! bulkSlot.from_time || ! bulkSlot.to_time || ! bulkSlot.duration ) return;
		const dur        = parseInt( bulkSlot.duration, 10 );
		const gap        = parseInt( bulkSlot.interval || 0, 10 );
		const [ fh, fm ] = bulkSlot.from_time.split( ':' ).map( Number );
		const [ th, tm ] = bulkSlot.to_time.split( ':' ).map( Number );
		const startMins  = fh * 60 + fm;
		const endMins    = th * 60 + tm;
		const fmt        = ( v ) => `${ String( Math.floor( v / 60 ) ).padStart( 2, '0' ) }:${ String( v % 60 ).padStart( 2, '0' ) }`;
		const days       = expandDays( bulkSlot.delivery_days );
		const timeRanges = [];
		for ( let s = startMins; s + dur <= endMins; s += dur + gap ) {
			timeRanges.push( { from_time: fmt( s ), to_time: fmt( s + dur ) } );
		}
		const slots = ( days.length ? days : [ '' ] ).flatMap( ( day ) =>
			timeRanges.map( ( range ) => ( {
				delivery_days: day, time_slot_for: bulkSlot.time_slot_for,
				time: range, max_orders: bulkSlot.max_orders,
				charges: bulkSlot.charges, charges_type: bulkSlot.charges_type,
				charges_label: bulkSlot.charges_label,
				blocked: false, edit: false, check: false,
			} ) )
		);
		setTimeSlots( ( p ) => [ ...p, ...slots ] );
		setBulkSlot( BULK_SLOT_EMPTY );
		setSlotMode( null );
	};

	const SLOT_COLS = [
		{ key: 'delivery_days', label: labels.delivery_days_table() },
		{ key: 'time',          label: __( 'Time Slot', 'order-delivery-date' ) },
		{ key: 'max_orders',    label: __( 'Maximum Order Deliveries per time slot','order-delivery-date' ) },
		{ key: 'charges',       label: __( 'Additional Charges', 'order-delivery-date' ) },
		{ key: 'charges_type',  label: __( 'Charges Type', 'order-delivery-date' ) },
		{ key: 'charges_label', label: __( 'Charges Label', 'order-delivery-date' ) },
	];

	const dayLabel = ( dayKey ) => WEEK_DAYS.find( ( d ) => d.key === dayKey )?.label ?? dayKey;

	const renderSlotCell = ( row, colKey ) => {
		if ( row.edit ) {
			if ( colKey === 'delivery_days' ) {
				return (
					<SelectControl
						value={ row.delivery_days }
						options={ [ { value: '', label: 'Select day' }, ...WEEK_DAYS.map( ( d ) => ( { value: d.key, label: d.label } ) ) ] }
						onChange={ ( v ) => updateSlot( timeSlots.indexOf( row ), 'delivery_days', v ) }
						__nextHasNoMarginBottom
					/>
				);
			}
			if ( colKey === 'time' ) {
				const idx = timeSlots.indexOf( row );
				let from = row.time?.from_time || '';
				let to   = row.time?.to_time   || '';
				if ( ! from && typeof row.time === 'string' && row.time.includes( ' - ' ) ) {
					const parts = row.time.split( ' - ' );
					from = parts[ 0 ] || '';
					to   = parts[ 1 ] || '';
				}
				return (
					<span style={ { display: 'flex', alignItems: 'center', gap: '4px', flexDirection: 'column' } }>
						<TimeInput value={ from } onChange={ ( v ) => updateSlot( idx, 'time', { from_time: v, to_time: to } ) } />
						<span>To</span>
						<TimeInput value={ to } onChange={ ( v ) => updateSlot( idx, 'time', { from_time: from, to_time: v } ) } />
					</span>
				);
			}
			if ( colKey === 'charges_type' ) {
				return (
					<SelectControl value={ row.charges_type } options={ [ { value: 'fixed', label: __( 'Fixed', 'order-delivery-date' ) } ] }
						onChange={ ( v ) => updateSlot( timeSlots.indexOf( row ), 'charges_type', v ) } __nextHasNoMarginBottom />
				);
			}
			return (
				<TextControl value={ row[ colKey ] }
					onChange={ ( v ) => updateSlot( timeSlots.indexOf( row ), colKey, v ) }
					type={ colKey === 'max_orders' ? 'number' : 'text' } __nextHasNoMarginBottom />
			);
		}

		if ( colKey === 'delivery_days' ) return dayLabel( row.delivery_days );
		if ( colKey === 'time' ) {
			const t = row.time;
			if ( t && typeof t === 'object' ) return `${ t.from_time || '' } - ${ t.to_time || '' }`;
			return t || '';
		}
		if ( colKey === 'charges' && row.charges ) return row.charges_type === 'percentage' ? `${ row.charges }%` : row.charges;
		if ( colKey === 'charges_type' && ! row.charges ) return '';
		return row[ colKey ] || '';
	};

	const renderSlotActions = ( row ) => {
		const idx = timeSlots.indexOf( row );
		return (
			<span style={ { display: 'inline-flex', gap: '4px', alignItems: 'center', flexWrap: 'wrap', fontSize: '13px' } }>
				<Button variant="link" style={ { color: '#41278d', padding: 0, minHeight: 0, height: 'auto' } }
					onClick={ () => row.edit ? setSlotEdit( idx, false ) : setSlotEdit( idx, true ) }>
					{ row.edit ? __( 'Update', 'order-delivery-date' ) : __( 'Edit', 'order-delivery-date' ) }
				</Button>
				<span style={ { color: '#ddd' } }>|</span>
				<Button variant="link" style={ { color: '#c0392b', padding: 0, minHeight: 0, height: 'auto' } }
					onClick={ () => deleteSlot( idx ) }>
					{ __( 'Delete', 'order-delivery-date' ) }
				</Button>
			</span>
		);
	};

	return (
		<>
			<SettingRow label={ __( 'Enable Time Slot Capture:', 'order-delivery-date' ) }
				tooltip={ __( 'Allows the customer to choose a time slot for delivery on the checkout page.', 'order-delivery-date' ) }>
				<Controller name="orddd_lite_enable_time_slot" control={ control }
					render={ ( { field } ) => (
						<ToggleControl checked={ field.value === 'on' }
							onChange={ ( v ) => field.onChange( v ? 'on' : '' ) } __nextHasNoMarginBottom />
					) }
				/>
			</SettingRow>
			<CardDivider />

			<SettingRow label={ __( 'Mandatory field?:', 'order-delivery-date' ) }
				tooltip={ __( 'Selection of Time slot on the checkout page will become mandatory.', 'order-delivery-date' )}>
				<Controller name="orddd_lite_time_slot_mandatory" control={ control }
					render={ ( { field } ) => (
						<ToggleControl checked={ field.value === 'checked' }
							onChange={ ( v ) => field.onChange( v ? 'checked' : '' ) } __nextHasNoMarginBottom />
					) }
				/>
			</SettingRow>
			<CardDivider />

			<SettingRow label={ __( 'Show \'As Soon As Possible\' Option:', 'order-delivery-date' ) }
				tooltip={ __( 'As Soon as possible option is shown in the time slots dropdown.', 'order-delivery-date' )}>
				<Controller name="orddd_lite_time_slot_asap" control={ control }
					render={ ( { field } ) => (
						<ToggleControl checked={ field.value === 'checked' }
							onChange={ ( v ) => field.onChange( v ? 'checked' : '' ) } __nextHasNoMarginBottom />
					) }
				/>
			</SettingRow>

			<CardDivider />

			<SettingRow label={ __( 'Time Slot Field Label:', 'order-delivery-date' ) }
				tooltip={ __( 'Choose a label that is to be displayed for the time slot field on the checkout page.', 'order-delivery-date' )}>
				<Controller name={ isDefault ? 'orddd_lite_delivery_timeslot_field_label' : 'orddd_shipping_based_delivery_timeslot_field_label' } control={ control }
					render={ ( { field } ) => (
						<TextControl placeholder="Time Slot"
							value={ field.value } onChange={ field.onChange } __nextHasNoMarginBottom />
					) }
				/>
			</SettingRow>

			<CardBody className="es-table-cardBody">
				<DataTable
					columns={ SLOT_COLS }
					rows={ timeSlots }
					rowKey={ ( row ) => timeSlots.indexOf( row ) }
					renderCell={ renderSlotCell }
					renderActions={ renderSlotActions }
					rowClassName={ ( row ) => row.blocked ? 'odt-row--blocked' : '' }
					bulkActions={ [ { value: 'delete', label: 'Delete' } ] }
					onBulkApply={ ( action, selected ) => {
						if ( action === 'delete' ) {
							const keys = selected.map( ( r ) => timeSlots.indexOf( r ) );
							setTimeSlots( ( p ) => p.filter( ( _, i ) => ! keys.includes( i ) ) );
						}
					} }
					emptyMessage="No time slots added yet."
				/>

				<div className="sf-slot-add-row">
					<button type="button" className="odt-add-more-link components-button is-link sf-slot-add-link"
						onClick={ () => { setSlotMode( slotMode === 'individual' ? null : 'individual' ); setNewSlot( INDIVIDUAL_SLOT_EMPTY ); } }>
						<span className="odt-add-more-plus">+</span> <span className="odt-add-more-label">{ __( 'Add Individual Time Slot', 'order-delivery-date' ) }</span>
					</button>
					<span className="sf-slot-add-sep">|</span>
					<button type="button" className="odt-add-more-link components-button is-link sf-slot-add-link"
						onClick={ () => { setSlotMode( slotMode === 'bulk' ? null : 'bulk' ); setBulkSlot( BULK_SLOT_EMPTY ); } }>
						<span className="odt-add-more-plus">+</span> <span className="odt-add-more-label">{ __( 'Add Bulk Time Slots', 'order-delivery-date' ) }</span>
					</button>
				</div>

				{ slotMode === 'individual' && (
					<AddIndividualTimeSlotsForm
						form={ newSlot } setForm={ setNewSlot } onSave={ saveIndividualSlot }
						weekdayOptions={ weekdayOptions } specificDateOptions={ specificDateOptions }
					/>
				) }

				{ slotMode === 'bulk' && (
					<AddBulkTimeSlotsForm
						form={ bulkSlot } setForm={ setBulkSlot } onSave={ saveBulkSlots }
						weekdayOptions={ weekdayOptions } specificDateOptions={ specificDateOptions }
					/>
				) }
			</CardBody>
			</>
	);
};

export default Panel2TimeSlot;

/**
 * WordPress dependencies
 */
import { useState, useEffect } from '@wordpress/element';
import { RadioControl, FormTokenField, Notice } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { __, _n, sprintf } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import DataTable from '../../../components/DataTable';
import SettingsFormCard, {
	SettingsFormRow,
	MultiDatePickerField,
	SettingsFormSaveBtn,
} from '../../../components/SettingsForm';

// ── Constants ─────────────────────────────────────────────────────────────────

const COLUMNS = [
	{ key: 'delivery_days', label: __( 'Delivery Days/Dates', 'order-delivery-date' ) },
	{ key: 'time_slot',     label: __( 'Time Slot', 'order-delivery-date' ) },
];

const BULK_ACTIONS = [
	{ value: 'delete', label: __( 'Unblock', 'order-delivery-date' ) },
];

const WEEKDAY_MAP = {
	orddd_lite_weekday_0: __( 'Sunday',    'order-delivery-date' ),
	orddd_lite_weekday_1: __( 'Monday',    'order-delivery-date' ),
	orddd_lite_weekday_2: __( 'Tuesday',   'order-delivery-date' ),
	orddd_lite_weekday_3: __( 'Wednesday', 'order-delivery-date' ),
	orddd_lite_weekday_4: __( 'Thursday',  'order-delivery-date' ),
	orddd_lite_weekday_5: __( 'Friday',    'order-delivery-date' ),
	orddd_lite_weekday_6: __( 'Saturday',  'order-delivery-date' ),
};

const BLOCK_FOR_OPTIONS = [
	{ value: 'dates',    label: __( 'Dates', 'order-delivery-date' ) },
	{ value: 'weekdays', label: __( 'Weekdays', 'order-delivery-date' ) },
];

const WEEKDAY_OPTIONS = Object.entries( WEEKDAY_MAP ).map( ( [ value, label ] ) => ( { value, label } ) );
const WEEKDAY_LABEL_TO_KEY = Object.fromEntries( Object.entries( WEEKDAY_MAP ).map( ( [ k, v ] ) => [ v, k ] ) );

const EMPTY_FORM = {
	block_time_slot_for: 'dates',
	delivery_days:       [],
	timeslot:            [],
};


// ── AddTimeSlotForm ────────────────────────────────────────────────────────────
// Uses the shared SettingsForm component so the layout matches every other
// Card-based form in the plugin (e.g. AddSpecificDatesForm in EditSchedule).

const AddTimeSlotForm = ( { form, setForm, onSave, saving, availableSlots } ) => {
	const setField = ( key ) => ( value ) =>
		setForm( ( prev ) => ( { ...prev, [ key ]: value } ) );

	const isDates = form.block_time_slot_for === 'dates';

	return (
		<SettingsFormCard title={ __( 'Add Time Slots', 'order-delivery-date' ) }>

			{ /* Block Time Slot For */ }
			<SettingsFormRow
				label={ __( 'Block Time Slot For:', 'order-delivery-date' ) }
				tooltip={ __( 'Choose whether to block by specific dates or recurring weekdays.', 'order-delivery-date' ) }
			>
				<RadioControl
					selected={ form.block_time_slot_for }
					options={ BLOCK_FOR_OPTIONS }
					onChange={ ( v ) => setForm( ( prev ) => ( {
						...prev,
						block_time_slot_for: v,
						delivery_days:       [],
					} ) ) }
					__nextHasNoMarginBottom
				/>
			</SettingsFormRow>

			{ /* Delivery Date / Weekday */ }
			<SettingsFormRow
				label={ isDates ? __( 'Delivery Date:', 'order-delivery-date' ) : __( 'Weekday:', 'order-delivery-date' ) }
				tooltip={ isDates
					? __( 'Pick the date you want to block.', 'order-delivery-date' )
					: __( 'Enter weekday names, e.g. Sunday, Monday.', 'order-delivery-date' ) }
				hint={ isDates ? __( 'Select Date', 'order-delivery-date' ) : __( 'Select Weekday', 'order-delivery-date' ) }
			>
				{ isDates ? (
					<MultiDatePickerField
						value={ Array.isArray( form.delivery_days ) ? form.delivery_days : [] }
						onChange={ setField( 'delivery_days' ) }
					/>
				) : (
					<div className="orddd-token-field">
						<FormTokenField
							value={ ( Array.isArray( form.delivery_days ) ? form.delivery_days : [] )
								.map( ( key ) => WEEKDAY_MAP[ key ] ?? key ) }
							suggestions={ WEEKDAY_OPTIONS.map( ( o ) => o.label ) }
							onChange={ ( labels ) => setField( 'delivery_days' )(
								labels.map( ( lbl ) => WEEKDAY_LABEL_TO_KEY[ lbl ] ?? lbl ).filter( Boolean )
							) }
							placeholder={ __( 'Select weekdays…', 'order-delivery-date' ) }
							__experimentalShowHowTo={ false }
							__nextHasNoMarginBottom
							__experimentalExpandOnFocus
						/>
					</div>
				) }
			</SettingsFormRow>

			{ /* Select Time Slots to Block */ }
			<SettingsFormRow
				label={ __( 'Select Time Slots to Block:', 'order-delivery-date' ) }
				tooltip={ __( 'Select the time slot you want to block.', 'order-delivery-date' ) }
				hint={ __( 'Select Time Slot to Block.', 'order-delivery-date' ) }
			>
				<div className="orddd-token-field">
					<FormTokenField
						value={ Array.isArray( form.timeslot ) ? form.timeslot : [] }
						suggestions={ availableSlots }
						onChange={ setField( 'timeslot' ) }
						placeholder={ __( 'Select time slots…', 'order-delivery-date' ) }
						__experimentalShowHowTo={ false }
						__nextHasNoMarginBottom
						__experimentalExpandOnFocus
					/>
				</div>
				
			</SettingsFormRow>

			<SettingsFormRow>
				<SettingsFormSaveBtn
					onClick={ onSave }
					isBusy={ saving }
					disabled={ saving }
				/>
			</SettingsFormRow>

		</SettingsFormCard>
	);
};

// ── BlockTimeSlot ─────────────────────────────────────────────────────────────

const BlockTimeSlot = () => {
	const [ loading,        setLoading        ] = useState( true );
	const [ slots,          setSlots          ] = useState( [] );
	const [ availableSlots, setAvailableSlots ] = useState( [] );
	const [ notice,         setNotice         ] = useState( null );
	const [ showForm,       setShowForm       ] = useState( false );
	const [ form,           setForm           ] = useState( EMPTY_FORM );
	const [ saving,         setSaving         ] = useState( false );
	const [ deletingKey,    setDeletingKey    ] = useState( null );

	// ── Fetch ──────────────────────────────────────────────────────────────────

	const fetchSlots = () => {
		setLoading( true );
		apiFetch( { path: '/orddd/v1/settings/block-time-slot' } )
			.then( ( data ) => {
				if ( data && ! Array.isArray( data ) && data.blocked_slots !== undefined ) {
					setSlots( Array.isArray( data.blocked_slots ) ? data.blocked_slots : [] );
					setAvailableSlots( Array.isArray( data.available_slots ) ? data.available_slots : [] );
				} else {
					// Fallback: old response shape was a plain array.
					setSlots( Array.isArray( data ) ? data : [] );
				}
			} )
			.catch( () => setNotice( { type: 'error', message: __( 'Could not load blocked time slots. Please refresh.', 'order-delivery-date' ) } ) )
			.finally( () => setLoading( false ) );
	};

	useEffect( () => { fetchSlots(); }, [] );

	// ── Add slot ───────────────────────────────────────────────────────────────

	const handleSave = async () => {
		const isDates = form.block_time_slot_for === 'dates';

		if ( ! Array.isArray( form.delivery_days ) || form.delivery_days.length === 0 ) {
			setNotice( { type: 'error', message: isDates
				? __( 'Please select at least one date.', 'order-delivery-date' )
				: __( 'Please select at least one weekday.', 'order-delivery-date' ) } );
			return;
		}

		if ( ! Array.isArray( form.timeslot ) || form.timeslot.length === 0 ) {
			setNotice( { type: 'error', message: __( 'Please select at least one time slot.', 'order-delivery-date' ) } );
			return;
		}

		setSaving( true );
		setNotice( null );

		try {
			for ( const day of form.delivery_days ) {
				for ( const slot of form.timeslot ) {
					await apiFetch( {
						path:   '/orddd/v1/settings/block-time-slot',
						method: 'POST',
						data:   {
							block_time_slot_for: form.block_time_slot_for,
							delivery_days:       day,
							timeslot:            slot,
						},
					} );
				}
			}

			setNotice( { type: 'success', message: __( 'Time slot blocked successfully.', 'order-delivery-date' ) } );
			setForm( EMPTY_FORM );
			setShowForm( false );
			fetchSlots();
		} catch ( err ) {
			setNotice( { type: 'error', message: err?.message ?? __( 'Failed to add blocked time slot.', 'order-delivery-date' ) } );
		} finally {
			setSaving( false );
		}
	};

	// ── Unblock slot ───────────────────────────────────────────────────────────

	const handleUnblock = async ( slot ) => {
		if ( ! window.confirm( __( 'Unblock this time slot?', 'order-delivery-date' ) ) ) return;

		const key = `${ slot.delivery_days }:${ slot.time_slot }`;
		setDeletingKey( key );
		setNotice( null );

		try {
			await apiFetch( {
				path:   '/orddd/v1/settings/block-time-slot',
				method: 'DELETE',
				data:   { key: 'single', delivery_days: slot.delivery_days, timeslot: slot.time_slot },
			} );
			setNotice( { type: 'success', message: __( 'Time slot unblocked.', 'order-delivery-date' ) } );
			fetchSlots();
		} catch ( err ) {
			setNotice( { type: 'error', message: err?.message ?? __( 'Failed to unblock time slot.', 'order-delivery-date' ) } );
		} finally {
			setDeletingKey( null );
		}
	};

	// ── Bulk apply ────────────────────────────────────────────────────────────

	const handleBulkApply = async ( action, selectedRows ) => {
		if ( action !== 'delete' || selectedRows.length === 0 ) return;
		if ( ! window.confirm( sprintf(
		/* translators: %s: number of blocked time slots to delete */
		_n( 'Delete %d blocked time slot?', 'Delete %d blocked time slots?', selectedRows.length, 'order-delivery-date' ),
		selectedRows.length
	) ) ) return;

		for ( const slot of selectedRows ) {
			await apiFetch( {
				path:   '/orddd/v1/settings/block-time-slot',
				method: 'DELETE',
				data:   { key: 'single', delivery_days: slot.delivery_days, timeslot: slot.time_slot },
			} ).catch( () => {} );
		}
		fetchSlots();
	};

	// ── Render ────────────────────────────────────────────────────────────────

	return (
		<div className="orddd-page-wrap osp-wrap">
			{ notice && (
				<Notice
					status={ notice.type }
					isDismissible
					onRemove={ () => setNotice( null ) }
				>
					{ notice.message }
				</Notice>
			) }

			<div className="osp-page-header">
				<div className="osp-page-header-text">
					<h1 className="osp-title">{ __( 'Block Time Slots', 'order-delivery-date' ) }</h1>
					<p className="osp-subtitle">
						{ __( 'Configure time slots to be blocked across all delivery schedules within the store', 'order-delivery-date' ) }
					</p>
				</div>
			</div>

			<DataTable
				columns={ COLUMNS }
				rows={ slots }
				rowKey={ ( slot ) => `${ slot.delivery_days }:${ slot.time_slot }` }
				renderCell={ ( row, colKey ) =>
					colKey === 'delivery_days'
						? ( WEEKDAY_MAP[ row.delivery_days ] ?? row.delivery_days )
						: ( row[ colKey ] ?? '—' )
				}
				loading={ loading }
				renderActions={ ( slot ) => {
					const key        = `${ slot.delivery_days }:${ slot.time_slot }`;
					const isDeleting = deletingKey === key;
					return (
						<button
							className="bts-unblock-link"
							onClick={ () => ! isDeleting && handleUnblock( slot ) }
							disabled={ isDeleting }
						>
							{ isDeleting ? __( 'Removing…', 'order-delivery-date' ) : __( 'Unblock', 'order-delivery-date' ) }
						</button>
					);
				} }
				showForm={ showForm }
				onToggleForm={ () => {
					setShowForm( ( v ) => ! v );
					setForm( EMPTY_FORM );
				} }
				renderForm={
					<AddTimeSlotForm
						form={ form }
						setForm={ setForm }
						onSave={ handleSave }
						saving={ saving }
						availableSlots={ availableSlots }
					/>
				}
				bulkActions={ BULK_ACTIONS }
				onBulkApply={ handleBulkApply }
				emptyMessage={ __( 'No blocked time slots found.', 'order-delivery-date' ) }
			/>
		</div>
	);
};

export default BlockTimeSlot;

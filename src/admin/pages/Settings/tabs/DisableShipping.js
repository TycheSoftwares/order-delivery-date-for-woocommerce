/**
 * WordPress dependencies
 */
import { useState, useEffect } from '@wordpress/element';
import {
	FormTokenField,
	Notice,
} from '@wordpress/components';
import InlineRadios from '../../EditSchedule/ui/InlineRadios';
import apiFetch from '@wordpress/api-fetch';
import { __, sprintf, _n } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import DataTable from '../../../components/DataTable';
import { UpgradePrompt } from '../../../components/ProNotice';
import SettingsFormCard, {
	SettingsFormRow,
	MultiDatePickerField,
	SettingsFormSaveBtn,
} from '../../../components/SettingsForm';

// ── Constants ─────────────────────────────────────────────────────────────────

const COLUMNS = [
	{ key: 'title', label: __( 'Shipping Methods',    'order-delivery-date' ) },
	{ key: 'date',  label: __( 'Delivery Days/Dates', 'order-delivery-date' ) },
];

const BULK_ACTIONS = [
	{ value: 'delete', label: __( 'Delete', 'order-delivery-date' ) },
];

const DISABLE_FOR_OPTIONS = [
	{ value: 'dates',    label: __( 'Dates',    'order-delivery-date' ) },
	{ value: 'weekdays', label: __( 'Weekdays', 'order-delivery-date' ) },
];

const WEEKDAY_OPTIONS = [
	{ value: 'orddd_weekday_0', label: __( 'Sunday',    'order-delivery-date' ) },
	{ value: 'orddd_weekday_1', label: __( 'Monday',    'order-delivery-date' ) },
	{ value: 'orddd_weekday_2', label: __( 'Tuesday',   'order-delivery-date' ) },
	{ value: 'orddd_weekday_3', label: __( 'Wednesday', 'order-delivery-date' ) },
	{ value: 'orddd_weekday_4', label: __( 'Thursday',  'order-delivery-date' ) },
	{ value: 'orddd_weekday_5', label: __( 'Friday',    'order-delivery-date' ) },
	{ value: 'orddd_weekday_6', label: __( 'Saturday',  'order-delivery-date' ) },
];

const WEEKDAY_MAP = Object.fromEntries( WEEKDAY_OPTIONS.map( ( o ) => [ o.value, o.label ] ) );

const EMPTY_FORM = {
	block_shipping_for: 'dates',
	shipping_methods:   [],  // array of method keys
	delivery_days:      [],  // array for dates, array of weekday keys for weekdays
};

// ── Helpers ───────────────────────────────────────────────────────────────────

const flattenRules = ( data ) => {
	if ( ! data || typeof data !== 'object' ) return [];

	const rows = [];
	Object.entries( data ).forEach( ( [ methodKey, entry ] ) => {
		if ( ! entry || typeof entry !== 'object' ) return;

		const entries = Array.isArray( entry ) ? entry : [ entry ];

		entries.forEach( ( e ) => {
			const dates = Array.isArray( e.delivery_date ) ? e.delivery_date : [];
			if ( dates.length === 0 ) {
				rows.push( { methodKey, title: e.title ?? methodKey, date: '—', dateKey: '' } );
			} else {
				dates.forEach( ( dateKey ) => {
					rows.push( {
						methodKey,
						title:   e.title ?? methodKey,
						date:    WEEKDAY_MAP[ dateKey ] ?? dateKey,
						dateKey,
					} );
				} );
			}
		} );
	} );

	return rows;
};

// ── AddShippingForm ───────────────────────────────────────────────────────────

const AddShippingForm = ( { form, setForm, onSave, saving } ) => {
	const setField = ( key ) => ( value ) =>
		setForm( ( prev ) => ( { ...prev, [ key ]: value } ) );

	const isDates = form.block_shipping_for === 'dates';

	// Shipping methods from site data
	const allMethods     = ( window.ordddAdminData?.all_shipping_methods || [] );
	const methodOpts     = allMethods.map( ( m ) => ( { value: m.method_key, label: m.title } ) );
	const toMethodLabels = ( values ) => values.map( ( v ) => methodOpts.find( ( o ) => o.value === v )?.label ?? v );
	const fromMethodLabels = ( labels ) => labels.map( ( l ) => methodOpts.find( ( o ) => o.label === l )?.value ?? l );

	// Weekday FormTokenField helpers
	const toWeekdayLabels   = ( values ) => values.map( ( v ) => WEEKDAY_MAP[ v ] ?? v );
	const fromWeekdayLabels = ( labels ) => labels.map( ( l ) => WEEKDAY_OPTIONS.find( ( o ) => o.label === l )?.value ?? l );

	return (
		<SettingsFormCard title={ __( 'Add Shipping Methods to Disable', 'order-delivery-date' ) }>

			{ /* Disable For */ }
			<SettingsFormRow
				label={ __( 'Disable Shipping Methods For:', 'order-delivery-date' ) }
				tooltip={ __( 'Choose whether to disable shipping on specific dates or recurring weekdays.', 'order-delivery-date' ) }
			>
				<InlineRadios
					name="ds_block_shipping_for"
					value={ form.block_shipping_for }
					options={ DISABLE_FOR_OPTIONS }
					onChange={ ( v ) => setForm( ( prev ) => ( {
						...prev,
						block_shipping_for: v,
						delivery_days:      [],
					} ) ) }
				/>
			</SettingsFormRow>

			{ /* Delivery Date / Weekday */ }
			<SettingsFormRow
				label={ isDates ? __( 'Delivery Date:', 'order-delivery-date' ) : __( 'Weekday:', 'order-delivery-date' ) }
				tooltip={ isDates
					? __( 'Pick the dates on which the shipping method should be disabled.', 'order-delivery-date' )
					: __( 'Select the weekdays on which the shipping method should be disabled.', 'order-delivery-date' ) }
				hint={ isDates ? __( 'Select Date', 'order-delivery-date' ) : __( 'Select Weekday', 'order-delivery-date' ) }
			>
				{ isDates ? (
					<MultiDatePickerField
						value={ Array.isArray( form.delivery_days ) ? form.delivery_days : [] }
						onChange={ setField( 'delivery_days' ) }
					/>
				) : (
					<FormTokenField
						label=""
						help=""
						suggestions={ WEEKDAY_OPTIONS.map( ( o ) => o.label ) }
						value={ toWeekdayLabels( Array.isArray( form.delivery_days ) ? form.delivery_days : [] ) }
						onChange={ ( labels ) => setField( 'delivery_days' )( fromWeekdayLabels( labels ) ) }
						__nextHasNoMarginBottom
						__experimentalExpandOnFocus
					/>
				) }
			</SettingsFormRow>

			{ /* Shipping Methods */ }
			<SettingsFormRow
				label={ __( 'Select Shipping Methods to Disable:', 'order-delivery-date' ) }
				tooltip={ __( 'Select the shipping methods you want to disable.', 'order-delivery-date' ) }
				hint={ __( 'Select Shipping Methods', 'order-delivery-date' ) }
			>
				<FormTokenField
					label=""
					help=""
					suggestions={ methodOpts.map( ( o ) => o.label ) }
					value={ toMethodLabels( form.shipping_methods ) }
					onChange={ ( labels ) => setField( 'shipping_methods' )( fromMethodLabels( labels ) ) }
					__nextHasNoMarginBottom
					__experimentalExpandOnFocus
				/>
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

// ── DisableShipping ───────────────────────────────────────────────────────────

const DisableShipping = ( { locked = false } ) => {
	const [ loading,     setLoading     ] = useState( ! locked );
	const [ rows,        setRows        ] = useState( [] );
	const [ notice,      setNotice      ] = useState( null );
	const [ showForm,    setShowForm    ] = useState( false );
	const [ form,        setForm        ] = useState( EMPTY_FORM );
	const [ saving,      setSaving      ] = useState( false );
	const [ deletingKey, setDeletingKey ] = useState( null );

	// ── Fetch rules ────────────────────────────────────────────────────────────

	const fetchRules = () => {
		if ( locked ) return;
		setLoading( true );
		apiFetch( { path: '/orddd/v1/settings/disable-shipping' } )
			.then( ( data ) => setRows( flattenRules( data ) ) )
			.catch( () => setNotice( { type: 'error', message: __( 'Could not load disable-shipping rules. Please refresh.', 'order-delivery-date' ) } ) )
			.finally( () => setLoading( false ) );
	};

	useEffect( () => { fetchRules(); }, [] );

	// ── Save rule ──────────────────────────────────────────────────────────────

	const handleSave = async () => {
		const isDates = form.block_shipping_for === 'dates';

		if ( form.shipping_methods.length === 0 ) {
			setNotice( { type: 'error', message: __( 'Please select at least one shipping method.', 'order-delivery-date' ) } );
			return;
		}

		if ( ! Array.isArray( form.delivery_days ) || form.delivery_days.length === 0 ) {
			setNotice( { type: 'error', message: isDates
				? __( 'Please select at least one date.', 'order-delivery-date' )
				: __( 'Please select at least one weekday.', 'order-delivery-date' ) } );
			return;
		}

		setSaving( true );
		setNotice( null );

		try {
			for ( const method of form.shipping_methods ) {
				for ( const day of form.delivery_days ) {
					await apiFetch( {
						path:   '/orddd/v1/settings/disable-shipping',
						method: 'POST',
						data:   {
							block_shipping_for: form.block_shipping_for,
							shipping_methods:   method,
							delivery_days:      day,
						},
					} );
				}
			}

			setNotice( { type: 'success', message: __( 'Shipping rule added successfully.', 'order-delivery-date' ) } );
			setForm( EMPTY_FORM );
			setShowForm( false );
			fetchRules();
		} catch ( err ) {
			setNotice( { type: 'error', message: err?.message ?? __( 'Failed to add rule. Please try again.', 'order-delivery-date' ) } );
		} finally {
			setSaving( false );
		}
	};

	// ── Enable (delete) rule ───────────────────────────────────────────────────

	const handleEnable = async ( row ) => {
		if ( ! window.confirm( __( 'Delete this shipping rule?', 'order-delivery-date' ) ) ) return;

		const key = `${ row.methodKey }:${ row.dateKey }`;
		setDeletingKey( key );
		setNotice( null );

		try {
			await apiFetch( {
				path:   '/orddd/v1/settings/disable-shipping',
				method: 'DELETE',
				data:   {
					key:             'single',
					shipping_method: row.methodKey,
					delivery_days:   row.dateKey,
				},
			} );
			setNotice( { type: 'success', message: __( 'Shipping rule deleted.', 'order-delivery-date' ) } );
			fetchRules();
		} catch ( err ) {
			setNotice( { type: 'error', message: err?.message ?? __( 'Failed to delete shipping rule.', 'order-delivery-date' ) } );
		} finally {
			setDeletingKey( null );
		}
	};

	// ── Bulk apply ────────────────────────────────────────────────────────────

	const handleBulkApply = async ( action, selectedRows ) => {
		if ( action !== 'delete' || selectedRows.length === 0 ) return;
		if ( ! window.confirm( sprintf(
			/* translators: %d: number of shipping rules */
			_n( 'Delete %d shipping rule?', 'Delete %d shipping rules?', selectedRows.length, 'order-delivery-date' ),
			selectedRows.length
		) ) ) return;

		for ( const row of selectedRows ) {
			await apiFetch( {
				path:   '/orddd/v1/settings/disable-shipping',
				method: 'DELETE',
				data:   { key: 'single', shipping_method: row.methodKey, delivery_days: row.dateKey },
			} ).catch( () => {} );
		}
		fetchRules();
	};

	// ── Render ────────────────────────────────────────────────────────────────

	return (
		<div className="orddd-page-wrap osp-wrap">
			{ locked && <UpgradePrompt message={ __( 'Unlock Disable Shipping settings by upgrading to Pro.', 'order-delivery-date' ) } /> }
			{ notice && ! locked && (
				<Notice
					status={ notice.type }
					isDismissible
					onRemove={ () => setNotice( null ) }
				>
					{ notice.message }
				</Notice>
			) }
			<div className={ locked ? 'orddd-locked-panel' : undefined }>

			<div className="osp-page-header">
				<div className="osp-page-header-text">
					<h1 className="osp-title">{ __( 'Disable Shipping', 'order-delivery-date' ) }</h1>
					<p className="osp-subtitle">
						{ __( 'Hide the shipping methods on checkout based on selected weekdays or specific dates.', 'order-delivery-date' ) }
					</p>
				</div>
			</div>

			<DataTable
				columns={ COLUMNS }
				rows={ rows }
				rowKey={ ( row ) => `${ row.methodKey }:${ row.dateKey }` }
				loading={ loading }
				renderActions={ ( row ) => {
					const key        = `${ row.methodKey }:${ row.dateKey }`;
					const isDeleting = deletingKey === key;
					return (
						<button
							className="ds-enable-link"
							onClick={ () => ! isDeleting && handleEnable( row ) }
							disabled={ isDeleting }
						>
							{ isDeleting ? __( 'Deleting\u2026', 'order-delivery-date' ) : __( 'Delete', 'order-delivery-date' ) }
						</button>
					);
				} }
				showForm={ showForm }
				onToggleForm={ () => {
					setShowForm( ( v ) => ! v );
					setForm( EMPTY_FORM );
				} }
				renderForm={
					<AddShippingForm
						form={ form }
						setForm={ setForm }
						onSave={ handleSave }
						saving={ saving }
					/>
				}
				bulkActions={ BULK_ACTIONS }
				onBulkApply={ handleBulkApply }
				emptyMessage={ __( 'No disabled shipping rules found.', 'order-delivery-date' ) }
			/>
			</div>
		</div>
	);
};

export default DisableShipping;

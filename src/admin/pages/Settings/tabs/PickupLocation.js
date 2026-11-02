/**
 * WordPress dependencies
 */
import { useState, useEffect } from '@wordpress/element';
import { ToggleControl, TextControl, SelectControl, Notice, Spinner, Button } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { __, _n, sprintf } from '@wordpress/i18n';

/**
 * External dependencies
 */
import { useForm, useWatch, Controller } from 'react-hook-form';

/**
 * Internal dependencies
 */
import SettingsPanel, { SettingRow } from '../../../components/SettingsPanel';
import { UpgradePrompt } from '../../../components/ProNotice';

// ── Constants ─────────────────────────────────────────────────────────────────

const SORT_ORDER_OPTIONS = [
	{ value: 'default',                label: __( 'Default (Order in which pickup locations are added)', 'order-delivery-date' ) },
	{ value: 'distance_from_customer', label: __( 'Distance from customer', 'order-delivery-date' ) },
	{ value: 'alphabetical',           label: __( 'Alphabetical by location name', 'order-delivery-date' ) },
];

const DEFAULT_VALUES = {
	orddd_enable_pickup_locations:             false,
	orddd_pickup_location_mandatory:           false,
	orddd_location_field_label:                '',
	orddd_location_sort_order:                 'default',
	orddd_geolocation_api_key:                 '',
	orddd_auto_populate_first_pickup_location: false,
};

const EMPTY_LOCATION = {
	address1: '',
	address2: '',
	city:     '',
	state:    '',
	postcode: '',
	country:  '',
	emails:   '',
	enabled:  true,
};

// ── Helpers ───────────────────────────────────────────────────────────────────

const formatAddress = ( loc ) =>
	[ loc.address1, loc.address2, loc.city, loc.state, loc.postcode, loc.country ]
		.filter( Boolean )
		.join( ', ' );

const getInitial = ( addr ) => ( addr ?? '' ).trim().charAt( 0 ).toUpperCase() || '?';

// ── LocationCard ──────────────────────────────────────────────────────────────

const LocationCard = ( { loc, onEdit, onDelete, isDeleting } ) => {
	const isActive = loc.enabled === 'yes' || loc.enabled === true;
	const address  = formatAddress( loc );

	return (
		<div className="pl-location-card">
			<div className="pl-location-avatar">{ getInitial( loc.address1 ) }</div>
			<div className="pl-location-body">
				<div className="pl-location-name-row">
					<span className="pl-location-name">{ address || '—' }</span>
					<span className={ `pl-badge ${ isActive ? 'pl-badge--active' : 'pl-badge--inactive' }` }>
						{ isActive ? __( 'Active', 'order-delivery-date' ) : __( 'Inactive', 'order-delivery-date' ) }
					</span>
				</div>
				{ loc.emails && (
					<p className="pl-location-email">{ loc.emails }</p>
				) }
			</div>
			<div className="pl-location-actions">
				<Button variant="tertiary" isSmall onClick={ onEdit }>
					{ __( 'Edit', 'order-delivery-date' ) }
				</Button>
				<Button variant="tertiary" isSmall isDestructive isBusy={ isDeleting } onClick={ onDelete }>
					{ __( 'Delete', 'order-delivery-date' ) }
				</Button>
			</div>
		</div>
	);
};

// ── LocationForm ──────────────────────────────────────────────────────────────

const LocationForm = ( { title, data, onChange, onSave, onCancel, isSaving } ) => {
	const set = ( field ) => ( value ) => onChange( { ...data, [ field ]: value } );

	return (
		<div className="pl-form-panel">
			<div className="pl-form-header">
				<span className="pl-form-dot" />
				<h2 className="pl-form-title">{ title }</h2>
			</div>

			<div className="pl-form-grid">
				<div className="pl-form-field">
					<label className="pl-form-label">{ __( 'Address Line 1', 'order-delivery-date' ) }</label>
					<TextControl
						value={ data.address1 }
						onChange={ set( 'address1' ) }
						placeholder={ __( 'Address Line 1', 'order-delivery-date' ) }
						__nextHasNoMarginBottom
					/>
				</div>
				<div className="pl-form-field">
					<label className="pl-form-label">{ __( 'Address Line 2 (optional)', 'order-delivery-date' ) }</label>
					<TextControl
						value={ data.address2 }
						onChange={ set( 'address2' ) }
						placeholder={ __( 'Address Line 2 (optional)', 'order-delivery-date' ) }
						__nextHasNoMarginBottom
					/>
				</div>
			</div>

			<div className="pl-form-grid">
				<div className="pl-form-field">
					<label className="pl-form-label">{ __( 'City', 'order-delivery-date' ) }</label>
					<TextControl
						value={ data.city }
						onChange={ set( 'city' ) }
						placeholder={ __( 'City', 'order-delivery-date' ) }
						__nextHasNoMarginBottom
					/>
				</div>
				<div className="pl-form-field">
					<label className="pl-form-label">{ __( 'State / Region', 'order-delivery-date' ) }</label>
					<TextControl
						value={ data.state }
						onChange={ set( 'state' ) }
						placeholder={ __( 'State / Region', 'order-delivery-date' ) }
						__nextHasNoMarginBottom
					/>
				</div>
			</div>

			<div className="pl-form-grid">
				<div className="pl-form-field">
					<label className="pl-form-label">{ __( 'Postcode / ZIP', 'order-delivery-date' ) }</label>
					<TextControl
						value={ data.postcode }
						onChange={ set( 'postcode' ) }
						placeholder={ __( 'Postcode / ZIP', 'order-delivery-date' ) }
						__nextHasNoMarginBottom
					/>
				</div>
				<div className="pl-form-field">
					<label className="pl-form-label">{ __( 'Country', 'order-delivery-date' ) }</label>
					<TextControl
						value={ data.country }
						onChange={ set( 'country' ) }
						placeholder={ __( 'Country', 'order-delivery-date' ) }
						__nextHasNoMarginBottom
					/>
				</div>
			</div>

			<div className="pl-form-half">
				<div className="pl-form-field">
					<label className="pl-form-label">{ __( 'Notification Email(s)', 'order-delivery-date' ) }</label>
					<TextControl
						value={ data.emails }
						onChange={ set( 'emails' ) }
						placeholder={ __( 'Notification Email(s)', 'order-delivery-date' ) }
						__nextHasNoMarginBottom
					/>
				</div>
			</div>

			<div className="pl-form-footer">
				<ToggleControl
					label={ __( 'Active — visible to customers at checkout', 'order-delivery-date' ) }
					checked={ data.enabled === true || data.enabled === 'yes' }
					onChange={ ( val ) => set( 'enabled' )( val ) }
					__nextHasNoMarginBottom
				/>
				<div className="pl-form-footer-actions">
					<Button
						variant="primary"
						isBusy={ isSaving }
						disabled={ isSaving }
						onClick={ onSave }
					>
						{ __( 'Save', 'order-delivery-date' ) }
					</Button>
					<Button variant="tertiary" onClick={ onCancel } disabled={ isSaving }>
						{ __( 'Cancel', 'order-delivery-date' ) }
					</Button>
				</div>
			</div>
		</div>
	);
};

// ── PickupLocation ────────────────────────────────────────────────────────────

const PickupLocation = ( { locked = false } ) => {
	const [ loading,       setLoading       ] = useState( ! locked );
	const [ saving,        setSaving        ] = useState( false );
	const [ notice,        setNotice        ] = useState( null );
	const [ locations,     setLocations     ] = useState( [] );
	const [ locLoading,    setLocLoading    ] = useState( ! locked );
	const [ locNotice,     setLocNotice     ] = useState( null );
	const [ editingId,     setEditingId     ] = useState( null );
	const [ editRow,       setEditRow       ] = useState( EMPTY_LOCATION );
	const [ addingNew,     setAddingNew     ] = useState( false );
	const [ newRow,        setNewRow        ] = useState( EMPTY_LOCATION );
	const [ savingLocId,   setSavingLocId   ] = useState( null );
	const [ deletingLocId, setDeletingLocId ] = useState( null );

	const { control, handleSubmit, reset } = useForm( { defaultValues: DEFAULT_VALUES } );

	const sortOrder = useWatch( { control, name: 'orddd_location_sort_order' } );

	// ── Fetch settings ─────────────────────────────────────────────────────────

	useEffect( () => {
		if ( locked ) return;

		apiFetch( { path: '/orddd/v1/settings' } )
			.then( ( data ) => {
				const pl = data?.pickup_locations ?? {};
				reset( {
					orddd_enable_pickup_locations:             pl.orddd_enable_pickup_locations             === 'on',
					orddd_pickup_location_mandatory:           pl.orddd_pickup_location_mandatory           === 'on',
					orddd_location_field_label:                pl.orddd_location_field_label                ?? '',
					orddd_location_sort_order:                 pl.orddd_location_sort_order                 || 'default',
					orddd_geolocation_api_key:                 pl.orddd_geolocation_api_key                 ?? '',
					orddd_auto_populate_first_pickup_location: pl.orddd_auto_populate_first_pickup_location === 'on',
				} );
			} )
			.catch( () => setNotice( { type: 'error', message: __( 'Could not load settings. Please refresh and try again.', 'order-delivery-date' ) } ) )
			.finally( () => setLoading( false ) );
	}, [] );

	// ── Fetch locations ────────────────────────────────────────────────────────

	const fetchLocations = () => {
		if ( locked ) return;
		setLocLoading( true );
		apiFetch( { path: '/orddd/v1/settings/pickup-locations' } )
			.then( ( data ) => setLocations( Array.isArray( data ) ? data : [] ) )
			.catch( () => setLocNotice( { type: 'error', message: __( 'Could not load pickup locations.', 'order-delivery-date' ) } ) )
			.finally( () => setLocLoading( false ) );
	};

	useEffect( () => { fetchLocations(); }, [] );

	// ── Save settings ──────────────────────────────────────────────────────────

	const onSubmit = async ( values ) => {
		setSaving( true );
		setNotice( null );
		try {
			const result = await apiFetch( {
				path:   '/orddd/v1/settings',
				method: 'POST',
				data:   {
					key:                                       'pickup_locations',
					orddd_enable_pickup_locations:             values.orddd_enable_pickup_locations             ? 'on' : '',
					orddd_pickup_location_mandatory:           values.orddd_pickup_location_mandatory           ? 'on' : '',
					orddd_location_field_label:                values.orddd_location_field_label,
					orddd_location_sort_order:                 values.orddd_location_sort_order,
					orddd_geolocation_api_key:                 values.orddd_geolocation_api_key,
					orddd_auto_populate_first_pickup_location: values.orddd_auto_populate_first_pickup_location ? 'on' : '',
				},
			} );
			setNotice( { type: 'success', message: result?.data?.message ?? __( 'Pickup location settings saved successfully.', 'order-delivery-date' ) } );
		} catch ( err ) {
			setNotice( { type: 'error', message: err?.message ?? __( 'Failed to save settings. Please try again.', 'order-delivery-date' ) } );
		} finally {
			setSaving( false );
		}
	};

	// ── Location actions ───────────────────────────────────────────────────────

	const startEdit = ( loc ) => {
		setAddingNew( false );
		setEditingId( loc.row_id );
		setEditRow( {
			address1: loc.address1 ?? '',
			address2: loc.address2 ?? '',
			city:     loc.city     ?? '',
			state:    loc.state    ?? '',
			postcode: loc.postcode ?? '',
			country:  loc.country  ?? '',
			emails:   loc.emails   ?? '',
			enabled:  loc.enabled === 'yes',
		} );
	};

	const cancelEdit = () => { setEditingId( null ); setEditRow( EMPTY_LOCATION ); };

	const saveEdit = async ( rowId ) => {
		setSavingLocId( rowId );
		setLocNotice( null );
		try {
			await apiFetch( {
				path:   `/orddd/v1/settings/pickup-locations/${ rowId }`,
				method: 'POST',
				data:   { ...editRow, enabled: editRow.enabled ? 'yes' : 'no' },
			} );
			setEditingId( null );
			setEditRow( EMPTY_LOCATION );
			fetchLocations();
		} catch ( err ) {
			setLocNotice( { type: 'error', message: err?.message ?? __( 'Failed to save location.', 'order-delivery-date' ) } );
		} finally {
			setSavingLocId( null );
		}
	};

	const saveNew = async () => {
		setSavingLocId( 'new' );
		setLocNotice( null );
		try {
			await apiFetch( {
				path:   '/orddd/v1/settings/pickup-locations',
				method: 'POST',
				data:   { ...newRow, enabled: newRow.enabled ? 'yes' : 'no' },
			} );
			setAddingNew( false );
			setNewRow( EMPTY_LOCATION );
			fetchLocations();
		} catch ( err ) {
			setLocNotice( { type: 'error', message: err?.message ?? __( 'Failed to add location.', 'order-delivery-date' ) } );
		} finally {
			setSavingLocId( null );
		}
	};

	const deleteLocation = async ( rowId ) => {
		if ( ! window.confirm( __( 'Are you sure you want to delete this pickup location?', 'order-delivery-date' ) ) ) return;
		setDeletingLocId( rowId );
		setLocNotice( null );
		try {
			await apiFetch( { path: `/orddd/v1/settings/pickup-locations/${ rowId }`, method: 'DELETE' } );
			fetchLocations();
		} catch ( err ) {
			setLocNotice( { type: 'error', message: err?.message ?? __( 'Failed to delete location.', 'order-delivery-date' ) } );
		} finally {
			setDeletingLocId( null );
		}
	};

	// ── Derived ───────────────────────────────────────────────────────────────

	const activeCount = locations.filter( ( l ) => l.enabled === 'yes' || l.enabled === true ).length;

	// ── Loading ───────────────────────────────────────────────────────────────

	if ( loading ) {
		return <div className="bd-loading"><Spinner /></div>;
	}

	// ── Render ────────────────────────────────────────────────────────────────

	return (
		<div className="orddd-page-wrap">
			{ locked && <UpgradePrompt message={ __( 'Unlock Pickup Location settings by upgrading to Pro.', 'order-delivery-date' ) } /> }
			{ notice && ! locked && (
				<Notice status={ notice.type } isDismissible onRemove={ () => setNotice( null ) }>
					{ notice.message }
				</Notice>
			) }

			<div className={ locked ? 'orddd-locked-panel' : undefined }>
			{ /* ── Pickup Location Settings ── */ }
			<SettingsPanel
				title={ __( 'Pickup Location Settings', 'order-delivery-date' ) }
				subtitle={ __( 'Configure general pickup location options for your checkout page.', 'order-delivery-date' ) }
				onSave={ locked ? undefined : handleSubmit( onSubmit ) }
				isSaving={ saving }
			>
				<SettingRow label={ __( 'Enable Pickup Locations:', 'order-delivery-date' ) } tooltip={ __( 'Allow customers to choose a pickup location on the checkout page.', 'order-delivery-date' ) }>
					<Controller
						name="orddd_enable_pickup_locations"
						control={ control }
						render={ ( { field } ) => (
							<ToggleControl checked={ field.value } onChange={ field.onChange } __nextHasNoMarginBottom />
						) }
					/>
				</SettingRow>

				<SettingRow label={ __( 'Customers must choose the pickup location:', 'order-delivery-date' ) } tooltip={ __( 'Selection of pickup location on the checkout page will become mandatory.', 'order-delivery-date' ) }>
					<Controller
						name="orddd_pickup_location_mandatory"
						control={ control }
						render={ ( { field } ) => (
							<ToggleControl checked={ field.value } onChange={ field.onChange } __nextHasNoMarginBottom />
						) }
					/>
				</SettingRow>

				<SettingRow label={ __( 'Locations Label:', 'order-delivery-date' ) } tooltip={ __( 'Label displayed for the delivery locations field on checkout. HTML allowed.', 'order-delivery-date' ) }>
					<Controller
						name="orddd_location_field_label"
						control={ control }
						render={ ( { field } ) => (
							<TextControl value={ field.value } onChange={ field.onChange } placeholder={ __( 'Select a pickup location', 'order-delivery-date' ) } __nextHasNoMarginBottom />
						) }
					/>
				</SettingRow>

				<SettingRow label={ __( 'Pickup Location Sort Order:', 'order-delivery-date' ) } tooltip={ __( 'Select how pickup locations should be sorted on checkout.', 'order-delivery-date' ) }>
					<Controller
						name="orddd_location_sort_order"
						control={ control }
						render={ ( { field } ) => (
							<SelectControl value={ field.value } options={ SORT_ORDER_OPTIONS } onChange={ field.onChange } __nextHasNoMarginBottom />
						) }
					/>
				</SettingRow>

				{ sortOrder === 'distance_from_customer' && (
					<SettingRow label={ __( 'Google Maps Geocoding API:', 'order-delivery-date' ) } tooltip={ __( 'Required for sorting locations by distance from customer.', 'order-delivery-date' ) }>
						<Controller
							name="orddd_geolocation_api_key"
							control={ control }
							render={ ( { field } ) => (
								<TextControl value={ field.value } onChange={ field.onChange } placeholder={ __( 'Enter your API key', 'order-delivery-date' ) } __nextHasNoMarginBottom />
							) }
						/>
						<p style={ { marginTop: '4px' } }>
							<a href="https://developers.google.com/maps/documentation/geocoding/get-api-key" target="_blank" rel="noreferrer">{ __( 'Get API Key', 'order-delivery-date' ) }</a>
						</p>
					</SettingRow>
				) }

				<SettingRow label={ __( 'Automatically pre-select first pickup location:', 'order-delivery-date' ) } tooltip={ __( 'The first pickup location will be auto-selected on the checkout page.', 'order-delivery-date' ) }>
					<Controller
						name="orddd_auto_populate_first_pickup_location"
						control={ control }
						render={ ( { field } ) => (
							<ToggleControl checked={ field.value } onChange={ field.onChange } __nextHasNoMarginBottom />
						) }
					/>
				</SettingRow>
			</SettingsPanel>

			{ /* ── Manage Pickup Locations ── */ }
			<div className="osp-wrap pl-locations-wrap">

				{ locNotice && (
					<Notice status={ locNotice.type } isDismissible onRemove={ () => setLocNotice( null ) }>
						{ locNotice.message }
					</Notice>
				) }

				<div className="osp-page-header">
					<div className="osp-page-header-text">
						<h1 className="osp-title">{ __( 'Pickup Locations', 'order-delivery-date' ) }</h1>
						<p className="osp-subtitle">
							{ locations.length > 0
								? sprintf(
									/* translators: 1: number of active locations, 2: total number of locations, 3: singular/plural "location(s)" */
									__( '%1$d of %2$d %3$s active', 'order-delivery-date' ),
									activeCount,
									locations.length,
									_n( 'location', 'locations', locations.length, 'order-delivery-date' )
								)
								: __( 'No locations added yet', 'order-delivery-date' )
							}
						</p>
					</div>
					<Button
						className="pl-add-btn"
						onClick={ () => { setAddingNew( true ); setEditingId( null ); setNewRow( EMPTY_LOCATION ); } }
						disabled={ addingNew }
					>
						{ __( '+ Add Location', 'order-delivery-date' ) }
					</Button>
				</div>

				{ /* Add form */ }
				{ addingNew && (
					<LocationForm
						title={ __( 'New Pickup Location', 'order-delivery-date' ) }
						data={ newRow }
						onChange={ setNewRow }
						onSave={ saveNew }
						onCancel={ () => { setAddingNew( false ); setNewRow( EMPTY_LOCATION ); } }
						isSaving={ savingLocId === 'new' }
					/>
				) }

				{ locLoading ? (
					<div className="bd-loading"><Spinner /></div>
				) : (
					<div className="pl-list">
						{ locations.length === 0 && ! addingNew && (
							<p className="pl-empty">{ __( 'No pickup locations found. Click', 'order-delivery-date' ) } <strong>{ __( '+ Add Location', 'order-delivery-date' ) }</strong> { __( 'to get started.', 'order-delivery-date' ) }</p>
						) }

						{ locations.map( ( loc ) => {
							if ( editingId === loc.row_id ) {
								return (
									<LocationForm
										key={ loc.row_id }
										title={ __( 'Edit Pickup Location', 'order-delivery-date' ) }
										data={ editRow }
										onChange={ setEditRow }
										onSave={ () => saveEdit( loc.row_id ) }
										onCancel={ cancelEdit }
										isSaving={ savingLocId === loc.row_id }
									/>
								);
							}

							return (
								<LocationCard
									key={ loc.row_id }
									loc={ loc }
									onEdit={ () => startEdit( loc ) }
									onDelete={ () => deleteLocation( loc.row_id ) }
									isDeleting={ deletingLocId === loc.row_id }
								/>
							);
						} ) }
					</div>
				) }
			</div>
			</div>
		</div>
	);
};

export default PickupLocation;

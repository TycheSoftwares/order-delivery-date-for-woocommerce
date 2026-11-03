/**
 * WordPress dependencies
 */
import { useState, useEffect } from '@wordpress/element';
import { ToggleControl, TextControl, SelectControl, Notice, Spinner } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { __ } from '@wordpress/i18n';

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

// ── PickupLocation ────────────────────────────────────────────────────────────

const PickupLocation = ( { locked = false } ) => {
	const [ loading, setLoading ] = useState( ! locked );
	const [ saving,  setSaving  ] = useState( false );
	const [ notice,  setNotice  ] = useState( null );

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
			</div>
		</div>
	);
};

export default PickupLocation;

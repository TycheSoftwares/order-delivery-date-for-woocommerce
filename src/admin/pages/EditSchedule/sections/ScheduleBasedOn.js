import { Controller, useWatch } from 'react-hook-form';
import { SelectControl, CardDivider, FormTokenField } from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';

import { SettingRow } from '../../../components/SettingsPanel';
import TokenField    from '../../../components/TokenField';

import { BASED_ON_OPTIONS } from '../constants';
import { getAdminData }     from '../helpers';
import InlineRadios         from '../ui/InlineRadios';

// ── ScheduleBasedOnSection ────────────────────────────────────────────────────

const ScheduleBasedOnSection = ( { control } ) => {
	const data         = getAdminData();
	const basedOn      = useWatch( { control, name: 'delivery_settings_based_on' } ) || 'shipping_methods';
	const scheduleType = useWatch( { control, name: 'schedule_type' } ) || 'delivery';
	const isPickup     = scheduleType === 'pickup';

	const allMethods   = data.all_shipping_methods || [];
	const shippingOpts = allMethods
		.filter( ( m ) => isPickup
			? /pickup/i.test( m.method_key )
			: ! /pickup/i.test( m.method_key )
		)
		.map( ( m ) => ( { value: m.method_key, label: m.title } ) );

	const userRoleOpts = Object.entries( data.user_roles || {} ).map( ( [ k, v ] ) => ( { value: k, label: v } ) );
	const pluginLocationOpts = ( Array.isArray( data.pickup_locations ) ? data.pickup_locations : [] )
		.filter( ( l ) => l.row_id && l.enabled !== 'no' )
		.map( ( l ) => ( { value: String( l.row_id ), label: l.formatted_address || l.address || String( l.row_id ) } ) );

	const wcLocationOpts = ( Array.isArray( data.wc_pickup_locations ) ? data.wc_pickup_locations : [] )
		.filter( ( l ) => l.name )
		.map( ( l, i ) => ( { value: `pickup_location:${ i }`, label: l.name.trim() } ) );

	const lppLocationOpts = Array.isArray( data.local_pickup_plus_locations )
		? data.local_pickup_plus_locations
		: [];

	const locationOpts = [ ...pluginLocationOpts, ...wcLocationOpts, ...lppLocationOpts ];

	const orderTypeOpts = isPickup
		? [ { value: 'pickup',   label: __( 'Pickup', 'order-delivery-date' )   } ]
		: [ { value: 'delivery', label: __( 'Delivery', 'order-delivery-date' ) } ];

	// Helpers for FormTokenField label↔value adapters
	const toLabels    = ( values, opts ) => values.map( ( v ) => opts.find( ( o ) => o.value === v )?.label ?? v );
	const fromLabels  = ( labels, opts ) => labels.map( ( l ) => opts.find( ( o ) => o.label === l )?.value ?? l );

	// Build options from the PHP-filtered settings_based_on object (respects orddd_settings_based_on_options filter).
	// Fall back to the full BASED_ON_OPTIONS list when ordddAdminData is not yet populated.
	// Pickup Locations is only valid for Pickup schedule type — exclude it for Delivery.
	const basedOnOptions = ( data.settings_based_on
		? Object.entries( data.settings_based_on ).map( ( [ value, label ] ) => ( { value, label } ) )
		: BASED_ON_OPTIONS
	).filter( ( o ) => isPickup || o.value !== 'orddd_locations' );

	return (
		<>
			<SettingRow label={ __( 'Schedule Type:', 'order-delivery-date' ) } tooltip={ __( 'Choose whether this schedule applies to Delivery or Pickup orders.', 'order-delivery-date' ) }>
				<Controller
					name="schedule_type"
					control={ control }
					render={ ( { field } ) => (
						<InlineRadios
							name="es_schedule_type"
							value={ field.value }
							onChange={ field.onChange }
							options={ [
								{ value: 'delivery', label: __( 'Delivery', 'order-delivery-date' ) },
								{ value: 'pickup',   label: __( 'Pickup', 'order-delivery-date' )   },
							] }
						/>
					) }
				/>
			</SettingRow>
			<CardDivider />

			<SettingRow label={ __( 'Schedule Based On:', 'order-delivery-date' ) } tooltip={ __( 'Select the criterion that determines which orders this schedule applies to.', 'order-delivery-date' ) }>
				<Controller
					name="delivery_settings_based_on"
					control={ control }
					render={ ( { field } ) => (
						<SelectControl
							value={ field.value }
							options={ basedOnOptions }
							onChange={ field.onChange }
							__nextHasNoMarginBottom
						/>
					) }
				/>
			</SettingRow>
			<CardDivider />

			{ basedOn === 'shipping_methods' && (
				<SettingRow label={ __( 'Shipping Methods:', 'order-delivery-date' ) } tooltip={ __( 'Select the shipping methods this schedule applies to.', 'order-delivery-date' ) }>
					<Controller
						name="shipping_methods"
						control={ control }
						render={ ( { field } ) => (
							<FormTokenField
								label=""
								help=""
								suggestions={ shippingOpts.map( ( o ) => o.label ) }
								value={ toLabels( field.value, shippingOpts ) }
								onChange={ ( labels ) => field.onChange( fromLabels( labels, shippingOpts ) ) }
								__nextHasNoMarginBottom
								__experimentalExpandOnFocus
							/>
						) }
					/>
				</SettingRow>
			) }

			{ basedOn === 'product_categories' && (
				<>
					<SettingRow label={ __( 'Product Categories:', 'order-delivery-date' ) } tooltip={ __( 'Search and select product categories for this schedule.', 'order-delivery-date' ) }>
						<Controller
							name="product_categories"
							control={ control }
							render={ ( { field } ) => (
								<TokenField type="categories" field={ { value: field.value, onChange: field.onChange } } />
							) }
						/>
					</SettingRow>
					<CardDivider />
					<SettingRow label={ __( 'Shipping Methods:', 'order-delivery-date' ) } tooltip={ __( 'Optional: restrict to specific shipping methods within the selected categories.', 'order-delivery-date' ) }>
						<Controller
							name="shipping_methods_for_categories"
							control={ control }
							render={ ( { field } ) => (
								<FormTokenField
									label=""
									help=""
									suggestions={ shippingOpts.map( ( o ) => o.label ) }
									value={ toLabels( field.value, shippingOpts ) }
									onChange={ ( labels ) => field.onChange( fromLabels( labels, shippingOpts ) ) }
									__nextHasNoMarginBottom
									__experimentalExpandOnFocus
								/>
							) }
						/>
					</SettingRow>
				</>
			) }

			{ basedOn === 'product_tags' && (
				<>
					<SettingRow label={ __( 'Product Tags:', 'order-delivery-date' ) } tooltip={ __( 'Search and select product tags for this schedule.', 'order-delivery-date' ) }>
						<Controller
							name="product_tags"
							control={ control }
							render={ ( { field } ) => (
								<TokenField type="tags" field={ { value: field.value, onChange: field.onChange } } />
							) }
						/>
					</SettingRow>
					<CardDivider />
					<SettingRow label={ __( 'Shipping Methods:', 'order-delivery-date' ) } tooltip={ __( 'Optional: restrict to specific shipping methods within the selected tags.', 'order-delivery-date' ) }>
						<Controller
							name="shipping_methods_for_product_tags"
							control={ control }
							render={ ( { field } ) => (
								<FormTokenField
									label=""
									help=""
									suggestions={ shippingOpts.map( ( o ) => o.label ) }
									value={ toLabels( field.value ?? [], shippingOpts ) }
									onChange={ ( labels ) => field.onChange( fromLabels( labels, shippingOpts ) ) }
									__nextHasNoMarginBottom
									__experimentalExpandOnFocus
								/>
							) }
						/>
					</SettingRow>
				</>
			) }

			{ basedOn === 'products' && (
				<>
					<SettingRow label={ __( 'Products:', 'order-delivery-date' ) } tooltip={ __( 'Search and select individual products for this schedule.', 'order-delivery-date' ) }>
						<Controller
							name="products"
							control={ control }
							render={ ( { field } ) => (
								<TokenField type="products" field={ { value: field.value, onChange: field.onChange } } />
							) }
						/>
					</SettingRow>
					<CardDivider />
					<SettingRow label={ __( 'Shipping Methods:', 'order-delivery-date' ) } tooltip={ __( 'Optional: restrict to specific shipping methods for the selected products.', 'order-delivery-date' ) }>
						<Controller
							name="shipping_methods_for_products"
							control={ control }
							render={ ( { field } ) => (
								<FormTokenField
									label=""
									help=""
									suggestions={ shippingOpts.map( ( o ) => o.label ) }
									value={ toLabels( field.value, shippingOpts ) }
									onChange={ ( labels ) => field.onChange( fromLabels( labels, shippingOpts ) ) }
									__nextHasNoMarginBottom
									__experimentalExpandOnFocus
								/>
							) }
						/>
					</SettingRow>
				</>
			) }

			{ basedOn === 'orddd_locations' && (
				<>
					<SettingRow label={ __( 'Pickup Locations:', 'order-delivery-date' ) } tooltip={ __( 'Select pickup locations this schedule applies to.', 'order-delivery-date' ) }>
						<Controller
							name="orddd_locations"
							control={ control }
							render={ ( { field } ) => (
								<FormTokenField
									label=""
									help=""
									suggestions={ locationOpts.map( ( o ) => o.label ) }
									value={ toLabels( field.value, locationOpts ) }
									onChange={ ( labels ) => field.onChange( fromLabels( labels, locationOpts ) ) }
									__nextHasNoMarginBottom
									__experimentalExpandOnFocus
								/>
							) }
						/>
						<p className="orddd-field-note">
							{ __( 'Note: Pickup Locations are managed separately.', 'order-delivery-date' ) }
							{ ' ' }
							<a href={ data.pickup_locations_url }>
								{ __( 'Click here to configure Pickup Locations.', 'order-delivery-date' ) }
							</a>
						</p>
					</SettingRow>
					<CardDivider />
					<SettingRow label={ __( 'Product Categories:', 'order-delivery-date' ) } tooltip={ __( 'Optional: restrict to specific product categories for the selected locations.', 'order-delivery-date' ) }>
						<Controller
							name="categories_for_pickup_locations"
							control={ control }
							render={ ( { field } ) => (
								<TokenField type="categories" field={ { value: field.value, onChange: field.onChange } } />
							) }
						/>
					</SettingRow>
				</>
			) }

			{ basedOn === 'order_type' && (
				<SettingRow label={ __( 'Order Type:', 'order-delivery-date' ) } tooltip={ __( 'Select order types this schedule applies to.', 'order-delivery-date' ) }>
					<Controller
						name="order_type"
						control={ control }
						render={ ( { field } ) => (
							<FormTokenField
								label=""
								help=""
								suggestions={ orderTypeOpts.map( ( o ) => o.label ) }
								value={ toLabels( field.value, orderTypeOpts ) }
								onChange={ ( labels ) => field.onChange( fromLabels( labels, orderTypeOpts ) ) }
								__nextHasNoMarginBottom
								__experimentalExpandOnFocus
							/>
						) }
					/>
				</SettingRow>
			) }

			{ basedOn === 'user_role' && (
				<SettingRow label={ __( 'User Role:', 'order-delivery-date' ) } tooltip={ __( 'Select user roles this schedule applies to.', 'order-delivery-date' ) }>
					<Controller
						name="user_role"
						control={ control }
						render={ ( { field } ) => (
							<FormTokenField
								label=""
								help=""
								suggestions={ userRoleOpts.map( ( o ) => o.label ) }
								value={ toLabels( field.value, userRoleOpts ) }
								onChange={ ( labels ) => field.onChange( fromLabels( labels, userRoleOpts ) ) }
								__nextHasNoMarginBottom
								__experimentalExpandOnFocus
							/>
						) }
					/>
				</SettingRow>
			) }
		</>
	);
};

export default ScheduleBasedOnSection;

/**
 * WordPress dependencies
 */
import { ToggleControl, TextControl, RadioControl } from '@wordpress/components';
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

// ── Constants ────────────────────────────────────────────────────────────────

const POSITION_OPTIONS = [
	{ value: 'before_billing_section', label: __( 'Before Billing Section', 'order-delivery-date' ) },
	{ value: 'before_delivery_fields', label: __( 'Before Delivery Fields', 'order-delivery-date' ) },
	{ value: 'custom',                 label: __( 'Custom',                 'order-delivery-date' ) },
];

const DEFAULT_VALUES = {
	orddd_enable_order_type:                false,
	orddd_order_type_field_label:           '',
	orddd_order_type_delivery_label:        '',
	orddd_order_type_pickup_label:          '',
	orddd_order_type_field_position:        'before_billing_section',
	orddd_custom_hook_for_order_type_field: '',
	orddd_order_type_minimum_amount:        '',
	orddd_hide_shipping_methods:            false,
};

// ── Component ─────────────────────────────────────────────────────────────────

const OrderType = ( { locked = false } ) => {
	const { control } = useForm( { defaultValues: DEFAULT_VALUES } );

	const fieldPosition = useWatch( { control, name: 'orddd_order_type_field_position' } );

	return (
		<div className="orddd-page-wrap">
			{ locked && <UpgradePrompt message={ __( 'Unlock Order Type settings by upgrading to Pro.', 'order-delivery-date' ) } /> }

			<div className={ locked ? 'orddd-locked-panel' : undefined }>
			<SettingsPanel
				title={ __( 'Order Type Settings', 'order-delivery-date' ) }
				subtitle={ __( 'Let customers select between Pickup or Delivery before entering the address.', 'order-delivery-date' ) }
			>
				<SettingRow
					label={ __( 'Enable option to choose from Delivery or Pickup:', 'order-delivery-date' ) }
					tooltip={ __( 'If enabled, customers can choose between Delivery & Pickup on the checkout page.', 'order-delivery-date' ) }
				>
					<Controller
						name="orddd_enable_order_type"
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
					label={ __( 'Field Label:', 'order-delivery-date' ) }
					tooltip={ __( 'Choose a heading for the order type section on checkout.', 'order-delivery-date' ) }
				>
					<Controller
						name="orddd_order_type_field_label"
						control={ control }
						render={ ( { field } ) => (
							<TextControl
								value={ field.value }
								onChange={ field.onChange }
								placeholder={ __( 'How do you want the order to be fulfilled?', 'order-delivery-date' ) }
								__nextHasNoMarginBottom
								style={ { maxWidth: '300px' } }
							/>
						) }
					/>
				</SettingRow>

				<SettingRow
					label={ __( "Label for 'Delivery' option:", 'order-delivery-date' ) }
					tooltip={ __( 'Choose the label displayed for the Delivery option.', 'order-delivery-date' ) }
				>
					<Controller
						name="orddd_order_type_delivery_label"
						control={ control }
						render={ ( { field } ) => (
							<TextControl
								value={ field.value }
								onChange={ field.onChange }
								placeholder={ __( 'Deliver to My Address', 'order-delivery-date' ) }
								__nextHasNoMarginBottom
							/>
						) }
					/>
				</SettingRow>

				<SettingRow
					label={ __( "Label for 'Pickup' option:", 'order-delivery-date' ) }
					tooltip={ __( 'Choose the label displayed for the Pickup option.', 'order-delivery-date' ) }
				>
					<Controller
						name="orddd_order_type_pickup_label"
						control={ control }
						render={ ( { field } ) => (
							<TextControl
								value={ field.value }
								onChange={ field.onChange }
								placeholder={ __( 'Pickup from Store', 'order-delivery-date' ) }
								__nextHasNoMarginBottom
							/>
						) }
					/>
				</SettingRow>

				<SettingRow
					label={ __( 'Field Position:', 'order-delivery-date' ) }
					tooltip={ __( 'Where to display the order type selection field on the checkout page.', 'order-delivery-date' ) }
				>
					<Controller
						name="orddd_order_type_field_position"
						control={ control }
						render={ ( { field } ) => (
							<RadioControl
								selected={ field.value }
								options={ POSITION_OPTIONS }
								onChange={ field.onChange }
								__nextHasNoMarginBottom
							/>
						) }
					/>
					{ fieldPosition === 'custom' && (
						<Controller
							name="orddd_custom_hook_for_order_type_field"
							control={ control }
							render={ ( { field } ) => (
								<TextControl
									value={ field.value }
									onChange={ field.onChange }
									placeholder={ __( 'Add a custom hook', 'order-delivery-date' ) }
									__nextHasNoMarginBottom
									style={ { marginTop: '10px', maxWidth: '300px' } }
								/>
							) }
						/>
					) }
				</SettingRow>

				<SettingRow
					label={ __( 'Minimum Order Amount required for availing Delivery:', 'order-delivery-date' ) }
					tooltip={ __( 'Set the minimum order amount required for customers to qualify for delivery.', 'order-delivery-date' ) }
				>
					<Controller
						name="orddd_order_type_minimum_amount"
						control={ control }
						render={ ( { field } ) => (
							<TextControl
								type="number"
								value={ field.value }
								onChange={ field.onChange }
								placeholder="0"
								help={ __( 'Note: Free delivery can be offered by setting up "Minimum Order Amount for Waiving off Delivery Charges:" option under the delivery schedules.', 'order-delivery-date' ) }
								__nextHasNoMarginBottom
							/>
						) }
					/>
				</SettingRow>

				<SettingRow
					label={ __( 'Dynamically show or hide shipping methods based on selection:', 'order-delivery-date' ) }
					tooltip={ __( 'If enabled, shipping methods will be shown/hidden based on the order type selected.', 'order-delivery-date' ) }
				>
					<Controller
						name="orddd_hide_shipping_methods"
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

			</SettingsPanel>
			</div>
		</div>
	);
};

export default OrderType;

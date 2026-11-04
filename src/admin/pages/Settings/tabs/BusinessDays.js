/**
 * WordPress dependencies
 */
import { ToggleControl, FormTokenField, TextControl } from '@wordpress/components';
import TimeInput from '../../../components/ui/TimeInput';
import { __ } from '@wordpress/i18n';
import { UpgradePrompt } from '../../../components/ProNotice';

/**
 * External dependencies
 */
import { useForm, Controller } from 'react-hook-form';

/**
 * Internal dependencies
 */
import SettingsPanel, { SettingRow } from '../../../components/SettingsPanel';

// ── Constants ────────────────────────────────────────────────────────────────

const DAY_OPTIONS = [
	{ key: 'orddd_shipping_day_0', label: 'Sunday'    },
	{ key: 'orddd_shipping_day_1', label: 'Monday'    },
	{ key: 'orddd_shipping_day_2', label: 'Tuesday'   },
	{ key: 'orddd_shipping_day_3', label: 'Wednesday' },
	{ key: 'orddd_shipping_day_4', label: 'Thursday'  },
	{ key: 'orddd_shipping_day_5', label: 'Friday'    },
	{ key: 'orddd_shipping_day_6', label: 'Saturday'  },
];

const DAY_LABELS = DAY_OPTIONS.map( ( d ) => d.label );

const DEFAULT_VALUES = {
	enableShippingDays: false,
	selectedDays:       [],
	openingTime:        '',
	closingTime:        '',
};

// ── Component ─────────────────────────────────────────────────────────────────

const BusinessDays = ( { locked = false } ) => {
	const { control } = useForm( { defaultValues: DEFAULT_VALUES } );

	return (
		<div className="orddd-page-wrap">
			{ locked && <UpgradePrompt message={ __( 'Unlock Business Days configuration by upgrading to Pro.', 'order-delivery-date' ) } /> }

			<div className={ locked ? 'orddd-locked-panel' : undefined }>
			<SettingsPanel
				title={ __( 'Business Days and Hours', 'order-delivery-date' ) }
				subtitle={
					<>
						{ __( 'Business days a.k.a. working days and hours of your own company play a major role in correct delivery calculation. Eager for the deets?', 'order-delivery-date' ) }{ ' ' }
						<a
							href="https://www.tychesoftwares.com/docs/docs/order-delivery-date-pro-for-woocommerce/setup-shipping-days/?utm_source=userwebsite&utm_medium=link&utm_campaign=OrderDeliveryDateProSetting"
							target="_blank"
							rel="noreferrer"
						>
							{ __( 'this post', 'order-delivery-date' ) }
						</a>
						{ ' ' }{ __( 'to find out more.', 'order-delivery-date' ) }
					</>
				}
			>
				<SettingRow
					label={ __( 'Calculate delivery by working days:', 'order-delivery-date' ) }
					tooltip={ __( 'Calculate Minimum Delivery Time, Same Day cut-off and Next Day cut-off based on the business days selected.', 'order-delivery-date' ) }
				>
					<Controller
						name="enableShippingDays"
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
					label={ __( 'Business Days:', 'order-delivery-date' ) }
					tooltip={ __( 'Business days of your store.', 'order-delivery-date' ) }
				>
					<Controller
						name="selectedDays"
						control={ control }
						render={ ( { field } ) => (
							<FormTokenField
								label={ __( 'Select \'Weekdays\' or \'Business Days\'', 'order-delivery-date' ) }
								value={ field.value }
								suggestions={ DAY_LABELS }
								onChange={ field.onChange }
								placeholder={ __( 'Select Weekdays', 'order-delivery-date' ) }
								__experimentalShowHowTo={ false }
								__experimentalExpandOnFocus
								__nextHasNoMarginBottom
							/>
						) }
					/>
				</SettingRow>

				<SettingRow
					label={ __( 'Opening Time:', 'order-delivery-date' ) }
					tooltip={ __( 'What time does your business start delivering orders?', 'order-delivery-date' ) }
				>
					<Controller
						name="openingTime"
						control={ control }
						render={ ( { field } ) => (
							<TimeInput
								value={ field.value }
								onChange={ field.onChange }
							/>
						) }
					/>
				</SettingRow>

				<SettingRow
					label={ __( 'Closing Time:', 'order-delivery-date' ) }
					tooltip={ __( 'What time does your business stop delivering orders?', 'order-delivery-date' ) }
				>
					<Controller
						name="closingTime"
						control={ control }
						render={ ( { field } ) => (
							<TimeInput
								value={ field.value }
								onChange={ field.onChange }
							/>
						) }
					/>
				</SettingRow>

			</SettingsPanel>
			</div>
		</div>
	);
};

export default BusinessDays;

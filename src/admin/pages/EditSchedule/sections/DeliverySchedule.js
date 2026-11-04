import { Controller } from 'react-hook-form';
import {
	TextControl,
	ToggleControl,
	CardDivider,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import { SettingRow } from '../../../components/SettingsPanel';
import { ProFieldNotice } from '../../../components/ProNotice';

// ── Panel3DeliverySchedule ────────────────────────────────────────────────────

const Panel3DeliverySchedule = ( { form, control, labels } ) => {
	return (
		<>
			<SettingRow
				label={ __( 'Priority:', 'order-delivery-date' ) }
				tooltip={ __( 'Add priority for the delivery schedule between 1 to 10 with 1 being the highest.', 'order-delivery-date' ) }
			>
				<ProFieldNotice />
			</SettingRow>
			<CardDivider />

			<SettingRow
				label={ labels.mdt() }
				tooltip={ __( 'Minimum number of hours required to prepare for delivery.', 'order-delivery-date' ) }
			>
				<Controller name="orddd_lite_minimumOrderDays" control={ control }
					render={ ( { field } ) => (
						<TextControl type="number" min={ 0 } step={ 0.1 } placeholder={ __( 'e.g. 2', 'order-delivery-date' ) }
							value={ field.value } onChange={ field.onChange } __nextHasNoMarginBottom />
					) }
				/>
			</SettingRow>
			<CardDivider />

			<SettingRow
				label={ __( 'Apply Minimum Delivery Time for non working weekdays:', 'order-delivery-date' ) }
				tooltip={ __( 'If selected, then the Minimum Delivery Time (in hours) will be applied on the non working weekdays which are unchecked in Delivery Weekdays.', 'order-delivery-date' ) }
			>
				<Controller name="orddd_lite_calculate_min_time_disabled_days" control={ control }
					render={ ( { field } ) => (
						<ToggleControl checked={ field.value === 'on' }
							onChange={ ( v ) => field.onChange( v ? 'on' : '' ) } __nextHasNoMarginBottom />
					) }
				/>
			</SettingRow>
			<CardDivider />

			<SettingRow
				label={ __( 'Minimum Pickup Time (in hours):', 'order-delivery-date' ) }
				tooltip={ __( 'Minimum number of hours required to prepare for pickup.', 'order-delivery-date' ) }
			>
				<ProFieldNotice />
			</SettingRow>
			<CardDivider />

			<SettingRow
				label={ labels.same_day_cutoff() }
				tooltip={ __( 'Current day will be disabled if an order is placed after this time.', 'order-delivery-date' ) }
			>
				<ProFieldNotice />
			</SettingRow>
			<CardDivider />

			<SettingRow
				label={ labels.next_day_cutoff() }
				tooltip={ __( 'Next day will be disabled if an order is placed after this time.', 'order-delivery-date' ) }
			>
				<ProFieldNotice />
			</SettingRow>
		</>
	);
};

export default Panel3DeliverySchedule;

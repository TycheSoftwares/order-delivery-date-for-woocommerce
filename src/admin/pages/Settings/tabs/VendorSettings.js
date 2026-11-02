/**
 * WordPress dependencies
 */
import { useState, useEffect } from '@wordpress/element';
import { ToggleControl, Notice, Spinner } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { __ } from '@wordpress/i18n';

/**
 * External dependencies
 */
import { useForm, useWatch, Controller } from 'react-hook-form';

/**
 * Internal dependencies
 */
import SettingsPanel, { AccordionCard, SettingRow } from '../../../components/SettingsPanel';

// ── Constants ─────────────────────────────────────────────────────────────────

const DEFAULT_VALUES = {
	orddd_enable_vendor_settings:            false,
	orddd_vendor_setting_delivery_calendar:  false,
	orddd_vendor_setting_delivery_schedule:  false,
	orddd_vendor_setting_send_reminder:      false,
	orddd_vendor_setting_pickup_location:    false,
	orddd_vendor_setting_block_timeslot:     false,
	orddd_vendor_settings_other_settings:    false,
};

// ── VendorSettings ────────────────────────────────────────────────────────────

const VendorSettings = () => {
	const [ loading, setLoading ] = useState( true );
	const [ saving,  setSaving  ] = useState( false );
	const [ notice,  setNotice  ] = useState( null );

	const { control, handleSubmit, reset } = useForm( { defaultValues: DEFAULT_VALUES } );

	const enableVendorSettings = useWatch( { control, name: 'orddd_enable_vendor_settings' } );

	// ── Fetch ──────────────────────────────────────────────────────────────────

	useEffect( () => {
		apiFetch( { path: '/orddd/v1/settings/integrations/vendor' } )
			.then( ( data ) => {
				const d = data?.data ?? data ?? {};
				reset( {
					orddd_enable_vendor_settings:           d.orddd_enable_vendor_settings           === 'on',
					orddd_vendor_setting_delivery_calendar: d.orddd_vendor_setting_delivery_calendar === 'on',
					orddd_vendor_setting_delivery_schedule: d.orddd_vendor_setting_delivery_schedule === 'on',
					orddd_vendor_setting_send_reminder:     d.orddd_vendor_setting_send_reminder     === 'on',
					orddd_vendor_setting_pickup_location:   d.orddd_vendor_setting_pickup_location   === 'on',
					orddd_vendor_setting_block_timeslot:    d.orddd_vendor_setting_block_timeslot    === 'on',
					orddd_vendor_settings_other_settings:   d.orddd_vendor_settings_other_settings   === 'on',
				} );
			} )
			.catch( () => {} )
			.finally( () => setLoading( false ) );
	}, [] );

	// ── Save ───────────────────────────────────────────────────────────────────

	const onSubmit = async ( values ) => {
		setSaving( true );
		setNotice( null );

		try {
			await apiFetch( {
				path:   '/orddd/v1/settings/integrations/vendor',
				method: 'POST',
				data:   {
					orddd_enable_vendor_settings:           values.orddd_enable_vendor_settings           ? 'on' : '',
					orddd_vendor_setting_delivery_calendar: values.orddd_vendor_setting_delivery_calendar ? 'on' : '',
					orddd_vendor_setting_delivery_schedule: values.orddd_vendor_setting_delivery_schedule ? 'on' : '',
					orddd_vendor_setting_send_reminder:     values.orddd_vendor_setting_send_reminder     ? 'on' : '',
					orddd_vendor_setting_pickup_location:   values.orddd_vendor_setting_pickup_location   ? 'on' : '',
					orddd_vendor_setting_block_timeslot:    values.orddd_vendor_setting_block_timeslot    ? 'on' : '',
					orddd_vendor_settings_other_settings:   values.orddd_vendor_settings_other_settings   ? 'on' : '',
				},
			} );

		} catch ( err ) {
			setNotice( {
				type:    'error',
				message: err?.message ?? __( 'Failed to save settings. Please try again.', 'order-delivery-date' ),
			} );
			throw err;
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
			{ notice && (
				<Notice status={ notice.type } isDismissible onRemove={ () => setNotice( null ) }>
					{ notice.message }
				</Notice>
			) }

			<SettingsPanel
				title={ __( 'Vendor Settings', 'order-delivery-date' ) }
				subtitle={ __( 'Give your vendors an opportunity to take charge of some aspects of your business! You can have them make a number of settings for products that they create on the Vendor Page.', 'order-delivery-date' ) }
				noCard
				onSave={ handleSubmit( onSubmit ) }
				isSaving={ saving }
			>
				<AccordionCard title={ __( 'General Settings', 'order-delivery-date' ) }>
					<SettingRow
						label={ __( 'Enable Vendor Settings for Vendors:', 'order-delivery-date' ) }
						tooltip={ __( 'Enables the Vendor to make Order Delivery Settings on the Vendor Page.', 'order-delivery-date' ) }
					>
						<Controller
							name="orddd_enable_vendor_settings"
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
				</AccordionCard>

				{ enableVendorSettings && (
					<AccordionCard title={ __( 'Vendor Modules', 'order-delivery-date' ) }>
						<SettingRow
							label={ __( 'Order Calendar:', 'order-delivery-date' ) }
							tooltip={ __( 'Allow vendors to sync deliveries with their Google Calendar.', 'order-delivery-date' ) }
						>
							<Controller
								name="orddd_vendor_setting_delivery_calendar"
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
							label={ __( 'Delivery & Pickup Schedule:', 'order-delivery-date' ) }
							tooltip={ __( 'Allow vendors to create custom delivery schedules.', 'order-delivery-date' ) }
						>
							<Controller
								name="orddd_vendor_setting_delivery_schedule"
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
							label={ __( 'Send Reminder:', 'order-delivery-date' ) }
							tooltip={ __( 'Allow vendors to send reminders on placed orders.', 'order-delivery-date' ) }
						>
							<Controller
								name="orddd_vendor_setting_send_reminder"
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
							label={ __( 'Pickup Locations:', 'order-delivery-date' ) }
							tooltip={ __( 'Allow vendors to set their pickup locations.', 'order-delivery-date' ) }
						>
							<Controller
								name="orddd_vendor_setting_pickup_location"
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
							label={ __( 'Block Timeslot:', 'order-delivery-date' ) }
							tooltip={ __( 'Allow vendors to block timeslots.', 'order-delivery-date' ) }
						>
							<Controller
								name="orddd_vendor_setting_block_timeslot"
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
							label={ __( 'Additional Settings:', 'order-delivery-date' ) }
							tooltip={ __( 'Allow vendors to set other additional settings.', 'order-delivery-date' ) }
						>
							<Controller
								name="orddd_vendor_settings_other_settings"
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
					</AccordionCard>
				) }
			</SettingsPanel>
		</div>
	);
};

export default VendorSettings;

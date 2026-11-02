/**
 * WordPress dependencies
 */
import { useState, useEffect } from '@wordpress/element';
import { ToggleControl, Notice, Spinner, Button } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { __ } from '@wordpress/i18n';

/**
 * External dependencies
 */
import { useForm, Controller } from 'react-hook-form';

/**
 * Internal dependencies
 */
import SettingsPanel, { SettingRow } from '../../../components/SettingsPanel';

// ── Constants ────────────────────────────────────────────────────────────────

const LITE_FIELDS = [
	'orddd_lite_show_column_on_orders_page_check',
	'orddd_lite_enable_default_sorting_of_column',
	'orddd_lite_show_filter_on_orders_page_check',
	'orddd_lite_no_fields_for_virtual_product',
	'orddd_lite_no_fields_for_featured_product',
];

const DEFAULT_VALUES = LITE_FIELDS.reduce( ( acc, key ) => ( { ...acc, [ key ]: false } ), {} );

const upgradeUrl =
	window.ordddAdminData?.upgradeUrl ??
	'https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/';

// ── LockedToggle ──────────────────────────────────────────────────────────────

const LockedToggle = () => (
	<div className="orddd-locked-toggle">
		<span className="orddd-locked-toggle__badge">
			{ __( 'Pro', 'order-delivery-date' ) }
		</span>
		<a
			href={ upgradeUrl }
			target="_blank"
			rel="noopener noreferrer"
			className="orddd-locked-toggle__link"
		>
			{ __( 'Upgrade to Pro', 'order-delivery-date' ) }
		</a>
	</div>
);

// ── Component ─────────────────────────────────────────────────────────────────

const AdditionalSettings = () => {
	const [ loading,        setLoading        ] = useState( true );
	const [ saving,         setSaving         ] = useState( false );
	const [ notice,         setNotice         ] = useState( null );
	const [ resetBusy,      setResetBusy      ] = useState( false );
	const [ resetNotice,    setResetNotice    ] = useState( null );

	const { control, handleSubmit, reset } = useForm( { defaultValues: DEFAULT_VALUES } );

	// ── Fetch ──────────────────────────────────────────────────────────────────

	useEffect( () => {
		apiFetch( { path: '/orddd/v1/settings' } )
			.then( ( data ) => {
				const ad = data?.additional ?? {};
				reset(
					LITE_FIELDS.reduce(
						( acc, key ) => ( { ...acc, [ key ]: ad[ key ] === 'on' } ),
						{}
					)
				);
			} )
			.catch( () => {
				setNotice( { type: 'error', message: __( 'Could not load settings. Please refresh and try again.', 'order-delivery-date' ) } );
			} )
			.finally( () => setLoading( false ) );
	}, [] );

	// ── Save ───────────────────────────────────────────────────────────────────

	const onSubmit = async ( values ) => {
		setSaving( true );
		setNotice( null );

		try {
			const payload = LITE_FIELDS.reduce(
				( acc, key ) => ( { ...acc, [ key ]: values[ key ] ? 'on' : '' } ),
				{ key: 'additional' }
			);

			await apiFetch( {
				path:   '/orddd/v1/settings',
				method: 'POST',
				data:   payload,
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

	// ── Loading state ─────────────────────────────────────────────────────────

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
				title={ __( 'Additional Settings', 'order-delivery-date' ) }
				subtitle={ __( 'Enable or disable various display and behavior settings for your store.', 'order-delivery-date' ) }
				onSave={ handleSubmit( onSubmit ) }
				isSaving={ saving }
			>
				<SettingRow
					label={ __( 'Orders Listing Page Display:', 'order-delivery-date' ) }
					tooltip={ __( 'Show delivery/pickup date column on the WooCommerce Orders page.', 'order-delivery-date' ) }
				>
					<Controller
						name="orddd_lite_show_column_on_orders_page_check"
						control={ control }
						render={ ( { field } ) => (
							<ToggleControl
								checked={ field.value }
								onChange={ field.onChange }
								label={ __( 'Displays the Delivery/Pickup Date on the WooCommerce → Orders page.', 'order-delivery-date' ) }
								__nextHasNoMarginBottom
							/>
						) }
					/>
				</SettingRow>

				<SettingRow
					label={ __( 'Enable Default Sorting on Orders Page:', 'order-delivery-date' ) }
					tooltip={ __( 'Orders will be sorted by delivery/pickup date in descending order.', 'order-delivery-date' ) }
				>
					<Controller
						name="orddd_lite_enable_default_sorting_of_column"
						control={ control }
						render={ ( { field } ) => (
							<ToggleControl
								checked={ field.value }
								onChange={ field.onChange }
								label={ __( 'Enable default sorting of orders by Delivery/Pickup Date on WooCommerce → Orders page.', 'order-delivery-date' ) }
								__nextHasNoMarginBottom
							/>
						) }
					/>
				</SettingRow>

				<SettingRow
					label={ __( 'Show Filter on Orders Listing Page:', 'order-delivery-date' ) }
					tooltip={ __( 'Show a date filter on the WooCommerce Orders page.', 'order-delivery-date' ) }
				>
					<Controller
						name="orddd_lite_show_filter_on_orders_page_check"
						control={ control }
						render={ ( { field } ) => (
							<ToggleControl
								checked={ field.value }
								onChange={ field.onChange }
								label={ __( 'Displays a filter on the Orders page to view orders due today, tomorrow, or later.', 'order-delivery-date' ) }
								__nextHasNoMarginBottom
							/>
						) }
					/>
				</SettingRow>

				<SettingRow
					label={ __( 'Disable date & time for Virtual Products:', 'order-delivery-date' ) }
					tooltip={ __( 'Hide delivery date/time fields when the cart contains only virtual products.', 'order-delivery-date' ) }
				>
					<Controller
						name="orddd_lite_no_fields_for_virtual_product"
						control={ control }
						render={ ( { field } ) => (
							<ToggleControl
								checked={ field.value }
								onChange={ field.onChange }
								label={ __( 'Virtual Products', 'order-delivery-date' ) }
								__nextHasNoMarginBottom
							/>
						) }
					/>
				</SettingRow>

				<SettingRow
					label={ __( 'Disable date & time for Featured Products:', 'order-delivery-date' ) }
					tooltip={ __( 'Hide delivery date/time fields when the cart contains only featured products.', 'order-delivery-date' ) }
				>
					<Controller
						name="orddd_lite_no_fields_for_featured_product"
						control={ control }
						render={ ( { field } ) => (
							<ToggleControl
								checked={ field.value }
								onChange={ field.onChange }
								label={ __( 'Featured Products', 'order-delivery-date' ) }
								__nextHasNoMarginBottom
							/>
						) }
					/>
				</SettingRow>

				<SettingRow
					label={ __( 'Enable Tax Calculation for Delivery/Pickup Charges:', 'order-delivery-date' ) }
					tooltip={ __( 'Enable tax calculation for delivery/pickup charges — Pro feature.', 'order-delivery-date' ) }
				>
					<LockedToggle />
				</SettingRow>

				<SettingRow
					label={ __( 'Allow Customers to Reschedule Date & Time:', 'order-delivery-date' ) }
					tooltip={ __( 'Allow customers to reschedule their delivery/pickup date and time — Pro feature.', 'order-delivery-date' ) }
				>
					<LockedToggle />
				</SettingRow>

				<SettingRow
					label={ __( 'Display Delivery Info in WooCommerce Mobile App:', 'order-delivery-date' ) }
					tooltip={ __( 'Display delivery/pickup info in the Order Notes in the WooCommerce mobile app — Pro feature.', 'order-delivery-date' ) }
				>
					<LockedToggle />
				</SettingRow>

				<SettingRow
					label={ __( 'Enable AI Abilities:', 'order-delivery-date' ) }
					tooltip={ __( 'Allow AI assistants to manage deliveries via MCP — Pro feature.', 'order-delivery-date' ) }
				>
					<LockedToggle />
				</SettingRow>

				<SettingRow
					label={ __( 'Reset Usage Tracking:', 'order-delivery-date' ) }
					tooltip={ __( 'Removes the stored tracking consent so the plugin will ask again whether to send usage data.', 'order-delivery-date' ) }
				>
					{ resetNotice && (
						<Notice
							status={ resetNotice.type }
							isDismissible={ true }
							onRemove={ () => setResetNotice( null ) }
						>
							{ resetNotice.message }
						</Notice>
					) }
					<Button
						variant="secondary"
						isBusy={ resetBusy }
						disabled={ resetBusy }
						onClick={ async () => {
							if ( ! window.confirm( __( 'Reset usage tracking? This will remove the tracking consent and schedule a fresh data send.', 'order-delivery-date' ) ) ) {
								return;
							}
							setResetBusy( true );
							setResetNotice( null );
							try {
								const body = new FormData();
								body.append( 'action', 'ts_reset_tracking_setting' );
								body.append( 'plugin_short_name', 'orddd_lite' );
								const response = await fetch( window.ajaxurl, { method: 'POST', body } );
								const data = await response.json();
								if ( 'success' !== data?.message ) {
									throw new Error();
								}
								setResetNotice( { type: 'success', message: __( 'Usage tracking has been reset.', 'order-delivery-date' ) } );
							} catch {
								setResetNotice( { type: 'error', message: __( 'Failed to reset usage tracking. Please try again.', 'order-delivery-date' ) } );
							} finally {
								setResetBusy( false );
							}
						} }
					>
						{ __( 'Reset', 'order-delivery-date' ) }
					</Button>
				</SettingRow>

			</SettingsPanel>
		</div>
	);
};

export default AdditionalSettings;

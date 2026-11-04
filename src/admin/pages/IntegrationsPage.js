/**
 * IntegrationsPage – renders a blurred static replica of the Pro integrations
 * UI (4 tabs) with the upgrade overlay on top.
 */

import { TabPanel, TextControl, TextareaControl, ToggleControl, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import SettingsPanel, { AccordionCard, SettingRow } from '../components/SettingsPanel';

// ── Upgrade overlay data ───────────────────────────────────────────────────────

const pluginUrl    = window.ordddAdminData?.plugin_url   ?? '';
const upgradeUrl   = window.ordddAdminData?.upgradeUrl   ?? 'https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/';
const proInstalled = window.ordddAdminData?.pro_installed ?? false;

const addIcon    = `${ pluginUrl }includes/component/upgrade-to-pro/assets/images/add.png`;
const deleteIcon = `${ pluginUrl }includes/component/upgrade-to-pro/assets/images/delete.png`;
const newTabIcon = `${ pluginUrl }includes/component/upgrade-to-pro/assets/images/icon-new-tab.png`;

const FEATURES = [
	__( 'Access all the premium features (Custom delivery schedules, Same Day & Next Day delivery, Delivery & Local Pickup options and much more)', 'order-delivery-date' ),
	__( 'Early access to new features', 'order-delivery-date' ),
	__( 'Dedicated Customer Support', 'order-delivery-date' ),
	__( 'Priority access to our other offers/services', 'order-delivery-date' ),
];

const OVERLAY = proInstalled
	? {
		featureIcon: deleteIcon,
		heading:     __( 'Your store was thriving with PRO - Why stop now?', 'order-delivery-date' ),
		subHeading:  __( 'Upgrade to Order Delivery Date PRO for WooCommerce', 'order-delivery-date' ),
		desc:        __( "Reclaim the 20+ powerful features that were driving your growth. Upgrade to PRO again and continue unlocking your store's full potential. Without PRO you are missing out on -", 'order-delivery-date' ),
		footerDesc:  <b><i>{ __( "Don't miss out on what you were achieving - take action today! Click the button below to Upgrade to PRO.", 'order-delivery-date' ) }</i></b>,
	}
	: {
		featureIcon: addIcon,
		heading:     __( 'Order Delivery Date PRO for WooCommerce is CHEAPER than you think', 'order-delivery-date' ),
		subHeading:  __( 'Upgrade from Lite to PRO with our Super Saver PRO Access Deal!', 'order-delivery-date' ),
		desc:        __( "Unlock all premium features of Order Delivery Date PRO for WooCommerce without restrictions — all while staying within budget. Your store's success is one click away, so don't miss the Super Saver PRO Access Deal that can skyrocket your sales today!", 'order-delivery-date' ),
		footerDesc:  <>{ __( 'Upgrade from lite to PRO at special pricing. Click the button below to ', 'order-delivery-date' ) }<i>{ __( 'access the latest deals.', 'order-delivery-date' ) }</i></>,
	};

// ── Static tab content ────────────────────────────────────────────────────────

const GoogleSyncTab = () => (
	<div className="orddd-page-wrap">
		<SettingsPanel
				title={ __( 'Google Calendar Sync', 'order-delivery-date' ) }
				subtitle={ __( 'Stay on top of your delivery & pickup schedule by syncing it with Google Calendar. Your customers can also add delivery info to their own calendar.', 'order-delivery-date' ) }
				noCard
			>
				<AccordionCard title={ __( 'General Settings', 'order-delivery-date' ) }>
					<SettingRow label={ __( 'Integration Mode:', 'order-delivery-date' ) }>
						<SelectControl
							value="disabled"
							options={ [
								{ value: 'disabled',        label: __( 'Disabled', 'order-delivery-date' )        },
								{ value: 'oauth_sync',      label: __( 'OAuth Sync', 'order-delivery-date' )      },
								{ value: 'service_account', label: __( 'Service Account', 'order-delivery-date' ) },
							] }
							onChange={ () => {} }
							__nextHasNoMarginBottom
						/>
					</SettingRow>

					<SettingRow label={ __( 'Event Location:', 'order-delivery-date' ) }>
						<TextControl
							value="ADDRESS"
							onChange={ () => {} }
							__nextHasNoMarginBottom
						/>
					</SettingRow>

					<SettingRow label={ __( 'Event summary (name):', 'order-delivery-date' ) }>
						<TextControl
							value="SITE_NAME - ORDER_NUMBER"
							onChange={ () => {} }
							__nextHasNoMarginBottom
						/>
					</SettingRow>

					<SettingRow label={ __( 'Event Description:', 'order-delivery-date' ) }>
						<TextareaControl
							className="fa-textarea"
							value={ 'CLIENT (EMAIL), <br> PRODUCT_WITH_QTY' }
							onChange={ () => {} }
							rows={ 4 }
							__nextHasNoMarginBottom
						/>
					</SettingRow>
				</AccordionCard>

				<AccordionCard title={ __( 'Customer Add to Calendar Button Settings', 'order-delivery-date' ) }>
					<SettingRow label={ __( 'Show Add to Calendar button on Order received page:', 'order-delivery-date' ) }>
						<ToggleControl checked={ true } onChange={ () => {} } __nextHasNoMarginBottom />
					</SettingRow>

					<SettingRow label={ __( 'Show Add to Calendar button in Customer Emails:', 'order-delivery-date' ) }>
						<ToggleControl checked={ false } onChange={ () => {} } __nextHasNoMarginBottom />
					</SettingRow>

					<SettingRow label={ __( 'Show Add to Calendar button in My Account:', 'order-delivery-date' ) }>
						<ToggleControl checked={ false } onChange={ () => {} } __nextHasNoMarginBottom />
					</SettingRow>
				</AccordionCard>

				<AccordionCard title={ __( 'Admin Calendar Settings', 'order-delivery-date' ) }>
					<SettingRow label={ __( 'Auto-add new orders to Admin Delivery Calendar:', 'order-delivery-date' ) }>
						<ToggleControl checked={ false } onChange={ () => {} } __nextHasNoMarginBottom />
					</SettingRow>

					<SettingRow label={ __( 'Send email notification on event creation:', 'order-delivery-date' ) }>
						<ToggleControl checked={ false } onChange={ () => {} } __nextHasNoMarginBottom />
					</SettingRow>

					<SettingRow label={ __( 'Enable real-time import:', 'order-delivery-date' ) }>
						<ToggleControl checked={ false } onChange={ () => {} } __nextHasNoMarginBottom />
					</SettingRow>
				</AccordionCard>
			</SettingsPanel>
	</div>
);

const IntegrationsTab = () => (
	<SettingsPanel
		title={ __( 'Integration Settings', 'order-delivery-date' ) }
		subtitle={ __( 'Enable compatibility with other WooCommerce plugins and addons.', 'order-delivery-date' ) }
		noCard
	>
		<AccordionCard title={ __( 'Plugin Compatibility', 'order-delivery-date' ) }>
			<SettingRow label={ __( 'Show delivery fields in CSV Export:', 'order-delivery-date' ) }>
				<ToggleControl checked={ false } onChange={ () => {} } __nextHasNoMarginBottom />
			</SettingRow>

			<SettingRow label={ __( 'Show delivery fields in PDF Invoices & Packing Slips:', 'order-delivery-date' ) }>
				<ToggleControl checked={ false } onChange={ () => {} } __nextHasNoMarginBottom />
			</SettingRow>

			<SettingRow label={ __( 'Show delivery fields in Cloud Print for Orders:', 'order-delivery-date' ) }>
				<ToggleControl checked={ false } onChange={ () => {} } __nextHasNoMarginBottom />
			</SettingRow>

			<SettingRow label={ __( 'Ship to Multiple Addresses compatibility:', 'order-delivery-date' ) }>
				<ToggleControl checked={ false } onChange={ () => {} } __nextHasNoMarginBottom />
			</SettingRow>

			<SettingRow label={ __( 'Amazon Payments Advanced gateway compatibility:', 'order-delivery-date' ) }>
				<ToggleControl checked={ false } onChange={ () => {} } __nextHasNoMarginBottom />
			</SettingRow>
		</AccordionCard>

		<AccordionCard title={ __( 'Feature Settings', 'order-delivery-date' ) }>
			<SettingRow label={ __( 'Enable Pickup Date:', 'order-delivery-date' ) }>
				<ToggleControl checked={ false } onChange={ () => {} } __nextHasNoMarginBottom />
			</SettingRow>

			<SettingRow label={ __( 'Enable Post-Delivery Email:', 'order-delivery-date' ) }>
				<ToggleControl checked={ false } onChange={ () => {} } __nextHasNoMarginBottom />
			</SettingRow>
		</AccordionCard>
	</SettingsPanel>
);

const VendorsTab = () => (
	<SettingsPanel
		title={ __( 'Vendor Settings', 'order-delivery-date' ) }
		subtitle={ __( 'Control which delivery settings vendors can manage in multi-vendor marketplaces.', 'order-delivery-date' ) }
		noCard
	>
		<AccordionCard title={ __( 'Vendor Permissions', 'order-delivery-date' ) }>
			<SettingRow label={ __( 'Enable vendor settings:', 'order-delivery-date' ) }>
				<ToggleControl checked={ false } onChange={ () => {} } __nextHasNoMarginBottom />
			</SettingRow>

			<SettingRow label={ __( 'Allow vendors to manage Delivery Calendar:', 'order-delivery-date' ) }>
				<ToggleControl checked={ false } onChange={ () => {} } __nextHasNoMarginBottom />
			</SettingRow>

			<SettingRow label={ __( 'Allow vendors to manage Pickup Locations:', 'order-delivery-date' ) }>
				<ToggleControl checked={ false } onChange={ () => {} } __nextHasNoMarginBottom />
			</SettingRow>

			<SettingRow label={ __( 'Allow vendors to manage Delivery Schedules:', 'order-delivery-date' ) }>
				<ToggleControl checked={ false } onChange={ () => {} } __nextHasNoMarginBottom />
			</SettingRow>

			<SettingRow label={ __( 'Allow vendors to send Reminders:', 'order-delivery-date' ) }>
				<ToggleControl checked={ false } onChange={ () => {} } __nextHasNoMarginBottom />
			</SettingRow>

			<SettingRow label={ __( 'Allow vendors to block Time Slots:', 'order-delivery-date' ) }>
				<ToggleControl checked={ false } onChange={ () => {} } __nextHasNoMarginBottom />
			</SettingRow>

			<SettingRow label={ __( 'Allow vendors to access Other Settings:', 'order-delivery-date' ) }>
				<ToggleControl checked={ false } onChange={ () => {} } __nextHasNoMarginBottom />
			</SettingRow>
		</AccordionCard>
	</SettingsPanel>
);

const SAMPLE_AUTOMATIONS = [
	{ title: '2 Days before Delivery',  trigger: 'Before delivery', delay: '2 days',  status: true  },
	{ title: 'Order Delivered',         trigger: 'Order status',    delay: 'On event', status: true  },
	{ title: '1 Hour before Delivery',  trigger: 'Before delivery', delay: '1 hour',  status: false },
];

const OrderStatusTab = () => (
	<div className="orddd-page-wrap">
		<div className="osp-page-header">
			<div className="osp-page-header-text">
				<h1 className="osp-title">{ __( 'Order Status Automation', 'order-delivery-date' ) }</h1>
				<p className="osp-subtitle">{ __( 'Automatically change order statuses based on delivery date and time.', 'order-delivery-date' ) }</p>
			</div>
		</div>

		<div className="orddd-automation-list">
			<table className="orddd-automation-table wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th>{ __( 'Status', 'order-delivery-date' ) }</th>
						<th>{ __( 'Rule Name', 'order-delivery-date' ) }</th>
						<th>{ __( 'Trigger', 'order-delivery-date' ) }</th>
						<th>{ __( 'Delay', 'order-delivery-date' ) }</th>
					</tr>
				</thead>
				<tbody>
					{ SAMPLE_AUTOMATIONS.map( ( row, i ) => (
						<tr key={ i }>
							<td>
								<ToggleControl checked={ row.status } onChange={ () => {} } __nextHasNoMarginBottom />
							</td>
							<td>{ row.title }</td>
							<td>{ row.trigger }</td>
							<td>{ row.delay }</td>
						</tr>
					) ) }
				</tbody>
			</table>
		</div>
	</div>
);

// ── Tabs config ───────────────────────────────────────────────────────────────

const TABS = [
	{ name: 'google-sync',             title: __( 'Google Sync', 'order-delivery-date' ),             Component: GoogleSyncTab    },
	{ name: 'integration-settings',    title: __( 'Integrations', 'order-delivery-date' ),            Component: IntegrationsTab  },
	{ name: 'vendor-settings',         title: __( 'Vendors', 'order-delivery-date' ),                 Component: VendorsTab       },
	{ name: 'order-status-automation', title: __( 'Order Status Automation', 'order-delivery-date' ), Component: OrderStatusTab   },
];

// ── IntegrationsPage ──────────────────────────────────────────────────────────

export default function IntegrationsPage() {
	return (
		<div className="orddd-integrations-page">
			<div className="orddd-integrations-upsell">

				{ /* ── Blurred static settings UI ── */ }
				<div className="orddd-integrations-upsell__content">
					<TabPanel tabs={ TABS }>
						{ ( tab ) => {
							const { Component } = TABS.find( ( t ) => t.name === tab.name );
							return <Component />;
						} }
					</TabPanel>
				</div>

				{ /* ── Upgrade overlay ── */ }
				<div className="ts-upsell-overlay">
					<div className="ts-upsell-top">
						<h2>{ OVERLAY.heading }</h2>
					</div>

					<div className="ts-upsell-content">
						<div className="ts-upsell-content__features">
							<h3>{ OVERLAY.subHeading }</h3>
							<h4>{ OVERLAY.desc }</h4>

							<ul className="ts-columns-2">
								{ FEATURES.map( ( text, i ) => (
									<li key={ i }>
										<img src={ OVERLAY.featureIcon } alt="" width="20" height="20" />
										{ text }
									</li>
								) ) }
							</ul>

							<div className="ts-upsell-content__features-cliff">
								<p>{ __( 'And more!', 'order-delivery-date' ) }</p>
							</div>

							<p>{ OVERLAY.footerDesc }</p>

							<a
								href={ upgradeUrl }
								target="_blank"
								rel="noopener noreferrer"
								className="ts-upgrade-button"
							>
								{ __( 'Upgrade and Unlock', 'order-delivery-date' ) }
								<img src={ newTabIcon } alt="" width="24" height="24" />
							</a>
						</div>
					</div>
				</div>

			</div>
		</div>
	);
}

/**
 * RemindersPage – renders a blurred static replica of the Pro reminders UI
 * (3 tabs) with the upgrade overlay on top.
 */

import { TabPanel, TextControl, TextareaControl, ToggleControl, SelectControl, Button } from '@wordpress/components';
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

const SAMPLE_REMINDERS = [
	{ title: __( 'Send reminder before 24 Hour(s)', 'order-delivery-date' ), sendTime: __( '24 hours before delivery', 'order-delivery-date' ), status: false },
	{ title: __( 'Send reminder after 24 Hour(s)',  'order-delivery-date' ), sendTime: __( '24 hours after delivery',  'order-delivery-date' ), status: false },
];

const CustomerRemindersTab = () => (
	<div className="orddd-page-wrap">
		<div className="osp-page-header">
			<div className="osp-page-header-text">
				<h1 className="osp-title">{ __( 'Customer Reminder Templates', 'order-delivery-date' ) }</h1>
				<p className="osp-subtitle">{ __( 'Create automated email reminders sent to customers before or after their delivery date.', 'order-delivery-date' ) }</p>
			</div>
			<Button variant="primary" onClick={ () => {} }>
				{ __( '+ Add New Reminder', 'order-delivery-date' ) }
			</Button>
		</div>

		<table className="orddd-automation-table wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th>{ __( 'Status', 'order-delivery-date' ) }</th>
					<th>{ __( 'Reminder Title', 'order-delivery-date' ) }</th>
					<th>{ __( 'Send Time', 'order-delivery-date' ) }</th>
					<th>{ __( 'Actions', 'order-delivery-date' ) }</th>
				</tr>
			</thead>
			<tbody>
				{ SAMPLE_REMINDERS.map( ( row, i ) => (
					<tr key={ i }>
						<td>
							<ToggleControl checked={ row.status } onChange={ () => {} } __nextHasNoMarginBottom />
						</td>
						<td>{ row.title }</td>
						<td>{ row.sendTime }</td>
						<td>
							<Button variant="tertiary" onClick={ () => {} }>{ __( 'Edit', 'order-delivery-date' ) }</Button>
							<Button variant="tertiary" isDestructive onClick={ () => {} }>{ __( 'Delete', 'order-delivery-date' ) }</Button>
						</td>
					</tr>
				) ) }
			</tbody>
		</table>
	</div>
);

const AdminReminderTab = () => (
	<div className="orddd-page-wrap">
		<SettingsPanel
			title={ __( 'Admin Reminder', 'order-delivery-date' ) }
			subtitle={ __( "Automatically notify your admin about the next day's scheduled deliveries every evening at 7 PM.", 'order-delivery-date' ) }
			noCard
		>
			<AccordionCard title={ __( 'Admin Reminder Settings', 'order-delivery-date' ) }>
				<SettingRow
					label={ __( "Send reminder to admin for next day's deliveries:", 'order-delivery-date' ) }
					tooltip={ __( 'When enabled, an automatic reminder email is sent to the admin at 7 PM every day listing the next day\'s deliveries.', 'order-delivery-date' ) }
				>
					<ToggleControl checked={ false } onChange={ () => {} } __nextHasNoMarginBottom />
				</SettingRow>

				<SettingRow
					label={ __( 'Include same day deliveries in admin reminder:', 'order-delivery-date' ) }
					tooltip={ __( "Include today's scheduled deliveries in the 7 PM admin reminder email.", 'order-delivery-date' ) }
				>
					<ToggleControl checked={ false } onChange={ () => {} } __nextHasNoMarginBottom />
				</SettingRow>
			</AccordionCard>
		</SettingsPanel>
	</div>
);

const ManualReminderTab = () => (
	<div className="orddd-page-wrap">
		<SettingsPanel
			title={ __( 'Manual Reminder', 'order-delivery-date' ) }
			subtitle={ __( 'Send a one-time reminder email to selected customers immediately.', 'order-delivery-date' ) }
			noCard
		>
			<AccordionCard title={ __( 'Compose Reminder', 'order-delivery-date' ) }>
				<SettingRow
					label={ __( 'Select Orders:', 'order-delivery-date' ) }
					tooltip={ __( 'Choose the orders whose customers should receive this reminder. Only orders with future delivery dates are listed.', 'order-delivery-date' ) }
				>
					<SelectControl
						value=""
						options={ [
							{ value: '',    label: __( '— Select orders —', 'order-delivery-date' ) },
							{ value: '101', label: __( 'Order #101 – John Doe – 2 Nov 2026', 'order-delivery-date' ) },
							{ value: '102', label: __( 'Order #102 – Jane Smith – 3 Nov 2026', 'order-delivery-date' ) },
						] }
						onChange={ () => {} }
						__nextHasNoMarginBottom
					/>
				</SettingRow>

				<SettingRow
					label={ __( 'Subject:', 'order-delivery-date' ) }
					tooltip={ __( 'The email subject line. Supports dynamic tags like {order_number}, {blogname}, {delivery_date}.', 'order-delivery-date' ) }
				>
					<TextControl
						value={ __( 'Delivery Reminder', 'order-delivery-date' ) }
						onChange={ () => {} }
						__nextHasNoMarginBottom
					/>
				</SettingRow>

				<SettingRow
					label={ __( 'Message:', 'order-delivery-date' ) }
					tooltip={ __( 'The email body. Supports dynamic tags: {customer_name}, {delivery_date}, {delivery_time}, {order_details}, and more.', 'order-delivery-date' ) }
				>
					<TextareaControl
						className="fa-textarea"
						value={ __( 'Dear {customer_name},\n\nThis is a friendly reminder that your order from {blogname} is scheduled for delivery on {delivery_date} at {delivery_time}.\n\n{order_details}\n\nThank you for choosing {blogname}!', 'order-delivery-date' ) }
						onChange={ () => {} }
						rows={ 8 }
						__nextHasNoMarginBottom
					/>
				</SettingRow>
			</AccordionCard>

			<div className="orddd-reminder-actions">
				<Button variant="secondary" onClick={ () => {} }>
					{ __( 'Save Draft', 'order-delivery-date' ) }
				</Button>
				<Button variant="primary" onClick={ () => {} }>
					{ __( 'Send Reminder', 'order-delivery-date' ) }
				</Button>
			</div>
		</SettingsPanel>
	</div>
);

// ── Tabs config ───────────────────────────────────────────────────────────────

const TABS = [
	{ name: 'customer-reminders', title: __( 'Customer Reminder Templates', 'order-delivery-date' ), Component: CustomerRemindersTab },
	{ name: 'admin-reminder',     title: __( 'Admin Reminder',              'order-delivery-date' ), Component: AdminReminderTab     },
	{ name: 'manual-reminder',    title: __( 'Manual Reminder',             'order-delivery-date' ), Component: ManualReminderTab    },
];

// ── RemindersPage ─────────────────────────────────────────────────────────────

export default function RemindersPage() {
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

/**
 * WordPress dependencies
 */
import { TabPanel, Icon } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { lock } from '@wordpress/icons';

/**
 * External dependencies
 */
import { useParams, useNavigate } from 'react-router-dom';

/**
 * Internal dependencies
 */
import BlockTimeSlot      from './tabs/BlockTimeSlot';
import AdditionalSettings from './tabs/AdditionalSettings';
import BusinessDays       from './tabs/BusinessDays';
import CommonSettings     from './tabs/CommonSettings';
import OrderType          from './tabs/OrderType';
import PickupLocation     from './tabs/PickupLocation';
import DisableShipping    from './tabs/DisableShipping';

const lockedTitle = ( label ) => (
	<>
		<Icon icon={ lock } size={ 14 } aria-hidden />
		{ label }
	</>
);

const tabs = [
	{ name: 'business-days',       title: lockedTitle( __( 'Business Days', 'order-delivery-date' ) ),       className: 'orddd-tab--locked' },
	{ name: 'block-time-slot',     title: __( 'Block Time Slot', 'order-delivery-date' )                                                     },
	{ name: 'common-settings',     title: lockedTitle( __( 'Common Settings', 'order-delivery-date' ) ),     className: 'orddd-tab--locked' },
	{ name: 'additional-settings', title: __( 'Additional Settings', 'order-delivery-date' )                                                  },
	{ name: 'order-type',          title: lockedTitle( __( 'Order Type', 'order-delivery-date' ) ),          className: 'orddd-tab--locked' },
	{ name: 'pickup-location',     title: lockedTitle( __( 'Pickup Location', 'order-delivery-date' ) ),     className: 'orddd-tab--locked' },
	{ name: 'disable-shipping',    title: lockedTitle( __( 'Disable Shipping', 'order-delivery-date' ) ),    className: 'orddd-tab--locked' },
];

const tabComponents = {
	'business-days':       () => <BusinessDays locked />,
	'block-time-slot':     BlockTimeSlot,
	'common-settings':     () => <CommonSettings locked />,
	'additional-settings': AdditionalSettings,
	'order-type':          () => <OrderType locked />,
	'pickup-location':     () => <PickupLocation locked />,
	'disable-shipping':    () => <DisableShipping locked />,
};

const validTabNames = tabs.map( ( t ) => t.name );

const Settings = () => {
	const { tab }  = useParams();
	const navigate = useNavigate();

	const initialTab = validTabNames.includes( tab ) ? tab : 'block-time-slot';

	return (
		<div className="ordd-settings-page">
			<TabPanel
				tabs={ tabs }
				initialTabName={ initialTab }
				onSelect={ ( selected ) => navigate( `/settings/${ selected }`, { replace: true } ) }
			>
				{ ( t ) => {
					const Component = tabComponents[ t.name ];
					return <Component />;
				} }
			</TabPanel>
		</div>
	);
};

export default Settings;

/**
 * WordPress dependencies
 */
import { TabPanel } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * External dependencies
 */
import { useParams, useNavigate } from 'react-router-dom';

/**
 * Internal dependencies
 */
import FieldAppearance    from './tabs/FieldAppearance';
import CalendarAppearance from './tabs/CalendarAppearance';

const tabs = [
	{ name: 'field-appearance',    title: __( 'Field Appearance', 'order-delivery-date' )    },
	{ name: 'calendar-appearance', title: __( 'Calendar Appearance', 'order-delivery-date' ) },
];

const tabComponents = {
	'field-appearance':    FieldAppearance,
	'calendar-appearance': CalendarAppearance,
};

const validTabNames = tabs.map( ( t ) => t.name );

const Appearance = () => {
	const { tab }  = useParams();
	const navigate = useNavigate();

	const initialTab = validTabNames.includes( tab ) ? tab : tabs[ 0 ].name;

	return (
		<div className="ordd-appearance-page">
			<TabPanel
				tabs={ tabs }
				initialTabName={ initialTab }
				onSelect={ ( selected ) => navigate( `/appearance/${ selected }`, { replace: true } ) }
			>
				{ ( t ) => {
					const Component = tabComponents[ t.name ];
					return <Component />;
				} }
			</TabPanel>
		</div>
	);
};

export default Appearance;

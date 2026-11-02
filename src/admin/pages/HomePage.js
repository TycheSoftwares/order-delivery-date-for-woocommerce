/**
 * HomePage – FAQ tab.
 */

import { useState } from '@wordpress/element';
import { TabPanel, Icon } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { chevronDown, chevronUp } from '@wordpress/icons';

const FAQS = Object.values( window.ordddAdminData?.faqs ?? {} );

const FaqItem = ( { question, answer } ) => {
	const [ open, setOpen ] = useState( false );

	return (
		<div className="orddd-faq-item" onClick={ () => setOpen( ( v ) => ! v ) }>
			<div className="orddd-faq-item__question">
				<span>{ question }</span>
				<Icon icon={ open ? chevronUp : chevronDown } size={ 18 } />
			</div>
			{ open && (
				<p
					className="orddd-faq-item__answer"
					dangerouslySetInnerHTML={ { __html: answer } }
				/>
			) }
		</div>
	);
};

const Faqs = () => (
	<div className="orddd-page-wrap">
		<div className="osp-page-header">
			<div className="osp-page-header-text">
				<h2 className="osp-title">{ __( 'Frequently Asked Questions', 'order-delivery-date' ) }</h2>
				<p className="osp-subtitle">{ __( 'Common questions about Order Delivery Date.', 'order-delivery-date' ) }</p>
			</div>
		</div>
		<div className="orddd-faq-list">
			{ FAQS.map( ( item, i ) => (
				<FaqItem key={ i } question={ item.question } answer={ item.answer } />
			) ) }
		</div>
	</div>
);

const tabs = [
	{ name: 'faqs', title: __( "FAQ's", 'order-delivery-date' ) },
];

const tabComponents = {
	faqs: Faqs,
};

export default function HomePage() {
	return (
		<div className="ordd-settings-page">
			<TabPanel
				tabs={ tabs }
				initialTabName="faqs"
			>
				{ ( t ) => {
					const Component = tabComponents[ t.name ];
					return <Component />;
				} }
			</TabPanel>
		</div>
	);
}

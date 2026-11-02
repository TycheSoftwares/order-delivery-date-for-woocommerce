/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Notice } from '@wordpress/components';
import { Icon, info } from '@wordpress/icons';

const SUPPORT_URL      = 'https://support.tychesoftwares.com/help/2285384554/?utm_source=ordddprofooter&utm_medium=link&utm_campaign=OrderDeliveryDatePRoPlugin';
const RATE_URL         = 'https://www.tychesoftwares.com/submit-review/?utm_source=ordddprofooter&utm_medium=link&utm_campaign=OrderDeliveryDatePRoPlugin';
const SUBSCRIPTIONS_URL = 'https://www.tychesoftwares.com/products/order-delivery-date-woocommerce-plugin/subscriptions-compatibility-addon/?utm_source=ordddprofooter&utm_medium=link&utm_campaign=OrderDeliveryDatePRoPlugin';

const Footer = () => (
	<div className="orddd-footer">
		<Notice status="info">
			<Icon icon={ info } size={ 18 } className="orddd-footer__notice-icon" />
			<a href={ SUBSCRIPTIONS_URL } target="_blank" rel="noreferrer">
				{ __( 'Get our Subscriptions Addon', 'order-delivery-date' ) }
			</a>
			{ __( ' & setup recurring deliveries with WooCommerce Subscriptions plugin & Order Delivery Date plugin.', 'order-delivery-date' ) }
		</Notice>
		<p className="orddd-footer__support">
			<a href={ SUPPORT_URL } target="_blank" rel="noreferrer">
				{ __( 'Need Support?', 'order-delivery-date' ) }
			</a>
			{ ' ' }
			<strong>{ __( "We're always happy to help you.", 'order-delivery-date' ) }</strong>
		</p>
		<p className="orddd-footer__rate">
			{ __( 'If this plugin helped you,', 'order-delivery-date' ) }
			{ ' ' }
			<a href={ RATE_URL } target="_blank" rel="noreferrer">
				{ __( 'please rate it', 'order-delivery-date' ) }
			</a>
			{ ' ' }
			<span className="orddd-footer__stars" aria-hidden="true">★★★★★</span>
		</p>
	</div>
);

export default Footer;

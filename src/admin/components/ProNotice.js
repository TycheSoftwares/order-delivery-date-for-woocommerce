import { __ } from '@wordpress/i18n';
import { CardBody, Icon } from '@wordpress/components';
import { lock } from '@wordpress/icons';

const trialUrl = 'https://www.tychesoftwares.com/products/woocommerce-order-delivery-date-pro-plugin-trial/?utm_source=wprepo&utm_medium=litesettings&utm_campaign=OrderDeliveryDateLite';

/**
 * Inline Pro notice — replaces a single field control.
 */
export const ProFieldNotice = () => (
	<div className="orddd-pro-field">
		<span className="orddd-pro-field__badge">{ __( 'Pro', 'order-delivery-date' ) }</span>
		<a
			href={ trialUrl }
			target="_blank"
			rel="noopener noreferrer"
			className="orddd-pro-field__link"
		>
			{ __( 'Upgrade to unlock', 'order-delivery-date' ) }
		</a>
	</div>
);

/**
 * Full-page upgrade prompt — shown in place of a Pro-only tab or page.
 *
 * @param {string} [message]  Override the default message.
 */
export const UpgradePrompt = ( { message } ) => (
	<div className="orddd-upgrade-prompt">
		<div className="orddd-upgrade-prompt__badge">
			{ __( 'Pro Feature', 'order-delivery-date' ) }
		</div>
		<Icon icon={ lock } className="orddd-upgrade-prompt__icon" size={ 18 } />
		<p className="orddd-upgrade-prompt__message">
			{ message ?? __( 'This feature is available in the Pro version.', 'order-delivery-date' ) }
		</p>
		<a
			href={ trialUrl }
			target="_blank"
			rel="noopener noreferrer"
			className="orddd-upgrade-prompt__btn"
		>
			{ __( 'Upgrade to Pro', 'order-delivery-date' ) }
		</a>
	</div>
);

/**
 * Section-level Pro notice — replaces an entire section's content.
 *
 * @param {string} feature  Short description of what the section offers.
 */
export const ProSectionNotice = ( { feature } ) => (
	<CardBody>
		<div className="orddd-pro-section-notice">
			<span className="orddd-pro-section-notice__icon" aria-hidden="true">🔒</span>
			<div className="orddd-pro-section-notice__body">
				<p className="orddd-pro-section-notice__feature">{ feature }</p>
				<a
					href={ trialUrl }
					target="_blank"
					rel="noopener noreferrer"
					className="orddd-pro-section-notice__link"
				>
					{ __( 'Upgrade to Order Delivery Date Pro →', 'order-delivery-date' ) }
				</a>
			</div>
		</div>
	</CardBody>
);

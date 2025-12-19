import metadata from '../../block.json';
import { ValidatedTextInput } from '@woocommerce/blocks-checkout';
import { __ } from '@wordpress/i18n';
import Block from './block';

// Global import
const { registerCheckoutBlock } = wc.blocksCheckout;

if ( wcSettings.shippingMethodsExist && !wcSettings.localPickupEnabled && true !== wcSettings.forcedBillingAddress ) {
	metadata.parent = [ "woocommerce/checkout-shipping-address-block" ]
} else {
	metadata.parent = [ "woocommerce/checkout-billing-address-block" ]
}

if ( 'yes' === orddd_lite_params.orddd_lite_has_virtual_products ) {
	metadata.parent = [ "woocommerce/checkout-billing-address-block" ]
}

const options = {
	metadata,
	component: Block
};

registerCheckoutBlock( options );
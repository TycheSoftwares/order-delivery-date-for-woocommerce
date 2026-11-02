import metadata from '../../block.json';
import { __ } from '@wordpress/i18n';
import Block from './block';

// Global import
const { registerCheckoutBlock } = wc.blocksCheckout;

if ( ! orddd_block_params.is_orddd_block_present ) {
	if ( wcSettings.shippingEnabled && ! wcSettings.forcedBillingAddress ) {
		metadata.parent = [ "woocommerce/checkout-shipping-address-block" ]
	} else {
		metadata.parent = [ "woocommerce/checkout-billing-address-block" ]
	}

	if ( wcSettings.localPickupEnabled && wcSettings.delivery_date_data.wc_pickup_locations ) {
		metadata.parent = [
			"woocommerce/checkout-pickup-options-block",
			"woocommerce/checkout-shipping-address-block"
		]
	}
	metadata.attributes = {
		"lock": {
			"type": "object",
			"default": {
				"remove": true,
				"move": true
			}
		}
	}
}


const options = {
	metadata,
	component: Block
};

registerCheckoutBlock( options );
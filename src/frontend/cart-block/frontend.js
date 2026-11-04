import metadata from '../../../blocks/cart/block.json';
import { ValidatedTextInput } from '@woocommerce/blocks-checkout';
import { __ } from '@wordpress/i18n';
import Block from './block';

// Global import
const { registerCheckoutBlock } = wc.blocksCheckout;

const options = {
	metadata,
	component: Block
};

registerCheckoutBlock( options );
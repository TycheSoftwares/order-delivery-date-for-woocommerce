/**
 * Order Delivery Date Lite – React Admin SPA entry point.
 *
 * Configures the apiFetch nonce middleware (using the same ordddAdminData
 * variable as the Pro plugin) and mounts the App into the DOM.
 */

import { render } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import App from './App';
import './admin.scss';

// Configure global nonce middleware once. ordddAdminData is injected by
// ORDDD_Lite_Admin_Scripts::enqueue() via wp_localize_script.
if ( window.ordddAdminData && window.ordddAdminData.nonce ) {
	apiFetch.use( apiFetch.createNonceMiddleware( window.ordddAdminData.nonce ) );
}

const root = document.getElementById( 'order-delivery-date-for-woocommerce' );
if ( root ) {
	render( <App />, root );
}

// File: your-bridge-plugin/assets/express-checkout-delivery-date.js

( function () {
    // Wait until wp.hooks is available (loaded by WooPayments).
    function init() {
        if ( ! window.wp || ! window.wp.hooks ) {
            return;
        }

        wp.hooks.addFilter(
            'wcpay.express-checkout.cart-place-order-extension-data',
            'orddd/express-checkout-delivery-date',
            function ( extensionData ) {
                var eDate    = document.getElementById( 'e_deliverydate' );
                var hDate    = document.getElementById( 'h_deliverydate' );
                var timeSlot = localStorage.getItem( 'orddd_lite_time_slot' );

                if ( ! eDate || ! eDate.value ) {
                    return extensionData;
                }

                var deliveryData = {
                    e_deliverydate: eDate.value,
                    h_deliverydate: hDate ? hDate.value : eDate.value,
                };

                if ( timeSlot && timeSlot !== '' && timeSlot !== 'select' && timeSlot !== 'choose' && timeSlot !== 'NA' ) {
                    deliveryData.orddd_lite_time_slot = timeSlot;
                }

                return Object.assign( {}, extensionData, {
                    'order-delivery-date': deliveryData
                } );
            }
        );
    }

    // The filter must be registered before the ECE JS fires.
    // DOMContentLoaded is early enough.
    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', init );
    } else {
        init();
    }
} )();
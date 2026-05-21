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
                // Read the delivery date the shopper selected in the datepicker.
                // e_deliverydate = the formatted display value (e.g. "23/05/2026")
                // h_deliverydate = the machine-readable value used for timestamps
                var eDate = document.getElementById( 'e_deliverydate' );
                var hDate = document.getElementById( 'h_deliverydate' );
                var timeSlot = localStorage.getItem( 'orddd_lite_time_slot' );

                //console.log(eDate);

                if ( ! eDate || ! eDate.value ) {
                    // No date selected — return unchanged.
                    return extensionData;
                }

                return Object.assign( {}, extensionData, {
                    'order-delivery-date': {
                        e_deliverydate:       eDate.value,
                        h_deliverydate:       hDate ? hDate.value : eDate.value,
                        orddd_lite_time_slot: timeSlot,
                    }
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
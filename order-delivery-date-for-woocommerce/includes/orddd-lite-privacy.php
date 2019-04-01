<?php

/**
 * Order Delivery Date for WooCommerce Lite
 *
 * GDPR related fixes. 
 *
 * @author      Tyche Softwares
 * @package     Order-Delivery-Date-Lite-for-WooCommerce/Privacy
 * @since       1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

include_once( dirname( __FILE__ ) . '/orddd-lite-common.php' );

/**
 * GDPR related fixes. 
 *
 * @class orddd_lite_privacy
 */
class orddd_lite_privacy {
	/**
	 * Default Constructor
	 *
	 * @since 3.5
	 */

	public function __construct() {
		add_filter( "woocommerce_privacy_export_order_personal_data_props", array( &$this, "orddd_lite_privacy_export_order_personal_data_props" ), 10, 2 );
        add_filter( "woocommerce_privacy_export_order_personal_data_prop", array( &$this, "orddd_lite_privacy_export_order_personal_data_prop_callback" ), 10, 3 );
	}

	function orddd_lite_privacy_export_order_personal_data_props( $props_to_export, $order ) {
        $my_key_value   = array( 'delivery_details' => __( 'Delivery Date', 'order-delivery-date' ) );
        $key_pos        = array_search( 'items', array_keys( $props_to_export ) );
        
        if ( $key_pos !== false ) {
            $key_pos++;
            
            $second_array       = array_splice( $props_to_export, $key_pos );        
            $props_to_export    = array_merge( $props_to_export, $my_key_value, $second_array );
        }

        return $props_to_export;
    }  

    function orddd_lite_privacy_export_order_personal_data_prop_callback( $value, $prop, $order ) {
        if ( $prop == "delivery_details" ) {
            $delivery_date = orddd_lite_common::orddd_lite_get_order_delivery_date( $order->get_id() );
            $value = $delivery_date;           
        }
        return $value;
    }
}

$orddd_lite_privacy = new orddd_lite_privacy();
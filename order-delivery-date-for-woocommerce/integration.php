<?php 

include_once( dirname( __FILE__ ) . '/orddd-lite-common.php' );
// Code to integrate the WooCommerce Order Delivery Date plugin with various 3rd party plugins
class orddd_lite_integration {

	public function __construct() {		
	    // Zapier integration
	    add_action( 'plugins_loaded', array( &$this, 'orddd_lite_plugins_loaded' ) );
	    
		// WooCommerce PDF Invoices & Packing Slips
		add_action( 'wpo_wcpdf_after_order_details', array( &$this, 'orddd_lite_plugins_packing_slip' ) );
		
		// add custom columns headers to csv when Order/Customer CSV Export Plugin is activate
		add_filter( 'wc_customer_order_csv_export_order_headers', array( &$this, 'orddd_lite_csv_export_modify_column_headers' ) );
		add_filter( 'wc_customer_order_csv_export_order_row', array( &$this, 'orddd_lite_csv_export_modify_row_data' ), 10, 3 );
		
		//WooCommerce Print Invoice & Delivery Note		
		add_filter( 'wcdn_order_info_fields', array( &$this, 'orddd_lite_print_invoice_delivery_note' ), 10, 2 );
		
		add_action( 'woocommerce_cloudprint_internaloutput_footer', array( &$this, 'orddd_lite_cloud_print_fields' ) );
		
		//WooCommerce Subscriptions
		add_filter( 'woocommerce_create_order', array( &$this, 'orddd_lite_filter_woocommerce_create_order' ), 1, 2 );
		
		//WooCommerce Print Invoice/Packing list plugin
		add_action( 'wc_print_invoice_packing_template_body_after_billing_address', array( &$this, 'orddd_lite_woocommerce_pip' ) );
	}
    
	/**
	 * Executed during the 'plugins_loaded' WordPress hook.
	 *
	 * - Load Supported Zapier Triggers
	 */
	
	function orddd_lite_plugins_loaded() {
	    if ( class_exists( 'WC_Zapier' ) ) {
	        $trigger_keys = array(
	            'wc.new_order', // New Order
	            'wc.order_status_change' // New Order Status Change
	        );
	        foreach ( $trigger_keys as $trigger_key ) {
	            add_filter( "wc_zapier_data_{$trigger_key}", array( &$this, 'orddd_lite_order_data_override' ), 10, 4 );
	        }
	    }
	}
	
	/**
	 * When sending WooCommerce Order data to Zapier, also send order delivery date field
	 * that have been created by the Order Delivery Date plugin.
	 *
	 * @param             array  $order_data Order data that will be overridden.
	 * @param WC_Zapier_Trigger  $trigger Trigger that initiated the data send.
	 *
	 * @return mixed
	 */
	
	function orddd_lite_order_data_override( $order_data, WC_Zapier_Trigger $trigger ) {
	    if ( $trigger->is_sample() ) {
	        // We're sending sample data.
	        // Send the label of the custom checkout field as the field's value.
	        $field_name = get_option( 'orddd_lite_delivery_date_field_label' );
	        $order_data[ $field_name ] = $field_name;
	    } else {
	        // We're sending real data.
	        // Send the saved value of this checkout field.
	        // If the order doesn't contain this custom field, an empty string will be used as the value.
	        $order_data[ $field_name ] = get_post_meta( $order_data[ 'id' ], $field_name, true );
	    }
	    return $order_data;
	}
	
	function orddd_lite_plugins_packing_slip() {
		global $wpo_wcpdf, $orddd_lite_date_formats;
		$order_export = $wpo_wcpdf->export;
		$order_obj = $order_export->order;
		$order_id = $order_obj->id;
		$delivery_date_formatted = orddd_lite_common::orddd_lite_get_order_delivery_date( $order_id );
		if( $delivery_date_formatted != '' ) {
		    echo '<p><strong>' . __( get_option( 'orddd_lite_delivery_date_field_label' ), 'order-delivery-date' ) . ': </strong>' . $delivery_date_formatted;
		}
	}

	function orddd_lite_csv_export_modify_column_headers( $column_headers ) { 
		$new_headers = array(
			'column_1' => __( get_option( 'orddd_lite_delivery_date_field_label' ), 'order-delivery-date' )
		);
		return array_merge( $column_headers, $new_headers );
	}

	public static function orddd_lite_csv_export_modify_row_data( $order_data, $order, $csv_generator ) {
	    $new_order_data = $custom_data = array();
		$order_id = $order->id;
		$delivery_date_formatted = orddd_lite_common::orddd_lite_get_order_delivery_date( $order_id );
		
		$custom_data = array(
		    'column_1' => $delivery_date_formatted,
		);
			
		if ( isset( $csv_generator->order_format ) && ( 'default_one_row_per_item' == $csv_generator->order_format || 'legacy_one_row_per_item' == $csv_generator->order_format ) ) {
			foreach ( $order_data as $data ) {
				$new_order_data[] = array_merge( (array) $data, $custom_data );
			}
		} else {
			$new_order_data = array_merge( $order_data, $custom_data );
		}
		
		return $new_order_data;
	}

	function orddd_lite_print_invoice_delivery_note( $fields, $order ) {
		$new_fields = array();
        $order_id = $order->id;
        $delivery_date_formatted = orddd_lite_common::orddd_lite_get_order_delivery_date( $order_id );
        if( $delivery_date_formatted != '' ) {
            $new_fields[ get_option( 'orddd_lite_delivery_date_field_label' ) ] = array(
                'label' => __( get_option( 'orddd_lite_delivery_date_field_label' ), 'order-delivery-date' ),
                'value' => $delivery_date_formatted
            );
        }
        return array_merge( $fields, $new_fields );
	}
	
	function orddd_lite_cloud_print_fields( $order ) { 
	    $field_date_label = get_option( 'orddd_lite_delivery_date_field_label' );
	    $order_id = $order->id;
	    
	    $delivery_date_formatted = orddd_lite_common::orddd_lite_get_order_delivery_date( $order_id );
	    echo '<p><strong>'.__( $field_date_label, 'order-delivery-date' ) . ': </strong>' . $delivery_date_formatted;
	}
	
	function orddd_lite_filter_woocommerce_create_order ( $order_id, $checkout_object ) {
	    if ( class_exists( 'WC_Subscriptions_Cart' ) ) {
	        $cart_item = WC_Subscriptions_Cart::cart_contains_subscription_renewal();
	        if ( $cart_item && 'child' == $cart_item[ 'subscription_renewal' ][ 'role' ] ) {
	            $product_id        = $cart_item[ 'product_id' ];
	            $failed_order_id   = $cart_item[ 'subscription_renewal' ][ 'failed_order' ];
	            $original_order_id = $cart_item[ 'subscription_renewal' ][ 'original_order' ];
	            $role              = $cart_item[ 'subscription_renewal' ][ 'role' ];
	
	            $renewal_order_args = array(
	                'new_order_role'   => $role,
	                'checkout_renewal' => true,
	                'failed_order_id'  => $failed_order_id
	            );
	            $renewal_order_id = WC_Subscriptions_Renewal_Order::generate_renewal_order( $original_order_id, $product_id, $renewal_order_args );
	            if ( isset( $_POST[ 'e_deliverydate' ] ) && $_POST[ 'e_deliverydate' ] != '' ) {
	                update_post_meta( $renewal_order_id, get_option( 'orddd_lite_delivery_date_field_label' ), esc_attr( $_POST[ 'e_deliverydate' ] ) );
	
	                $date_format = 'dd-mm-y';
	                if( isset( $_POST[ 'h_deliverydate' ] ) && $_POST[ 'h_deliverydate' ] != '' ) {
	                    $delivery_date = $_POST[ 'h_deliverydate' ];
	                } else {
	                    $delivery_date = '';
	                }
	                
	                $timestamp = orddd_lite_common::orddd_lite_get_timestamp( $delivery_date, $date_format );
	                update_post_meta( $renewal_order_id, '_orddd_lite_timestamp', $timestamp );
	                if ( get_option( 'orddd_lite_lockout_date_after_orders' ) > 0 ) {
	                    order_delivery_date_lite::orddd_lite_update_lockout_days( $delivery_date );
	                }
	            } else {
	                update_post_meta( $renewal_order_id, get_option( 'orddd_lite_delivery_date_field_label' ), '' );
	            }
	        }
	    }
	}
	
	function orddd_lite_woocommerce_pip( $order ) {
	    global $orddd_date_formats;
	    $delivery_date = get_option( 'orddd_lite_delivery_date_field_label' );
	    
        $order_id = $order->id;
        $delivery_date_formatted = orddd_lite_common::orddd_lite_get_order_delivery_date( $order_id );
        if( $delivery_date_formatted != '' ) {
            echo '<p><strong>' . __( $delivery_date, 'order-delivery-date' ) . ': </strong>' . $delivery_date_formatted;
        }
	}
}
$orddd_lite_integration = new orddd_lite_integration();
?>
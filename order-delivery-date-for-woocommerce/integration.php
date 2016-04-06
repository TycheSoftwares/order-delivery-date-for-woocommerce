<?php 

include_once( dirname( __FILE__ ) . '/orddd-lite-common.php' );
// Code to integrate the WooCommerce Order Delivery Date plugin with various 3rd party plugins
class orddd_lite_integration {

	public function __construct() {		
	    // WooCommerce PDF Invoices & Packing Slips
		add_action( 'wpo_wcpdf_after_order_details', array( &$this, 'orddd_lite_plugins_packing_slip' ) );
		
		// add custom columns headers to csv when Order/Customer CSV Export Plugin is activate
		add_filter( 'wc_customer_order_csv_export_order_headers', array( &$this, 'orddd_lite_csv_export_modify_column_headers' ) );
		add_filter( 'wc_customer_order_csv_export_order_row', array( &$this, 'orddd_lite_csv_export_modify_row_data' ), 10, 3 );
		
		//WooCommerce Print Invoice & Delivery Note		
		add_filter( 'wcdn_order_info_fields', array( &$this, 'orddd_lite_print_invoice_delivery_note' ), 10, 2 );
		
		add_action( 'woocommerce_cloudprint_internaloutput_footer', array( &$this, 'orddd_lite_cloud_print_fields' ) );
		
		//WooCommerce Print Invoice/Packing list plugin
		add_action( 'wc_pip_after_body', array( &$this, 'orddd_lite_woocommerce_pip' ), 10, 4 );
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
	
	function orddd_lite_woocommerce_pip( $type, $action, $document, $order ) {
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
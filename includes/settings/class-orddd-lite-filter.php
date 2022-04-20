<?php
/**
 * Order Delivery Date for WooCommerce Lite
 *
 * Functions to add columns on WooCommerced->Orders page.
 *
 * @author      Tyche Softwares
 * @package     Order-Delivery-Date-Lite-for-WooCommerce/Admin/Delivery-Date-on-Edit-Order-Page
 * @since       1.9
 */

// Include Common file.
require_once WP_PLUGIN_DIR . '/order-delivery-date-for-woocommerce/includes/class-orddd-lite-common.php';

/**
 * Order Delivery Date Filter Class
 *
 * @class Orddd_Lite_Filter
 */
class Orddd_Lite_Filter {

	/**
	 * Default Constructor
	 *
	 * @since 3.12.0
	 */
	public function __construct() {
		//Delivery Date & Time on WooCommerce Edit Order page in Admin
		if ( get_option( 'orddd_lite_delivery_date_fields_on_checkout_page' ) == 'billing_section' || get_option( 'orddd_lite_delivery_date_fields_on_checkout_page' ) == 'after_your_order_table' || get_option( 'orddd_lite_delivery_date_fields_on_checkout_page' ) == 'custom' ) {
		    add_action( 'woocommerce_admin_order_data_after_billing_address',  array( &$this, 'orddd_lite_display_delivery_info_admin_order_meta') , 10, 1 );
		} else if ( get_option( 'orddd_lite_delivery_date_fields_on_checkout_page' ) == 'shipping_section'|| get_option( 'orddd_lite_delivery_date_fields_on_checkout_page' ) == 'before_order_notes' || get_option( 'orddd_lite_delivery_date_fields_on_checkout_page' ) == 'after_order_notes' ) {
		    add_action( 'woocommerce_admin_order_data_after_shipping_address', array( &$this, 'orddd_lite_display_delivery_info_admin_order_meta') , 10, 1 );
		}
		// Delivery date & Delivery Time in Order Preview in Admin
		add_filter( 'woocommerce_admin_order_preview_get_order_details', array( &$this, 'orddd_lite_admin_order_preview_add_delivery_date' ), 20, 2 );
	}

	/**
	 * This function adds the Delivery Date column to WooCommerce->Orders page
	 *
	 * @param array $columns - List of columns already present.
	 * @return array $new_columns - List of columns with the new ones added
	 * @since 1.9
	 */
	public static function orddd_lite_woocommerce_order_delivery_date_column( $columns ) {
		$new_columns = ( is_array( $columns ) ) ? $columns : array();
		unset( $new_columns['wc_actions'] );
		// edit this for you column(s).
		// all of your columns will be added before the actions column.
		$date_field_label = get_option( 'orddd_lite_delivery_date_field_label' );
		// phpcs:ignore
		$new_columns['order_delivery_date'] = __( $date_field_label, 'order-delivery-date' ); // Title for column heading.
		$new_columns['wc_actions']          = $columns['wc_actions'];
		return $new_columns;
	}

	/**
	 * This function adds the Delivery Date for each order on WooCommerce->Orders page
	 *
	 * @param str $column - Name of the column.
	 * @since 1.9
	 */
	public static function orddd_lite_woocommerce_custom_column_value( $column, $post_id ) {
		global $post, $the_order, $orddd_lite_date_formats;
		if ( 'order_delivery_date' === $column ) {
			$delivery_date_formatted = Orddd_Lite_Common::orddd_lite_get_order_delivery_date(  $post_id );
			echo esc_attr( $delivery_date_formatted );
			$time_slot = orddd_lite_common::orddd_get_order_timeslot(  $post_id );
			echo '<p>' . esc_attr( $time_slot ) . '</p>';
		}
	}

	/**
	 * Adds the Delivery Date column to the list of sortable columns
	 * in WooCommerce->Orders page.
	 *
	 * @param array $columns - list of sortable columns.
	 * @return array $columns - list of sortable columns with our column added
	 * @since 1.9
	 */
	public static function orddd_lite_woocommerce_custom_column_value_sort( $columns ) {
		$columns['order_delivery_date'] = '_orddd_lite_timestamp';
		return $columns;
	}

	/**
	 * Delivery date column orderby. This help WooCommerce to understand the field on which the sorting should be based on.
	 * The delivery date is stored as a timestamp in the _orddd_lite_timestamp variable in wp_postmeta
	 *
	 * @param array $vars Query attributes for sorting delivery date column.
	 * @return array
	 * @since 1.9
	 **/
	public static function orddd_lite_woocommerce_delivery_date_orderby( $vars ) {
		global $typenow;
		$delivery_field_label = '_orddd_lite_timestamp';
		if ( isset( $vars['orderby'] ) && '' !== $vars['orderby'] ) {
			if ( $delivery_field_label === $vars['orderby'] ) {
				$sorting_vars = array(
					'orderby' => array(
						'meta_value_num' => $vars['order'],
						'date'           => 'ASC',
					),
				);
				// phpcs:ignore
				$sorting_vars['meta_query'] = array(
					'relation' => 'OR',
					array(
						'key'     => $delivery_field_label,
						'value'   => '',
						'compare' => 'NOT EXISTS',
					),
					array(
						'key'     => $delivery_field_label,
						'compare' => 'EXISTS',
					),
				);

				$vars = array_merge( $vars, $sorting_vars );
			}
		} elseif ( get_option( 'orddd_lite_enable_default_sorting_of_column' ) === 'checked' ) {
			if ( 'shop_order' !== $typenow ) {
				return $vars;
			}

			$sorting_vars = array(
				'orderby' => array(
					'meta_value_num' => 'DESC',
					'date'           => 'ASC',
				),
				'order'   => 'DESC',
			);
			// phpcs:ignore
			$sorting_vars['meta_query'] = array(
				'relation' => 'OR',
				array(
					'key'     => $delivery_field_label,
					'value'   => '',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key'     => $delivery_field_label,
					'compare' => 'EXISTS',
				),
			);

			$vars = array_merge( $vars, $sorting_vars );
		}
		return $vars;
	}

	/**
	 * Displays the Delivery date & Delivery time on WooCommerce->Orders->Edit Order page.
	 * 
	 * @param WC_Order $order - Order object
	 * 
	 * @hook woocommerce_admin_order_data_after_billing_address
	 *       woocommerce_admin_order_data_after_shipping_address
	 * @since 3.12.0      
	 */
	public static function orddd_lite_display_delivery_info_admin_order_meta( $order ) {		
		if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {            
            $order_id = $order->get_id();
        } else {
            $order_id = $order->id;
        }
		
		$delivery_date_formatted = Orddd_Lite_Common::orddd_lite_get_order_delivery_date( $order_id );
		$date_field_label        = get_option( 'orddd_lite_delivery_date_field_label' );

		if( '' !== $delivery_date_formatted ) {
			echo '<p><strong>' . __( $date_field_label, 'order-delivery-date' ) . ': </strong>' . $delivery_date_formatted;
		}
		
		$time_slot        = Orddd_Lite_Common::orddd_get_order_timeslot( $order_id );
		$time_field_label = get_option( 'orddd_lite_delivery_timeslot_field_label' );

		if ( '' !== $time_slot ) {
			echo '<p><strong>' . __( $time_field_label, 'order-delivery-date' ) . ': </strong>' . $time_slot . '</p>';
		}
	}

	/**
	 * Displays the Delivery Date & Delivery time on Order Preview page in Admin
	 * 
	 * @param $data 
	 * @param WC_Order $order - Order object
	 * 
	 * @hook woocommerce_admin_order_preview_get_order_details
	 * @since 3.12.0
	 */
	public static function orddd_lite_admin_order_preview_add_delivery_date( $data, $order ) {
		$order_id                = $order->get_id();
		$delivery_date_formatted = Orddd_Lite_Common::orddd_lite_get_order_delivery_date( $order_id );
		$field_date_label        = get_option( 'orddd_lite_delivery_date_field_label' );
		$orddd_timeslot          = Orddd_Lite_Common::orddd_get_order_timeslot( $order_id );

		if ( '' != $delivery_date_formatted ) {
	        $data[ 'payment_via' ] = $data[ 'payment_via' ] . '<br>' . '<strong>'.$field_date_label.'</strong>' . $delivery_date_formatted;

	        if ( '' != $orddd_timeslot ) {
	        	$data[ 'payment_via' ] = $data[ 'payment_via' ] . ',<br>' . $orddd_timeslot;
	    	}
		}
    	return $data;
	}
}
$orddd_lite_filter = new Orddd_Lite_Filter();


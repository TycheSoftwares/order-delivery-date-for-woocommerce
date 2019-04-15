<?php 

/**
* Order Delivery Date for WooCommerce Lite
*
* Common functions used in multiple files are added
*
* @author      Tyche Softwares
* @package     Order-Delivery-Date-Lite-for-WooCommerce/Common-Functions
* @since       3.5
*/

/**
 * Class for the common functions used in the plugin
 */
class orddd_lite_common {

	/**
     * Tracking data to send when No, thanks. button is clicked.
     *
     * @hook ts_tracker_opt_out_data
     *
     * @param array $params Parameters to pass for tracking data.
     *
     * @return array Data to track when opted out.
     * 
     */
	public static function orddd_lite_get_data_for_opt_out ( $params ) {
	    $plugin_data[ 'ts_meta_data_table_name' ]   = 'ts_tracking_orddd_lite_meta_data';
	    $plugin_data[ 'ts_plugin_name' ]		   = 'Order Delivery Date for WooCommerce (Lite version)';
	    
	    $params[ 'plugin_data' ]  				   = $plugin_data;
	    
	    return $params;
	}

	/**
     * Plugin's data to be tracked when Allow option is choosed.
     *
     * @hook ts_tracker_data
     *
     * @param array $data Contains the data to be tracked.
     *
     * @return array Plugin's data to track.
     * 
     */

    public static function orddd_lite_ts_add_plugin_tracking_data ( $data ) {
    	if ( isset( $_GET[ 'orddd_lite_tracker_optin' ] ) && isset( $_GET[ 'orddd_lite_tracker_nonce' ] ) && wp_verify_nonce( $_GET[ 'orddd_lite_tracker_nonce' ], 'orddd_lite_tracker_optin' ) ) {

	        $plugin_data[ 'ts_meta_data_table_name' ]   = 'ts_tracking_orddd_lite_meta_data';
	        $plugin_data[ 'ts_plugin_name' ]		    = 'Order Delivery Date for WooCommerce (Lite version)';
	        
	        // Store count info
	        $plugin_data[ 'deliveries_count' ]          = self::orddd_lite_ts_get_order_counts();
	        
	        // Get all plugin options info
	        $plugin_data[ 'deliveries_settings' ]       = self::orddd_lite_ts_get_all_plugin_options_values();
	        $plugin_data[ 'orddd_lite_plugin_version' ] = self::orddd_get_version();
	        $plugin_data[ 'orddd_lite_allow_tracking' ] = get_option ( 'orddd_lite_allow_tracking' );
	        $data[ 'plugin_data' ]                      = $plugin_data;
	    }
        return $data;
    }

	/**
	 * Get order counts based on order status.
	 * 
	 * @globals resource WordPress object
	 *
	 * @return int $order_count Number of Deliveries
	 * 
	 */	
	public static function orddd_lite_ts_get_order_counts () {
		global $wpdb;
        $order_count = 0;
        $orddd_query = "SELECT count(ID) AS delivery_orders_count FROM `" . $wpdb->prefix . "posts` WHERE post_type = 'shop_order' AND post_status NOT IN ('wc-cancelled', 'wc-refunded', 'trash', 'wc-failed' ) AND ID IN ( SELECT post_id FROM `" . $wpdb->prefix . "postmeta` WHERE meta_key IN ( %s, %s ) )";

        $results = $wpdb->get_results( $wpdb->prepare( $orddd_query, '_orddd_lite_timestamp', get_option( 'orddd_lite_delivery_date_field_label' ) ) );
        if( isset( $results[0] ) ) {
            $order_count = $results[0]->delivery_orders_count;    
        }
        
        return $order_count;
	}

	/**
	 * Get all plugin options starting with orddd_ prefix.
	 *
	 * @globals resource WordPress object
	 *
	 * @return array Plugin Settings
	 * 
	 */
	public static function orddd_lite_ts_get_all_plugin_options_values () {
		return array(
            'enable_delivery'                          => get_option( 'orddd_lite_enable_delivery_date' ),
            'date_mandatory'                           => get_option( 'orddd_lite_date_field_mandatory' ),
            'populate_first_delivery_date'             => get_option( 'orddd_lite_auto_populate_first_available_date' ),
            'allow_minimum_hours_for_non_working_days' => get_option( 'orddd_lite_calculate_min_time_disabled_days' ),
            'no_fields_for'                            => array( 'virtual_product'  => get_option( 'orddd_lite_no_fields_for_virtual_product' ),         'featured_product' 						   => get_option( 'orddd_lite_no_fields_for_featured_product' ) ),
            'cart_page_delivery'               		   => get_option( 'orddd_lite_delivery_date_on_cart_page' )
		);
	}


	/**
	 * It will add the question for the deactivate popup modal
	 * @return array $orddd_lite_add_questions All questions.
	 */
	public static function orddd_lite_deactivate_add_questions () {

		$orddd_lite_add_questions = array(
			0 => array(
				'id'                => 4,
				'text'              => __( "Minimum Delivery Time (in hours) is not working as expected.", "order-delivery-date" ),
				'input_type'        => '',
				'input_placeholder' => ''
				), 
			1 =>  array(
				'id'                => 5,
				'text'              => __( "I need delivery time along with the delivery date.", "order-delivery-date" ),
				'input_type'        => '',
				'input_placeholder' => ''
			),
			2 => array(
				'id'                => 6,
				'text'              => __( "The plugin is not compatible with another plugin.", "order-delivery-date" ),
				'input_type'        => 'textfield',
				'input_placeholder' => 'Which Plugin?'
			),
			3 => array(
				'id'                => 7,
				'text'              => __( "I have purchased the Pro version of the Plugin.", "order-delivery-date" ),
				'input_type'        => '',
				'input_placeholder' => ''
			)

		);
		return $orddd_lite_add_questions;
	}
    
    /**
     * Return the date with the selected langauge in Appearance tab
     * 
     * @param string $delivery_date_formatted Default Delivery Date
     * @param string $delivery_date_timestamp Delivery Date Timestamp
     * @return string Translated Delivery Date
     * @globals array $orddd_lite_languages Languages array
     * @globals array $orddd_lite_languages_locale Locale of all languages array
     * @since 1.9
     */
	public static function delivery_date_lite_language( $delivery_date_formatted, $delivery_date_timestamp ) {
		global $orddd_lite_languages, $orddd_lite_languages_locale;
		$date_language = get_option( 'orddd_lite_language_selected' );
		if( $delivery_date_timestamp != '' ) {
            if( $date_language != 'en-GB' ) {
                $locale_format = $orddd_lite_languages[ $date_language ];
                $time = setlocale( LC_ALL, $orddd_lite_languages_locale[ $locale_format ] );
                $date_format = get_option( 'orddd_lite_delivery_date_format' );
                switch ( $date_format ) {
                    case 'd M, y':
                        $date_str = str_replace( 'd', '%d', $date_format );
                        $month_str = str_replace( 'M', '%b', $date_str );
                        $year_str = str_replace( 'y', '%y', $month_str );
                        break;
                    case 'd M, yy':
                        $date_str = str_replace( 'd', '%d', $date_format );
                        $month_str = str_replace( 'M', '%b', $date_str );
                        $year_str = str_replace( 'yy', '%Y', $month_str );
					    break;
                    case 'd MM, y':
                        $date_str = str_replace( 'd', '%d', $date_format );
                        $month_str = str_replace( 'MM', '%B', $date_str );
                        $year_str = str_replace( 'y', '%y', $month_str );
                        break;
                    case 'd MM, yy':
                        $date_str = str_replace( 'd', '%d', $date_format );
                        $month_str = str_replace( 'MM', '%B', $date_str );
                        $year_str = str_replace( 'yy', '%Y', $month_str );
                        break;
                    case 'DD, d MM, yy':
                        $day_str = str_replace( 'DD', '%A', $date_format );
                        $date_str = str_replace( 'd', '%d', $day_str );
                        $month_str = str_replace( 'MM', '%B', $date_str );
                        $year_str = str_replace( 'yy', '%Y', $month_str );
                        break;
                    case 'D, M d, yy':
                        $day_str = str_replace( 'D', '%a', $date_format );
                        $date_str = str_replace( 'd', '%d', $day_str );
                        $month_str = str_replace( 'M', '%b', $date_str );
                        $year_str = str_replace( 'yy', '%Y', $month_str );
                        break;
                    case 'DD, M d, yy':
				        $day_str = str_replace( 'DD', '%A', $date_format );
                        $date_str = str_replace( 'd', '%d', $day_str );
                        $month_str = str_replace( 'M', '%b', $date_str );
                        $year_str = str_replace( 'yy', '%Y', $month_str );
                        break;
                    case 'DD, MM d, yy':
                        $day_str = str_replace( 'DD', '%A', $date_format );
                        $date_str = str_replace( 'd', '%d', $day_str );
                        $month_str = str_replace( 'MM', '%B', $date_str );
                        $year_str = str_replace( 'yy', '%Y', $month_str );
                        break;
                    case 'D, MM d, yy':
				        $day_str = str_replace( 'D', '%a', $date_format );
                        $date_str = str_replace( 'd', '%d', $day_str );
                        $month_str = str_replace( 'MM', '%B', $date_str );
                        $year_str = str_replace( 'yy', '%Y', $month_str );
                        break;
                }
                                
                if( isset( $year_str ) ) {
                    $delivery_date_formatted = strftime( $year_str, $delivery_date_timestamp );
                }                
                setlocale( LC_ALL, 'en_GB.utf8' );
            }
        }
		return $delivery_date_formatted;
	}
	
	/**
     * Return the delivery date selected for the order
     *
     * @param int $order_id Order ID
     * @return string Delivery Date for the order
     * @globals array $orddd_lite_date_formats Date Format array
     * @since 1.9
     */

	public static function orddd_lite_get_order_delivery_date( $order_id ) {
	    global $orddd_lite_date_formats;
	    $data = get_post_meta( $order_id );
	    $field_date_label = get_option( 'orddd_lite_delivery_date_field_label' );
	    $delivery_date_formatted = $delivery_date_timestamp = '';
	    if ( isset( $data[ '_orddd_lite_timestamp' ] ) || isset( $data[ get_option( 'orddd_lite_delivery_date_field_label' ) ] ) ) {
	        if ( isset( $data[ '_orddd_lite_timestamp' ] ) ) {
	            $delivery_date_timestamp = $data[ '_orddd_lite_timestamp' ][ 0 ];
	        }
	        $delivery_date_formatted = '';
	        if ( $delivery_date_timestamp != '' ) {
	            $delivery_date_formatted = date( $orddd_lite_date_formats[ get_option( 'orddd_lite_delivery_date_format') ], $delivery_date_timestamp );
	        } else {
	            if ( array_key_exists( get_option( 'orddd_lite_delivery_date_field_label' ), $data ) ) {
	                //$delivery_date_replace = str_replace(","," ",$data[ get_option( 'orddd_lite_delivery_date_field_label' ) ][ 0 ]);
	                $delivery_date_timestamp = strtotime( $data[ get_option( 'orddd_lite_delivery_date_field_label' ) ][ 0 ] );
	                if ( $delivery_date_timestamp != '' ) {
	                    $delivery_date_formatted = date( $orddd_lite_date_formats[ get_option( 'orddd_lite_delivery_date_format' ) ], $delivery_date_timestamp );
	                }
	            } elseif ( array_key_exists( ORDDD_DELIVERY_DATE_FIELD_LABEL, $data ) ) {
	                $delivery_date_timestamp = strtotime( $data[ ORDDD_DELIVERY_DATE_FIELD_LABEL ][ 0 ] );
	                if ( $delivery_date_timestamp != '' ) {
	                    $delivery_date_formatted = date( $orddd_lite_date_formats[ get_option( 'orddd_lite_delivery_date_format' ) ], $delivery_date_timestamp );
	                }
	            }
	        }
	        $delivery_date_formatted = orddd_lite_common::delivery_date_lite_language( $delivery_date_formatted, $delivery_date_timestamp );
	    }
	    return $delivery_date_formatted;
	}
	
	
	/**
	 * Returns timestamp for the selected Delivery date
	 * 
	 * @param string $delivery_date Selected Delivery Date 
	 * @param string $date_format Date Format 
	 * @return string Timestamp for the selected delivery date
	 * @since 1.7
	 */
	
	public static function orddd_lite_get_timestamp( $delivery_date, $date_format ) {
	    $hour = 0;
	    $min = 1;
	    $date_str = '';
	    $m = $d = $y = 0;
	    if( $delivery_date != '' ) {
            switch ( $date_format ) {
                case 'mm/dd/y':
                    $date_arr = explode( '/', $delivery_date );
                    $m = $date_arr[ 0 ];
                    $d = $date_arr[ 1 ];
                    $y = $date_arr[ 2 ];
                    break;
                case 'dd/mm/y':
                    $date_arr = explode( '/', $delivery_date );
                    $m = $date_arr[ 1 ];
                    $d = $date_arr[ 0 ];
                    $y = $date_arr[ 2 ];
                    break;
                case 'y/mm/dd':
                    $date_arr = explode( '/', $delivery_date );
                    $m = $date_arr[ 1 ];
                    $d = $date_arr[ 2 ];
                    $y = $date_arr[ 0 ];
                    break;
                case 'dd.mm.y':
                    $date_arr = explode( '.', $delivery_date );
                    $m = $date_arr[ 1 ];
                    $d = $date_arr[ 0 ];
                    $y = $date_arr[ 2 ];
                    break;
                case 'y.mm.dd':
                    $date_arr = explode( '.', $delivery_date );
                    $m = $date_arr[ 1 ];
                    $d = $date_arr[ 2 ];
                    $y = $date_arr[ 0 ];
                    break;
                case 'yy-mm-dd':
                    $date_arr = explode( '-', $delivery_date );
                    $m = $date_arr[ 1 ];
                    $d = $date_arr[ 2 ];
                    $y = $date_arr[ 0 ];
                    break;
                case 'dd-mm-y':
                    $date_arr = explode( '-', $delivery_date );
                    $m = $date_arr[ 1 ];
                    $d = $date_arr[ 0 ];
                    $y = $date_arr[ 2 ];
                    break;
                case 'd M, y':
                    $date_str = str_replace( ',', '', $delivery_date );
                    break;
                case 'd M, yy':
                    $date_str = str_replace( ',', '', $delivery_date );
                    break;
                case 'd MM, y':
                    $date_str = str_replace( ',', '', $delivery_date );
                    break;
                case 'd MM, yy':
                    $date_str = str_replace( ',', '', $delivery_date );
                    break;
                case 'DD, d MM, yy':
                    $date_str = str_replace( ',', '', $delivery_date );
                    break;
                case 'D, M d, yy':
                    $date_str = str_replace( ',', '', $delivery_date );
                    break;
                case 'DD, M d, yy':
                    $date_str = str_replace( ',', '', $delivery_date );
                    break;
                case 'DD, MM d, yy':
                    $date_str = str_replace( ',', '', $delivery_date );
                    break;
                case 'D, MM d, yy':
                    $date_str = str_replace( ',', '', $delivery_date );
                    break;
            }
            if ( isset( $date_str ) && $date_str != '' ) {
	            $timestamp = strtotime( $date_str );
            } else {
                $timestamp = mktime( 0, 0, 0, $m, $d, $y );
            }
	    } else {
	        $timestamp = '';
	    }

	    return $timestamp;
	}
	
	/**
	 * Free up the delivery date and time if an order is moved to trashed
	 * 
	 * @hook wp_trash_post
	 *
	 * @param int $order_id Order ID
	 * @globals string typenow
	 * @since 2.5
	 */

	public static function orddd_lite_cancel_delivery_for_trashed( $order_id ) {
	    global $typenow;
	    $post_obj = get_post( $order_id );
	    if ( 'shop_order' != $typenow ) {
	        return;
	    } else {
	        if ( 'wc-cancelled' == $post_obj->post_status || 'wc-refunded' == $post_obj->post_status || 'wc-failed' == $post_obj->post_status ) {
	        } else {
	            orddd_lite_common::orddd_lite_cancel_delivery( $order_id );
	        }
	    }
	}
		

	/**
	 * Free up the delivery date and time if an order is cancelled, refunded or failed
	 * 
	 * @hook woocommerce_order_status_cancelled
	 * @hook woocommerce_order_status_refunded
	 * @hook woocommerce_order_status_failed
	 *
	 * @param int $order_id Order ID
	 * @globals string typenow
	 * @since 2.5
	 */

	public static function orddd_lite_cancel_delivery( $order_id ) {
	    global $wpdb, $typenow;
	    $post_meta = get_post_meta( $order_id, '_orddd_lite_timestamp' );
	    if( isset( $post_meta[0] ) && $post_meta[0] != '' && $post_meta[0] != null ) {
	        $delivery_date_timestamp = $post_meta[0];
	    } else {
	        $delivery_date_timestamp = '';
	    }
	     
	    if( $delivery_date_timestamp != '' ) {
	        $delivery_date = date( ORDDD_LITE_LOCKOUT_DATE_FORMAT, $delivery_date_timestamp );
	    } else {
	        $delivery_date = '';
	    }
	    $lockout_days = get_option( 'orddd_lite_lockout_days' );
	    if ( $lockout_days == '' || $lockout_days == '{}' || $lockout_days == '[]' || $lockout_days == "null" ) {
	        $lockout_days_arr = array();
	    } else {
	        $lockout_days_arr = (array) json_decode( $lockout_days );
	    }
	    foreach ( $lockout_days_arr as $k => $v ) {
	        $orders = $v->o;
	        if ( $delivery_date == $v->d ) {
	            if( $v->o == '1' ) {
	                unset( $lockout_days_arr[ $k ] );
	            } else {
	                $orders = $v->o - 1;
	                $lockout_days_arr[ $k ] = array( 'o' => $orders, 'd' => $v->d );
	            }
	        }
	    }
	     
	    $lockout_days_jarr = json_encode( $lockout_days_arr );
	    update_option( 'orddd_lite_lockout_days', $lockout_days_jarr );
	}
	

	/**
	 * Checks if there is a Virtual product in cart
	 *
	 * @globals resource $woocommerce WooCommerce Object
	 * @return string yes if virtual product is there in the cart else no
	 * @since 1.7
	 */
	public static function orddd_lite_is_delivery_enabled() {

        $delivery_enabled = wp_cache_get( 'orddd_lite_delivery_enabled' );
        if ( false === $delivery_enabled ) {
    	    global $woocommerce;
    	    $delivery_enabled = 'yes';
            $fields_for_virtual_product  = get_option( 'orddd_lite_no_fields_for_virtual_product' );
            $fields_for_featured_product = get_option( 'orddd_lite_no_fields_for_featured_product' );

    	    if ( $fields_for_virtual_product == 'on' && $fields_for_featured_product == 'on' ) {
    	        foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
    	            $product_id = $values[ 'product_id' ];
                    $_product = wc_get_product( $product_id );
    	            if( $_product->is_virtual() == false && $_product->is_featured() == false ) {
    	                $delivery_enabled = 'yes';
    	                break;
    	            } else {
    	                $delivery_enabled = 'no';
    	            }
    	        }
    	    } else if( $fields_for_virtual_product == 'on' && $fields_for_featured_product != 'on' ) {
    	        foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
    	            $_product = $values[ 'data' ];
    	            if( $_product->is_virtual() == false ) {
    	                $delivery_enabled = 'yes';
    	                break;
    	            } else {
    	                $delivery_enabled = 'no';
    	            }
    	        }
    	    } else if( $fields_for_virtual_product != 'on' && $fields_for_featured_product == 'on' ) {
    	        foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
    	            $product_id = $values[ 'product_id' ];
                    $_product = wc_get_product( $product_id );
    	            if( $_product->is_featured() == false ) {
    	                $delivery_enabled = 'yes';
    	                break;
    	            } else {
    	                $delivery_enabled = 'no';
    	            }
    	        }
    	    } else {
    	        $delivery_enabled = 'yes';
    	    }
            wp_cache_set( 'orddd_lite_delivery_enabled', $delivery_enabled );
        }
	    return $delivery_enabled;
	}

	/**
     * This function returns the Order Delivery Date Lite plugin version number.
     *     
	 * @return string Version of the plugin
	 * @since 3.3
     */
    public static function orddd_get_version() {

        $plugin_version = wp_cache_get( 'orddd_lite_get_version' );
        if ( false === $plugin_version ) {

            $orddd_plugin_dir =  dirname ( dirname (__FILE__) );
            $orddd_plugin_dir .= '/order-delivery-date-for-woocommerce/order_delivery_date.php';

            $plugin_data = get_file_data( $orddd_plugin_dir, array( 'Version' => 'Version' ) );
            if ( ! empty( $plugin_data['Version'] ) ) {
                $plugin_version = $plugin_data[ 'Version' ];
            }
            wp_cache_set( 'orddd_lite_get_version', $plugin_version );
        }
        return $plugin_version;
    }

    /**
     * This function returns the plugin url 
     * @return string Base URL of the plugin
	 * @since 3.3
     */
    public static function orddd_get_plugin_url() {
        return plugins_url() . '/order-delivery-date-for-woocommerce/';
    }
}
?>
<?php
/**
 * Order Delivery Date for WooCommerce Lite
 *
 * Processes performed on the frontend checkout page
 *
 * @author      Tyche Softwares
 * @package     Order-Delivery-Date-Lite-for-WooCommerce/Frontend/Checkout-Page-Processes
 * @since       1.5
 */

/**
 * Class for adding processes to be performed on the checkout page
 */

class orddd_lite_process {

    /**
     * Adds hidden fields and delivery date field on the frontend checkout page
     * 
     * @hook woocommerce_after_checkout_billing_form
     * @hook woocommerce_after_checkout_shipping_form
     * @hook woocommerce_before_order_notes
     * @hook woocommerce_after_order_notes
     *
     * @globals array $orddd_lite_weekdays Weekdays array
     *
     * @param resource $checkout WooCommerce checkout object
     * @since 1.5
     */

	public static function orddd_lite_my_custom_checkout_field( $checkout = '' ) {
        global $orddd_lite_weekdays;
        if ( get_option( 'orddd_lite_enable_delivery_date' ) == 'on' ) {
        	$var = '';

            $first_day_of_week = '1';
            if( get_option( 'orddd_lite_start_of_week' ) != '' ) {
                $first_day_of_week = get_option( 'orddd_lite_start_of_week' );
            }
            $var .= '<input type="hidden" name="orddd_first_day_of_week" id="orddd_first_day_of_week" value="' . $first_day_of_week . '">';

            $var .= '<input type="hidden" name="orddd_lite_delivery_date_format" id="orddd_lite_delivery_date_format" value="'. get_option( 'orddd_lite_delivery_date_format' ) . '">';

            $field_note_text = __( get_option( 'orddd_lite_delivery_date_field_note' ), 'order-delivery-date' );
	        $field_note_text = str_replace( array( "\r\n", "\r", "\n" ), "<br/>", $field_note_text );
	        if( strpos( $field_note_text, '"' ) !== false ) {
	            $var .= '<input type="hidden" name="orddd_lite_field_note" id="orddd_lite_field_note" value=\'' . $field_note_text . '\'>';
	        } else {
	            $var .= '<input type="hidden" name="orddd_lite_field_note" id="orddd_lite_field_note" value="' . $field_note_text . '">';
	        }

            $alldays_orddd_lite = array();
        	foreach ( $orddd_lite_weekdays as $n => $day_name ) {
                $alldays_orddd_lite[ $n ] = get_option( $n );
            }
            $alldayskeys_orddd_lite = array_keys( $alldays_orddd_lite );
            $checked = "No";
        	foreach( $alldayskeys_orddd_lite as $key ) {
                if( $alldays_orddd_lite[ $key ] == 'checked' ) {
        	       $checked = "Yes";
                }
            }
            
            if( $checked == 'Yes' ) {
                foreach( $alldayskeys_orddd_lite as $key ) {
                    $var .= '<input type="hidden" id="' . $key . '" value="' . $alldays_orddd_lite[ $key ] . '">' ;
                }
            } else if( $checked == 'No') {
                foreach( $alldayskeys_orddd_lite as $key )  {
                    $var .= '<input type="hidden" id="' . $key . '" value="checked">';
                }
            }
            
            $min_date = '';
            $current_time = current_time( 'timestamp' );
            
            if( '' ==  get_option( 'orddd_lite_minimumOrderDays' ) ) {
                $minimum_delivery_time_orddd_lite = 0;
            } else {
                $minimum_delivery_time_orddd_lite = get_option( 'orddd_lite_minimumOrderDays' );
            }

            $delivery_time_seconds = $minimum_delivery_time_orddd_lite *60 *60;
            $cut_off_timestamp = $current_time + $delivery_time_seconds;
            $cut_off_date = date( "d-m-Y", $cut_off_timestamp );
            $min_date = date( "j-n-Y", strtotime( $cut_off_date ) );
            
            $var .= '<input type="hidden" name="orddd_lite_minimumOrderDays" id="orddd_lite_minimumOrderDays" value="' . $min_date . '">';
            $var .= '<input type="hidden" name="orddd_lite_number_of_dates" id="orddd_lite_number_of_dates" value="' . get_option( 'orddd_lite_number_of_dates' ) . '">';
        	$var .= '<input type="hidden" name="orddd_lite_date_field_mandatory" id="orddd_lite_date_field_mandatory" value="' . get_option( 'orddd_lite_date_field_mandatory' ) . '">';
        	$var .= '<input type="hidden" name="orddd_lite_number_of_months" id="orddd_lite_number_of_months" value="' . get_option( 'orddd_lite_number_of_months' ) . '">';
        	$var .= '<input type="hidden" name="h_deliverydate" id="h_deliverydate" value="">';
        	 
        	$lockout_days_str = '';
        	if ( get_option( 'orddd_lite_lockout_date_after_orders' ) > 0 ) {
        	    $lockout_days_arr = array();
        	    $lockout_days = get_option( 'orddd_lite_lockout_days' );
        	    if ( $lockout_days != '' && $lockout_days != '{}' && $lockout_days != '[]' ) {
        	        $lockout_days_arr = json_decode( get_option( 'orddd_lite_lockout_days' ) );
        	    }
        	    foreach ( $lockout_days_arr as $k => $v ) {
        	        if ( $v->o >= get_option( 'orddd_lite_lockout_date_after_orders' ) ) {
        	            $lockout_days_str .= '"' . $v->d . '",';
        	        }
        	    }
        	    $lockout_days_str = substr( $lockout_days_str, 0, strlen( $lockout_days_str ) -1 );
        	}
        	$var .= '<input type="hidden" name="orddd_lite_lockout_days" id="orddd_lite_lockout_days" value=\'' . $lockout_days_str . '\'>';

            //fetch holidays
            $holidays_arr = array();
            $holidays = get_option( 'orddd_lite_holidays' );
            if ( $holidays != '' && $holidays != '{}' && $holidays != '[]' && $holidays != 'null' ) {
                $holidays_arr = json_decode( get_option( 'orddd_lite_holidays' ) );
            }
            $holidays_str = "";
            foreach ( $holidays_arr as $k => $v ) {
                $name = str_replace( "'", "&apos;", $v->n );
                $name = str_replace( "'", "&quot;", $name );
                $holidays_str .= '"' . $name . ":" . $v->d . '",';
            }
            
            $holidays_str = substr( $holidays_str, 0, strlen( $holidays_str )-1 );
            $var .= '<input type="hidden" name="orddd_lite_holidays" id="orddd_lite_holidays" value=\'' . $holidays_str . '\'>';

            $var .= '<input type="hidden" name="orddd_lite_auto_populate_first_available_date" id="orddd_lite_auto_populate_first_available_date" value="' . get_option( 'orddd_lite_auto_populate_first_available_date' ) . '">';

            $var .= '<input type="hidden" name="orddd_lite_calculate_min_time_disabled_days" id="orddd_lite_calculate_min_time_disabled_days" value="' . get_option( 'orddd_lite_calculate_min_time_disabled_days' ) . '">';

	    	$current_date = date( "j-n-Y", $current_time );
            $var .= '<input type="hidden" name="orddd_lite_current_day" id="orddd_lite_current_day" value="' . $current_date . '">';

            $admin_url = get_admin_url();
            $admin_url_arr = explode( "://", $admin_url );
            $home_url = get_home_url();
            $home_url_arr = explode( "://", $home_url );
            if( $admin_url_arr[ 0 ] != $home_url_arr[ 0 ] ) {
                $admin_url_arr[ 0 ] = $home_url_arr[ 0 ];
                $ajax_url = implode( "://", $admin_url_arr );
            } else {
                $ajax_url = $admin_url;
            }

            $var .= '<input type="hidden" name="orddd_admin_url" id="orddd_admin_url" value="' . $ajax_url . '">';

            $orddd_lite_disable_for_holidays = 'no';
            if( has_filter( 'orddd_to_calculate_minimum_hours_for_holidays' ) ) {
                $orddd_lite_disable_for_holidays = apply_filters( 'orddd_to_calculate_minimum_hours_for_holidays', $orddd_lite_disable_for_holidays );
            }
            
            $var .= '<input type="hidden" name="orddd_lite_disable_for_holidays" id="orddd_lite_disable_for_holidays" value="' . $orddd_lite_disable_for_holidays . '">';

            $var .= '<input type="hidden" name="orddd_lite_delivery_date_on_cart_page" id="orddd_lite_delivery_date_on_cart_page" value ="' . get_option( 'orddd_lite_delivery_date_on_cart_page' ) . '">';

            $current_hour = date( "H", $current_time );
            $current_minute  = date( "i", $current_time );

            $var .= '<input type="hidden" name="orddd_lite_current_day" id="orddd_lite_current_day" value="' . $current_date . '">';
            $var .= '<input type="hidden" name="orddd_lite_current_hour" id="orddd_lite_current_hour" value="' . $current_hour . '">';
            $var .= '<input type="hidden" name="orddd_lite_current_minute" id="orddd_lite_current_minute" value="' . $current_minute . '">';

			echo $var;

            $is_delivery_enabled = orddd_lite_common::orddd_lite_is_delivery_enabled();
            
            if( $is_delivery_enabled == 'yes' ) {
                $validate_wpefield = false;
                if ( get_option( 'orddd_lite_date_field_mandatory' ) == 'checked' ) {
                    $validate_wpefield = true;
                }

                if( '' == $checkout ) {
                    woocommerce_form_field( 'e_deliverydate', array(
                        'type'              => 'text',
                        'label'             => __( get_option( 'orddd_lite_delivery_date_field_label' ), 'order-delivery-date' ),
                        'required'          => $validate_wpefield,
                        'placeholder'       => __( get_option( 'orddd_lite_delivery_date_field_placeholder' ), 'order-delivery-date' ),
                        'custom_attributes' => array( 'style'=>'cursor:text !important;'),
                        'class' => array( 'form-row-cart' )
                    ) );
                } else {
                    woocommerce_form_field( 'e_deliverydate', array(
                        'type'              => 'text',
                        'label'             => __( get_option( 'orddd_lite_delivery_date_field_label' ), 'order-delivery-date' ),
                        'required'          => $validate_wpefield,
                        'placeholder'       => __( get_option( 'orddd_lite_delivery_date_field_placeholder' ), 'order-delivery-date' ),
                        'custom_attributes' => array( 'style'=>'cursor:text !important;'),
                        'class' => array( 'form-row-wide' ) 
                    ),
                    $checkout->get_value( 'e_deliverydate' ) );
                }
            }
        }
    }

    /**
     * Saves the selected delivery date into the post meta table 
     *
     * @hook woocommerce_checkout_update_order_meta
     *
     * @param int $order_id Order ID
     * @since 1.5
     */
    public static function orddd_lite_my_custom_checkout_field_update_order_meta( $order_id ) {
        if ( isset( $_POST[ 'e_deliverydate' ] ) && $_POST[ 'e_deliverydate' ] != '' ) {
            $delivery_date = '';
            $date_format = 'dd-mm-y';

            if( isset( $_POST[ 'h_deliverydate' ] ) ) {	    
                $delivery_date = sanitize_text_field( $_POST[ 'h_deliverydate' ] );
            }

            update_post_meta( $order_id, get_option( 'orddd_lite_delivery_date_field_label' ), sanitize_text_field( $_POST[ 'e_deliverydate' ] ) );
	    
            $timestamp = orddd_lite_common::orddd_lite_get_timestamp( $delivery_date, $date_format );
            update_post_meta( $order_id, '_orddd_lite_timestamp', $timestamp );
		    orddd_lite_process::orddd_lite_update_lockout_days( $delivery_date );
        } else {
		    $delivery_enabled = orddd_lite_common::orddd_lite_is_delivery_enabled();
		    $is_delivery_enabled = 'yes';
		    if ( $delivery_enabled == 'no' ) {
		        $is_delivery_enabled = 'no';
		    }
            
            if( $is_delivery_enabled == 'yes' ) {
                update_post_meta( $order_id, get_option( 'orddd_delivery_date_field_label' ), '' );
            }
        }
    }
    

    /**
     * Updates the lockout for the delivery date in the options table
     *
     * @param string $delivery_date Selected Delivery Date
     * @since 1.5
     */

    public static function orddd_lite_update_lockout_days( $delivery_date ) {
        
        $lockout_date = date( 'n-j-Y', strtotime( $delivery_date ) );
        $lockout_days = get_option( 'orddd_lite_lockout_days' );
        if ( $lockout_days == '' || $lockout_days == '{}' || $lockout_days == '[]' ) {
            $lockout_days_arr = array();
        } else {
            $lockout_days_arr = json_decode( $lockout_days );
        }
        //existing lockout days
        $existing_days = array();
        foreach ( $lockout_days_arr as $k => $v ) {
            $orders = $v->o;
            if ( $lockout_date == $v->d ) {
                $orders = $v->o + 1;
            }
            $existing_days[] = $v->d;
            $lockout_days_new_arr[] = array( 'o' => $orders, 'd' => $v->d );
        }
        // add the currently selected date if it does not already exist
        if ( !in_array( $lockout_date, $existing_days ) ) {
            $lockout_days_new_arr[] = array( 'o' => 1,
                'd' => $lockout_date );
        }
        $lockout_days_jarr = json_encode( $lockout_days_new_arr );
        update_option( 'orddd_lite_lockout_days', $lockout_days_jarr );
    }
        
    /**
     * Show delivery date in the email notification for the WooCommerce version below 2.3
     * 
     * @hook woocommerce_email_order_meta_keys
     * 
     * @param array $keys
     * @return array $keys
     * @since 1.3
     */
    public static function orddd_lite_add_delivery_date_to_order_woo_deprecated( $keys ) {
        $label_name = __( get_option( 'orddd_lite_delivery_date_field_label' ), "order-delivery-date" );
        $keys[] = get_option( 'orddd_lite_delivery_date_field_label' );
        return $keys;
    }
        
    /**
     * Display Delivery Date in Customer notification email for WooCOmmerce version 2.3 and above
     *
     * @hook woocommerce_email_order_meta_fields
     * @param array $fields
     * @param bool $sent_to_admin
     * @param resource $order
     * @return array fields
     * @since 1.3
     */
    
    public static function orddd_lite_add_delivery_date_to_order_woo_new( $fields, $sent_to_admin, $order ) {
        if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {            
            $order_id = $order->get_id();
        } else {
            $order_id = $order->id;
        }
        $fields[ get_option( 'orddd_lite_delivery_date_field_label' ) ] = array(
           'label' => __( get_option( 'orddd_lite_delivery_date_field_label' ), 'order-delivery-date' ),
           'value' => get_post_meta( $order_id, get_option( 'orddd_lite_delivery_date_field_label' ), true ),
       );
       return $fields;
    }
        
    /**
     * Validate delivery date field
     *
     * @hook woocommerce_checkout_process
     * @since 1.4
     **/

    public static function orddd_lite_validate_date_wpefield() {

        $is_delivery_enabled = orddd_lite_common::orddd_lite_is_delivery_enabled();
        
        $delivery_date = '';
        if( isset( $_POST[ 'e_deliverydate' ] ) ) {
            $delivery_date = $_POST[ 'e_deliverydate' ];
        } 

        if( $is_delivery_enabled == 'yes' ) {
            //Check if set, if its not set add an error.
            if ( $delivery_date == '' ) {
                $message = '<strong>' . __( get_option( 'orddd_lite_delivery_date_field_label' ), 'order-delivery-date' ) . '</strong>' . ' ' . __( 'is a required field.', 'order-delivery-date' );
                wc_add_notice( $message, $notice_type = 'error' );
            }
        }
    }
        
    /**
     * Display Delivery Date on Order Recieved Page
     *
     * @hook woocommerce_order_details_after_order_table
     *
     * @globals array orddd_lite_date_formats Date Format array
     * 
     * @param resource $order
     * @since 1.0
     */
    public static function orddd_lite_add_delivery_date_to_order_page_woo( $order ) {
        global $orddd_lite_date_formats;
        if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {            
            $order_id = $order->get_id();
        } else {
            $order_id = $order->id;
        }
        $delivery_date_formatted = orddd_lite_common::orddd_lite_get_order_delivery_date( $order_id );
        if( $delivery_date_formatted != '' ) {
            echo '<p><strong>'.__( get_option( 'orddd_lite_delivery_date_field_label' ), 'order-delivery-date' ) . ':</strong> ' . $delivery_date_formatted . '</p>';
        }
    }
}
$orddd_lite_process = new orddd_lite_process();
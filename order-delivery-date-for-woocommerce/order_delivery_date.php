<?php 
/*
Plugin Name: Order Delivery Date for WooCommerce (Lite version)
Plugin URI: http://www.tychesoftwares.com/store/free-plugin/order-delivery-date-on-checkout/
Description: This plugin allows customers to choose their preferred Order Delivery Date during checkout.
Author: Tyche Softwares
Version: 1.7
Author URI: http://www.tychesoftwares.com/about
Contributor: Tyche Softwares, http://www.tychesoftwares.com/
*/

$wpefield_version = '1.7';

global $weekdays_orddd_lite;

$weekdays_orddd_lite = array('orddd_lite_weekday_0' => __( 'Sunday', 'order-delivery-date' ),
				  'orddd_lite_weekday_1' => __( 'Monday', 'order-delivery-date' ),
				  'orddd_lite_weekday_2' => __( 'Tuesday', 'order-delivery-date' ),
				  'orddd_lite_weekday_3' => __( 'Wednesday', 'order-delivery-date' ),
				  'orddd_lite_weekday_4' => __( 'Thursday', 'order-delivery-date' ),
				  'orddd_lite_weekday_5' => __( 'Friday', 'order-delivery-date' ),
				  'orddd_lite_weekday_6' => __( 'Saturday', 'order-delivery-date' )
				  );
include_once( 'integration.php' );

register_uninstall_hook( __FILE__, 'orddd_lite_deactivate' );

function orddd_lite_deactivate() {
    global $weekdays_orddd_lite;
    delete_option( 'orddd_lite_db_version' );
    foreach ( $weekdays_orddd_lite as $n => $day_name ) {
        delete_option( $n );
    }
    delete_option( 'orddd_lite_minimumOrderDays' );
    delete_option( 'orddd_lite_number_of_dates' );
    delete_option( 'orddd_lite_date_field_mandatory' );
    delete_option( 'orddd_lite_admin_notices' );
    delete_option( 'orddd_lite_lockout_date_after_orders' );
    delete_option( 'orddd_lite_lockout_days' );
    delete_option( 'orddd_lite_update_value' );
    delete_option( 'orddd_lite_abp_hrs' );
}

if ( !class_exists( 'order_delivery_date_lite' ) ) {
    class order_delivery_date_lite {
        
        public function __construct() {
            add_action( 'init', array( &$this, 'orddd_lite_update_po_file' ) );
            
            //Initialize settings
            register_activation_hook( __FILE__, array( &$this, 'orddd_lite_activate' ) );
            add_action( 'admin_init', array( &$this, 'orddd_lite_update_db_check' ) );
            
            // ADMIN
            add_action( 'admin_footer', array( &$this, 'admin_notices_scripts' ) );
            add_action( 'wp_ajax_admin_notices', array( &$this, 'orddd_lite_admin_notices' ) );
            add_action( 'admin_notices', array( &$this, 'order_lite_coupon_notice' ) );
            
            add_action( 'admin_enqueue_scripts', array( &$this,  'orddd_lite_my_enqueue' ) );
            add_action( 'admin_menu', array( &$this, 'orddd_lite_order_delivery_date_menu' ) );
            add_action( 'admin_init', array( &$this, 'order_lite_delivery_date_admin_settings' ) );
            
            add_filter( 'manage_edit-shop_order_columns', array( &$this, 'orddd_lite_woocommerce_order_delivery_date_column'), 20, 1 );
            add_action( 'manage_shop_order_posts_custom_column', array( &$this, 'orddd_lite_woocommerce_custom_column_value') , 20, 1 );
            add_filter( 'woocommerce_order_details_after_order_table', array( &$this, 'orddd_lite_add_delivery_date_to_order_page_woo' ) );
            
            add_action( 'woocommerce_after_checkout_billing_form', array( &$this, 'orddd_lite_my_custom_checkout_field' ) );
            add_action( 'woocommerce_checkout_update_order_meta', array( &$this, 'orddd_lite_my_custom_checkout_field_update_order_meta' ) );
            add_filter( 'woocommerce_email_order_meta_keys', array( &$this, 'orddd_lite_add_delivery_date_to_order_woo' ), 10, 1 );
    
            if ( get_option( 'orddd_lite_date_field_mandatory' ) == 'checked' ) {
                add_action( 'woocommerce_checkout_process', array( &$this, 'orddd_lite_validate_date_wpefield' ) );
            }
        }
        
        function orddd_lite_activate() {
            global $weekdays_orddd_lite;
        
            foreach ( $weekdays_orddd_lite as $n => $day_name ) {
                add_option( $n, 'checked' );
            }
            add_option( 'orddd_lite_minimumOrderDays', '0' );
            add_option( 'orddd_lite_number_of_dates', '30' );
            add_option( 'orddd_lite_date_field_mandatory', '' );
            add_option( 'orddd_lite_lockout_date_after_orders', '' );
            add_option( 'orddd_lite_lockout_days', '' );
            add_option( 'orddd_lite_update_value', 'yes' );
            add_option( 'orddd_lite_abp_hrs', 'HOURS' );
        }

        /***********************************************************
         * This function returns the order delivery date plugin version number
         **********************************************************/
        
        function get_orddd_lite_version() {
            $plugin_data = get_plugin_data( __FILE__ );
            $plugin_version = $plugin_data[ 'Version' ];
            return $plugin_version;
        }
        
        /***************************************************************
         *  This function is executed when the plugin is updated using
         *  the Automatic Updater. It calls the wpefield_update_install function
         *  which will check the options for the plugin and
         *  make any changes if necessary.
         ***************************************************************/
        
        function orddd_lite_update_db_check() {
            global $orddd_lite_plugin_version, $wpefield_version;
            $orddd_lite_plugin_version = $wpefield_version;
            if ( $orddd_lite_plugin_version == "1.7" ) {
                order_delivery_date_lite::orddd_lite_update_install();
            }
        }
        
        function orddd_lite_update_install() {
            global $wpdb, $weekdays_orddd_lite;
        
            //code to set the option to on as default
            $orddd_lite_plugin_version = get_option( 'orddd_lite_db_version' );
            if ( $orddd_lite_plugin_version != order_delivery_date_lite::get_orddd_lite_version() ) {
                update_option('orddd_lite_db_version','1.7');
                if ( get_option( 'orddd_lite_update_value' ) != 'yes' ) {
                    $i = 0;
                    foreach ( $weekdays_orddd_lite as $n => $day_name ) {
        
                        $orddd_lite_weekday = get_option( 'orddd_weekday_'.$i );
                        update_option( $n , $orddd_lite_weekday );
                        delete_option( 'orddd_weekday_'.$i );
                        $i++;
                    }
        
                    $orddd_lite_minimumOrderDays = get_option( 'orddd_minimumOrderDays' );
                    update_option( 'orddd_lite_minimumOrderDays', $orddd_lite_minimumOrderDays );
                    delete_option( 'orddd_minimumOrderDays' );
            
                    $orddd_lite_number_of_dates = get_option( 'orddd_number_of_dates' );
                    update_option( 'orddd_lite_number_of_dates', $orddd_lite_number_of_dates );
                    delete_option( 'orddd_number_of_dates' );
        
                    $orddd_lite_date_field_mandatory = get_option( 'orddd_date_field_mandatory' );
                    update_option( 'orddd_lite_date_field_mandatory', $orddd_lite_date_field_mandatory );
                    delete_option( 'orddd_date_field_mandatory' );
        
                    $orddd_lite_lockout_date_after_orders = get_option( 'orddd_lockout_date_after_orders' );
                    update_option( 'orddd_lite_lockout_date_after_orders', $orddd_lite_lockout_date_after_orders );
                    delete_option( 'orddd_lockout_date_after_orders' );
        
                    $orddd_lite_lockout_days = get_option( 'orddd_lockout_days' );
                    update_option( 'orddd_lite_lockout_days', $orddd_lite_lockout_days );
                    delete_option( 'orddd_lockout_days' );
                    
                    // Code to convert the Minimum delivery time(in days) to Minimum delivery time(in hours)
                    $orddd_abp_hrs = get_option( 'orddd_lite_abp_hrs' );
                    if ( $orddd_abp_hrs != 'HOURS' ) {
                        // Convert the Minimum Delivery time in days to hours
                        if ( get_option( 'orddd_lite_minimumOrderDays' ) > 0 ) {
                            $advance_period_hrs = ( get_option( 'orddd_lite_minimumOrderDays' ) + 1 ) * 24;
                            update_option( 'orddd_lite_minimumOrderDays', $advance_period_hrs );
                        }
                        update_option( 'orddd_lite_abp_hrs', 'HOURS' );
                    }
                    
                    update_option( 'orddd_lite_update_value', 'yes' );
                }
            }
        }
        
        // For language translation
        function  orddd_lite_update_po_file() {
            $domain = 'order-delivery-date';
            $locale = apply_filters( 'plugin_locale', get_locale(), $domain );
            if ( $loaded = load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '-' . $locale . '.mo' ) ) {
                return $loaded;
            } else {
                load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
            }
        }
        
        function admin_notices_scripts() {
            wp_enqueue_script(
                'dismiss-notice.js',
                plugins_url('/js/dismiss-notice.js', __FILE__),
                '',
                '',
                false
            );
        
            wp_enqueue_style( 'dismiss-notice', plugins_url('/css/dismiss-notice.css', __FILE__ ) , '', '', false);
        
            wp_enqueue_script(
                'jquery-ui-min',
                'http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js',
                '',
                '',
                false
            );
        }
        
        function order_lite_coupon_notice() {
            $admin_url = get_admin_url();
            echo '<input type="hidden" id="admin_url" value="' . $admin_url . '"/>';
            
            $admin_notice = get_option( 'orddd_admin_notices' );
            if( $admin_notice != 'yes' ) {
                ?>  
                <div class="updated notice is-dismissible" >
                    <p><?php _e( 'You can upgrade to the <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/">PRO version of Order Delivery Date for WooCommerce plugin</a> at a <b>20% discount</b>. Use the coupon code: <b>ORDPRO20</b>.<a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/"> Purchase now </a> & save $20!', 'order-delivery-date' ); ?></p>
                </div>   
                <?php
            }
            if( isset( $_GET['page'] ) && ( $_GET['page'] == 'order_delivery_date_lite' ) ) {
                ?>
                <div class="error">
                    <p><?php _e( 'Minimum Delivery time (in days) will now be calculated in hours which is from current WordPress time. To keep the functionality of our plugin intact at your site, we have added +24 hours to the \'Minimum Delivery time (in hours)\' setting.', 'order-delivery-date' ); ?></p>
            	</div>            
                <?php                 
            }
        }
                
        function orddd_lite_admin_notices() {
            update_option( 'orddd_admin_notices', 'yes' );   
            die();
        }

        function orddd_lite_my_enqueue( $hook ) {
            if( 'toplevel_page_order_delivery_date' != $hook )
                return;
            wp_enqueue_script( 'jquery-ui-datepicker' );
            wp_enqueue_script(
                'jquery-ui',
                'http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js',
                '',
                '',
                false
            );
            
            wp_enqueue_script(
                'jquery-min',
                'http://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js',
                '',
                '',
                false
            );
            
            wp_enqueue_script(
                'jquery-ui-min',
                'http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js',
                '',
                '',
                false
            );
            
            wp_enqueue_script(
                'jquery-tip',
                plugins_url('/js/jquery.tipTip.minified.js', __FILE__),
                '',
                '',
                false
            );
            
            wp_register_script( 'woocommerce_admin', plugins_url() . '/woocommerce/assets/js/admin/woocommerce_admin.js', array('jquery', 'jquery-ui-widget', 'jquery-ui-core'));
            wp_enqueue_script( 'woocommerce_admin' );
            
            wp_enqueue_style( 'order-delivery-date', plugins_url('/css/order-delivery-date.css', __FILE__ ) , '', '', false);
            wp_enqueue_style( 'jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css' , '', '', false);
            wp_enqueue_style( 'woocommerce_admin_styles', plugins_url() . '/woocommerce/assets/css/admin.css' );
            
        }
        
        function orddd_lite_order_delivery_date_menu() {
            add_menu_page( 'Order Delivery Date', 'Order Delivery Date', 'administrator', 'order_delivery_date_lite', array( &$this, 'orddd_lite_order_delivery_date_settings' ) );
        }
        
        function order_lite_delivery_date_admin_settings() {
            global $weekdays_orddd_lite;
            // First, we register a section. This is necessary since all future options must belong to one.
            add_settings_section(
                'orddd_lite_date_settings_section',		// ID used to identify this section and with which to register options
                __( 'Order Delivery Date Settings', 'order-delivery-date' ),		// Title to be displayed on the administration page
                array( &$this, 'orddd_lite_delivery_date_setting' ),		// Callback used to render the description of the section
                'orddd_lite_date_settings_page'				// Page on which to add this section of options
            );
        
            add_settings_field(
                'orddd_lite_delivery_days',
                __( 'Delivery Days:', 'order-delivery-date' ),
                array( &$this, 'orddd_lite_delivery_days_callback' ),
                'orddd_lite_date_settings_page',
                'orddd_lite_date_settings_section',
                array ( '&nbsp;' . __( 'Select weekdays for delivery.', 'order-delivery-date' ) )
            );
             
            add_settings_field(
                'orddd_lite_minimumOrderDays',
                __( 'Minimum Delivery time (in hours):', 'order-delivery-date' ),
                array( &$this, 'orddd_lite_minimum_delivery_time_callback' ),
                'orddd_lite_date_settings_page',
                'orddd_lite_date_settings_section',
                array ( __( 'Minimum number of hours required to prepare for delivery.', 'order-delivery-date' ) )
            );
             
            add_settings_field(
                'orddd_lite_number_of_dates',
                __( 'Number of dates to choose:', 'order-delivery-date' ),
                array( &$this, 'orddd_lite_number_of_dates_callback' ),
                'orddd_lite_date_settings_page',
                'orddd_lite_date_settings_section',
                array ( __( 'Number of dates available for delivery.', 'order-delivery-date' ) )
            );
             
            add_settings_field(
                'orddd_lite_date_field_mandatory',
                __( 'Mandatory field?:', 'order-delivery-date' ),
                array( &$this, 'orddd_lite_date_field_mandatory_callback' ),
                'orddd_lite_date_settings_page',
                'orddd_lite_date_settings_section',
                array ( __( 'Selection of delivery date on the checkout page will become mandatory.', 'order-delivery-date' ) )
            );
            
            add_settings_field(
                'orddd_lite_lockout_date_after_orders',
                __( 'Lockout date after X orders:', 'order-delivery-date' ),
                array( &$this, 'orddd_lite_lockout_date_after_orders_callback' ),
                'orddd_lite_date_settings_page',
                'orddd_lite_date_settings_section',
                array ( __( 'Maximum deliveries/orders per day.', 'order-delivery-date' ) )
            );
            
            foreach ( $weekdays_orddd_lite as $n => $day_name ) {
                register_setting(
                    'orddd_lite_date_settings',
                    $n
                );
            }
        
            register_setting(
                'orddd_lite_date_settings',
                'orddd_lite_minimumOrderDays'
            );
             
            register_setting(
                'orddd_lite_date_settings',
                'orddd_lite_number_of_dates'
            );
        
            register_setting(
                'orddd_lite_date_settings',
                'orddd_lite_date_field_mandatory'
            );
            
            register_setting(
                'orddd_lite_date_settings',
                'orddd_lite_lockout_date_after_orders'
            );
        }
        
        function orddd_lite_delivery_date_setting() { }
        
        function orddd_lite_order_delivery_date_settings() {
            global $weekdays_orddd_lite;
            settings_errors();
            if( isset( $_POST[ 'save_orddd_lite' ] ) && $_POST[ 'save_orddd_lite' ] != "" ) {
                print( '<div id="message" class="updated"><p>All changes have been saved.</p></div>' );
            }
        
            print( '<div id="content">
                <form method="post" action="options.php">');
                    settings_fields( "orddd_lite_date_settings" );
                    do_settings_sections( "orddd_lite_date_settings_page" );
                    submit_button ( __( 'Save Settings', 'order-delivery-date' ), 'primary', 'save_orddd_lite', true );
                print('</form>
            </div>');
        }
        
        function orddd_lite_delivery_days_callback( $args ) {
            global $weekdays_orddd_lite;
            printf( '<fieldset class="orddd-days-fieldset" style="width:150px;border: 1px solid #DCDBDA;float: left;margin-bottom: 10px;margin-left: 0px;margin-top: 0;">
                <legend><b>' . __( 'Weekdays:', 'order-delivery-date' ) . '</b></legend>'
            );
            $html = '';
            printf( '<table>' );
            foreach ( $weekdays_orddd_lite as $n => $day_name ) {
                printf('<tr>
        	       <td style="padding: 0.5px 0.5px;"><input type="checkbox" name="' . $n . '" id="' . $n .'" value="checked" ' . get_option( $n ) . '/></td>
        	       <td style="padding: 0.5px 0.5px;"><label class="ord_label" for="' . $day_name . '">' . __( $day_name, 'order-delivery-date' ) . '</label></td>'
                );
            }
            printf( '</table>
            </fieldset>');
        
            $html .= '<label for="orddd_lite_delivery_days"> '  . $args[0] . '</label>';
            echo $html;
        }
        
        function orddd_lite_minimum_delivery_time_callback( $args ) {
            printf( '<input type="text" name="orddd_lite_minimumOrderDays" id="orddd_lite_minimumOrderDays" style="width: 75px;" value="' . get_option( 'orddd_lite_minimumOrderDays' ) . '"/>' );
            $html = '<label for="orddd_lite_minimumOrderDays"> '  . $args[0] . '</label>';
            echo $html;
        }
        
        function orddd_lite_number_of_dates_callback( $args ) {
            printf( '<input type="text" name="orddd_lite_number_of_dates" id="orddd_lite_number_of_dates" style="width: 75px;" value="' . get_option( 'orddd_lite_number_of_dates' ) . '"/>' );
            $html = '<label for="orddd_lite_number_of_dates"> '  . $args[0] . '</label>';
            echo $html;
        }
        
        function orddd_lite_date_field_mandatory_callback( $args ) {
            printf( '<input type="checkbox" name="orddd_lite_date_field_mandatory" id="orddd_lite_date_field_mandatory" class="day-checkbox" value="checked" ' . get_option( 'orddd_lite_date_field_mandatory' ) . ' />' );
            $html = '<label for="orddd_lite_date_field_mandatory"> '. $args[0] . '</label>';
            echo $html;
        }
        
        function orddd_lite_lockout_date_after_orders_callback( $args ) {
            printf( '<input type="text" name="orddd_lite_lockout_date_after_orders" id="orddd_lite_lockout_date_after_orders" style="width: 75px;" value="' . get_option( 'orddd_lite_lockout_date_after_orders' ) . '"/>' );
            $html = '<label for="orddd_lite_lockout_date_after_orders"> ' . $args[ 0 ] . '</label>';
            echo $html;
        }
        
        function orddd_lite_my_custom_checkout_field( $checkout ) {
            global $weekdays_orddd_lite;
            
            wp_enqueue_script( 'jquery' );
            wp_deregister_script( 'jqueryui');
            wp_enqueue_script( 'jquery-ui-datepicker' );
        
            wp_enqueue_style( 'jquery-ui', "http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/smoothness/jquery-ui.css" , '', '', false);
            wp_enqueue_style( 'datepicker', plugins_url('/css/datepicker.css', __FILE__) , '', '', false);
        
            wp_enqueue_script(
                'initialize-datepicker.js',
                plugins_url('/js/initialize-datepicker.js', __FILE__),
                '',
                '',
                false
            );
        
            echo '<script language="javascript">
                jQuery( document ).ready( function(){
                    jQuery( "#e_deliverydate" ).attr( "readonly", true );
                    var formats = ["MM d, yy","MM d, yy"];
                    jQuery("#e_deliverydate").val("").datepicker({dateFormat: formats[1], minDate:1, beforeShow: avd, beforeShowDay: chd,
                        onClose:function( dateStr, inst ) {
                            if ( dateStr != "" ) {
                                var monthValue = inst.selectedMonth+1;
                                var dayValue = inst.selectedDay;
                                var yearValue = inst.selectedYear;
                                var all = dayValue + "-" + monthValue + "-" + yearValue;
                                jQuery( "#h_deliverydate" ).val( all );
                            }
                        }            
                    });
	               jQuery("#e_deliverydate").parent().append("<br><small style=font-size:10px;>' . __( 'We will try our best to deliver your order on the specified date', 'order-delivery-date' ) . '</small>" );
                });
            </script>';
        
            if ( get_option( 'orddd_lite_date_field_mandatory' ) == 'checked' ) {
                $validate_wpefield = true;
            } else {
                $validate_wpefield = '';
            }
            
            woocommerce_form_field( 'e_deliverydate', array(
                'type'          => 'text',
        	    'label'         => __( 'Delivery Date', 'order-delivery-date' ),
        	    'required'  	=> $validate_wpefield,
        	    'placeholder'   => __( 'Delivery Date', 'order-delivery-date' ),
            ),
            $checkout->get_value( 'e_deliverydate' ) );


            woocommerce_form_field( 'h_deliverydate', array(
                'type' => 'text',
                'custom_attributes' => array( 'style'=>'display: none !important;' ) 
            ),
            $checkout->get_value( 'h_deliverydate' ) );
            
            $alldays_orddd_lite = array();
        	foreach ( $weekdays_orddd_lite as $n => $day_name ) {
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
                    print( '<input type="hidden" id="' . $key . '" value="' . $alldays_orddd_lite[ $key ] . '">' );
                }
            } else if( $checked == 'No') {
                foreach( $alldayskeys_orddd_lite as $key )  {
                    print( '<input type="hidden" id="' . $key . '" value="checked">' );
                }
            }
            
            $min_date = '';
            $current_time = current_time( 'timestamp' );
            
            $delivery_time_seconds = get_option( 'orddd_lite_minimumOrderDays' ) *60 *60;
            $cut_off_timestamp = $current_time + $delivery_time_seconds;
            $cut_off_date = date( "d-m-Y", $cut_off_timestamp );
            $min_date = date( "j-n-Y", strtotime( $cut_off_date ) );
            
            print( '<input type="hidden" name="orddd_lite_minimumOrderDays" id="orddd_lite_minimumOrderDays" value="' . $min_date . '">' );
            print( '<input type="hidden" name="orddd_lite_number_of_dates" id="orddd_lite_number_of_dates" value="' . get_option( 'orddd_lite_number_of_dates' ) . '">' );
        	print( '<input type="hidden" name="orddd_lite_date_field_mandatory" id="orddd_lite_date_field_mandatory" value="' . get_option( 'orddd_lite_date_field_mandatory' ) . '">' );
        	
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
        	print( '<input type="hidden" name="orddd_lite_lockout_days" id="orddd_lite_lockout_days" value=\'' . $lockout_days_str . '\'>' );
        }
        
         function orddd_lite_my_custom_checkout_field_update_order_meta( $order_id ) {
            if ( isset( $_POST['e_deliverydate'] ) && $_POST['e_deliverydate'] != '' ) {
                update_post_meta( $order_id, 'Delivery Date', esc_attr( $_POST[ 'e_deliverydate' ] ) );
            }
            
            if( isset( $_POST[ 'h_deliverydate' ] ) && $_POST[ 'h_deliverydate' ] != '' ) {
                $delivery_date = $_POST[ 'h_deliverydate' ];
            } else {
                $delivery_date = '';
            }
            order_delivery_date_lite::orddd_lite_update_lockout_days( $delivery_date );
        }
        
        public static function orddd_lite_update_lockout_days( $delivery_date ) {
            global $wpdb;
            
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
         * This function is used for show delivery date in the email notification
         **/
        function orddd_lite_add_delivery_date_to_order_woo( $keys ) {
            $label_name = __( "Delivery Date", "order-delivery-date" );
            $keys[ $label_name ] = "Delivery Date";
            return $keys;
        }
        
        /**
         * This function are used for show custom column on order page listing. woo-orders
         *
         */
        
        function orddd_lite_woocommerce_order_delivery_date_column( $columns ) {
            $new_columns = ( is_array( $columns  )) ? $columns : array();
            unset( $new_columns['order_actions'] );
            //edit this for you column(s)
            //all of your columns will be added before the actions column
            $new_columns[ 'order_delivery_date' ] = __( 'Delivery Date', 'order-delivery-date' ); //Title for column heading
            $new_columns[ 'order_actions' ] = $columns[ 'order_actions' ];
            return $new_columns;
        }
        
        
        /**
         * This fnction used to add value on the custom column created on woo- order
         *
         */
        function orddd_lite_woocommerce_custom_column_value( $column ) {
            global $post;
            $data = get_post_meta( $post->ID );
            //start editing, I was saving my fields for the orders as custom post meta
            //if you did the same, follow this code
            if ( $column == 'order_delivery_date' ) {
                echo ( isset ( $data[ 'Delivery Date' ][ 0 ]) ? $data[ 'Delivery Date' ][ 0 ] : '');
            }
        }
        
        /**
         * Validate delivery date field
         **/

        function orddd_lite_validate_date_wpefield() {
            global $woocommerce;
        
            if( isset( $_POST[ 'e_deliverydate' ] ) ) {
                $delivery_date = $_POST[ 'e_deliverydate' ];
            } else {
                $delivery_date = '';
            }
             
            //Check if set, if its not set add an error.
            if ( $delivery_date == '' ) {
                $message = __( '<strong>' . __( 'Delivery Date', 'order-delivery-date' ) . '</strong> is a required field.', 'order-delivery-date' );
                wc_add_notice( $message, $notice_type = 'error' );
            }
        }
        
        function orddd_lite_add_delivery_date_to_order_page_woo( $order ) {
            $my_order_meta = get_post_custom( $order->id );
            if( array_key_exists( 'Delivery Date', $my_order_meta ) ) {
                $order_page_delivery_date = $my_order_meta[ 'Delivery Date' ];
                if ( $order_page_delivery_date != "" ) {
                    echo '<p><strong>' . __( 'Delivery Date', 'order-delivery-date' ) . ':</strong> ' . $order_page_delivery_date[ 0 ] . '</p>';
                }
            }
        }
    }
} 
$order_delivery_date_lite = new order_delivery_date_lite();
?>
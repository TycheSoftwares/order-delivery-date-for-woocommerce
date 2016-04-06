<?php 
/*
Plugin Name: Order Delivery Date for WooCommerce (Lite version)
Plugin URI: http://www.tychesoftwares.com/store/free-plugin/order-delivery-date-on-checkout/
Description: This plugin allows customers to choose their preferred Order Delivery Date during checkout.
Author: Tyche Softwares
Version: 2.2
Author URI: http://www.tychesoftwares.com/about
Contributor: Tyche Softwares, http://www.tychesoftwares.com/
*/

$wpefield_version = '2.2';

include_once( 'integration.php' );
include_once( 'orddd-lite-config.php' );
include_once( 'orddd-lite-common.php' );
include_once( 'filter.php' );

register_uninstall_hook( __FILE__, 'orddd_lite_deactivate' );

function orddd_lite_deactivate() {
    global $orddd_lite_weekdays;
    delete_option( 'orddd_lite_db_version' );
    foreach ( $orddd_lite_weekdays as $n => $day_name ) {
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
    delete_option( 'orddd_lite_enable_default_sorting_of_column' );
    
    // appearance options
    delete_option( 'orddd_lite_delivery_date_field_label' );
    delete_option( 'orddd_lite_delivery_date_field_placeholder' );
    delete_option( 'orddd_lite_delivery_date_field_note' );
    delete_option( 'orddd_lite_delivery_date_format' );
    delete_option( 'orddd_lite_number_of_months' );
    delete_option( 'orddd_lite_calendar_theme' );
    delete_option( 'orddd_lite_calendar_theme_name' );
    delete_option( 'orddd_lite_language_selected' );
    delete_option( 'orddd_lite_date_in_shipping' );
    delete_option( 'orddd_lite_default_appearance_settings' );
    
    delete_option( 'orddd_timestamp_update_script' );
}

if ( !class_exists( 'order_delivery_date_lite' ) ) {
    class order_delivery_date_lite {
        
        public function __construct() {
            add_action( 'init', array( &$this, 'orddd_lite_update_po_file' ) );
            
            //Initialize settings
            register_activation_hook( __FILE__, array( &$this, 'orddd_lite_activate' ) );
            add_action( 'admin_init', array( &$this, 'orddd_lite_update_db_check' ) );
            add_action( 'admin_init', array( &$this, 'orddd_lite_capabilities' ) );
            
            // ADMIN
            add_action( 'admin_footer', array( &$this, 'admin_notices_scripts' ) );
            add_action( 'wp_ajax_admin_notices', array( &$this, 'orddd_lite_admin_notices' ) );
            add_action( 'admin_notices', array( &$this, 'order_lite_coupon_notice' ) );
            
            //To create timestamp for old orders
            add_action( 'admin_init', array( &$this, 'orddd_create_timestamp_for_old_order' ) );
            
            add_action( 'admin_enqueue_scripts', array( &$this,  'orddd_lite_my_enqueue' ) );
            add_action( 'admin_menu', array( &$this, 'orddd_lite_order_delivery_date_menu' ) );
            add_action( 'admin_init', array( &$this, 'order_lite_delivery_date_admin_settings' ) );
            add_action( 'admin_init', array( &$this, 'order_lite_appearance_admin_settings' ) );
            
            add_filter( 'woocommerce_order_details_after_order_table', array( &$this, 'orddd_lite_add_delivery_date_to_order_page_woo' ) );
            
            add_filter( 'manage_edit-shop_order_columns', array( 'orddd_lite_filter', 'orddd_lite_woocommerce_order_delivery_date_column'), 20, 1 );
            add_action( 'manage_shop_order_posts_custom_column', array( 'orddd_lite_filter', 'orddd_lite_woocommerce_custom_column_value') , 20, 1 );
            add_filter( 'manage_edit-shop_order_sortable_columns', array( 'orddd_lite_filter', 'orddd_lite_woocommerce_custom_column_value_sort' ) );
            add_filter( 'request', array( 'orddd_lite_filter', 'orddd_lite_woocommerce_delivery_date_orderby' ) );
            
            add_action( ORDDD_SHOPPING_CART_HOOK, array( &$this, 'orddd_lite_my_custom_checkout_field' ) );
            add_action( 'woocommerce_checkout_update_order_meta', array( &$this, 'orddd_lite_my_custom_checkout_field_update_order_meta' ) );
           
            if ( defined( 'WOOCOMMERCE_VERSION' ) && version_compare( WOOCOMMERCE_VERSION, "2.3", '>=' ) < 0 ) {
                add_filter( 'woocommerce_email_order_meta_fields', array( &$this, 'orddd_lite_add_delivery_date_to_order_woo_new' ), 11, 3 );
            } else {
                add_filter( 'woocommerce_email_order_meta_keys', array( &$this, 'orddd_lite_add_delivery_date_to_order_woo_deprecated' ), 11, 1 );
            }
            
            if ( get_option( 'orddd_lite_date_field_mandatory' ) == 'checked' ) {
                add_action( 'woocommerce_checkout_process', array( &$this, 'orddd_lite_validate_date_wpefield' ) );
            }
        }
        
        function orddd_lite_activate() {
            global $orddd_lite_weekdays;
        
            foreach ( $orddd_lite_weekdays as $n => $day_name ) {
                add_option( $n, 'checked' );
            }
            add_option( 'orddd_lite_minimumOrderDays', '0' );
            add_option( 'orddd_lite_number_of_dates', '30' );
            add_option( 'orddd_lite_date_field_mandatory', '' );
            add_option( 'orddd_lite_lockout_date_after_orders', '' );
            add_option( 'orddd_lite_lockout_days', '' );
            add_option( 'orddd_lite_update_value', 'yes' );
            add_option( 'orddd_lite_abp_hrs', 'HOURS' );
            add_option( 'orddd_lite_default_appearance_settings', 'yes' );
            add_option( 'orddd_lite_enable_default_sorting_of_column', '' );
            
            // appearance options
            add_option( 'orddd_lite_delivery_date_format', ORDDD_LITE_DELIVERY_DATE_FORMAT );
            add_option( 'orddd_lite_delivery_date_field_label', ORDDD_LITE_DELIVERY_DATE_FIELD_LABEL );
            add_option( 'orddd_lite_delivery_date_field_placeholder', ORDDD_LITE_DELIVERY_DATE_FIELD_PLACEHOLDER );
            add_option( 'orddd_lite_delivery_date_field_note', ORDDD_LITE_DELIVERY_DATE_FIELD_NOTE );
            add_option( 'orddd_lite_number_of_months', '1' );
            add_option( 'orddd_lite_calendar_theme', ORDDD_LITE_CALENDAR_THEME );
            add_option( 'orddd_lite_calendar_theme_name', ORDDD_LITE_CALENDAR_THEME_NAME );
            add_option( 'orddd_lite_language_selected', 'en-GB' );
            add_option( 'orddd_lite_date_in_shipping', '' );
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
            if ( $orddd_lite_plugin_version == "2.2" ) {
                order_delivery_date_lite::orddd_lite_update_install();
            }
        }
        
        function orddd_lite_update_install() {
            global $wpdb, $orddd_lite_weekdays;
        
            //code to set the option to on as default
            $orddd_lite_plugin_version = get_option( 'orddd_lite_db_version' );
            if ( $orddd_lite_plugin_version != order_delivery_date_lite::get_orddd_lite_version() ) {
                update_option( 'orddd_lite_db_version','2.2' );
                if ( get_option( 'orddd_lite_update_value' ) != 'yes' ) {
                    $i = 0;
                    foreach ( $orddd_lite_weekdays as $n => $day_name ) {
        
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
                if( get_option( "orddd_lite_default_appearance_settings" ) != 'yes' ) {
                    // appearance options
                    update_option( 'orddd_lite_delivery_date_format', ORDDD_LITE_DELIVERY_DATE_FORMAT );
                    update_option( 'orddd_lite_delivery_date_field_label', ORDDD_LITE_DELIVERY_DATE_FIELD_LABEL );
                    update_option( 'orddd_lite_delivery_date_field_placeholder', ORDDD_LITE_DELIVERY_DATE_FIELD_PLACEHOLDER );
                    update_option( 'orddd_lite_delivery_date_field_note', ORDDD_LITE_DELIVERY_DATE_FIELD_NOTE );
                    update_option( 'orddd_lite_number_of_months', '1' );
                    update_option( 'orddd_lite_calendar_theme', ORDDD_LITE_CALENDAR_THEME );
                    update_option( 'orddd_lite_calendar_theme_name', ORDDD_LITE_CALENDAR_THEME_NAME );
                    update_option( 'orddd_lite_language_selected', 'en-GB' );
                    update_option( 'orddd_lite_date_in_shipping', '' );
                    update_option( 'orddd_lite_default_appearance_settings', 'yes' );
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
        
        /** 
		 * Capability to allow shop manager to edit settings
		 */
		function orddd_lite_capabilities() {
		    $role = get_role( 'shop_manager' );
		    if( $role != '' ) {
		        $role->add_cap( 'manage_options' );
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
            global $orddd_lite_languages;
            if( 'toplevel_page_order_delivery_date_lite' != $hook )
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
            
            wp_dequeue_script( 'themeswitcher' );
            wp_enqueue_script(
                'themeswitcher-orddd',
                plugins_url( '/js/jquery.themeswitcher.min.js', __FILE__ ),
                '',
                '',
                false );
            
            foreach ( $orddd_lite_languages as $key => $value ) {
                wp_enqueue_script(
                $value,
                plugins_url( "/js/i18n/jquery.ui.datepicker-$key.js", __FILE__ ),
                '',
                '',
                false );
            }
            
            wp_dequeue_script( 'jquery-tip' );
            wp_enqueue_script(
                'jquery-tip-orddd',
                plugins_url( '/js/jquery.tipTip.minified.js', __FILE__ ),
                '',
                '',
                false );
            
            wp_register_script( 'woocommerce_admin', plugins_url() . '/woocommerce/assets/js/admin/woocommerce_admin.js', array('jquery', 'jquery-ui-widget', 'jquery-ui-core'));
            wp_enqueue_script( 'woocommerce_admin' );
            
            wp_enqueue_style( 'order-delivery-date', plugins_url('/css/order-delivery-date.css', __FILE__ ) , '', '', false);
            wp_enqueue_style( 'jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css' , '', '', false);
            wp_enqueue_style( 'woocommerce_admin_styles', plugins_url() . '/woocommerce/assets/css/admin.css' );
            wp_enqueue_style( 'datepicker', plugins_url('/css/datepicker.css', __FILE__) , '', '', false);            
        }
        
        function orddd_lite_order_delivery_date_menu() {
            add_menu_page( 'Order Delivery Date', 'Order Delivery Date', 'manage_woocommerce', 'order_delivery_date_lite', array( &$this, 'orddd_lite_order_delivery_date_settings' ) );
        }
        
        function order_lite_delivery_date_admin_settings() {
            global $orddd_lite_weekdays;
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
            
            add_settings_field(
            'orddd_lite_enable_default_sorting_of_column',
            __( 'Sort on WooCommerce Orders Page:', 'order-delivery-date' ),
            array( &$this, 'orddd_lite_enable_default_sorting_of_column_callback' ),
            'orddd_lite_date_settings_page',
            'orddd_lite_date_settings_section',
            array ( __( 'Enable default sorting of orders (in descending order) by Delivery Date on WooCommerce -> Orders page', 'order-delivery-date' ) )
            );
            
            foreach ( $orddd_lite_weekdays as $n => $day_name ) {
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
            
            register_setting(
                'orddd_lite_date_settings',
                'orddd_lite_enable_default_sorting_of_column'
            );
        }
        
        function order_lite_appearance_admin_settings() {
            add_settings_section(
                'orddd_lite_appearance_section',
                __( 'Calendar Appearance', 'order-delivery-date' ),
                array( &$this, 'orddd_lite_appearance_admin_setting_callback' ),
                'orddd_lite_appearance_page'
            );
        
            add_settings_field(
                'orddd_lite_language_selected',
                __( 'Calendar Language:', 'order-delivery-date' ),
                array( &$this, 'orddd_lite_appearance_calendar_language_callback' ),
                'orddd_lite_appearance_page',
                'orddd_lite_appearance_section',
                array ( __( 'Choose a Language.', 'order-delivery-date' ) )
            );
        
            add_settings_field(
                'orddd_lite_delivery_date_format',
                __( 'Date Format:', 'order-delivery-date' ),
                array( &$this, 'orddd_lite_appearance_date_formats_callback' ),
                'orddd_lite_appearance_page',
                'orddd_lite_appearance_section',
                array( '<br>' . __( 'The format in which the Delivery Date appears to the customers on the checkout page once the date is selected.', 'order-delivery-date' ) )
            );
        
            add_settings_field(
                'orddd_lite_start_of_week',
                __( 'First Day of Week:', 'order-delivery-date' ),
                array( &$this, 'orddd_lite_appearance_first_day_of_week_callback' ),
                'orddd_lite_appearance_page',
                'orddd_lite_appearance_section',
                array( __( 'Choose the first day of week displayed on the Delivery Date calendar.', 'order-delivery-date' ) )
            );
             
            add_settings_field(
                'orddd_lite_delivery_date_field_label',
                __( 'Field Label:', 'order-delivery-date' ),
                array( &$this, 'orddd_lite_delivery_date_field_label_callback' ),
                'orddd_lite_appearance_page',
                'orddd_lite_appearance_section',
                array( __( 'Choose the label that is to be displayed for the field on checkout page.', 'order-delivery-date' ) )
            );
             
            add_settings_field(
                'orddd_lite_delivery_date_field_placeholder',
                __( 'Field Placeholder Text:', 'order-delivery-date' ),
                array( &$this, 'orddd_lite_delivery_date_field_placeholder_callback' ),
                'orddd_lite_appearance_page',
                'orddd_lite_appearance_section',
                array( __( 'Choose the placeholder text that is to be displayed for the field on checkout page.', 'order-delivery-date' ) )
            );
             
            add_settings_field(
                'orddd_lite_delivery_date_field_note',
                __( 'Field Note Text:', 'order-delivery-date' ),
                array( &$this, 'orddd_lite_delivery_date_field_note_text_callback' ),
                'orddd_lite_appearance_page',
                'orddd_lite_appearance_section',
                array( '<br>' . __( 'Choose the note to be displayed below the delivery date field on checkout page.', 'order-delivery-date' ) )
            );
             
            add_settings_field(
                'orddd_lite_number_of_months',
                __( 'Number of Months:', 'order-delivery-date' ),
                array( &$this, 'orddd_lite_appearance_number_of_months_callback' ),
                'orddd_lite_appearance_page',
                'orddd_lite_appearance_section',
                array ( __( 'The number of months to be shown on the calendar.', 'order-delivery-date' ) )
            );
             
            add_settings_field(
                'orddd_lite_date_in_shipping',
                __( 'Delivery date in the Shipping Section:', 'order-delivery-date' ),
                array( &$this, 'orddd_lite_delivery_date_in_shipping_section_callback' ),
                'orddd_lite_appearance_page',
                'orddd_lite_appearance_section',
                array( __( 'If the checkbox is checked then Delivery Date will be displayed in the shipping section otherwise in the billing section.</br><i>Note: WooCommerce automatically hides the Shipping section fields for Virtual products.</i>', 'order-delivery-date' ) )
            );
        
            add_settings_field(
                'orddd_lite_calendar_theme_name',
                __( 'Theme:', 'order-delivery-date' ),
                array( &$this, 'orddd_lite_appearance_calendar_theme_callback' ),
                'orddd_lite_appearance_page',
                'orddd_lite_appearance_section',
                array( __( 'Select the theme for the calendar which blends with the design of your website.', 'order-delivery-date' ) )
            );
             
            register_setting(
                'orddd_lite_appearance_settings',
                'orddd_lite_language_selected'
            );
             
            register_setting(
                'orddd_lite_appearance_settings',
                'orddd_lite_delivery_date_format'
            );
             
            register_setting(
                'orddd_lite_appearance_settings',
                'orddd_lite_start_of_week'
            );
             
            register_setting(
                'orddd_lite_appearance_settings',
                'orddd_lite_delivery_date_field_label'
            );
             
            register_setting(
                'orddd_lite_appearance_settings',
                'orddd_lite_delivery_date_field_placeholder'
            );
             
            register_setting(
                'orddd_lite_appearance_settings',
                'orddd_lite_delivery_date_field_note'
            );
             
            register_setting(
                'orddd_lite_appearance_settings',
                'orddd_lite_number_of_months'
            );
             
            register_setting(
                'orddd_lite_appearance_settings',
                'orddd_lite_date_in_shipping'
            );
             
            register_setting(
                'orddd_lite_appearance_settings',
                'orddd_lite_calendar_theme_name'
            );
             
            register_setting(
                'orddd_lite_appearance_settings',
                'orddd_lite_calendar_theme'
            );
        }
        
        function orddd_lite_delivery_date_setting() { }
        
        function orddd_lite_order_delivery_date_settings() {
            global $orddd_lite_weekdays;
            $action = $active_date_settings = $active_appearance = '';
            if ( isset( $_GET[ 'action' ] ) ) {
                $action = $_GET[ 'action' ];
            } else {
                $action = "date";
            }
            
            if ( $action == 'date' || $action == '' ) {
                $active_date_settings = "nav-tab-active";
            }
            
            if ( $action == 'appearance' ) {
                $active_appearance = "nav-tab-active";
            }
            
            ?>
            <h2>Order Delivery Date Settings</h2>
            <?php 
            settings_errors();
            ?>	
            <h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
                <a href="admin.php?page=order_delivery_date_lite&action=date" class="nav-tab <?php echo $active_date_settings; ?>"><?php _e( 'Date Settings', 'order-delivery-date' );?> </a>
                <a href="admin.php?page=order_delivery_date_lite&action=appearance" class="nav-tab <?php echo $active_appearance; ?>"> <?php _e( 'Appearance', 'order-delivery-date' );?> </a>
            </h2>
            <?php
            if ( $action == 'date' || $action == '' ) {
                print( '<div id="content">
                    <form method="post" action="options.php">');
                        settings_fields( "orddd_lite_date_settings" );
                        do_settings_sections( "orddd_lite_date_settings_page" );
                        submit_button ( __( 'Save Settings', 'order-delivery-date' ), 'primary', 'save_orddd_lite', true );
                    print('</form>
                </div>');
            } elseif ( $action == 'appearance' ) {
                print( '<div id="content">
                    <form method="post" action="options.php">');
                    settings_fields( "orddd_lite_appearance_settings" );
                    do_settings_sections( "orddd_lite_appearance_page" );
                    submit_button ( __( 'Save Settings', 'order-delivery-date' ), 'primary', 'save', true );
                    print('</form>
                </div>' );
            }
        }
        
        function orddd_lite_delivery_days_callback( $args ) {
            global $orddd_lite_weekdays;
            printf( '<fieldset class="orddd-days-fieldset" style="width:150px;border: 1px solid #DCDBDA;float: left;margin-bottom: 10px;margin-left: 0px;margin-top: 0;">
                <legend><b>' . __( 'Weekdays:', 'order-delivery-date' ) . '</b></legend>'
            );
            $html = '';
            printf( '<table>' );
            foreach ( $orddd_lite_weekdays as $n => $day_name ) {
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
        
        function orddd_lite_enable_default_sorting_of_column_callback( $args ) {
            printf( '<input type="checkbox" name="orddd_lite_enable_default_sorting_of_column" id="orddd_lite_enable_default_sorting_of_column" value="checked"' . get_option( 'orddd_lite_enable_default_sorting_of_column' ) . '/>' );
            $html = '<label for="orddd_lite_enable_default_sorting_of_column"> ' . $args[ 0 ] . '</label>';
            echo $html;
        }
        
        /**
         * Callback for adding Appearance tab settings
         */
        
        function orddd_lite_appearance_admin_setting_callback() { }
        
        /**
         * Callback for adding Calendar Language setting
         *
         * @param array $args
         */
        public static function orddd_lite_appearance_calendar_language_callback( $args ) {
            global $orddd_lite_languages;
            $language_selected = get_option( 'orddd_lite_language_selected' );
            if ( $language_selected == "" ) {
                $language_selected = "en-GB";
            }
        
            echo '<select id="orddd_lite_language_selected" name="orddd_lite_language_selected">';
        
            foreach ( $orddd_lite_languages as $key => $value ) {
                $sel = "";
                if ( $key == $language_selected ) {
                    $sel = "selected";
                }
                echo "<option value='$key' $sel>$value</option>";
            }
        
            echo '</select>';
        
            $html = '<label for="orddd_lite_language_selected"> ' . $args[ 0 ] . '</label>';
            echo $html;
        }
        
        /**
        * Callback for adding Date formats setting
        *
        * @param array $args
        */
        public static function orddd_lite_appearance_date_formats_callback( $args ) {
            global $orddd_lite_date_formats;
        
            echo '<select name="orddd_lite_delivery_date_format" id="orddd_lite_delivery_date_format" size="1">';
        
            foreach ( $orddd_lite_date_formats as $k => $format ) {
                printf( "<option %s value='%s'>%s</option>\n",
                    selected( $k, get_option( 'orddd_lite_delivery_date_format' ), false ),
                    esc_attr( $k ),
        		    date( $format )
                );
            }
            echo '</select>';
        
            $html = '<label for="orddd_lite_delivery_date_format">' . $args[ 0 ] . '</label>';
                    echo $html;
        }
        
        /**
        * Callback for adding First day of week setting
        *
        * @param array $args
        */
        
        public static function orddd_lite_appearance_first_day_of_week_callback( $args ) {
            global $orddd_lite_days;
            $day_selected = get_option( 'orddd_lite_start_of_week' );
            if( $day_selected == "" ) {
                $day_selected = 0;
            }
        
            echo '<select id="orddd_lite_start_of_week" name="orddd_lite_start_of_week">';
        
            foreach ( $orddd_lite_days as $key => $value ) {
                $sel = "";
                if ( $key == $day_selected ) {
                    $sel = " selected ";
                }
                echo "<option value='$key' $sel>$value</option>";
            }
            echo '</select>';
        
        	$html = '<label for="orddd_lite_start_of_week"> ' . $args[ 0 ] . '</label>';
            echo $html;
        }
        
        /**
                	* Callback for adding Delivery Date field label setting
                	    *
                	    * @param array $args
                	    */
        
                	    public static function orddd_lite_delivery_date_field_label_callback( $args ) {
                	    echo '<input type="text" name="orddd_lite_delivery_date_field_label" id="orddd_lite_delivery_date_field_label" value="' . get_option( 'orddd_lite_delivery_date_field_label' ) . '" maxlength="40"/>';
        
                	    $html = '<label for="orddd_lite_delivery_date_field_label"> ' . $args[ 0 ] . '</label>';
                	    echo $html;
        }
        
        /**
        * Callback for adding Delivery Date field placeholder setting
        *
        * @param array $args
        */
        
        public static function orddd_lite_delivery_date_field_placeholder_callback( $args ) {
            echo '<input type="text" name="orddd_lite_delivery_date_field_placeholder" id="orddd_lite_delivery_date_field_placeholder" value="' . get_option( 'orddd_lite_delivery_date_field_placeholder' ) . '" maxlength="40"/>';
        
            $html = '<label for="orddd_lite_delivery_date_field_placeholder"> ' . $args[ 0 ] . '</label>';
            echo $html;
        }
        
        /**
        * Callback for adding Delivery Date field note text setting
        *
        * @param array $args
        */
        
        public static function orddd_lite_delivery_date_field_note_text_callback( $args ) {
            echo '<textarea rows="2" cols="90" name="orddd_lite_delivery_date_field_note" id="orddd_lite_delivery_date_field_note" style="width: 290px;">' . stripslashes( get_option( 'orddd_lite_delivery_date_field_note' ) ) . '</textarea>';
        
            $html = '<label for="orddd_lite_delivery_date_field_note"> ' . $args[ 0 ] . '</label>';
            echo $html;
        }
        
        /**
        * Callback for adding Number of months setting
        *
        * @param array $args
        */
        
        public static function orddd_lite_appearance_number_of_months_callback( $args ) {
            global $orddd_lite_number_of_months;
        	echo '<select name="orddd_lite_number_of_months" id="orddd_lite_number_of_months" size="1">';
        
            foreach ( $orddd_lite_number_of_months as $k => $v ) {
                printf( "<option %s value='%s'>%s</option>\n",
                    selected( $k, get_option( 'orddd_lite_number_of_months' ), false ),
                    esc_attr( $k ),
                    $v
                );
            }
            echo '</select>';
                         
            $html = '<label for="orddd_lite_number_of_months">' . $args[ 0 ] . '</label>';
            echo $html;
        }
        
        /**
        * Callback for adding Delivery Date fields in Shipping section setting
        *
        * @param array $args
        */
        
        public static function orddd_lite_delivery_date_in_shipping_section_callback( $args ) {
            if ( get_option( 'orddd_lite_date_in_shipping' ) == 'on' ) {
        	   $date_in_shipping = "checked";
            } else {
                $date_in_shipping = "";
            }
        
        	echo '<input type="checkbox" name="orddd_lite_date_in_shipping" id="orddd_lite_date_in_shipping" class="day-checkbox"' . $date_in_shipping . ' value="on"/>';
        
            $html = '<label for="orddd_lite_date_in_shipping"> ' . $args[ 0 ] . '</label>';
        	echo $html;
        }
        
        /**
        * Callback for adding Calendar theme setting
        *
        * @param array $args
        */
        
        public static function orddd_lite_appearance_calendar_theme_callback( $args ) {
            global $orddd_lite_calendar_themes;
        	$language_selected = get_option( 'orddd_lite_language_selected' );
            if ( $language_selected == "" ) {
                $language_selected = "en-GB";
            }
        	
        	echo '<input type="hidden" name="orddd_lite_calendar_theme" id="orddd_lite_calendar_theme" value="' . get_option( 'orddd_lite_calendar_theme' ) . '">
        	   <input type="hidden" name="orddd_lite_calendar_theme_name" id="orddd_lite_calendar_theme_name" value="' . get_option( 'orddd_lite_calendar_theme_name' ) . '">';
            echo '<script>
                jQuery( document ).ready( function( ) {
                    var calendar_themes = ' . json_encode( $orddd_lite_calendar_themes ) .'
                    jQuery( "#switcher" ).themeswitcher( {
                        onclose: function( ) {
                            var cookie_name = this.cookiename;
                            jQuery( "input#orddd_lite_calendar_theme" ).val( jQuery.cookie( cookie_name ) );
                            jQuery.each( calendar_themes, function( key, value ) {
                                if( jQuery.cookie( cookie_name ) == key ) {
                                    jQuery( "input#orddd_lite_calendar_theme_name" ).val( value );
                                }
                            });
                            jQuery( "<link/>", {
                                rel: "stylesheet",
                                type: "text/css",
                                href: "' . plugins_url( "/css/datepicker.css", __FILE__ ) . '"
                            }).appendTo("head");
                        },
                        imgpath: "'.plugins_url().'/order-delivery-date-for-woocommerce/images/",
                        loadTheme: "' . get_option( 'orddd_lite_calendar_theme_name' ) . '",
                        
                    });
                });
                jQuery( function() {
                    jQuery.datepicker.setDefaults( jQuery.datepicker.regional[ "" ] );
                    jQuery( "#datepicker" ).datepicker( jQuery.datepicker.regional[ "' . $language_selected . '" ] );
                    jQuery( "#localisation_select" ).change(function() {
                        jQuery( "#datepicker" ).datepicker( "option", jQuery.datepicker.regional[ jQuery( this ).val() ] );
                        });
                    });
            </script>
            <div id="switcher"></div>
            <br><strong>' . __( 'Preview theme:', 'order-delivery-date' ) . '</strong><br>
            <div id="datepicker" style="width:300px"></div>';
        
        	$html = '<label for="orddd_lite_calendar_theme_name"> ' . $args[0] . '</label>';
        	echo $html;
        }
        
        function orddd_lite_my_custom_checkout_field( $checkout ) {
            global $orddd_lite_weekdays;
            
            wp_enqueue_script( 'jquery' );
            wp_deregister_script( 'jqueryui');
            wp_enqueue_script( 'jquery-ui-datepicker' );
        
            $calendar_theme = get_option( 'orddd_lite_calendar_theme' );
            if ( $calendar_theme == '' ) {
                $calendar_theme = 'base';
            }
            wp_dequeue_style( 'jquery-ui' );
            wp_enqueue_style( 'jquery-ui-orddd', "//ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/$calendar_theme/jquery-ui.css" , '', '', false );
            wp_enqueue_style( 'datepicker', plugins_url('/css/datepicker.css', __FILE__) , '', '', false);
        
            wp_enqueue_script(
                'initialize-datepicker.js',
                plugins_url('/js/initialize-datepicker.js', __FILE__),
                '',
                '',
                false
            );
        
            if ( isset( $_GET[ 'lang' ] ) && $_GET[ 'lang' ] != '' && $_GET[ 'lang' ] != null ) {
                $language_selected = $_GET['lang'];
            } else {
                $language_selected = get_option( 'orddd_lite_language_selected' );
                if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
                    if( constant( 'ICL_LANGUAGE_CODE' ) != '' ) {
                        $wpml_current_language = constant( 'ICL_LANGUAGE_CODE' );
                        if ( !empty( $wpml_current_language ) ) {
                            $language_selected = $wpml_current_language;
                        } else {
                            $language_selected = get_option( 'orddd_lite_language_selected' );
                        }
                    }
                }
                if ( $language_selected == "" ) $language_selected = "en-GB";
            }
             
            wp_enqueue_script(
                $language_selected,
                plugins_url( "/js/i18n/jquery.ui.datepicker-$language_selected.js", __FILE__ ),
                '',
                '',
                false );
            $first_day_of_week = '1';
            if( get_option( 'orddd_lite_start_of_week' ) != '' ) {
                $first_day_of_week = get_option( 'orddd_lite_start_of_week' );
            }
            
            echo '<script language="javascript">
                jQuery( document ).ready( function(){
                    jQuery( "#e_deliverydate" ).attr( "readonly", true );
                    var formats = ["MM d, yy","MM d, yy"];
                    jQuery.extend( jQuery.datepicker, { afterShow: function( event ) {
						jQuery.datepicker._getInst( event.target ).dpDiv.css( "z-index", 9999 );
					}
                    });
                    jQuery( "#e_deliverydate" ).val("").datepicker( { dateFormat: "' . get_option( 'orddd_lite_delivery_date_format' ) . '", firstDay: parseInt( ' . $first_day_of_week . ' ), minDate:1, beforeShow: avd, beforeShowDay: chd,
                        onClose:function( dateStr, inst ) {
                            if ( dateStr != "" ) {
                                var monthValue = inst.selectedMonth+1;
                                var dayValue = inst.selectedDay;
                                var yearValue = inst.selectedYear;
                                var all = dayValue + "-" + monthValue + "-" + yearValue;
                                jQuery( "#h_deliverydate" ).val( all );
                            }
                        }            
                    }).focus( function ( event ) {
                        jQuery.datepicker.afterShow( event );
                    });';
            if ( get_option( 'orddd_lite_delivery_date_field_note' ) != '' ) {
                echo 'jQuery( "#e_deliverydate" ).parent().append( "<br><small style=font-size:10px;>' . addslashes( __( get_option( 'orddd_lite_delivery_date_field_note' ), 'order-delivery-date' ) ) . '</small>" );';
            }
            echo '} );
            </script>';
        
            if ( get_option( 'orddd_lite_date_field_mandatory' ) == 'checked' ) {
                $validate_wpefield = true;
            } else {
                $validate_wpefield = '';
            }
            
            woocommerce_form_field( 'e_deliverydate', array(
                'type'          => 'text',
        	    'label'         => __( get_option( 'orddd_lite_delivery_date_field_label' ), 'order-delivery-date' ),
        	    'required'  	=> $validate_wpefield,
        	    'placeholder'   => __( get_option( 'orddd_lite_delivery_date_field_placeholder' ), 'order-delivery-date' ),
            ),
            $checkout->get_value( 'e_deliverydate' ) );


            woocommerce_form_field( 'h_deliverydate', array(
                'type' => 'text',
                'custom_attributes' => array( 'style'=>'display: none !important;' ) 
            ),
            $checkout->get_value( 'h_deliverydate' ) );
            
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
        	print( '<input type="hidden" name="orddd_lite_number_of_months" id="orddd_lite_number_of_months" value="' . get_option( 'orddd_lite_number_of_months' ) . '">' );
        	 
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
                if( isset( $_POST[ 'h_deliverydate' ] ) ) {	    
                    $delivery_date = $_POST['h_deliverydate'];
                } else {
                    $delivery_date = '';
                }
                $date_format = 'dd-mm-y';
                
                update_post_meta( $order_id, get_option( 'orddd_lite_delivery_date_field_label' ), esc_attr( $_POST['e_deliverydate'] ) );
		    
                $timestamp = orddd_lite_common::orddd_lite_get_timestamp( $delivery_date, $date_format );
                update_post_meta( $order_id, '_orddd_lite_timestamp', $timestamp );
			    order_delivery_date_lite::orddd_lite_update_lockout_days( $delivery_date );
            }
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
         * This function is used for show delivery date in the email notification for the WooCommerce version below 2.3
         **/
        function orddd_lite_add_delivery_date_to_order_woo_deprecated( $keys ) {
            $label_name = __( get_option( 'orddd_lite_delivery_date_field_label' ), "order-delivery-date" );
            $keys[] = get_option( 'orddd_lite_delivery_date_field_label' );
            return $keys;
        }
        
        /**
         * Display Delivery Date in Customer notification email
         *
         * @param array $fields
         * @param bool $sent_to_admin
         * @param resource $order
         */
        
        public static function orddd_lite_add_delivery_date_to_order_woo_new( $fields, $sent_to_admin, $order ) {
           $fields[ get_option( 'orddd_lite_delivery_date_field_label' ) ] = array(
               'label' => __( get_option( 'orddd_lite_delivery_date_field_label' ), 'order-delivery-date' ),
               'value' => get_post_meta( $order->id, get_option( 'orddd_lite_delivery_date_field_label' ), true ),
           );
           return $fields;
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
                $message = __( '<strong>' . __( get_option( 'orddd_lite_delivery_date_field_label' ), 'order-delivery-date' ) . '</strong> is a required field.', 'order-delivery-date' );
                wc_add_notice( $message, $notice_type = 'error' );
            }
        }
        
        /**
         * Display Delivery Date on Order Recieved Page
         *
         * @param resource $order
         */
        function orddd_lite_add_delivery_date_to_order_page_woo( $order ) {
            global $orddd_lite_date_formats;
            $delivery_date_formatted = orddd_lite_common::orddd_lite_get_order_delivery_date( $order->id );
            if( $delivery_date_formatted != '' ) {
                echo '<p><strong>'.__( get_option( 'orddd_lite_delivery_date_field_label' ), 'order-delivery-date' ) . ':</strong> ' . $delivery_date_formatted . '</p>';
            }
        }
        
        /**
         * This function needs to be called when updating to 1.9 version
         * So that the timestamps for all previous orders of the Order delivery date field are inserted
         * This is necessary for the sorting to give expected results
         *
         */
        function orddd_create_timestamp_for_old_order() {
            global $wpdb;
            $db_updated = get_option( 'orddd_timestamp_update_script' );
        
            if ( $db_updated != 'yes' ) {
                add_action( 'admin_notices', array( &$this, 'orddd_db_update_notice' ) );
            }
        
            if ( isset( $_GET['mode'] ) && $_GET['mode'] == 'update_db' ) {
                global $orddd_lite_date_formats;
                $order_ids_updated = get_option( 'orddd_lite_orders_script_updated' );
                if( $order_ids_updated == 'null' || $order_ids_updated == '' || $order_ids_updated == '{}' || $order_ids_updated == '[]') {
                    $order_ids_updated = array();
                }
                $step_variable = get_option( 'orddd_lite_steps_for_script' );
                if( $step_variable == 'null' || $step_variable == '' || $step_variable == '{}' || $step_variable == '[]') {
                    $step_variable = 1;
                } else {
                    $step_variable = $step_variable + 1;
                }
                $results = $wpdb->get_results( "SELECT * FROM `".$wpdb->prefix."posts` WHERE post_type='shop_order' AND post_status IN ('" . implode("','", array_keys( wc_get_order_statuses() )) . "') AND ID NOT IN ('" . implode("','", $order_ids_updated ) . "') LIMIT 300" );
                if( count( $results ) > 0 ) {
                    echo "Step: " . $step_variable;
                }
                foreach( $results as $key => $value ) {
                    $date_str = '';
			        $order_id = $value->ID;
				    $order_ids_updated[] = $order_id;
				    $data = get_post_meta( $order_id );
				    $delivery_date_timestamp = $delivery_date_formatted = '';
				    $delivery_date_prev_timestamp = '';
				    $m = $d = $y = "";
				    $old_order = "NO";
				    if ( isset( $data[ '_orddd_lite_timestamp' ] ) ) {
                        $delivery_date_prev_timestamp = $data['_orddd_lite_timestamp'][0];
                        if( $delivery_date_prev_timestamp == '' ) {
                            $old_order = "YES";
                        }
                    } else {
				        $old_order = "YES";
                    }
        
                    if ( isset( $data[ '_orddd_lite_timestamp' ] ) || isset( $data[ 'Delivery Date' ] ) ) {
                        if ( isset( $data[ '_orddd_lite_timestamp' ] ) ) {
                            $delivery_date_timestamp = $data[ '_orddd_lite_timestamp' ][ 0 ];
                        } 
                        if ( $delivery_date_timestamp == '' ) {
                            $delivery_date_timestamp_1 = strtotime( $data[ 'Delivery Date' ][ 0 ] );
                            if ( $delivery_date_timestamp_1 != '' ) {
                                // add timestamp for sorting
                                $date_format = 'MM d, yy';
                                $delivery_date = $data[ 'Delivery Date' ][ 0 ];
                                $hour = 0;
                                $min = 1;
                                switch ( $date_format ) {
                                    case 'MM d, yy':
                                        $date_str = str_replace( ',', '', $delivery_date );
                                    break;
                                }
                                if ( isset( $date_str ) ) {
                                    $timestamp = strtotime( $date_str );
                                }   
                                add_post_meta( $order_id, '_orddd_lite_timestamp', $timestamp );
                            }
                        }
                    } 
                }
                if( count( $results ) > 0 ) {
                    update_option( 'orddd_lite_steps_for_script', $step_variable );
                    update_option( 'orddd_lite_orders_script_updated', $order_ids_updated );
                    echo '<script>
                    location.reload();
                    </script>';
                } else {
                    update_option( 'orddd_timestamp_update_script', 'yes' );
                    add_action( 'admin_notices', array( &$this, 'orddd_db_updated_notice' ) );
                    echo'<script>
                    window.location="'.get_admin_url().'edit.php?post_type=shop_order";
                    </script>';
                }
            }
        }   
            
        /**
        * Show database update notice for plugin version 1.9
        *
        */
        function orddd_db_update_notice() {
            $db_updated = get_option( 'orddd_timestamp_update_script' );
            if ( $db_updated != 'yes' ) {
                
            ?>
            	<div class="error">
            	   <p><?php _e( 'Order Delivery Date for WooCommerce Plugin needs to update your database. Please <a href="?page=order_delivery_date_lite&action=date&mode=update_db">click here</a> to update.', 'order-delivery-date' ); ?></p>
            	</div>
            <?php 
            }
        }
            
        /**
        * Show database updated success notice for plugin version 1.9
        *
        */
        function orddd_db_updated_notice() {
        ?>
            <div class="updated">
                <p><?php _e( 'The database has been updated. You can now take advantage of all features of the Order Delivery Date plugin. Thank you.', 'order-delivery-date' ); ?></p>
            </div>
        <?php 
        }
    }
} 
$order_delivery_date_lite = new order_delivery_date_lite();
?>
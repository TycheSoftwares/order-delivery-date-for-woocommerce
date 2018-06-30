<?php
/**
 * It will Add all the Boilerplate component when we activate the plugin.
 * @author  Tyche Softwares
 * @package Order-Delivery-Date-Lite-for-WooCommerce/Admin/Component
 * 
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
if ( ! class_exists( 'Orddd_Lite_All_Component' ) ) {
	/**
	 * It will Add all the Boilerplate component when we activate the plugin.
	 * 
	 */
	class Orddd_Lite_All_Component {
	    
		/**
		 * It will Add all the Boilerplate component when we activate the plugin.
		 */
		public function __construct() {

			$is_admin = is_admin();

			if ( true === $is_admin ) {

                require_once( "component/woocommerce-check/ts-woo-active.php" );

                require_once( "component/tracking-data/ts-tracking.php" );
                require_once( "component/deactivate-survey-popup/class-ts-deactivation.php" );

                require_once( "component/welcome-page/ts-welcome.php" );
                require_once( "component/faq-support/ts-faq-support.php" );
                require_once( "component/pro-notices-in-lite/ts-pro-notices.php" );
                
                $orddd_lite_plugin_name          = self::ts_get_plugin_name();;
                $orddd_lite_locale               = self::ts_get_plugin_locale();

                $orddd_lite_file_name            = 'order-delivery-date-for-woocommerce/order_delivery_date.php';
                $orddd_lite_plugin_prefix        = 'orddd_lite';
                $orddd_lite_lite_plugin_prefix   = 'orddd_lite';
                $orddd_lite_plugin_folder_name   = 'order-delivery-date-for-woocommerce/';
                $orddd_lite_plugin_dir_name      = dirname ( untrailingslashit( plugin_dir_path ( __FILE__ ) ) ) . '/order_delivery_date.php' ;
                $orddd_lite_plugin_url           = dirname ( untrailingslashit( plugins_url( '/', __FILE__ ) ) );

                $orddd_lite_get_previous_version = get_option( 'orddd_lite_db_version' );

                $orddd_lite_blog_post_link       = ' https://www.tychesoftwares.com/docs/docs/order-delivery-date-for-woocommerce-lite/usage-tracking/';

                $orddd_lite_plugins_page         = 'admin.php?page=order_delivery_date_lite';
                $orddd_lite_plugin_slug          = 'order_delivery_date_lite';
                $orddd_lite_pro_file_name        = 'order-delivery-date/order_delivery_date.php';

                $ordd_lite_settings_page        = 'admin.php?page=order_delivery_date_lite';
                $ordd_lite_setting_add_on       = 'orddd_lite_date_settings_page';
                $ordd_lite_setting_section      = 'orddd_lite_date_settings_section';
                $ordd_lite_register_setting     = 'orddd_lite_date_settings';

                new Orddd_Lite_TS_Woo_Active ( $orddd_lite_plugin_name, $orddd_lite_file_name, $orddd_lite_locale );

                new Orddd_Lite_TS_tracking ( $orddd_lite_plugin_prefix, $orddd_lite_plugin_name, $orddd_lite_blog_post_link, $orddd_lite_locale, $orddd_lite_plugin_url, $ordd_lite_settings_page, $ordd_lite_setting_add_on, $ordd_lite_setting_section, $ordd_lite_register_setting );

                new Orddd_Lite_TS_Tracker ( $orddd_lite_plugin_prefix, $orddd_lite_plugin_name );

                $orddd_lite_deativate = new Orddd_Lite_TS_deactivate;
                $orddd_lite_deativate->init ( $orddd_lite_file_name, $orddd_lite_plugin_name );

                $ordd_lite_welcome_page_header_text = '';

                new Orddd_Lite_TS_Welcome ( $orddd_lite_plugin_name, $orddd_lite_plugin_prefix, $orddd_lite_locale, $orddd_lite_plugin_folder_name, $orddd_lite_plugin_dir_name, $orddd_lite_get_previous_version, $ordd_lite_welcome_page_header_text );
                
                $ts_pro_faq = self::orddd_lite_get_faq ();
                new Orddd_Lite_TS_Faq_Support( $orddd_lite_plugin_name, $orddd_lite_plugin_prefix, $orddd_lite_plugins_page, $orddd_lite_locale, $orddd_lite_plugin_folder_name, $orddd_lite_plugin_slug, $ts_pro_faq );
                
                $ts_pro_notices = self::orddd_lite_get_notice_text ();
				new Orddd_Lite_ts_pro_notices( $orddd_lite_plugin_name, $orddd_lite_lite_plugin_prefix, $orddd_lite_plugin_prefix, $ts_pro_notices, $orddd_lite_file_name, $orddd_lite_pro_file_name );

            }
        }

         /**
         * It will retrun the plguin name.
         * @return string $ts_plugin_name Name of the plugin
         */
		public static function ts_get_plugin_name () {
            $ordd_plugin_dir =  dirname ( dirname ( __FILE__ ) );
            $ordd_plugin_dir .= '/order_delivery_date.php';

            $ts_plugin_name = '';
            $plugin_data = get_file_data( $ordd_plugin_dir, array( 'name' => 'Plugin Name' ) );
            if ( ! empty( $plugin_data['name'] ) ) {
                $ts_plugin_name = $plugin_data[ 'name' ];
            }
            return $ts_plugin_name;
        }

        /**
         * It will retrun the Plugin text Domain
         * @return string $ts_plugin_domain Name of the Plugin domain
         */
        public static function ts_get_plugin_locale () {
            $ordd_plugin_dir =  dirname ( dirname ( __FILE__ ) );
            $ordd_plugin_dir .= '/order_delivery_date.php';

            $ts_plugin_domain = '';
            $plugin_data = get_file_data( $ordd_plugin_dir, array( 'domain' => 'Text Domain' ) );
            if ( ! empty( $plugin_data['domain'] ) ) {
                $ts_plugin_domain = $plugin_data[ 'domain' ];
            }
            return $ts_plugin_domain;
        }
        
        /**
         * It will Display the notices in the admin dashboard for the pro vesion of the plugin.
         * @return array $ts_pro_notices All text of the notices
         */
        public static function orddd_lite_get_notice_text () {
            $ts_pro_notices = array();

            $orddd_lite_locale               = self::ts_get_plugin_locale();

            $message_first = wp_kses_post ( __( 'Thank you for using Order Delivery Date for WooCommerce! Never login to your admin to check your deliveries by syncing the delivery dates to the Google Calendar from Order Delivery Date Pro for WooCommerce. <strong><a target="_blank" href= "https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/checkout?edd_action=add_to_cart&download_id=16&utm_source=wpnotice&utm_medium=first&utm_campaign=OrderDeliveryDateLitePlugin">Get it now!</a></strong>', $orddd_lite_locale ) );  

            $message_two = wp_kses_post ( __( 'Send Product review emails to the customers on the next day of delivery using Post Delivery Product Reviews Addon for Order Delivery Date plugin. <strong><a target="_blank" href= "https://www.tychesoftwares.com/store/premium-plugins/post-delivery-product-reviews-addon-order-delivery-date-woocommerce/checkout?edd_action=add_to_cart&download_id=278278&utm_source=wpnotice&utm_medium=second&utm_campaign=OrderDeliveryDateLitePlugin">Have it now!</a></strong>', $orddd_lite_locale ) );

            $message_three = wp_kses_post ( __( 'Create Delivery Settings by Shipping Zones & Shipping Classes using Order Delivery Date Pro for WooCommerce plugin. <br>Use discount code "ORDPRO20" and grab 20% discount on the purchase of the plugin. The discount code is valid only for the first 20 customers. <strong><a target="_blank" href= "\'https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/checkout?edd_action=add_to_cart&download_id=16&utm_source=wpnotice&utm_medium=third&utm_campaign=OrderDeliveryDateLitePlugin">Purchase now</a></strong>.', $orddd_lite_locale ) );

            $message_four = wp_kses_post ( __( 'Allow recurring deliveries for the subscriptions from the WooCommerce Subscriptions plugin by using WooCommerce Subscriptions Compatibility Addons. <strong><a target="_blank" href= "https://www.tychesoftwares.com/store/premium-plugins/woocommerce-subscriptions-compatibility-addon-for-order-delivery-date-pro-for-woocommerce-plugin/checkout?edd_action=add_to_cart&download_id=278278&utm_source=wpnotice&utm_medium=fourth&utm_campaign=OrderDeliveryDateLitePlugin">Have it now!</a></strong>.', $orddd_lite_locale ) );

            $message_five = wp_kses_post ( __( 'Receive feedbacks for your products from verified owners by sending them post delivery emails using Post Delivery Product Reviews addon of Order Delivery Date plugin. <strong><a target="_blank" href= "https://www.tychesoftwares.com/store/premium-plugins/post-delivery-product-reviews-addon-order-delivery-date-woocommerce/checkout?edd_action=add_to_cart&download_id=278278&utm_source=wpnotice&utm_medium=fifth&utm_campaign=OrderDeliveryDateLitePlugin">Have it now!</a></strong>.', $orddd_lite_locale ) );

            $orddd_wcal_lite_link = 'https://www.tychesoftwares.com/store/premium-plugins/woocommerce-abandoned-cart-pro/checkout?edd_action=add_to_cart&download_id=20&utm_source=wpnotice&utm_medium=sixth&utm_campaign=OrderDeliveryDateLitePlugin';

            $message_six = wp_kses_post ( __( 'Boost your sales by recovering up to 60% of the abandoned carts with our Abandoned Cart Pro for WooCommerce plugin. It allows you to capture guest customer\'s email address on the shop page using Add to cart pop modal.<strong><a target="_blank" href= "'.$orddd_wcal_lite_link.'"> Install it now.</a></strong>', $orddd_lite_locale ) );
            

            $_link = 'https://www.tychesoftwares.com/store/premium-plugins/product-delivery-date-pro-for-woocommerce/checkout?edd_action=add_to_cart&download_id=238877&utm_source=wpnotice&utm_medium=seventh&utm_campaign=OrderDeliveryDateLitePlugin';
            $message_seven = wp_kses_post ( __( 'Allow your customers to select the Delivery Date on Single Product Page using our Product Delivery Date pro for WooCommerce Plugin. <br> 
            <strong><a target="_blank" href= "'.$_link.'">Shop now</a></strong> & be one of the 20 customers to get 20% discount on the plugin price. Use the code "PRDPRO20". Hurry!!', $orddd_lite_locale ) );
            
            $_link = 'https://www.tychesoftwares.com/store/premium-plugins/woocommerce-booking-plugin/checkout?edd_action=add_to_cart&download_id=22&utm_source=wpnotice&utm_medium=eight&utm_campaign=OrderDeliveryDateLitePlugin';
            $message_eight = wp_kses_post ( __( ' Allow your customers to book an appointment or rent an apartment with our Booking and Appointment for WooCommerce plugin. You can also sell your product as a resource or integrate with a few Vendor plugins. <br>Shop now & Save 20% on the plugin with the code "BKAP20". Only for first 20 customers. <strong><a target="_blank" href= "'.$_link.'">Have it now!</a></strong>', $orddd_lite_locale ) );
            
            $_link = 'https://www.tychesoftwares.com/store/premium-plugins/deposits-for-woocommerce/checkout?edd_action=add_to_cart&download_id=286371&utm_source=wpnotice&utm_medium=eight&utm_campaign=OrderDeliveryDateLitePlugin';
            $message_nine = wp_kses_post ( __( ' Allow your customers to pay deposits on products using our Deposits for WooCommerce plguin. <br>
            <strong><a target="_blank" href= "'.$_link.'">Purchase now</a></strong> & Grab 20% discount with the code "DFWP20". The discount code is valid only for the first 20 customers.', $orddd_lite_locale ) );
			
            $ts_pro_notices = array (
                1 => $message_first,
                2 => $message_two,
                3 => $message_three,
                4 => $message_four,
                5 => $message_five,
                6 => $message_six,
                7 => $message_seven,
                8 => $message_eight,
                9 => $message_nine,
            ) ;

            return $ts_pro_notices;
        }
		
		/**
         * It will contain all the FAQ which need to be display on the FAQ page.
         * @return array $ts_faq All questions and answers.
         * 
         */
        public static function orddd_lite_get_faq () {

            $ts_faq = array ();

            $ts_faq = array(
                1 => array (
                        'question' => 'Is it possible to add delivery date calendar for each product?',
                        'answer'   => 'It is not possible to add Delivery date calendar for each product from Order Delivery Date for WooCommerce. However, we do have a plugin name <a href="https://www.tychesoftwares.com/store/premium-plugins/product-delivery-date-pro-for-woocommerce/?utm_source=wprepo&amp;utm_medium=link&amp;utm_campaign=OrderDeliveryDateLite" rel="nofollow">Product Delivery Date for WooCommerce Pro</a> and <a href="https://wordpress.org/plugins/product-delivery-date-for-woocommerce-lite/">Lite</a> version both.'
                    ), 
                2 => array (
                        'question' => 'Can the customer enter the preferred order delivery time?',
                        'answer'   => 'Currently, there is no provision for entering the delivery time in the free version. This is possible in the Pro version. <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=wprepo&amp;utm_medium=demolink&amp;utm_campaign=OrderDeliveryDateLite" title="View Demo" rel="nofollow">View Demo</a>.'
                    ),
                3 => array (
						'question' => 'Is the order delivery date field mandatory on the checkout page?',
						'answer'   => 'The field can be configured as Mandatory or optional using the "Mandatory field?" setting.'
                ),
                4 => array (
						'question' => 'Can we change the language of the delivery date calendar?',
						'answer'   => 'Yes, you can change the language of the delivery date calendar on the checkout page. There are 64 different languages provided under Appearance tab.'
                ),
                5 => array (
						'question' => 'Is it possible to add extra charges for weekdays or specific dates?',
						'answer'   => 'Currently, it is not possible to add the extra charges for deliveries on weekdays or for specific dates in the free version. However, this feature is available in the <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=wprepo&amp;utm_medium=faqlink&amp;utm_campaign=OrderDeliveryDateLite" rel="nofollow">Order Delivery Date Pro for WooCommerce plugin</a>.'
                ),
                6 => array (
						'question' => 'Why the Delivery Date field is not shown on the checkout page when Virtual Products are added to the cart?',
						'answer'   => 'If the Delivery Date field is not shown on the checkout page when Virtual Products are added to the cart. Then please check if the "Disable the Delivery Date Field for" checkbox for the Virtual product is checked on the Appearance tab. If this checkbox is checked then the delivery will be disabled on the checkout page. Same for the Featured Products.'
                ),
                7 => array (
						'question' => 'Why Delivery Date field is not shown on the checkout page?',
						'answer'   => 'If the Delivery Date field is not shown on the checkout page, then please check what option is selected in the "Field placement on the Checkout page" option under Appearance tab. If "In Shipping section" option is selected and if there is no shipping section added on the checkout page or if the Ship to different address checkbox is unchecked, then the delivery date field will not be shown on the checkout page.'
                ),
                8 => array (
						'question' => 'Is it possible to edit the selected delivery date for the already placed WooCommerce orders?',
						'answer'   => 'Currently, it is not possible to edit the selected delivery date for the WooCommerce orders in the free version. However, this feature is available in the <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=wprepo&amp;utm_medium=faqlink&amp;utm_campaign=OrderDeliveryDateLite" rel="nofollow">Order Delivery Date Pro for WooCommerce plugin</a>. The admin, as well as the customers, can edit the delivery date for the already placed WooCommerce orders.'
                ),
                9 => array (
						'question' => 'Can we set different delivery settings for different shipping methods or different product categories?',
						'answer'   => 'Currently, it is not possible to add different delivery settings for different shipping methods or different products categories in the free version. However, this feature is available in the <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=wprepo&amp;utm_medium=faqlink&amp;utm_campaign=OrderDeliveryDateLite" rel="nofollow">Order Delivery Date Pro for WooCommerce plugin</a>.'
                ),
                10 => array (
						'question' => 'Difference between Lite and Pro version of the plugin.',
						'answer'   => 'You can refer <strong><a href="https://www.tychesoftwares.com/differences-pro-lite-versions-order-delivery-date-woocommerce-plugin/?utm_source=wprepo&amp;utm_medium=faqlink&amp;utm_campaign=OrderDeliveryDate" title="Lite and Pro version Difference" rel="nofollow">here</a>.'
                )    
            );

            return $ts_faq;
        }
	}
	$Orddd_Lite_All_Component = new Orddd_Lite_All_Component();
}

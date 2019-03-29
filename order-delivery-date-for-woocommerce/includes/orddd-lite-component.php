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

                require_once( "component/faq-support/ts-faq-support.php" );
                
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

                $ts_pro_faq = self::orddd_lite_get_faq ();
                new Orddd_Lite_TS_Faq_Support( $orddd_lite_plugin_name, $orddd_lite_plugin_prefix, $orddd_lite_plugins_page, $orddd_lite_locale, $orddd_lite_plugin_folder_name, $orddd_lite_plugin_slug, $ts_pro_faq );
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

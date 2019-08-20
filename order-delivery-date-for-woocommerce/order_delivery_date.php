<?php
/**
 * Plugin Name: Order Delivery Date for WooCommerce (Lite version)
 * Plugin URI: https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/
 * Description: This plugin allows customers to choose their preferred Order Delivery Date during checkout.
 * Author: Tyche Softwares
 * Version: 3.10
 * Author URI: https://www.tychesoftwares.com/
 * Contributor: Tyche Softwares, https://www.tychesoftwares.com/
 * Text Domain: order-delivery-date
 * Requires PHP: 5.6
 * WC requires at least: 3.0.0
 * WC tested up to: 3.7.0
 *
 * @package  Order-Delivery-Date-Lite-for-WooCommerce
 */

/**
 * Latest version of the plugin
 *
 * @since 1.0
 */
$wpefield_version = '3.10';

/**
 * Include the require files
 *
 * @since 1.0
 */
require_once 'includes/class-orddd-lite-integration.php';
require_once 'includes/orddd-lite-config.php';
require_once 'includes/class-orddd-lite-common.php';
require_once 'includes/settings/class-orddd-lite-settings.php';
require_once 'includes/class-orddd-lite-process.php';
require_once 'includes/settings/class-orddd-lite-filter.php';
require_once 'includes/class-orddd-lite-privacy.php';

/**
* Defines the plugin version and url when on the admin page
*
* @since 3.4
*/

if ( ! class_exists( 'order_delivery_date_lite' ) ) {
	/**
	 * Main Order Delivery Date class
	 */
	class Order_Delivery_Date_Lite {

		/**
		 * Default Constructor
		 *
		 * @since 1.0
		 */
		public function __construct() {

			// Initialize settings.
			register_activation_hook( __FILE__, array( &$this, 'orddd_lite_activate' ) );

			add_action( 'init', array( &$this, 'orddd_lite_update_po_file' ) );
			add_action( 'admin_init', array( &$this, 'orddd_lite_update_db_check' ) );
			add_action( 'admin_init', array( &$this, 'orddd_lite_capabilities' ) );
			add_action( 'admin_init', array( &$this, 'orddd_lite_check_if_woocommerce_active' ) );

			// Settings.
			add_action( 'admin_menu', array( 'Orddd_Lite_Settings', 'orddd_lite_order_delivery_date_menu' ) );
			add_action( 'admin_init', array( 'Orddd_Lite_Settings', 'order_lite_delivery_date_admin_settings' ) );
			add_action( 'admin_init', array( 'Orddd_Lite_Settings', 'order_lite_appearance_admin_settings' ) );
			add_action( 'admin_init', array( 'Orddd_Lite_Settings', 'order_lite_holidays_admin_settings' ) );
			add_action( 'admin_init', array( 'Orddd_Lite_Settings', 'orddd_lite_delete_settings' ) );
			add_action( 'admin_init', array( 'Orddd_Lite_Settings', 'orddd_lite_calendar_sync_settings_callback' ) );

			// Admin scripts.
			add_action( 'admin_enqueue_scripts', array( &$this, 'orddd_lite_my_enqueue' ) );

			// Frontend.
			add_action( ORDDD_LITE_SHOPPING_CART_HOOK, array( 'Orddd_Lite_Process', 'orddd_lite_my_custom_checkout_field' ) );
			add_action( ORDDD_LITE_SHOPPING_CART_HOOK, array( &$this, 'orddd_lite_front_scripts_js' ) );

			if ( 'on' === get_option( 'orddd_lite_delivery_date_on_cart_page' ) ) {
				add_action( 'woocommerce_cart_collaterals', array( 'Orddd_Lite_Process', 'orddd_lite_my_custom_checkout_field' ) );
				add_action( 'woocommerce_cart_collaterals', array( &$this, 'orddd_lite_front_scripts_js' ) );
			}

			add_action( 'woocommerce_checkout_update_order_meta', array( 'Orddd_Lite_Process', 'orddd_lite_my_custom_checkout_field_update_order_meta' ) );

			if ( defined( 'WOOCOMMERCE_VERSION' ) && version_compare( WOOCOMMERCE_VERSION, '2.3', '>=' ) < 0 ) {
				add_filter( 'woocommerce_email_order_meta_fields', array( 'Orddd_Lite_Process', 'orddd_lite_add_delivery_date_to_order_woo_new' ), 11, 3 );
			} else {
				add_filter( 'woocommerce_email_order_meta_keys', array( 'Orddd_Lite_Process', 'orddd_lite_add_delivery_date_to_order_woo_deprecated' ), 11, 1 );
			}

			if ( get_option( 'orddd_lite_date_field_mandatory' ) === 'checked' &&
				get_option( 'orddd_lite_enable_delivery_date' ) === 'on' ) {
				add_action( 'woocommerce_checkout_process', array( 'Orddd_Lite_Process', 'orddd_lite_validate_date_wpefield' ) );
			}

			add_filter( 'woocommerce_order_details_after_order_table', array( 'Orddd_Lite_Process', 'orddd_lite_add_delivery_date_to_order_page_woo' ) );

			// phpcs:ignore WordPress.Security.NonceVerification
			if ( is_admin() && isset( $_GET['post_type'] ) ) {
				// phpcs:ignore WordPress.Security.NonceVerification
				$post_type = sanitize_text_field( wp_unslash( $_GET['post_type'] ) );
				if ( 'shop_order' === $post_type ) {
					// WooCommerce Edit Order page.
					add_filter( 'manage_edit-shop_order_columns', array( 'Orddd_Lite_Filter', 'orddd_lite_woocommerce_order_delivery_date_column' ), 20, 1 );
					add_action( 'manage_shop_order_posts_custom_column', array( 'Orddd_Lite_Filter', 'orddd_lite_woocommerce_custom_column_value' ), 20, 1 );
					add_filter( 'manage_edit-shop_order_sortable_columns', array( 'Orddd_Lite_Filter', 'orddd_lite_woocommerce_custom_column_value_sort' ) );
					add_filter( 'request', array( 'Orddd_Lite_Filter', 'orddd_lite_woocommerce_delivery_date_orderby' ) );
				}
			}

			// To recover the delivery date when order is cancelled, refunded, failed or trashed.
			add_action( 'woocommerce_order_status_cancelled', array( 'Orddd_Lite_Common', 'orddd_lite_cancel_delivery' ), 10, 1 );
			add_action( 'woocommerce_order_status_refunded', array( 'Orddd_Lite_Common', 'orddd_lite_cancel_delivery' ), 10, 1 );
			add_action( 'woocommerce_order_status_failed', array( 'Orddd_Lite_Common', 'orddd_lite_cancel_delivery' ), 10, 1 );
			add_action( 'wp_trash_post', array( 'Orddd_Lite_Common', 'orddd_lite_cancel_delivery_for_trashed' ), 10, 1 );

			// Ajax calls.
			add_action( 'init', array( &$this, 'orddd_lite_add_component_file' ) );

			// It will add the actions for the components.
			if ( is_admin() ) {
				add_filter( 'ts_tracker_data', array( 'Orddd_Lite_Common', 'orddd_lite_ts_add_plugin_tracking_data' ), 10, 1 );
				add_filter( 'ts_tracker_opt_out_data', array( 'Orddd_Lite_Common', 'orddd_lite_get_data_for_opt_out' ), 10, 1 );
				add_filter( 'ts_deativate_plugin_questions', array( 'Orddd_Lite_Common', 'orddd_lite_deactivate_add_questions' ), 10, 1 );
			}

			add_filter( 'plugin_row_meta', array( &$this, 'orddd_lite_plugin_row_meta' ), 10, 2 );
		}

		/**
		 * Add default settings when plugin is activated for the first time
		 *
		 * @hook register_activation_hook
		 * @globals array $orddd_lite_weekdays Weekdays array
		 * @since 1.5
		 */
		public function orddd_lite_activate() {
			global $orddd_lite_weekdays;

			add_option( 'orddd_lite_enable_delivery_date', '' );
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
			add_option( 'orddd_lite_enable_delivery_date_enabled', 'yes' );
			add_option( 'orddd_lite_auto_populate_first_available_date', 'on' );

			// Appearance options.
			add_option( 'orddd_lite_delivery_date_format', ORDDD_LITE_DELIVERY_DATE_FORMAT );
			add_option( 'orddd_lite_delivery_date_field_label', ORDDD_LITE_DELIVERY_DATE_FIELD_LABEL );
			add_option( 'orddd_lite_delivery_date_field_placeholder', ORDDD_LITE_DELIVERY_DATE_FIELD_PLACEHOLDER );
			add_option( 'orddd_lite_delivery_date_field_note', ORDDD_LITE_DELIVERY_DATE_FIELD_NOTE );
			add_option( 'orddd_lite_number_of_months', '1' );
			add_option( 'orddd_lite_calendar_theme', ORDDD_LITE_CALENDAR_THEME );
			add_option( 'orddd_lite_calendar_theme_name', ORDDD_LITE_CALENDAR_THEME_NAME );
			add_option( 'orddd_lite_language_selected', 'en-GB' );
			add_option( 'orddd_lite_delivery_date_fields_on_checkout_page', 'billing_section' );
			add_option( 'orddd_lite_no_fields_for_virtual_product', '' );
			add_option( 'orddd_lite_no_fields_for_featured_product', '' );

			// Flags.
			add_option( 'orddd_lite_update_calculate_min_time_disabled_days', 'yes' );

			// Pro admin Notices.
			if ( ! get_option( 'orddd_lite_activate_time' ) ) {
				add_option( 'orddd_lite_activate_time', current_time( 'timestamp' ) );
			}

			add_option( 'orddd_lite_installed', 'yes' );
		}

		/**
		 * Load text domain for language translation
		 *
		 * @hook init
		 * @since 1.5
		 */
		public function orddd_lite_update_po_file() {
			$domain = 'order-delivery-date';
			$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
			$loaded = load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '-' . $locale . '.mo' );
			if ( $loaded ) {
				return $loaded;
			} else {
				load_plugin_textdomain( $domain, false, basename( dirname( __FILE__ ) ) . '/languages/' );
			}
		}

		/**
		 * Check if WooCommerce is active
		 *
		 * @return bool
		 * @since 2.6
		 */
		public static function orddd_lite_check_woo_installed() {
			if ( class_exists( 'WooCommerce' ) ) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Check if WooCommerce plugin is active or not. If it is not active then it will display a notice.
		 *
		 * @hook admin_init
		 * @since 2.6
		 */
		public function orddd_lite_check_if_woocommerce_active() {
			if ( ! self::orddd_lite_check_woo_installed() ) {
				if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
					deactivate_plugins( plugin_basename( __FILE__ ) );
					add_action( 'admin_notices', array( 'order_delivery_date_lite', 'orddd_lite_disabled_notice' ) );
					if ( isset( $_GET['activate'] ) ) {
						$activate = wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['activate'] ) ) );
						unset( $activate );
					}
				}
			}
		}

		/**
		 * Display a notice in the admin Plugins page if the plugin is activated while WooCommerce is deactivated.
		 *
		 * @hook admin_notices
		 * @since 2.6
		 */
		public static function orddd_lite_disabled_notice() {
			$class   = 'notice notice-error';
			$message = __( 'Order Delivery Date for WooCommerce (Lite version) plugin requires WooCommerce installed and activate.', 'order-delivery-date' );

			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_attr( $message ) );
		}

		/**
		 * Returns the order delivery date plugin version number
		 *
		 * @return int $plugin_version Plugin Version
		 * @since 1.0
		 */
		public function get_orddd_lite_version() {
			$plugin_data    = get_plugin_data( __FILE__ );
			$plugin_version = $plugin_data['Version'];
			return $plugin_version;
		}

		/**
		 * This function is executed when the plugin is updated using the Automatic Updater.
		 *
		 * @globals int $wpefield_version Plugin Version
		 *
		 * @hook admin_init
		 * @since 1.0
		 */
		public function orddd_lite_update_db_check() {
			global $wpefield_version;
			if ( '3.10' === $wpefield_version ) {
				self::orddd_lite_update_install();
			}
		}

		/**
		 * Updates the require options when the plugin is updated using the Automatic Updater.
		 *
		 * @globals resource $wpdb
		 * @globals array $orddd_lite_weekdays Weekdays array
		 * @since 1.0
		 */
		public function orddd_lite_update_install() {
			global $wpdb, $orddd_lite_weekdays, $wpefield_version;

			// Code to set the option to on as default.
			$orddd_lite_plugin_version = get_option( 'orddd_lite_db_version' );
			if ( self::get_orddd_lite_version() !== $orddd_lite_plugin_version ) {
				update_option( 'orddd_lite_db_version', $wpefield_version );
			}
		}


		/**
		 * Capability to allow shop manager to edit settings
		 *
		 * @hook admin_init
		 * @since 2.2
		 */
		public function orddd_lite_capabilities() {
			$role = get_role( 'shop_manager' );
			if ( '' !== $role ) {
				$role->add_cap( 'manage_options' );
			}
		}

		/**
		 * Enqueue scripts on the admin Order Delivery Date menu page
		 *
		 * @hook admin_enqueue_scripts
		 *
		 * @param string $hook Page slug.
		 * @since 1.0
		 */
		public function orddd_lite_my_enqueue( $hook ) {
			global $orddd_lite_languages, $wpefield_version;
			if ( 'toplevel_page_order_delivery_date_lite' !== $hook ) {
				return;
			}

			$calendar_theme = get_option( 'orddd_lite_calendar_theme' );
			if ( '' === $calendar_theme ) {
				$calendar_theme = 'base';
			}

			wp_dequeue_script( 'themeswitcher' );
			wp_enqueue_script( 'themeswitcher-orddd', plugins_url( '/js/jquery.themeswitcher.min.js', __FILE__ ), array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-datepicker' ), $wpefield_version, true );

			foreach ( $orddd_lite_languages as $key => $value ) {
				wp_enqueue_script( $value, plugins_url( "/js/i18n/jquery.ui.datepicker-$key.js", __FILE__ ), array( 'jquery', 'jquery-ui-datepicker' ), $wpefield_version, false );
			}

			wp_register_script( 'orddd-lite-select2', plugins_url() . '/woocommerce/assets/js/select2/select2.min.js', array( 'jquery', 'jquery-ui-widget', 'jquery-ui-core' ), $wpefield_version, false );
			wp_enqueue_script( 'orddd-lite-select2' );

			wp_register_style( 'woocommerce_admin_styles', plugins_url() . '/woocommerce/assets/css/admin.css', array(), WC_VERSION );
			wp_enqueue_style( 'woocommerce_admin_styles' );
			wp_enqueue_style( 'order-delivery-date', plugins_url( '/css/order-delivery-date.css', __FILE__ ), '', $wpefield_version );
			wp_register_style( 'jquery-ui-style', plugins_url( '/css/themes/' . $calendar_theme . '/jquery-ui.css', __FILE__ ), '', $wpefield_version );
			wp_enqueue_style( 'jquery-ui-style' );
			wp_enqueue_style( 'datepicker', plugins_url( '/css/datepicker.css', __FILE__ ), '', $wpefield_version );
		}

		/**
		 * It will load the boilerplate components file. In this file we have included all boilerplate files.
		 * We need to inlcude this file after the init hook.
		 *
		 * @hook init
		 */
		public static function orddd_lite_add_component_file() {
			if ( is_admin() ) {
				require_once 'includes/orddd-lite-component.php';
			}
		}

		/**
		 * Enqueue scripts on the frontend checkout page
		 *
		 * @hook woocommerce_after_checkout_billing_form
		 * @hook woocommerce_after_checkout_shipping_form
		 * @hook woocommerce_before_order_notes
		 * @hook woocommerce_after_order_notes
		 *
		 * @since 1.0
		 */
		public function orddd_lite_front_scripts_js() {
			global $wpefield_version;
			if ( get_option( 'orddd_lite_enable_delivery_date' ) === 'on' ) {
				$calendar_theme = get_option( 'orddd_lite_calendar_theme' );
				if ( '' === $calendar_theme ) {
					$calendar_theme = 'base';
				}
				wp_dequeue_style( 'jquery-ui-style' );
				wp_register_style( 'jquery-ui-style-orddd-lite', plugins_url( '/css/themes/' . $calendar_theme . '/jquery-ui.css', __FILE__ ), '', $wpefield_version, false );
				wp_enqueue_style( 'jquery-ui-style-orddd-lite' );
				wp_enqueue_style( 'datepicker', plugins_url( '/css/datepicker.css', __FILE__ ), '', $wpefield_version, false );

				wp_dequeue_script( 'initialize-datepicker' );
				wp_enqueue_script( 'initialize-datepicker-orddd', plugins_url( '/js/orddd-lite-initialize-datepicker.js', __FILE__ ), '', $wpefield_version, false );

				$js_args = array(
					'clearText'   => __( 'Clear', 'order-delivery-date' ),
					'holidayText' => __( 'Holiday', 'order-delivery-date' ),
					'bookedText'  => __( 'Booked', 'order-delivery-date' ),
				);
				wp_localize_script( 'initialize-datepicker-orddd', 'jsL10n', $js_args );

				if ( isset( $_GET['lang'] ) && '' !== $_GET['lang'] && null !== $_GET['lang'] ) {
					$language_selected = wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['lang'] ) ) );
				} else {
					$language_selected = get_option( 'orddd_lite_language_selected' );
					if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
						if ( constant( 'ICL_LANGUAGE_CODE' ) !== '' ) {
							$wpml_current_language = constant( 'ICL_LANGUAGE_CODE' );
							if ( ! empty( $wpml_current_language ) ) {
								$language_selected = $wpml_current_language;
							} else {
								$language_selected = get_option( 'orddd_lite_language_selected' );
							}
						}
					}
					if ( '' === $language_selected ) {
						$language_selected = 'en-GB';
					}
				}

				wp_enqueue_script( $language_selected, plugins_url( "/js/i18n/jquery.ui.datepicker-$language_selected.js", __FILE__ ), array( 'jquery', 'jquery-ui-datepicker' ), $wpefield_version, false );
			}
		}

		/**
		 * Add links for Submitting a ticket, Link to Pro version and link to docs on the Plugins page in admin.
		 *
		 * @hook plugin_row_meta
		 *
		 * @param array  $links Links to be displayed.
		 * @param string $file Path of the file.
		 *
		 * @since 1.0
		 */
		public static function orddd_lite_plugin_row_meta( $links, $file ) {
			if ( plugin_basename( __FILE__ ) === $file ) {
				unset( $links[2] );

				$row_meta = array(
					'docs'        => '<a href="' . esc_url( apply_filters( 'orddd_docs_url', 'https://www.tychesoftwares.com/docs/docs/order-delivery-date-for-woocommerce-lite/?utm_source=pluginwebsite&utm_medium=pluginspage&utm_campaign=OrderDeliveryDateLite' ) ) . '" target="_blank" title="' . esc_attr( __( 'Docs', 'order-delivery-date' ) ) . '">' . __( 'Docs', 'order-delivery-date' ) . '</a>',
					'support'     => '<a href="' . esc_url( apply_filters( 'orddd_support_url', 'https://tychesoftwares.freshdesk.com/support/tickets/new?utm_source=pluginwebsite&utm_medium=pluginspage&utm_campaign=OrderDeliveryDateLite' ) ) . '" target="_blank" title="' . esc_attr( __( 'Submit Ticket', 'order-delivery-date' ) ) . '">' . __( 'Submit Ticket', 'order-delivery-date' ) . '</a>',
					'plugin_site' => '<a href="' . esc_url( apply_filters( 'orddd_plugin_site_url', 'https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=pluginwebsite&utm_medium=pluginspage&utm_campaign=OrderDeliveryDateLite' ) ) . '" target="_blank" title="' . esc_attr( __( 'Go Pro', 'order-delivery-date' ) ) . '">' . __( 'Premium version', 'order-delivery-date' ) . '</a>',
				);
				return array_merge( $links, $row_meta );
			}
			return (array) $links;
		}
	}
}
$order_delivery_date_lite = new Order_Delivery_Date_Lite();

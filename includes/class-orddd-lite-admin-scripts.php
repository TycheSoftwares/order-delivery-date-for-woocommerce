<?php
/**
 * Order Delivery Date Lite – Admin Scripts
 *
 * Enqueues the React admin SPA (build/admin/index.js) on the plugin settings page
 * and passes the necessary data via wp_localize_script as `ordddAdminData`
 * (matching the Pro plugin's variable name for consistent upgrade paths).
 *
 * @package Order-Delivery-Date-Lite-for-WooCommerce
 * @since   4.7.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class ORDDD_Lite_Admin_Scripts
 */
class ORDDD_Lite_Admin_Scripts {

	/**
	 * Constructor — hooks registration and enqueue.
	 */
	public function __construct() {
		//add_action( 'admin_init',             array( $this, 'register_script' ) );
		add_action( 'admin_enqueue_scripts',  array( $this, 'enqueue' ), 1000001 );
	}

	/**
	 * Always register the React admin SPA so Loco Translate can find it
	 * on any admin page and generate the correct script-translation JSON.
	 */
	public function register_script() {
		$asset = array(
			'dependencies' => array( 'wp-api-fetch', 'wp-i18n', 'wp-date', 'wp-element', 'wp-components' ),
			'version'      => '2.0.0',
		);

		wp_register_script(
			'orddd-admin-app',
			plugins_url( 'build/admin/index.js', dirname( __FILE__ ) . '/../order_delivery_date.php' ),
			$asset['dependencies'],
			$asset['version'],
			true
		);

		wp_set_script_translations(
			'orddd-admin-app',
			'order-delivery-date',
			plugin_dir_path( ORDDD_LITE_PLUGIN_FILE ) . 'languages'
		);
	}

	/**
	 * Enqueue the React admin SPA and pass localised data.
	 *
	 * @param string $hook Current admin page hook suffix.
	 */
	public function enqueue( $hook ) {
		// Only load on our settings page.

		if ( 'woocommerce_page_order_delivery_date_lite' !== $hook ) {
			return;
		}

		$asset = array(
			'dependencies' => array( 'wp-api-fetch', 'wp-i18n', 'wp-date', 'wp-element', 'wp-components' ),
			'version'      => ORDDD_LITE_VERSION,
		);

		wp_register_script(
			'orddd-admin-app',
			plugins_url( 'build/admin/index.js', ORDDD_LITE_PLUGIN_FILE ),
			$asset['dependencies'],
			$asset['version'],
			true
		);

		wp_enqueue_script( 'orddd-admin-app' );

		wp_set_script_translations(
			'orddd-admin-app',
			'order-delivery-date',
			plugin_dir_path( ORDDD_LITE_PLUGIN_FILE ) . 'languages'
		);

		if ( file_exists( plugin_dir_path( __DIR__ ) . 'build/admin/index.css' ) ) {
			wp_enqueue_style(
				'orddd-admin-app',
				plugins_url( 'build/admin/index.css', dirname( __FILE__ ) . '/../order_delivery_date.php' ),
				array( 'wp-components' ),
				$asset['version']
			);
		}

		// qtip2 — needed by the Order Calendar tooltip popups.
		wp_enqueue_script(
			'orddd-images-loaded',
			plugins_url( 'js/imagesloaded.pkg.min.js', ORDDD_LITE_PLUGIN_FILE ),
			array(),
			$asset['version'],
			true
		);

		wp_enqueue_script(
			'orddd-qtip',
			plugins_url( 'js/jquery.qtip.min.js', ORDDD_LITE_PLUGIN_FILE ),
			array( 'jquery', 'orddd-images-loaded' ),
			$asset['version'],
			true
		);

		wp_enqueue_style(
			'orddd-qtip-css',
			plugins_url( 'css/jquery.qtip.min.css', ORDDD_LITE_PLUGIN_FILE ),
			array(),
			$asset['version']
		);

		// Calendar data for the React Order Calendar page (window.orddd_calendar_js).
		$calendar_nonce  = wp_create_nonce( 'orddd-delivery-calendar-event-json' );
		$cal_export_nonce = wp_create_nonce( 'orddd_export_nonce' );

		wp_localize_script(
			'orddd-admin-app',
			'orddd_calendar_js',
			array(
				'pluginurl'          => add_query_arg(
					array(
						'action'    => 'orddd-delivery-calendar-event-json',
						'vendor_id' => 0,
						'security'  => $calendar_nonce,
					),
					admin_url( 'admin.php' )
				),
				'ajax_url'           => admin_url( 'admin-ajax.php' ),
				'security'           => $calendar_nonce,
				'vendor_id'          => 0,
				'orddd_export_nonce' => $cal_export_nonce,
				'print_url'          => add_query_arg(
					array(
						'download'           => 'orddd_data.print',
						'orddd_export_nonce' => $cal_export_nonce,
					),
					admin_url( 'admin.php' )
				),
				'csv_url'            => add_query_arg(
					array(
						'download'           => 'orddd_data.csv',
						'orddd_export_nonce' => $cal_export_nonce,
					),
					admin_url( 'admin.php' )
				),
				'order_statuses'     => wc_get_order_statuses(),
				'shipping_methods'   => Orddd_Lite_Common::get_all_wc_shipping_methods(),
				'orddd_holiday_color' => get_option( 'orddd_lite_holiday_color' ),
				'orddd_holidays'     => '',
				'calendar_language'  => get_option( 'orddd_lite_language_selected' ) ?: 'en-GB',
			)
		);

		wp_localize_script(
			'orddd-admin-app',
			'ordddAdminData',
			array(
				'nonce'                  => wp_create_nonce( 'wp_rest' ),
				'restUrl'                => get_rest_url( null, 'orddd/v1/' ),
				'adminUrl'               => admin_url(),
				'plugin_url'             => plugin_dir_url( ORDDD_LITE_PLUGIN_FILE ),
				'welcome_page_displayed' => get_option( 'orddd_lite_welcome_page_displayed', 'yes' ),
				'license_active'         => false,
				'display_all_settings'   => false,
				'pro_installed'          => '' !== get_option( 'orddd_pro_installed', '' ),
				'weekdays'               => $this->get_weekdays(),
				'date_formats'           => $this->get_date_formats(),
				'time_formats'           => array(
					'1' => __( '12 hour', 'order-delivery-date' ),
					'2' => __( '24 hour', 'order-delivery-date' ),
				),
				'calendar_themes'        => $this->get_calendar_themes(),
				'languages'              => $this->get_languages(),
				'checkout_positions'     => $this->get_checkout_positions(),
				'upgradeUrl'             => 'https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/',
				'faqs'                   => Orddd_Lite_All_Component::orddd_lite_get_faq(),
			)
		);
	}

	/**
	 * Returns localised weekday names keyed by orddd_lite_weekday_N.
	 *
	 * @return array
	 */
	private function get_weekdays() {
		global $orddd_lite_weekdays;

		if ( ! empty( $orddd_lite_weekdays ) && is_array( $orddd_lite_weekdays ) ) {
			return $orddd_lite_weekdays;
		}

		// Fallback if global not loaded.
		return array(
			'orddd_lite_weekday_1' => __( 'Monday', 'order-delivery-date' ),
			'orddd_lite_weekday_2' => __( 'Tuesday', 'order-delivery-date' ),
			'orddd_lite_weekday_3' => __( 'Wednesday', 'order-delivery-date' ),
			'orddd_lite_weekday_4' => __( 'Thursday', 'order-delivery-date' ),
			'orddd_lite_weekday_5' => __( 'Friday', 'order-delivery-date' ),
			'orddd_lite_weekday_6' => __( 'Saturday', 'order-delivery-date' ),
			'orddd_lite_weekday_0' => __( 'Sunday', 'order-delivery-date' ),
		);
	}

	/**
	 * Returns available date formats.
	 *
	 * @return array
	 */
	private function get_date_formats() {
		global $orddd_lite_date_formats;

		if ( ! empty( $orddd_lite_date_formats ) ) {
			return $orddd_lite_date_formats;
		}

		return array(
			'dd/mm/yy' => 'dd/mm/yy',
			'mm/dd/yy' => 'mm/dd/yy',
			'yy/mm/dd' => 'yy/mm/dd',
			'dd-mm-yy' => 'dd-mm-yy',
			'mm-dd-yy' => 'mm-dd-yy',
			'yy-mm-dd' => 'yy-mm-dd',
			'dd.mm.yy' => 'dd.mm.yy',
			'mm.dd.yy' => 'mm.dd.yy',
			'yy.mm.dd' => 'yy.mm.dd',
			'DD, d MM, yy' => 'DD, d MM, yy',
		);
	}

	/**
	 * Returns available calendar themes.
	 *
	 * @return array
	 */
	private function get_calendar_themes() {
		global $orddd_lite_calendar_themes;

		if ( ! empty( $orddd_lite_calendar_themes ) ) {
			return $orddd_lite_calendar_themes;
		}

		return array(
			'base'        => __( 'Base', 'order-delivery-date' ),
			'black-tie'   => __( 'Black Tie', 'order-delivery-date' ),
			'blitzer'     => __( 'Blitzer', 'order-delivery-date' ),
			'cupertino'   => __( 'Cupertino', 'order-delivery-date' ),
			'dark-hive'   => __( 'Dark Hive', 'order-delivery-date' ),
			'dot-luv'     => __( 'Dot Luv', 'order-delivery-date' ),
			'eggplant'    => __( 'Eggplant', 'order-delivery-date' ),
			'excite-bike' => __( 'Excite Bike', 'order-delivery-date' ),
			'flick'       => __( 'Flick', 'order-delivery-date' ),
			'hot-sneaks'  => __( 'Hot Sneaks', 'order-delivery-date' ),
			'humanity'    => __( 'Humanity', 'order-delivery-date' ),
			'le-frog'     => __( 'Le Frog', 'order-delivery-date' ),
			'mint-choc'   => __( 'Mint Choc', 'order-delivery-date' ),
			'overcast'    => __( 'Overcast', 'order-delivery-date' ),
			'pepper-grinder' => __( 'Pepper Grinder', 'order-delivery-date' ),
			'redmond'     => __( 'Redmond', 'order-delivery-date' ),
			'smoothness'  => __( 'Smoothness', 'order-delivery-date' ),
			'south-street' => __( 'South Street', 'order-delivery-date' ),
			'start'       => __( 'Start', 'order-delivery-date' ),
			'sunny'       => __( 'Sunny', 'order-delivery-date' ),
			'swanky-purse' => __( 'Swanky Purse', 'order-delivery-date' ),
			'trontastic'  => __( 'Trontastic', 'order-delivery-date' ),
			'ui-darkness' => __( 'UI Darkness', 'order-delivery-date' ),
			'ui-lightness' => __( 'UI Lightness', 'order-delivery-date' ),
			'vader'       => __( 'Vader', 'order-delivery-date' ),
		);
	}

	/**
	 * Returns available calendar languages.
	 *
	 * @return array
	 */
	private function get_languages() {
		global $orddd_lite_languages;

		if ( ! empty( $orddd_lite_languages ) ) {
			return $orddd_lite_languages;
		}

		return array( 'en-GB' => 'English' );
	}

	/**
	 * Returns checkout page position options.
	 *
	 * @return array
	 */
	private function get_checkout_positions() {
		return array(
			'billing_section'        => __( 'After Billing Section', 'order-delivery-date' ),
			'shipping_section'       => __( 'After Shipping Section', 'order-delivery-date' ),
			'before_order_notes'     => __( 'Before Order Notes', 'order-delivery-date' ),
			'after_order_notes'      => __( 'After Order Notes', 'order-delivery-date' ),
			'after_your_order_table' => __( 'After Your Order Table', 'order-delivery-date' ),
		);
	}
}

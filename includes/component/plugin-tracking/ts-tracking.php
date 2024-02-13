<?php //phpcs:ignore
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Adds the Tracking non-senstive data notice
 *
 * @since 6.8
 */
class Orddd_Lite_TS_Tracking {

	/**
	 * Plugin prefix
	 *
	 * @var string Plugin prefix
	 * @access public
	 */

	public static $plugin_prefix = '';

	/**
	 * Plugin name
	 *
	 * @var string Plugin Name
	 * @access public
	 */

	public static $plugin_name = '';

	/**
	 * Blog post link
	 *
	 * @var string Tracking data blog post link
	 * @access public
	 */

	public static $blog_post_link = '';

	/**
	 *
	 * Plugin context
	 *
	 * @var string Plugin context
	 * @access public
	 */

	public static $plugin_context = '';

	/**
	 *
	 * Plugin url
	 *
	 * @var string Plugin url
	 * @access public
	 */
	public static $plugin_url = '';

	/**
	 *
	 * File path
	 *
	 * @var string File path
	 * @access public
	 */
	public static $ts_file_path = '';
	/**
	 *
	 * Plugin locale
	 *
	 * @var string plugin locale
	 * @access public
	 */
	public static $ts_plugin_locale = '';

	/**
	 * Setting page
	 *
	 * @var string Settings page
	 * @access public
	 */
	public static $ts_settings_page = '';

	/**
	 *
	 * On which setting page need to add setting
	 *
	 * @var string On which setting page need to add setting
	 * @access public
	 */
	public static $ts_add_setting_on_page = '';
	/**
	 *
	 * On which section setting need to add
	 *
	 * @var string On which section setting need to add
	 * @access public
	 */
	public static $ts_add_setting_on_section = '';
	/**
	 *
	 * Register setting
	 *
	 * @var string Register setting
	 * @access public
	 */
	public static $ts_register_setting = '';
	/** /phpcs:ignore
	 * Default Constructor
	 */
	public function __construct( $ts_plugin_prefix = '', $ts_plugin_name = '', $ts_blog_post_link = '', $ts_plugin_context = '', $ts_plugin_url = '', $setting_page = '', $add_setting = '', $setting_section = '', $setting_register = '' ) {

		self::$plugin_prefix             = $ts_plugin_prefix;
		self::$plugin_name               = $ts_plugin_name;
		self::$blog_post_link            = $ts_blog_post_link;
		self::$plugin_url                = $ts_plugin_url;
		self::$ts_plugin_locale          = $ts_plugin_context;
		self::$ts_settings_page          = $setting_page;
		self::$ts_add_setting_on_page    = $add_setting;
		self::$ts_add_setting_on_section = $setting_section;
		self::$ts_register_setting       = $setting_register;

		self::$ts_file_path = untrailingslashit( plugins_url( '/', __FILE__ ) );
		// Tracking Data.

		add_action( self::$plugin_prefix . '_add_new_settings', array( 'Orddd_Lite_TS_tracking', 'ts_add_reset_tracking_setting' ) );

		add_action( 'admin_init', array( 'Orddd_Lite_TS_tracking', 'ts_reset_tracking_setting' ) );
		// Include JS script for the notice.
		add_filter( 'orddd_lite_ts_tracker_data', array( __CLASS__, 'orddd_lite_ts_add_plugin_tracking_data' ), 10, 1 );
		add_action( 'admin_footer', array( __CLASS__, 'ts_admin_notices_scripts' ) );
		// Send Tracker Data.
		add_action( 'orddd_lite_init_tracker_completed', array( __CLASS__, 'init_tracker_completed' ), 10, 2 );
		add_filter( 'orddd_lite_ts_tracker_display_notice', array( __CLASS__, 'orddd_lite_ts_tracker_display_notice' ), 10, 1 );
	}

		/**
		 * It will delete the tracking option from the database.
		 */
	public static function ts_reset_tracking_setting() {

		if ( isset( $_GET ['ts_action'] ) && 'reset_tracking' === $_GET ['ts_action'] ) { //phpcs:ignore
			Tyche_Plugin_Tracking::reset_tracker_setting( 'orddd_lite' );
			$ts_url = remove_query_arg( 'ts_action' );
			wp_safe_redirect( $ts_url );
		}
	}
		/**
		 * Add tracker completed.
		 */
	public static function init_tracker_completed() {
		header( 'Location: ' . admin_url( 'admin.php?page=order_delivery_date_lite' ) );
		exit;
	}

		/**
		 * Display admin notice on specific page.
		 *
		 * @param array $is_flag Is Flag defailt value true.
		 */
	public static function orddd_lite_ts_tracker_display_notice( $is_flag ) {
		global $current_section;
		if ( isset( $_GET['page'] ) && 'order_delivery_date_lite' === $_GET['page'] ) { // phpcs:ignore
			$is_flag = true;
		}
		return $is_flag;
	}

		/**
		 * Send the plugin data when the user has opted in
		 *
		 * @hook ts_tracker_data
		 * @param array $data All data to send to server.
		 *
		 * @return array $plugin_data All data to send to server.
		 */
	public static function orddd_lite_ts_add_plugin_tracking_data( $data ) {
		$plugin_short_name = 'orddd_lite';
		if ( ! isset( $_GET[ $plugin_short_name . '_tracker_nonce' ] ) ) {
			return $data;
		}

		$tracker_option = isset( $_GET[ $plugin_short_name . '_tracker_optin' ] ) ? $plugin_short_name . '_tracker_optin' : ( isset( $_GET[ $plugin_short_name . '_tracker_optout' ] ) ? $plugin_short_name . '_tracker_optout' : '' ); // phpcs:ignore
		if ( '' === $tracker_option || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET[ $plugin_short_name . '_tracker_nonce' ] ) ), $tracker_option ) ) {
			return $data;
		}

		$data = Orddd_Lite_Common::orddd_lite_ts_add_plugin_tracking_data( $data );
		return $data;
	}

	/**
	 * It will add the settinig, which will allow store owner to reset the tracking data. Which will result into stop trakcing the data.
	 *
	 * @hook self::$plugin_prefix . '_add_new_settings'
	 */
	public static function ts_add_reset_tracking_setting() {

		add_settings_field(
			'ts_reset_tracking',
			__( 'Reset usage tracking', 'order-delivery-date' ),
			array( 'Orddd_Lite_TS_tracking', 'ts_rereset_tracking_callback' ),
			self::$ts_add_setting_on_page,
			self::$ts_add_setting_on_section,
			array( 'This will reset your usage tracking settings, causing it to show the opt-in banner again and not sending any data.', self::$ts_plugin_locale )
		);

		register_setting(
			self::$ts_register_setting,
			'ts_reset_tracking'
		);
	}
	/**
	 * Reset setting section callback function.
	 */
	public static function ts_reset_tracking_setting_section_callback() {
	}

	/**
	 * It will add the Reset button on the settings page.
	 *
	 * @param array $args args.
	 */
	public static function ts_rereset_tracking_callback( $args ) {
		$wcap_restrict_domain_address = get_option( 'wcap_restrict_domain_address' );
		$domain_value                 = isset( $wcap_restrict_domain_address ) ? esc_attr( $wcap_restrict_domain_address ) : '';
		// Next, we update the name attribute to access this element's ID in the context of the display options array
		// We also access the show_header element of the options collection in the call to the checked() helper function.
		$ts_action = self::$ts_settings_page . '&amp;ts_action=reset_tracking';
		printf( '<a href="' . $ts_action . '" class="button button-large reset_tracking">Reset</a>' );//phpcs:ignore

		// Here, we'll take the first argument of the array and add it to a label next to the checkbox.
		$html = '<label for="wcap_restrict_domain_address_label"> ' . $args[0] . '</label>';
		echo $html; //phpcs:ignore
	}

	/**
	 * Load the js file in the admin
	 *
	 * @since 6.8
	 * @access public
	 */
	public static function ts_admin_notices_scripts() {
		$nonce = wp_create_nonce( 'tracking_notice' );
			wp_enqueue_script(
				'orddd_ts_dismiss_notice',
				self::$ts_file_path . '/js/tyche-dismiss-tracking-notice.js',
				array( 'jquery' ),
				'4.5.6',
				false
			);

			wp_localize_script(
				'orddd_ts_dismiss_notice',
				'orddd_ts_dismiss_notice',
				array(
					'ts_prefix_of_plugin' => 'orddd_lite',
					'ts_admin_url'        => admin_url( 'admin-ajax.php' ),
					'tracking_notice'     => $nonce,
				)
			);
	}
}

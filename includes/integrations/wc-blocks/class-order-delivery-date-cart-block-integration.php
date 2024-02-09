<?php //phpcs:ignore
/**
 * Register scripts for delivery date blocks.
 *
 * @package order-delivery-date/blocks
 */

use Automattic\WooCommerce\Blocks\Integrations\IntegrationInterface;

define( 'ORDD_LITE_CART_BLOCK_VERSION', '1.0.0' );

/**
 * Class for integrating with WooCommerce Blocks
 */
class Order_DeliveryDate_Lite_Cart_Block_Integration implements IntegrationInterface {

	/**
	 * The name of the integration.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'delivery-date';
	}

	/**
	 * When called invokes any initialization/setup for the integration.
	 */
	public function initialize() {
		$this->register_block_frontend_scripts();
		$this->register_block_editor_scripts();
	}

	/**
	 * Returns an array of script handles to enqueue in the frontend context.
	 *
	 * @return string[]
	 */
	public function get_script_handles() {
		return array( 'delivery-date-cart-block-frontend' );
	}

	/**
	 * Returns an array of script handles to enqueue in the editor context.
	 *
	 * @return string[]
	 */
	public function get_editor_script_handles() {
		return array( 'ordd-cart-block-editor' );
	}

	/**
	 * An array of key, value pairs of data made available to the block on the client side.
	 *
	 * @return array
	 */
	public function get_script_data() {
		$validate_date_field = ( 'checked' === get_option( 'orddd_lite_date_field_mandatory' ) ) ? true : false;
		$validate_time_field = ( 'checked' === get_option( 'orddd_lite_time_slot_mandatory' ) ) ? true : false;
		$date_field_label    = '' !== get_option( 'orddd_lite_delivery_date_field_label' ) ? get_option( 'orddd_lite_delivery_date_field_label' ) : __( 'Delivery Date', 'order-delivery-date' );
		$time_field_label    = '' !== get_option( 'orddd_lite_delivery_timeslot_field_label' ) ? get_option( 'orddd_lite_delivery_timeslot_field_label' ) : __( 'Delivery Time', 'order-delivery-date' );
		$time_slot_options   = array();
		$time_slot_enabled   = false;

		// Add time slot values if enabled.
		if ( 'on' === get_option( 'orddd_lite_enable_time_slot' ) ) {
			$time_slot_str     = get_option( 'orddd_lite_delivery_time_slot_log' );
			$time_slots        = json_decode( $time_slot_str, true );
			$time_slot_options = array( __( 'Select a time slot', 'order-delivery-date' ) );
			$time_slot_enabled = true;
		}

		$data = array(
			'dateLabel'           => $date_field_label,
			'checkout'            => '',
			'validate_date_field' => $validate_date_field,
			'time_slot_enabled'   => $time_slot_enabled,
			'timeLabel'           => $time_field_label,
			'time_slot_options'   => $time_slot_options,
			'validate_time_field' => $validate_time_field,
		);
		return $data;
	}

	/**
	 * Register scripts for delivery date block editor.
	 *
	 * @return void
	 */
	public function register_block_editor_scripts() {
		$script_path       = '/build/cart-block.js';
		$script_url        = plugins_url( 'order-delivery-date-for-woocommerce' . $script_path );
		$script_asset_path = plugins_url( 'order-delivery-date-for-woocommerce/build/cart-block.asset.php' );
		$script_asset      = file_exists( $script_asset_path )
			? require $script_asset_path
			: array(
				'dependencies' => array(),
				'version'      => $this->get_file_version( $script_asset_path ),
			);

		wp_register_script(
			'ordd-cart-block-editor',
			$script_url,
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);
	}

	/**
	 * Register scripts for frontend block.
	 *
	 * @return void
	 */
	public function register_block_frontend_scripts() {
		$script_path       = '/build/cart-block-frontend.js';
		$script_url        = plugins_url( '/order-delivery-date-for-woocommerce' . $script_path );
		$style_url         = plugins_url( '/order-delivery-date-for-woocommerce/build/style-index.css' );
		$script_asset_path = WP_PLUGIN_DIR . '/order-delivery-date-for-woocommerce/build/cart-block-frontend.asset.php';

		$script_asset = file_exists( $script_asset_path )
			? require $script_asset_path
			: array(
				'dependencies' => array(),
				'version'      => $this->get_file_version( $script_asset_path ),
			);

		wp_register_script(
			'delivery-date-cart-block-frontend',
			$script_url,
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);

		wp_enqueue_style(
			'delivery-date-cart-block-frontend',
			$style_url,
			array(),
			$script_asset['version']
		);
	}

	/**
	 * Get the file modified time as a cache buster if we're in dev mode.
	 *
	 * @param string $file Local path to the file.
	 * @return string The cache buster value to use for the given file.
	 */
	protected function get_file_version( $file ) {
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG && file_exists( $file ) ) {
			return filemtime( $file );
		}
		return ORDD_LITE_CART_BLOCK_VERSION;
	}
}

<?php
/**
 * Order Delivery Date for WooCommerce Lite
 *
 * Settings added for the plugin in the admin
 *
 * @author      Tyche Softwares
 * @package     Order-Delivery-Date-Lite-for-WooCommerce/Admin/Settings
 * @since       1.5
 */

/**
 * Class for adding the settings of the plugin in admin.
 */
class Orddd_Lite_Settings {

	/**
	 * Adds Order Delivery Date menu in admin dashboard
	 *
	 * @hook admin_menu
	 * @since 1.5
	 */
	public static function orddd_lite_order_delivery_date_menu() {
		add_submenu_page(
			'woocommerce',
			'Order Delivery Date',
			'Order Delivery Date',
			'manage_woocommerce',
			'order_delivery_date_lite',
			array( 'Orddd_Lite_Settings', 'orddd_lite_order_delivery_date_settings' )
		);

		do_action( 'orddd_lite_add_submenu' );
	}

	/**
	 * Callback for adding settings tab in the Order Delivery Date menu
	 *
	 * @globals array $orddd_lite_weekdays Weekdays array
	 * @since 1.5
	 */
	public static function orddd_lite_order_delivery_date_settings() {
		echo '<div id="order-delivery-date-for-woocommerce"></div>';
	}

	/**
	 * Callback for showing the notice for helping small businesses during Covid 19 crisis
	 *
	 * @since 3.10.2
	 */
	public function orddd_lite_info_notice() {

		_e( '<div class="notice notice-info my-dismiss-notice is-dismissible"><p style="font-size:17px;">Across the world, businesses are going through a tough time with COVID-19. In such times, we want to do our bit to support small businesses. Since shoppers are increasingly relying on delivery right now, we are giving <strong>50% off on the Order Delivery Date Pro plugin until April 14 2020</strong>. If you don\'t love it, get a full refund in 30 days, no questions asked!<br><br>Please use the coupon code STAYSAFE to avail the discount. <strong><a target="_blank" href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=customerstore&utm_medium=link&utm_campaign=OrderDeliveryDateLiteCovidNotice"><u>BUY NOW FOR $49.50 (<span style="text-decoration:line-through;">$99.00</span>)</u></a><strong>.</p></div>', 'order-delivery-date' ); //phpcs:ignore
	}
}

$orddd_lite_settings = new orddd_lite_settings();

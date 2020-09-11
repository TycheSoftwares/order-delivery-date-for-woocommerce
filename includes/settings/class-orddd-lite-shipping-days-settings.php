<?php
/**
 * Order Delivery Shipping Days Settings
 *
 * @author Tyche Softwares
 * @package Order-Delivery-Date-Lite-for-WooCommerce/Admin/Settings/Shipping-Days
 * @since 3.9
 * @category Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Orddd_Lite_Shipping_Days_Settings class
 */
class Orddd_Lite_Shipping_Days_Settings {

	/**
	 * Callback for adding Shipping days tab settings
	 */
	public static function orddd_lite_shipping_days_settings_section_callback() {
		?><b><?php echo esc_attr_e( 'Shipping Days', 'order-delivery-date' ); ?> </b><?php echo esc_attr_e( 'refers to the working days of your own company.', 'order-delivery-date' ); ?> <b><?php echo esc_attr_e( 'Delivery Days', 'order-delivery-date' ); ?></b><?php echo esc_attr_e( 'refers to the working days of your shipping company to whom you submit your orders for deliveries.', 'order-delivery-date' ); ?><br> <?php echo esc_attr_e( 'Leave this unchanged if you handle delivery & shipping by yourself.', 'order-delivery-date' ); ?> <a href="https://www.tychesoftwares.com/docs/docs/order-delivery-date-pro-for-woocommerce/setup-delivery-dates/?utm_source=userwebsite&utm_medium=link&utm_campaign=OrderDeliveryDateProSetting" target="_blank" class="dashicons dashicons-external" style="line-height:unset;"></a>
		<?php
	}

	/**
	 * Callback for adding Enable time slot setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 */
	public static function orddd_lite_enable_shipping_days_callback( $args ) {
		?>
		<input type="checkbox" name="orddd_enable_shipping_days" id="orddd_enable_shipping_days" class="day-checkbox" disabled readonly/>
		<label for="orddd_enable_shipping_days"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for selecting weekdays if 'Weekdays' option is selected
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 */
	public static function orddd_lite_shipping_days_callback( $args ) {
		global $orddd_lite_weekdays;
		echo '<select class="orddd_lite_shipping_days" id="orddd_lite_shipping_days" name="orddd_lite_shipping_days[]" placeholder="Select Weekdays" multiple="multiple" disabled readonly>';
		foreach ( $orddd_lite_weekdays as $n => $day_name ) {
			?>
			<option name="<?php echo esc_attr( $n ); ?>" value="<?php echo esc_attr( $n ); ?>" selected><?php echo esc_attr( $day_name ); ?></option>
			<?php
		}
		echo '</select>';
		echo '<script>
            jQuery( ".orddd_lite_shipping_days" ).select2();
        </script>';

		$html = '<label for="orddd_lite_shipping_days"> ' . $args[0] . '</label>';
		echo wp_kses_post( $html );
	}
}

<?php
/**
 * Display General Settings -> Time Settings in admin.
 *
 * @author Tyche Softwares
 * @package Order-Delivery-Date-Pro-for-WooCommerce/Admin/Settings/General
 * @since 2.4
 * @category Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Orddd_Time_Settings class
 *
 * @class Orddd_Time_Settings
 */
class Orddd_Lite_Time_Settings {

	/**
	 * Callback for adding Time settings tab settings
	 */
	public static function orddd_delivery_time_settings_callback() { }

	/**
	 * Callback for adding Enable Time capture setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.4
	 */
	public static function orddd_enable_delivery_time_capture_callback( $args ) {
		?>
		<input type="checkbox" name="orddd_enable_delivery_time" id="orddd_enable_delivery_time" class="day-checkbox" disabled />
		<label for="orddd_enable_delivery_time"><?php echo wp_kses_post( $args[0] ); ?></label>
		<br><strong><em><?php esc_attr_e( 'Upgrade to', 'order-delivery-date' ); ?> <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank"> <?php esc_attr_e( 'Order Delivery Date Pro for WooCommerce' ); ?> </a> <?php esc_attr_e( 'to enable the setting.', 'order-delivery-date' ); ?></em></strong>
		<?php
	}

	/**
	 * Callback for adding Time range setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.4
	 */
	public static function orddd_time_range_callback( $args ) {
		echo '<select name="orddd_delivery_from_hours" id="orddd_delivery_from_hours" size="1" disabled>';

		echo "<option value='1'>1</option>\n";
		echo '</select>&nbsp;:&nbsp;';

		echo '<select name="orddd_delivery_from_mins" id="orddd_delivery_from_mins" size="1" disabled>';
		echo "<option value='0'>0</option>\n";
		echo '</select>&nbsp;-&nbsp;';

		echo '<select name="orddd_delivery_to_hours" id="orddd_delivery_to_hours" size="1" disabled>';
		echo "<option value='1'>1</option>\n";

		echo '</select>&nbsp;:&nbsp;';
		echo '<select name="orddd_delivery_to_mins" id="orddd_delivery_to_mins" size="1" disabled>';
		echo "<option value='0'>0</option>\n";
		echo '</select>';

		?>
		<label for="orddd_time_range"><?php echo wp_kses_post( $args[0] ); ?></label>
		<br><strong><em><?php esc_attr_e( 'Upgrade to', 'order-delivery-date' ); ?> <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank"> <?php esc_attr_e( 'Order Delivery Date Pro for WooCommerce' ); ?> </a> <?php esc_attr_e( 'to enable the setting.', 'order-delivery-date' ); ?></em></strong>
		<?php
	}

	/**
	 * Callback for adding Same day settings
	 */
	public static function orddd_same_day_delivery_callback() { }

	/**
	 * Callback for adding Enable same day delivery setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.4
	 */
	public static function orddd_enable_same_day_delivery_callback( $args ) {
		?>
		<input type="checkbox" name="orddd_enable_same_day_delivery" id="orddd_enable_same_day_delivery" class="day-checkbox" disabled />
		<label for="orddd_enable_same_day_delivery"><?php echo wp_kses_post( $args[0] ); ?></label>
		<br><strong><em><?php esc_attr_e( 'Upgrade to', 'order-delivery-date' ); ?> <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank"> <?php esc_attr_e( 'Order Delivery Date Pro for WooCommerce' ); ?> </a> <?php esc_attr_e( 'to enable the setting.', 'order-delivery-date' ); ?></em></strong>
		<?php
	}

	/**
	 * Callback for adding Cut-off time for same day delivery setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.4
	 */
	public static function orddd_cutoff_time_for_same_day_delivery_orders_callback( $args ) {
		echo '<fieldset>';
		echo '<label for="orddd_disable_same_day_delivery_after_hours">' . esc_attr( __( 'Hours:', 'order-delivery-date' ) ) . '</label><select name="orddd_disable_same_day_delivery_after_hours" id="orddd_disable_same_day_delivery_after_hours" size="1" disabled>';
		echo '<option value="0">0</option>';

		echo '</select>';

		echo '&nbsp;&nbsp;<label for="orddd_disable_same_day_delivery_after_minutes">' . esc_attr( __( 'Mins:', 'order-delivery-date' ) ) . '</label><select name="orddd_disable_same_day_delivery_after_minutes" id="orddd_disable_same_day_delivery_after_minutes" size="1" disabled>';
		echo '<option value="0">0</option>';

		echo '</select>';
		echo '</fieldset>';
		?>
		<label for="cutoff_time_for_same_day_delivery_orders"><?php echo wp_kses_post( $args[0] ); ?></label>
		<br><strong><em><?php esc_attr_e( 'Upgrade to', 'order-delivery-date' ); ?> <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank"> <?php esc_attr_e( 'Order Delivery Date Pro for WooCommerce' ); ?> </a> <?php esc_attr_e( 'to enable the setting.', 'order-delivery-date' ); ?></em></strong>
		<?php
	}

	/**
	 * Callback for adding Additional charges setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.4
	 */
	public static function orddd_additional_charges_for_same_day_delivery_callback( $args ) {
		?>
		<input type="text" name="orddd_same_day_additional_charges" id="orddd_same_day_additional_charges" value="" disabled/>
		<label for="orddd_same_day_additional_charges"><?php echo wp_kses_post( $args[0] ); ?></label>
		<br><strong><em><?php esc_attr_e( 'Upgrade to', 'order-delivery-date' ); ?> <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank"> <?php esc_attr_e( 'Order Delivery Date Pro for WooCommerce' ); ?> </a> <?php esc_attr_e( 'to enable the setting.', 'order-delivery-date' ); ?></em></strong>
		<?php
	}

	/**
	 * Callback for adding Next day settings
	 */
	public static function orddd_next_day_delivery_callback() { }

	/**
	 * Callback for adding Enable next day delivery setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.4
	 */
	public static function orddd_enable_next_day_delivery_callback( $args ) {
		?>
		<input type="checkbox" name="orddd_enable_next_day_delivery" id="orddd_enable_next_day_delivery" class="day-checkbox" disabled/>
		<label for="orddd_enable_next_day_delivery"><?php echo wp_kses_post( $args[0] ); ?></label>
		<br><strong><em><?php esc_attr_e( 'Upgrade to', 'order-delivery-date' ); ?> <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank"> <?php esc_attr_e( 'Order Delivery Date Pro for WooCommerce' ); ?> </a> <?php esc_attr_e( 'to enable the setting.', 'order-delivery-date' ); ?></em></strong>
		<?php
	}

	/**
	 * Callback for adding Cut-off time for next day delivery setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.4
	 */
	public static function orddd_cutoff_time_for_next_day_delivery_orders_callback( $args ) {
		echo '<fieldset>';

		echo '<label for="orddd_disable_next_day_delivery_after_hours">' . esc_attr( __( 'Hours:', 'order-delivery-date' ) ) . '</label><select name="orddd_disable_next_day_delivery_after_hours" id="orddd_disable_next_day_delivery_after_hours" size="1" disabled>';
		echo '<option value="0">0</option>';

		echo '</select>&nbsp;&nbsp;<label for="orddd_disable_next_day_delivery_after_minutes">' . esc_attr( __( 'Mins:', 'order-delivery-date' ) ) . '</label><select name="orddd_disable_next_day_delivery_after_minutes" id="orddd_disable_next_day_delivery_after_minutes" size="1" disabled>';
		echo '<option value="0">0</option>';

		printf( '</select>' );
		echo '</fieldset>';
		?>
		<label for="cutoff_time_for_next_day_delivery_orders"><?php echo wp_kses_post( $args[0] ); ?></label>
		<br><strong><em><?php esc_attr_e( 'Upgrade to', 'order-delivery-date' ); ?> <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank"> <?php esc_attr_e( 'Order Delivery Date Pro for WooCommerce' ); ?> </a> <?php esc_attr_e( 'to enable the setting.', 'order-delivery-date' ); ?></em></strong>
		<?php
	}

	/**
	 * Callback for adding Additional charges for next day delivery setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.4
	 */
	public static function orddd_additional_charges_for_next_day_delivery_callback( $args ) {
		printf(
			'<input type="text" name="orddd_next_day_additional_charges" id="orddd_next_day_additional_charges" value="" disabled/>'
		);

		?>
		<label for="orddd_next_day_additional_charges"><?php echo wp_kses_post( $args[0] ); ?></label>
		<br><strong><em><?php esc_attr_e( 'Upgrade to', 'order-delivery-date' ); ?> <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank"> <?php esc_attr_e( 'Order Delivery Date Pro for WooCommerce' ); ?> </a> <?php esc_attr_e( 'to enable the setting.', 'order-delivery-date' ); ?></em></strong>
		<?php
	}

}

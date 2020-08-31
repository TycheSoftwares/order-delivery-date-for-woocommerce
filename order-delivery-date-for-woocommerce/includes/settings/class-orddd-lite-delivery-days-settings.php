<?php
/**
 * Display General Settings -> Specific Delivery Dates Settings in admin.
 *
 * @author Tyche Softwares
 * @package Order-Delivery-Date-Pro-for-WooCommerce/Admin/Settings/General
 * @since 2.8.3
 * @category Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Specific dates
 */
class ORDDD_Lite_Delivery_Days_Settings {

	/**
	 * Callback for adding Delivery Dates tab settings
	 */
	public static function orddd_delivery_days_admin_setting_callback() {
		?>
		<em><?php echo esc_attr_e( 'Add delivery charges (if applicable), maximum order deliveries per day and select the specific delivery date.', 'order-delivery-date' ); ?></em>
		<br>
		<strong><em><?php esc_attr_e( 'Upgrade to', 'order-delivery-date' ); ?> <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank"> <?php esc_attr_e( 'Order Delivery Date Pro for WooCommerce' ); ?> </a> <?php esc_attr_e( 'to enable the setting.', 'order-delivery-date' ); ?></em></strong>
		<?php
	}

	/**
	 * Callback for adding Enable Specific date setting
	 *
	 * @param array $args Extra arguments for outputting the field.
	 * @since 3.11.0
	 */
	public static function orddd_delivery_days_enable_callback( $args ) {
		echo '<input type="checkbox" name="orddd_enable_specific_delivery_dates" id="orddd_enable_specific_delivery_dates" class="day-checkbox" disabled/>';

		$html = '<label for="orddd_enable_specific_delivery_dates"> ' . $args[0] . '</label>';
		echo $html; //phpcs:ignore
	}

	/**
	 * Callback to add first Specific date field
	 *
	 * @param array $args Extra arguments for outputting the field.
	 */
	public static function orddd_delivery_days_datepicker_1_callback( $args ) {
		$currency_symbol = get_woocommerce_currency_symbol();

		echo '<input type="text" name="orddd_delivery_date_1" id="orddd_delivery_date_1" class="day-checkbox" placeholder="Select Date" disabled/>' . esc_attr( $currency_symbol ) . '<input class="orddd_specific_charges" type="text" name="additional_charges_1" id="additional_charges_1" placeholder="Charges" disabled/>';
		echo '<input class="orddd_specific_charges_label" type="text" name="specific_charges_label_1" id="specific_charges_label_1" placeholder="Delivery Charges Label" disabled />';
		echo '<input class="orddd_max_orders_specific" type="number" min="0" step="1" name="orddd_max_orders_specific_1" id="orddd_max_orders_specific_1" placeholder="Max Order Deliveries" style="margin-top:6px;" disabled/>';

		$html = '<label for="orddd_delivery_date_1"> ' . $args[0] . '</label>';
		echo $html; // phpcs:ignore
	}

	/**
	 * Callback to add second Specific date field
	 *
	 * @param array $args Extra arguments for outputting the field.
	 */
	public static function orddd_delivery_days_datepicker_2_callback( $args ) {
		$currency_symbol = get_woocommerce_currency_symbol();

		echo '<input type="text" name="orddd_delivery_date_2" id="orddd_delivery_date_2" class="day-checkbox" placeholder="Select Date" disabled/>' . esc_attr( $currency_symbol ) . '<input class="orddd_specific_charges" type="text" name="additional_charges_2" id="additional_charges_2" placeholder="Charges" disabled />';
		echo '<input class="orddd_specific_charges_label" type="text" name="specific_charges_label_2" id="specific_charges_label_2" placeholder="Delivery Charges Label" disabled />';
		echo '<input class="orddd_max_orders_specific" type="number" min="0" step="1" name="orddd_max_orders_specific_2" id="orddd_max_orders_specific_2" placeholder="Max Order Deliveries" style="margin-top:6px;" disabled/>';

		$html = '<label for="orddd_delivery_date_2"> ' . $args[0] . '</label>';
		echo $html; //phpcs:ignore
	}

	/**
	 * Callback to add third Specific date field
	 *
	 * @param array $args Extra arguments for outputting the field.
	 */
	public static function orddd_delivery_days_datepicker_3_callback( $args ) {
		$currency_symbol = get_woocommerce_currency_symbol();

		echo '<input type="text" name="orddd_delivery_date_3" id="orddd_delivery_date_3" class="day-checkbox" placeholder="Select Date" disabled/>' . esc_attr( $currency_symbol ) . '<input class="orddd_specific_charges" type="text" name="additional_charges_3" id="additional_charges_3" placeholder="Charges" disabled />';
		echo '<input class="orddd_specific_charges_label" type="text" name="specific_charges_label_3" id="specific_charges_label_3" placeholder="Delivery Charges Label" disabled />';
		echo '<input class="orddd_max_orders_specific" type="number" min="0" step="1" name="orddd_max_orders_specific_3" id="orddd_max_orders_specific_3" placeholder="Max Order Deliveries" style="margin-top:6px;" disabled/>';
		$html = '<label for="orddd_delivery_date_3"> ' . $args[0] . '</label>';
		echo $html; //phpcs:ignore
	}
}

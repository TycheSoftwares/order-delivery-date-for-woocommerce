<?php
/**
 * Order Delivery Date Additional Settings in General Settings in admin.
 *
 * @author Tyche Softwares
 * @package Order-Delivery-Date-Lite/Admin/Settings/General
 * @since 3.11.0
 * @category Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Orddd_Additional_Settings class
 *
 * @class Orddd_Additional_Settings
 */
class Orddd_Lite_Additional_Settings {

	/**
	 * Callback for adding Additional Settings tab settings
	 */
	public static function orddd_lite_additional_settings_section_callback() { }

	/**
	 * Callback for adding Delivery date column on WooCommerce->Orders page setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 1.5
	 */
	public static function orddd_lite_show_column_on_orders_page_check_callback( $args ) {
		?>
		<input type="checkbox" name="orddd_show_column_on_orders_page_check" id="orddd_show_column_on_orders_page_check" class="day-checkbox" disabled/>
		<label for="orddd_show_column_on_orders_page_check"><?php echo wp_kses_post( $args[0] ); ?></label>
		<strong><em>Upgrade to <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank">Order Delivery Date Pro for WooCommerce</a> to enable the setting.</em></strong>

		<br>
		<input type="checkbox" name="orddd_lite_enable_default_sorting_of_column" id="orddd_lite_enable_default_sorting_of_column" value="checked" <?php echo esc_attr( get_option( 'orddd_lite_enable_default_sorting_of_column' ) ); ?>/>
		<label for="orddd_lite_enable_default_sorting_of_column"><?php echo esc_attr( __( 'Enable default sorting of orders (in descending order) by Delivery Date on WooCommerce -> Orders page', 'order-delivery-date' ) ); ?></label>

		<?php
	}

	/**
	 * Autofill date & time on the checkout page
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_lite_enable_autofill_of_delivery_date_callback( $args ) {
		$orddd_lite_auto_populate_first_available_date = '';
		if ( get_option( 'orddd_lite_auto_populate_first_available_date' ) === 'on' ) {
			$orddd_lite_auto_populate_first_available_date = 'checked';
		}

		?>
		<input type="checkbox" name="orddd_lite_auto_populate_first_available_date" id="orddd_lite_auto_populate_first_available_date" class="day-checkbox" <?php echo esc_attr( $orddd_lite_auto_populate_first_available_date ); ?> />
		<label for="orddd_lite_auto_populate_first_available_date"><?php echo esc_attr( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Filter on WooCommerce->Orders page setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_lite_show_filter_on_orders_page_check_callback( $args ) {
		?>
		<input type="checkbox" name="orddd_show_filter_on_orders_page_check" id="orddd_show_filter_on_orders_page_check" class="day-checkbox" disabled />
		<label for="orddd_show_filter_on_orders_page_check"><?php echo wp_kses_post( $args[0] ); ?></label>
		<br><strong><em>Upgrade to <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank">Order Delivery Date Pro for WooCommerce</a> to enable the setting.</em></strong>
		<?php
	}

	/**
	 * Callback for hiding Delivery Date fields on the checkout page for Virtual product setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_lite_appearance_virtual_product_callback( $args ) {
		if ( get_option( 'orddd_lite_no_fields_for_virtual_product' ) === 'on' ) {
			$orddd_lite_no_fields_for_virtual_product = 'checked';
		} else {
			$orddd_lite_no_fields_for_virtual_product = '';
		}

		?>
		<input type="checkbox" name="orddd_lite_no_fields_for_virtual_product" id="orddd_lite_no_fields_for_virtual_product" class="day-checkbox" <?php echo esc_attr( $orddd_lite_no_fields_for_virtual_product ); ?> />
		<label class="orddd_lite_no_fields_for_product_type"><?php esc_attr_e( 'Virtual Products', 'order-delivery-date' ); ?></label>
		<?php
		$orddd_lite_no_fields_for_featured_product = '';
		if ( get_option( 'orddd_lite_no_fields_for_featured_product' ) === 'on' ) {
			$orddd_lite_no_fields_for_featured_product = 'checked';
		}

		?>
		<input type="checkbox" name="orddd_lite_no_fields_for_featured_product" id="orddd_lite_no_fields_for_featured_product" class="day-checkbox" <?php echo esc_attr( $orddd_lite_no_fields_for_featured_product ); ?> />
		<label class="orddd_lite_no_fields_for_product_type"><?php esc_attr_e( 'Featured Products', 'order-delivery-date' ); ?></label>

		<label for="orddd_lite_no_fields_for_product_type"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Integration with Other Plugins settings
	 */
	public static function orddd_lite_integration_with_other_plugins_callback() { }

	/**
	 * Callback for adding Delivery date and/or Time slot in csv export setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 3.11.0
	 */
	public static function orddd_lite_show_fields_in_csv_export_check_callback( $args ) {
		?>
		<input type="checkbox" name="orddd_show_fields_in_csv_export_check" id="orddd_show_fields_in_csv_export_check" class="day-checkbox" disabled/>
		<label for="orddd_show_fields_in_csv_export_check"><?php echo wp_kses_post( $args[0] ); ?></label>
		<br><strong><em>Upgrade to <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank">Order Delivery Date Pro for WooCommerce</a> to enable the setting.</em></strong>
		<?php
	}

	/**
	 * Callback for adding Delivery date and/or Time slot in PDF invoices and Packing slips setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 3.11.0
	 */
	public static function orddd_lite_show_fields_in_pdf_invoice_and_packing_slips_callback( $args ) {
		?>
		<input type="checkbox" name="orddd_show_fields_in_pdf_invoice_and_packing_slips" id="orddd_show_fields_in_pdf_invoice_and_packing_slips" class="day-checkbox" disabled/>
		<label for="orddd_show_fields_in_pdf_invoice_and_packing_slips"><?php echo wp_kses_post( $args[0] ); ?></label>
		<br><strong><em>Upgrade to <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank">Order Delivery Date Pro for WooCommerce</a> to enable the setting.</em></strong>
		<?php
	}

	/**
	 * Callback for adding Delivery date and/or Time slot in Print Invoice and Packing slips setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 3.11.0
	 */
	public static function orddd_lite_show_fields_in_invoice_and_delivery_note_callback( $args ) {
		?>
		<input type="checkbox" name="orddd_show_fields_in_invoice_and_delivery_note" id="orddd_show_fields_in_invoice_and_delivery_note" class="day-checkbox" disabled />
		<label for="orddd_show_fields_in_invoice_and_delivery_note"><?php echo wp_kses_post( $args[0] ); ?></label>
		<br><strong><em>Upgrade to <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank">Order Delivery Date Pro for WooCommerce</a> to enable the setting.</em></strong>
		<?php
	}

	/**
	 * Callback for adding Delivery date and/or Time slot in Cloud print setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 3.11.0
	 */
	public static function orddd_lite_show_fields_in_cloud_print_orders_callback( $args ) {
		?>
		<input type="checkbox" name="orddd_show_fields_in_cloud_print_orders" id="orddd_show_fields_in_cloud_print_orders" class="day-checkbox" disabled/>
		<label for="orddd_show_fields_in_cloud_print_orders"><?php echo wp_kses_post( $args[0] ); ?></label>;
		<br><strong><em>Upgrade to <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank">Order Delivery Date Pro for WooCommerce</a> to enable the setting.</em></strong>
		<?php
	}

	/**
	 * Callback for enabling tax calculation on the checkout page for Delivery Charges
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 3.11.0
	 */
	public static function orddd_lite_enable_tax_calculation_for_delivery_charges_callback( $args ) {
		?>
		<input type="checkbox" name="orddd_enable_tax_calculation_for_delivery_charges" id="orddd_enable_tax_calculation_for_delivery_charges" class="day-checkbox" disabled/>
		<label for="orddd_enable_tax_calculation_for_delivery_charges"><?php echo wp_kses_post( $args[0] ); ?></label>
		<br><strong><em>Upgrade to <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank">Order Delivery Date Pro for WooCommerce</a> to enable the setting.</em></strong>
		<?php
	}

	/**
	 * Callback for adding Compatibility with other plugin section
	 */
	public static function orddd_lite_compatibility_with_other_plugins_callback() {}

	/**
	 * Enable Compatibility with WooCommerce Shipping Multiple Addresses plugin
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 3.11.0
	 */
	public static function orddd_lite_shipping_multiple_address_compatibility_callback( $args ) {
		?>
		<input type="checkbox" name="orddd_shipping_multiple_address_compatibility" id="orddd_shipping_multiple_address_compatibility" class="day-checkbox" disabled/>
		<label for="orddd_shipping_multiple_address_compatibility"><?php echo wp_kses_post( $args[0] ); ?></label>
		<br><strong><em>Upgrade to <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank">Order Delivery Date Pro for WooCommerce</a> to enable the setting.</em></strong>
		<?php
	}

	/**
	 * Enable Compatibility with WooCommerce Amazon Payments Advanced Gateway
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 3.11.0
	 */
	public static function orddd_lite_amazon_payments_advanced_gateway_compatibility_callback( $args ) {
		?>
		<input type="checkbox" name="orddd_amazon_payments_advanced_gateway_compatibility" id="orddd_amazon_payments_advanced_gateway_compatibility" class="day-checkbox" disabled />
		<label for="orddd_amazon_payments_advanced_gateway_compatibility"><?php echo wp_kses_post( $args[0] ); ?></label>
		<br><strong><em>Upgrade to <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank">Order Delivery Date Pro for WooCommerce</a> to enable the setting.</em></strong>
		<?php
	}

	/**
	 * Enable custom to display the availability on hover of the calendar date on the checkout page.
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 */
	public static function orddd_lite_enable_availability_display_callback( $args ) {
		?>
		<input type="checkbox" name="orddd_enable_availability_display" id="orddd_enable_availability_display" class="day-checkbox" disabled />
		<label for="orddd_enable_availability_display"><?php echo wp_kses_post( $args[0] ); ?></label>
		<br><strong><em>Upgrade to <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank">Order Delivery Date Pro for WooCommerce</a> to enable the setting.</em></strong>
		<?php
	}


	/**
	 * Enable customers to show partially booked dates with diagonal seperate colors for booked and available dates.
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 */
	public static function orddd_lite_show_partially_booked_dates_callback( $args ) {
		?>
		<input type="checkbox" name="orddd_show_partially_booked_dates" id="orddd_show_partially_booked_dates" class="day-checkbox" disabled />
		<label for="orddd_show_partially_booked_dates"><?php echo wp_kses_post( $args[0] ); ?></label>
		<br><strong><em>Upgrade to <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank">Order Delivery Date Pro for WooCommerce</a> to enable the setting.</em></strong>
		<?php
	}

	/**
	 * Enable customers to edit or modify the deliveru date
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 3.11.0
	 */
	public static function orddd_lite_allow_customers_to_edit_date_callback( $args ) {
		?>
		<input type="checkbox" name="orddd_allow_customers_to_edit_date" id="orddd_allow_customers_to_edit_date" class="day-checkbox" disabled/>
		<label for="orddd_allow_customers_to_edit_date"><?php echo wp_kses_post( $args[0] ); ?></label>

		<input type="checkbox" name="orddd_send_email_to_admin_when_date_updated" id="orddd_send_email_to_admin_when_date_updated" class="day-checkbox" disabled />
		<label for="orddd_send_email_to_admin_when_date_updated" id="orddd_send_email_to_admin_when_date_updated"><?php echo esc_attr( __( 'When enabled, email notification will be sent to the admin when the Delivery Date & Time is edited by the customers on the My Account -> Orders -> View page. So customers will be able to edit the date and time once the order is placed.', 'order-delivery-date' ) ); ?></label>

		<br><input type="checkbox" name="orddd_disable_edit_after_cutoff" id="orddd_disable_edit_after_cutoff" class="day-checkbox" disabled/>
		<label for="orddd_disable_edit_after_cutoff"><?php echo esc_attr( __( 'Do not allow customers to edit the delivery date after cut off time has passed.', 'order-delivery-date' ) ); ?></label>
		<br><strong><em>Upgrade to <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank">Order Delivery Date Pro for WooCommerce</a> to enable the setting.</em></strong>
		<?php
	}
}

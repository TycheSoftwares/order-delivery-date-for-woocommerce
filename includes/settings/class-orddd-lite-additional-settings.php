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
		$orddd_lite_show_column_on_orders_page_check = '';
		if ( get_option( 'orddd_lite_show_column_on_orders_page_check' ) === 'on' ) {
			$orddd_lite_show_column_on_orders_page_check = 'checked';
		}
		$orddd_lite_enable_default_sorting_of_column = '';
		if ( get_option( 'orddd_lite_enable_default_sorting_of_column' ) === 'on' ) {
			$orddd_lite_enable_default_sorting_of_column = 'checked';
		}
		?>
		<input type="checkbox" name="orddd_lite_show_column_on_orders_page_check" id="orddd_lite_show_column_on_orders_page_check" <?php echo esc_attr( $orddd_lite_show_column_on_orders_page_check ); ?> class="day-checkbox" />
		<label for="orddd_lite_show_column_on_orders_page_check"><?php echo wp_kses_post( $args[0] ); ?></label>
		<br>		
		<input type="checkbox" name="orddd_lite_enable_default_sorting_of_column" id="orddd_lite_enable_default_sorting_of_column"  <?php echo esc_attr( $orddd_lite_enable_default_sorting_of_column ); ?>  />
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
		$show_filter_on_orders_page = 'on' === get_option( 'orddd_lite_show_filter_on_orders_page_check' ) ? 'checked' : '';
		?>
		<input type="checkbox" name="orddd_lite_show_filter_on_orders_page_check" id="orddd_lite_show_filter_on_orders_page_check" class="day-checkbox" <?php echo esc_attr( $show_filter_on_orders_page ); ?> />
		<label for="orddd_lite_show_filter_on_orders_page_check"><?php echo wp_kses_post( $args[0] ); ?></label>
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
	 * Enable customers to show partially booked dates with diagonal seperate colors for booked and available dates.
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 */
	public static function orddd_lite_show_partially_booked_dates_callback( $args ) {
		$orddd_lite_show_partially_booked_dates = '';
		if ( 'on' === get_option( 'orddd_lite_show_partially_booked_dates' ) ) {
			$orddd_lite_show_partially_booked_dates = 'checked';
		}
		?>
		<input type="checkbox" name="orddd_lite_show_partially_booked_dates" id="orddd_lite_show_partially_booked_dates" class="day-checkbox" <?php echo esc_attr( $orddd_lite_show_partially_booked_dates ); ?> />
		<label for="orddd_lite_show_partially_booked_dates"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}
}

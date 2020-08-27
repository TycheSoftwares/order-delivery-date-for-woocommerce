<?php
/**
 * General Settings for Order Delivery Date
 *
 * @author Tyche Softwares
 * @package Order-Delivery-Date-Lite-for-WooCommerce/Admin/Settings/Date
 * @since 3.9
 * @category Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Orddd_Lite_Date_Settings Class
 *
 * @class Orddd_Lite_Date_Settings
 */
class Orddd_Lite_Date_Settings {

	/**
	 * Callback for Order Delivery Date Settings section
	 *
	 * @since 1.5
	 */
	public static function orddd_lite_delivery_date_setting() { }

	/**
	 * Callback for adding Enable Delivery Date checkbox
	 *
	 * @param array $args Callback arguments.
	 *
	 * @since 1.5
	 */
	public static function orddd_lite_enable_delivery_date_callback( $args ) {
		$enable_delivery_date = '';
		if ( get_option( 'orddd_lite_enable_delivery_date' ) === 'on' ) {
			$enable_delivery_date = 'checked';
		}

		?>
		<input type="checkbox" name="orddd_lite_enable_delivery_date" id="orddd_lite_enable_delivery_date" class="day-checkbox" value="on" <?php echo esc_attr( $enable_delivery_date ); ?> />
		<label for="orddd_lite_enable_delivery_date"><?php echo esc_html( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for delivery checkout option to select Calendar or Text Block
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 3.9
	 *
	 * @todo: disable this field
	 */
	public static function orddd_lite_delivery_checkout_options_callback( $args ) {
		global $orddd_weekdays;

		$orddd_delivery_checkout_options_delivery_calendar = 'checked';

		?>
		<p>
			<label><input type="radio" name="orddd_lite_delivery_checkout_options" id="orddd_lite_delivery_checkout_options" value="delivery_calendar"<?php echo esc_attr( $orddd_delivery_checkout_options_delivery_calendar ); ?>/><?php esc_attr_e( 'Calendar', 'order-delivery-date' ); ?></label>
			<label><input type="radio" name="orddd_delivery_checkout_options" id="orddd_delivery_checkout_options" value="text_block" disabled readonly/><?php esc_attr_e( 'Text block', 'order-delivery-date' ); ?> <b><i><?php esc_attr_e( 'Upgrade to', 'order-delivery-date' ); ?> <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank"> <?php esc_attr_e( 'Order Delivery Date Pro for WooCommerce' ); ?> </a> <?php esc_attr_e( 'to enable the setting.', 'order-delivery-date' ); ?></i></b></label>
		</p>
		<label for="orddd_lite_delivery_checkout_options"><?php echo esc_attr( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Delivery Weekdays setting
	 *
	 * @param string $input Value of the weekday setting.
	 *
	 * @return string $input
	 *
	 * @todo Unused Function. Need to check and remove it.
	 * @since 3.9
	 */
	public static function orddd_lite_weekday_0_save( $input ) {
		$input = self::return_orddd_lite_weekday_input( 'orddd_lite_weekday_0' );
		return $input;
	}

	/**
	 * Callback for adding Delivery Weekdays setting
	 *
	 * @param string $input Value of the weekday setting.
	 *
	 * @return string $input
	 *
	 * @todo Unused Function. Need to check and remove it.
	 * @since 3.9
	 */
	public static function orddd_lite_weekday_1_save( $input ) {
		$input = self::return_orddd_lite_weekday_input( 'orddd_lite_weekday_1' );
		return $input;
	}

	/**
	 * Callback for adding Delivery Weekdays setting
	 *
	 * @param string $input Value of the weekday setting.
	 *
	 * @return string $input
	 *
	 * @todo Unused Function. Need to check and remove it.
	 * @since 3.9
	 */
	public static function orddd_lite_weekday_2_save( $input ) {
		$input = self::return_orddd_lite_weekday_input( 'orddd_lite_weekday_2' );
		return $input;
	}

	/**
	 * Callback for adding Delivery Weekdays setting
	 *
	 * @param string $input Value of the weekday setting.
	 *
	 * @return string $input
	 *
	 * @todo Unused Function. Need to check and remove it.
	 * @since 3.9
	 */
	public static function orddd_lite_weekday_3_save( $input ) {
		$input = self::return_orddd_lite_weekday_input( 'orddd_lite_weekday_3' );
		return $input;
	}

	/**
	 * Callback for adding Delivery Weekdays setting
	 *
	 * @param string $input Value of the weekday setting.
	 *
	 * @return string $input
	 *
	 * @todo Unused Function. Need to check and remove it.
	 * @since 3.9
	 */
	public static function orddd_lite_weekday_4_save( $input ) {
		$input = self::return_orddd_lite_weekday_input( 'orddd_lite_weekday_4' );
		return $input;
	}

	/**
	 * Callback for adding Delivery Weekdays setting
	 *
	 * @param string $input Value of the weekday setting.
	 *
	 * @return string $input
	 *
	 * @todo Unused Function. Need to check and remove it.
	 * @since 3.9
	 */
	public static function orddd_lite_weekday_5_save( $input ) {
		$input = self::return_orddd_lite_weekday_input( 'orddd_lite_weekday_5' );
		return $input;
	}

	/**
	 * Callback for adding Delivery Weekdays setting
	 *
	 * @param string $input Value of the weekday setting.
	 *
	 * @return string $input
	 *
	 * @todo Unused Function. Need to check and remove it.
	 * @since 3.9
	 */
	public static function orddd_lite_weekday_6_save( $input ) {
		$input = self::return_orddd_lite_weekday_input( 'orddd_lite_weekday_6' );
		return $input;
	}

	/**
	 * Return the selected weekdays
	 *
	 * @todo Unused Function. Need to check and remove it.
	 * @param string $weekday Weekday string.
	 * @return string $input
	 * @since 3.9
	 */
	public static function return_orddd_lite_weekday_input( $weekday ) {
		global $orddd_lite_weekdays;
		$input = '';
		// phpcs:ignore WordPress.Security.NonceVerification
		if ( isset( $_POST['orddd_lite_weekdays'] ) ) {
			// Any of the WordPress data sanitization functions can be used here.
			// phpcs:ignore WordPress.Security.NonceVerification
			$weekdays = array_map( 'sanitize_text_field', wp_unslash( $_POST['orddd_lite_weekdays'] ) );

			if ( in_array( $weekday, $weekdays, true ) ) {
				$input = 'checked';
			}
		}
		return $input;
	}

	/**
	 * Callback for adding Delivery Weekdays dropdown
	 *
	 * @param array $args Callback arguments.
	 *
	 * @since 1.5
	 */
	public static function orddd_lite_delivery_days_callback( $args ) {
		global $orddd_lite_weekdays;

		?>
		<select class="orddd_lite_weekdays" id="orddd_lite_weekdays" name="orddd_lite_weekdays[]" placeholder="Select Weekdays" multiple="multiple">
		<?php

		foreach ( $orddd_lite_weekdays as $n => $day_name ) {
			if ( 'checked' === get_option( $n ) ) {
				?>
				<option name="<?php echo esc_attr( $n ); ?>" value="<?php echo esc_attr( $n ); ?>" selected><?php echo esc_attr( $day_name ); ?></option>
				<?php
			} else {
				?>
				<option name="<?php echo esc_attr( $n ); ?>" value="<?php echo esc_attr( $n ); ?>"><?php echo esc_attr( $day_name ); ?></option>
				<?php
			}
		}
		echo '</select>';
		echo '<script>
            jQuery( ".orddd_lite_weekdays" ).select2();
        </script>';

		?>
		<label for="orddd_lite_weekdays"><?php echo esc_html( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback to add Weekday Settings field
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 3.9
	 *
	 * @todo: disable this field
	 */
	public static function orddd_lite_enable_day_wise_settings_callback( $args ) {
		?>
		<input type="checkbox" name="orddd_enable_day_wise_settings" id="orddd_enable_day_wise_settings" class="day-checkbox" value="on" disabled readonly />
		<label for="orddd_enable_day_wise_settings"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Minimum Delivery Time (in hours) text field
	 *
	 * @param array $args Callback arguments.
	 *
	 * @since 1.5
	 */
	public static function orddd_lite_minimum_delivery_time_callback( $args ) {
		?>
		<input type="number" min="0" name="orddd_lite_minimumOrderDays" id="orddd_lite_minimumOrderDays" value="<?php echo esc_attr( get_option( 'orddd_lite_minimumOrderDays' ) ); ?>"/>
		<label for="orddd_lite_minimumOrderDays"><?php echo esc_attr( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Number of Dates to choose text field
	 *
	 * @param array $args Callback arguments.
	 *
	 * @since 1.5
	 */
	public static function orddd_lite_number_of_dates_callback( $args ) {
		?>
		<input type="number" min="0" name="orddd_lite_number_of_dates" id="orddd_lite_number_of_dates" value="<?php echo esc_attr( get_option( 'orddd_lite_number_of_dates' ) ); ?>"/>
		<label for="orddd_lite_number_of_dates"><?php echo esc_attr( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Mandatory checkbox
	 *
	 * @param array $args Callback arguments.
	 *
	 * @since 1.5
	 */
	public static function orddd_lite_date_field_mandatory_callback( $args ) {
		?>
		<input type="checkbox" name="orddd_lite_date_field_mandatory" id="orddd_lite_date_field_mandatory" class="day-checkbox" value="checked" <?php echo esc_attr( get_option( 'orddd_lite_date_field_mandatory' ) ); ?> />
		<label for="orddd_lite_date_field_mandatory"><?php echo esc_attr( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Maximum orders per day text field
	 *
	 * @param array $args Callback arguments.
	 *
	 * @since 1.5
	 */
	public static function orddd_lite_lockout_date_after_orders_callback( $args ) {
		?>
		<input type="number" min="0" name="orddd_lite_lockout_date_after_orders" id="orddd_lite_lockout_date_after_orders" value="<?php echo esc_attr( get_option( 'orddd_lite_lockout_date_after_orders' ) ); ?>"/>
		<label for="orddd_lite_lockout_date_after_orders"><?php echo esc_attr( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback to add the Maximum Deliveries based on per product quantity setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 3.9
	 *
	 * @todo: disable this field.
	 */
	public static function orddd_lockout_date_quantity_based_callback( $args ) {
		$orddd_lockout_date_quantity_based = '';
		if ( get_option( 'orddd_lockout_date_quantity_based' ) === 'on' ) {
			$orddd_lockout_date_quantity_based = 'checked';
		}

		?>
		<input type="checkbox" name="orddd_lockout_date_quantity_based" id="orddd_lockout_date_quantity_based" value="on" <?php echo esc_html( $orddd_lockout_date_quantity_based ); ?>/>
		<label for="orddd_lockout_date_quantity_based"><?php echo esc_attr( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding a checkbox of Calculating minimum delivery time on disable days
	 *
	 * @param array $args Callback arguments.
	 *
	 * @since 1.5
	 */
	public static function orddd_lite_calculate_min_time_disabled_days_callback( $args ) {
		$orddd_lite_calculate_min_time_disabled_days = '';
		if ( get_option( 'orddd_lite_calculate_min_time_disabled_days' ) === 'on' ) {
			$orddd_lite_calculate_min_time_disabled_days = 'checked';
		}

		?>
		<input type="checkbox" name="orddd_lite_calculate_min_time_disabled_days" id="orddd_lite_calculate_min_time_disabled_days" class="day-checkbox" <?php echo esc_attr( $orddd_lite_calculate_min_time_disabled_days ); ?> />
		<label for="orddd_lite_calculate_min_time_disabled_days"><?php echo esc_attr( $args[0] ); ?></label>
		<?php
	}
}

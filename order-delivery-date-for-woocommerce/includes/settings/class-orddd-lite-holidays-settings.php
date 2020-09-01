<?php
/**
 * ORDDD Holiday Settings
 *
 * @author Tyche Softwares
 * @package Order-Delivery-Date-Lite-for-WooCommerce/Admin/Settings/Holidays
 * @since 3.9
 * @category Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Orddd_Lite_Holidays_Settings Class
 *
 * @class Orddd_Lite_Holidays_Settings
 */
class Orddd_Lite_Holidays_Settings {

	/**
	 * Callback for adding Holidays tab
	 *
	 * $params array $args Callback arguments
	 *
	 * @since 1.5
	 */
	public static function orddd_lite_holidays_admin_settings_callback() {}

	/**
	 * Callback for adding Holiday name text field
	 *
	 * @param array $args Callback arguments.
	 *
	 * @since 1.5
	 */
	public static function orddd_lite_holidays_name_callback( $args ) {
		?>
		<input type="text" name="orddd_lite_holiday_name" id="orddd_lite_holiday_name" class="orddd_lite_holiday_name"/>
		<?php
	}

	/**
	 * Callback for adding Holiday start date setting
	 *
	 * @since 1.5
	 */
	public static function orddd_lite_holidays_from_date_callback() {
		$current_language = get_option( 'orddd_lite_language_selected' );
		$day_selected     = get_option( 'orddd_lite_start_of_week' );
		if ( '' === $day_selected ||
			false === $day_selected ) {
			$day_selected = 0;
		}
		print( '<script type="text/javascript">
                 jQuery( document ).ready( function() {
                    jQuery.datepicker.setDefaults( jQuery.datepicker.regional[ "en-GB" ] );
                    var formats = [ "mm-dd-yy", "d.m.y", "d M, yy","MM d, yy" ];
                    jQuery( "#orddd_lite_holiday_from_date" ).val( "" ).datepicker( {
                        constrainInput: true,
                        dateFormat: formats[0],
                        onSelect: function( selectedDate,inst ) {
                            var monthValue = inst.selectedMonth+1;
                            var dayValue = inst.selectedDay;
                            var yearValue = inst.selectedYear;
                            var current_dt = dayValue + "-" + monthValue + "-" + yearValue;
                            var to_date = jQuery("#orddd_lite_holiday_to_date").val();
                            if ( to_date == "") {    
                                var split = current_dt.split("-");
                                split[1] = split[1] - 1;        
                                var minDate = new Date(split[2],split[1],split[0]);
                                jQuery("#orddd_lite_holiday_to_date").datepicker("setDate",minDate);
                            }
                        },
                        firstDay:' . esc_attr( $day_selected ) . '
                    } );
                } );
       </script>' );

		?>
		<input type="text" name="orddd_lite_holiday_from_date" id="orddd_lite_holiday_from_date" class="orddd_lite_holiday_from_date" />
		<?php
	}

	/**
	 * Callback for adding Holiday end date setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 3.9
	 */
	public static function orddd_lite_holidays_to_date_callback( $args ) {
		$current_language = get_option( 'orddd_lite_language_selected' );
		$day_selected     = get_option( 'orddd_lite_start_of_week' );
		if ( '' === $day_selected ||
		false === $day_selected ) {
			$day_selected = 0;
		}
		print( '<script type="text/javascript">
                 jQuery( document ).ready( function() {
                    jQuery.datepicker.setDefaults( jQuery.datepicker.regional[ "en-GB" ] );
                    var formats = [ "mm-dd-yy", "d.m.y", "d M, yy","MM d, yy" ];
                    jQuery( "#orddd_lite_holiday_to_date" ).val( "" ).datepicker( {
                        constrainInput: true,
                        dateFormat: formats[0],
                        firstDay:' . esc_attr( $day_selected ) . '
                    } );
                } );
        </script>' );

		?>
		<input type="text" name="orddd_lite_holiday_to_date" id="orddd_lite_holiday_to_date" class="orddd_lite_holiday_to_date"/>
		<label for="orddd_lite_holiday_to_date"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Allow Recurring Holidays settings
	 *
	 * @param array $args Callback arguments.
	 *
	 * @since 3.9
	 */
	public static function orddd_lite_allow_recurring_holiday_callback( $args ) {
		?>
		<input type="checkbox" name="orddd_lite_allow_recurring_holiday" id="orddd_lite_allow_recurring_holiday" class="day-checkbox" />
		<label for="orddd_lite_allow_recurring_holiday"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for saving holidays in json object
	 *
	 * @param array $input Content of the selected settings.
	 *
	 * @return array $output Json object of the holidays added
	 * @since 1.5
	 */
	public static function orddd_lite_holidays_callback( $input ) {
		// phpcs:ignore WordPress.Security.NonceVerification
		if ( isset( $_POST['orddd_lite_holiday_from_date'] ) && '' !== $_POST['orddd_lite_holiday_from_date'] ) {
			$output            = array();
			$holidays          = get_option( 'orddd_lite_holidays' );
			$holiday_dates_arr = array();
			$holidays_new_arr  = array();
			$holidays_arr      = array();

			$orddd_allow_recurring_holiday = '""';
			$holiday_name                  = '';

			if ( false !== $holidays && '' !== $holidays && '{}' !== $holidays && '[]' !== $holidays && 'null' !== $holidays ) {
				$holidays_arr = json_decode( $holidays );
			}

			foreach ( $holidays_arr as $k => $v ) {
				if ( isset( $v->r_type ) ) {
					$holidays_new_arr[] = array(
						'n'      => $v->n,
						'd'      => $v->d,
						'r_type' => $v->r_type,
					);
				} else {
					$holidays_new_arr[] = array(
						'n'      => $v->n,
						'd'      => $v->d,
						'r_type' => '',
					);
				}
				$holiday_dates_arr[] = $v->d;
			}
			// phpcs:ignore WordPress.Security.NonceVerification
			if ( isset( $_POST['orddd_lite_holiday_name'] ) ) {
				// phpcs:ignore WordPress.Security.NonceVerification
				$holiday_name = str_replace( "\'", "'", sanitize_text_field( wp_unslash( $_POST['orddd_lite_holiday_name'] ) ) );
				$holiday_name = str_replace( '\"', '"', $holiday_name );
			}

			// phpcs:ignore WordPress.Security.NonceVerification
			if ( isset( $_POST['orddd_lite_allow_recurring_holiday'] ) ) {
				// phpcs:ignore WordPress.Security.NonceVerification
				$orddd_allow_recurring_holiday = sanitize_text_field( wp_unslash( $_POST['orddd_lite_allow_recurring_holiday'] ) );
			}

			// phpcs:ignore WordPress.Security.NonceVerification
			if ( isset( $_POST['orddd_lite_holiday_from_date'] ) && '' !== $_POST['orddd_lite_holiday_from_date'] && isset( $_POST['orddd_lite_holiday_to_date'] ) && '' !== $_POST['orddd_lite_holiday_to_date'] ) {
				// phpcs:ignore WordPress.Security.NonceVerification
				$date_from_arr = explode( '-', sanitize_text_field( wp_unslash( $_POST['orddd_lite_holiday_from_date'] ) ) );
				// phpcs:ignore WordPress.Security.NonceVerification
				$date_to_arr   = explode( '-', sanitize_text_field( wp_unslash( $_POST['orddd_lite_holiday_to_date'] ) ) );
				$tstmp_from    = gmdate( 'd-n-Y', gmmktime( 0, 0, 0, $date_from_arr[0], $date_from_arr[1], $date_from_arr[2] ) );
				$tstmp_to      = gmdate( 'd-n-Y', gmmktime( 0, 0, 0, $date_to_arr[0], $date_to_arr[1], $date_to_arr[2] ) );
				$holiday_dates = orddd_lite_common::orddd_lite_get_betweendays( $tstmp_from, $tstmp_to );

				$holiday_date = '';
				$output       = array();
				foreach ( $holiday_dates as $k => $v ) {
					$v1 = gmdate( ORDDD_LITE_HOLIDAY_DATE_FORMAT, strtotime( $v ) );
					if ( ! in_array( $v1, $holiday_dates_arr, true ) ) {
						$holidays_new_arr[] = array(
							'n'      => $holiday_name,
							'd'      => $v1,
							'r_type' => $orddd_allow_recurring_holiday,
						);
					}
				}
			}

			$holidays_save = wp_json_encode( $holidays_new_arr );
			$output        = $holidays_save;
		} else {
			$output = $input;
		}
		return $output;
	}

}

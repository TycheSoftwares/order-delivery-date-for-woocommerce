<?php
/**
 * Display General Settings -> Time slot settings in admin.
 *
 * @author Tyche Softwares
 * @package Order-Delivery-Date/Admin/Settings/General
 * @since 3.11.0
 * @category Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Orddd_Time_Slot_Settings class
 *
 * @class Orddd_Time_Slot_Settings
 */
class Orddd_Lite_Time_Slot_Settings {

	/**
	 * Callback for adding Time slot tab settings.
	 */
	public static function orddd_lite_time_slot_admin_settings_callback() { }

	/**
	 * Callback for adding Enable time slot setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.4
	 */
	public static function orddd_lite_time_slot_enable_callback( $args ) {
		$enable_time_slot = '';
		if ( 'on' === get_option( 'orddd_lite_enable_time_slot' ) ) {
			$enable_time_slot = 'checked';
		}
		?>
		<input type="checkbox" name="orddd_lite_enable_time_slot" id="orddd_lite_enable_time_slot" class="day-checkbox" <?php echo esc_attr( $enable_time_slot ); ?>/>
		<label for="orddd_lite_enable_time_slot"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}


	/**
	 * Callback for adding Time slot field mandatory setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.4
	 */
	public static function orddd_lite_time_slot_mandatory_callback( $args ) {
		?>
		<input type="checkbox" name="orddd_lite_time_slot_mandatory" id="orddd_lite_time_slot_mandatory" class="timeslot-checkbox" value="checked" <?php echo esc_attr( get_option( 'orddd_lite_time_slot_mandatory' ) ); ?>/>
		<label for="orddd_lite_time_slot_mandatory"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding As soon as possible option in time slot dropdown on checkout page
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 7.9
	 */
	public static function orddd_lite_time_slot_asap_callback( $args ) {
		?>
		<input type="checkbox" name="orddd_lite_time_slot_asap" id="orddd_lite_time_slot_asap" class="timeslot-checkbox" value="checked" <?php echo esc_attr( get_option( 'orddd_lite_time_slot_asap' ) ); ?> />
		<label for="orddd_lite_time_slot_asap"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Global lockout for Time slot setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.4
	 */
	public static function orddd_lite_global_lockout_time_slots_callback( $args ) {
		?>
		<input type="number" min="0" step="1" name="orddd_lite_global_lockout_time_slots" id="orddd_lite_global_lockout_time_slots" value="" disabled/>
		<label for="orddd_lite_global_lockout_time_slots"><?php echo wp_kses_post( $args[0] ); ?></label>
		<br><strong><em><?php esc_attr_e( 'Upgrade to', 'order-delivery-date' ); ?> <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank"> <?php esc_attr_e( 'Order Delivery Date Pro for WooCommerce' ); ?> </a> <?php esc_attr_e( 'to enable the setting.', 'order-delivery-date' ); ?></em></strong>
		<?php
	}

	/**
	 * Callback for adding Show first available Time slot setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.4
	 */
	public static function orddd_lite_show_first_available_time_slot_callback( $args ) {
		$orddd_show_select = '';
		if ( 'on' === get_option( 'orddd_lite_auto_populate_first_available_time_slot' ) ) {
			$orddd_show_select = 'checked';
		}
		?>
		<input type='checkbox' name='orddd_lite_auto_populate_first_available_time_slot' id='orddd_lite_auto_populate_first_available_time_slot' value='on' <?php echo esc_attr( $orddd_show_select ); ?>>
		<label for="orddd_lite_auto_populate_first_available_time_slot"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Time slot settings Extra arguments containing label & class for the field
	 */
	public static function orddd_lite_add_time_slot_admin_settings_callback() { }

	/**
	 * Callback to add time slots for weekday or specific dates
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.4
	 */
	public static function orddd_lite_time_slot_for_delivery_days_callback( $args ) {
		global $orddd_lite_weekdays;
		$orddd_time_slot_for_weekdays = 'checked';

		if ( 'weekdays' === get_option( 'orddd_lite_time_slot_for_delivery_days' ) ) {
			$orddd_time_slot_for_weekdays       = 'checked';
			$orddd_time_slot_for_specific_dates = '';
		}
		?>
		<p><label><input type="radio" name="orddd_lite_time_slot_for_delivery_days" id="orddd_lite_time_slot_for_delivery_days" value="weekdays"<?php echo esc_attr( $orddd_time_slot_for_weekdays ); ?>/><?php esc_html_e( 'Weekdays', 'order-delivery-date' ); ?></label>
		<label><input type="radio" name="orddd_lite_time_slot_for_delivery_days" id="orddd_lite_time_slot_for_delivery_days" value="specific_dates" disabled /><?php esc_html_e( 'Specific Dates', 'order-delivery-date' ); ?></label></p>
		<?php
		$alldays = array();
		foreach ( $orddd_lite_weekdays as $n => $day_name ) {
			$alldays[ $n ] = get_option( $n );
		}

		$alldayskeys = array_keys( $alldays );
		$checked     = 'No';
		foreach ( $alldayskeys as $key ) {
			if ( 'checked' === $alldays[ $key ] ) {
				$checked = 'Yes';
			}
		}
		?>
		<label for="orddd_lite_time_slot_for_delivery_days"><?php echo wp_kses_post( $args[0] ); ?></label>
		<script type='text/javascript'>
			jQuery( document ).ready( function(){
				if ( jQuery( "input[type=radio][id=\"orddd_lite_time_slot_for_delivery_days\"][value=\"weekdays\"]" ).is(":checked") ) {
					jQuery( '.time_slot_options' ).slideUp();
					jQuery( '.time_slot_for_weekdays' ).slideDown();
				} else {
					jQuery( '.time_slot_options' ).slideDown();
					jQuery( '.time_slot_for_weekdays' ).slideUp();
				}
				jQuery( '.orddd_lite_time_slot_for_weekdays' ).select2({'width': '300px' });
			});
		</script>
		<?php
	}

	/**
	 * Callback for adding Weekdays for Time slot setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.4
	 */
	public static function orddd_lite_time_slot_for_weekdays_callback( $args ) {
		global $orddd_lite_weekdays;
		foreach ( $orddd_lite_weekdays as $n => $day_name ) {
			$alldays[ $n ] = get_option( $n );
		}
		$alldayskeys = array_keys( $alldays );
		$checked     = 'No';
		foreach ( $alldayskeys as $key ) {
			if ( 'checked' === $alldays[ $key ] ) {
				$checked = 'Yes';
			}
		}

		printf(
			'<div class="time_slot_options time_slot_for_weekdays">
             <select class="orddd_lite_time_slot_for_weekdays" id="orddd_lite_time_slot_for_weekdays" name="orddd_lite_time_slot_for_weekdays[]" multiple="multiple" placeholder="Select Weekdays">
                <option name="all" value="all">All</option>'
		);
		$weekdays_arr = array();
		foreach ( $orddd_lite_weekdays as $n => $day_name ) {
			if ( 'checked' === get_option( $n ) ) {
				$weekdays[ $n ] = $day_name;
				printf( '<option name="' . esc_attr( $n ) . '" value="' . esc_attr( $n ) . '">' . esc_attr( $weekdays[ $n ] ) . '</option>' );
			}
		}

		if ( 'No' === $checked ) {
			foreach ( $orddd_lite_weekdays as $n => $day_name ) {
				$weekdays[ $n ] = $day_name;
				printf( '<option name="' . esc_attr( $n ) . '" value="' . esc_attr( $n ) . '">' . esc_attr( $weekdays[ $n ] ) . '</option>' );
			}
		}
		print( '</select></div>' );
		?>

		<label for="orddd_lite_time_slot_for_weekdays"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding From hours for Time slot setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.4
	 */
	public static function orddd_lite_time_from_hours_callback( $args ) {
		echo '<fieldset>
            <label for="orddd_lite_time_from_hours">
                <select name="orddd_lite_time_from_hours" id="orddd_lite_time_from_hours" size="1">';

		for ( $i = 0; $i <= 23; $i++ ) {
			printf(
				"<option value='%s'>%s</option>\n",
				esc_attr( $i ),
				esc_attr( $i )
			);
		}
				echo '</select>
                <label>&nbsp;' . esc_html__( 'Hours', 'order-delivery-date' ) . '</label>&nbsp&nbsp&nbsp;
                <select name="orddd_lite_time_from_minutes" id="orddd_lite_time_from_minutes" size="1">';
		for ( $i = 0; $i <= 59; $i++ ) {
			if ( $i < 10 ) {
				$i = '0' . $i;
			}
			printf(
				"<option value='%s'>%s</option>\n",
				esc_attr( $i ),
				esc_attr( $i )
			);
		}
				echo '</select>
                <label>&nbsp;' . esc_html__( 'Minutes', 'order-delivery-date' ) . '</label>
            </label>';
		echo '<p>' . wp_kses_post( $args[0] ) . '</p></fieldset>';
	}

	/**
	 * Callback for adding To hours for Time slot setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.4
	 */
	public static function orddd_lite_time_to_hours_callback( $args ) {
		echo '<fieldset>
            <label for="orddd_lite_time_to_hours">
                <select name="orddd_lite_time_to_hours" id="orddd_lite_time_to_hours" size="1">';

		for ( $i = 0; $i <= 23; $i++ ) {
			printf(
				"<option value='%s'>%s</option>\n",
				esc_attr( $i ),
				esc_attr( $i )
			);
		}
				echo '</select>
                <label>&nbsp;' . esc_html__( 'Hours', 'order-delivery-date' ) . '</lable>&nbsp&nbsp&nbsp;
                <select name="orddd_lite_time_to_minutes" id="orddd_lite_time_to_minutes" size="1">';
		for ( $i = 0; $i <= 59; $i++ ) {
			if ( $i < 10 ) {
				$i = '0' . $i;
			}
			printf(
				"<option value='%s'>%s</option>\n",
				esc_attr( $i ),
				esc_attr( $i )
			);
		}
				echo '</select>
                <label>&nbsp;' . esc_html__( 'Minutes', 'order-delivery-date' ) . '</label>
            </label>';
		echo '<p>' . wp_kses_post( $args[0] ) . '</p></fieldset>';
	}

	/**
	 * Callback for adding Lockout Time slot after X orders setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.4
	 */
	public static function orddd_lite_time_slot_lockout_callback( $args ) {
		?>
		<input type="number" min="0" step="1" name="orddd_lite_time_slot_lockout" id="orddd_lite_time_slot_lockout"/>
		<label for="orddd_lite_time_slot_lockout"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback to add additional charges for a time slot
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.4
	 */
	public static function orddd_lite_time_slot_additional_charges_callback( $args ) {
		?>
		<input type="text" name="orddd_lite_time_slot_additional_charges" id="orddd_lite_time_slot_additional_charges" placeholder="Charges"/>
		<input type="text" name="orddd_lite_time_slot_additional_charges_label" id="orddd_lite_time_slot_additional_charges_label" placeholder="Time slot Charges Label" />
		<label for="orddd_lite_time_slot_additional_charges"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for saving time slots
	 *
	 * @return string
	 * @since 2.4
	 */
	public static function orddd_lite_delivery_time_slot_callback() {
		global $orddd_lite_weekdays;
		foreach ( $orddd_lite_weekdays as $n => $day_name ) {
			$alldays[ $n ] = get_option( $n );
		}
		$alldayskeys = array_keys( $alldays );

		$timeslot         = get_option( 'orddd_lite_delivery_time_slot_log' );
		$timeslot_new_arr = array();
		if ( 'null' === $timeslot ||
			'' === $timeslot ||
			'{}' === $timeslot ||
			'[]' === $timeslot ) {
			$timeslot_arr = array();
		} else {
			$timeslot_arr = json_decode( $timeslot );
		}

		if ( isset( $timeslot_arr ) && is_array( $timeslot_arr ) && count( $timeslot_arr ) > 0 ) {
			foreach ( $timeslot_arr as $k => $v ) {
				$timeslot_new_arr[] = array(
					'tv'                       => $v->tv,
					'dd'                       => $v->dd,
					'lockout'                  => $v->lockout,
					'additional_charges'       => $v->additional_charges,
					'additional_charges_label' => $v->additional_charges_label,
					'fh'                       => $v->fh,
					'fm'                       => $v->fm,
					'th'                       => $v->th,
					'tm'                       => $v->tm,
				);
			}
		}

		if ( ( ! isset( $_POST['orddd_lite_time_slot_for_weekdays'] ) ) && isset( $_POST['orddd_lite_time_from_hours'] ) && '0' !== $_POST['orddd_lite_time_from_hours'] && isset( $_POST['orddd_lite_time_to_hours'] ) && '0' !== $_POST['orddd_lite_time_to_hours'] ) { //phpcs:ignore
			add_settings_error( 'orddd_delivery_time_slot_log_error', 'time_slot_save_error', 'Please Select Delivery Days/Dates for the Time slot', 'error' );
		} else {
			$devel_dates = '';
			if ( isset( $_POST['orddd_lite_time_slot_for_delivery_days'] ) ) { //phpcs:ignore
				$time_slot_value = $_POST['orddd_lite_time_slot_for_delivery_days']; //phpcs:ignore
				if ( 'weekdays' === $time_slot_value ) {
					if ( isset( $_POST['orddd_lite_time_slot_for_weekdays'] ) ) { //phpcs:ignore
						$orddd_time_slot_for_weekdays = $_POST['orddd_lite_time_slot_for_weekdays']; //phpcs:ignore

						// Add all the individual enabled weekdays if 'all' is selected.
						if ( in_array( 'all', $orddd_time_slot_for_weekdays, true ) ) {
							$weekdays = array();
							foreach ( $alldayskeys as $key ) {
								if ( 'checked' === $alldays[ $key ] ) {
									array_push( $weekdays, $key );
								}
							}
						} else {
							$weekdays = $_POST['orddd_lite_time_slot_for_weekdays']; //phpcs:ignore
						}

						$devel_dates = wp_json_encode( $weekdays );
					}
				}
			} else {
				$time_slot_value = '';
			}

			$from_hour                = 0;
			$from_minute              = 0;
			$to_hour                  = 0;
			$to_minute                = 0;
			$lockouttime              = '';
			$additional_charges       = '';
			$additional_charges_label = '';

			if ( isset( $_POST['orddd_lite_time_from_hours'] ) ) { //phpcs:ignore
				$from_hour = $_POST['orddd_lite_time_from_hours']; //phpcs:ignore
			}

			if ( isset( $_POST['orddd_lite_time_from_minutes'] ) ) { //phpcs:ignore
				$from_minute = $_POST['orddd_lite_time_from_minutes']; //phpcs:ignore
			}

			if ( isset( $_POST['orddd_lite_time_to_hours'] ) ) { //phpcs:ignore
				$to_hour = $_POST['orddd_lite_time_to_hours']; //phpcs:ignore
			}

			if ( isset( $_POST['orddd_lite_time_to_minutes'] ) ) { //phpcs:ignore
				$to_minute = $_POST['orddd_lite_time_to_minutes']; //phpcs:ignore
			}

			if ( isset( $_POST['orddd_lite_time_slot_lockout'] ) ) { //phpcs:ignore
				$lockouttime = $_POST['orddd_lite_time_slot_lockout']; //phpcs:ignore
			}

			if ( isset( $_POST['orddd_lite_time_slot_additional_charges'] ) ) { //phpcs:ignore
				$additional_charges = $_POST['orddd_lite_time_slot_additional_charges']; //phpcs:ignore
			}

			if ( isset( $_POST['orddd_lite_time_slot_additional_charges_label'] ) ) { //phpcs:ignore
				$additional_charges_label = $_POST['orddd_lite_time_slot_additional_charges_label']; //phpcs:ignore
			}

			$from_hour_new   = gmdate( 'G', gmmktime( $from_hour, $from_minute, 0, gmdate( 'm' ), gmdate( 'd' ), gmdate( 'Y' ) ) );
			$from_minute_new = gmdate( 'i ', gmmktime( $from_hour, $from_minute, 0, gmdate( 'm' ), gmdate( 'd' ), gmdate( 'Y' ) ) );
			$to_hour_new     = gmdate( 'G', gmmktime( $to_hour, $to_minute, 0, gmdate( 'm' ), gmdate( 'd' ), gmdate( 'Y' ) ) );
			$to_minute_new   = gmdate( 'i ', gmmktime( $to_hour, $to_minute, 0, gmdate( 'm' ), gmdate( 'd' ), gmdate( 'Y' ) ) );

			$timeslot_present = 'no';
			foreach ( $timeslot_new_arr as $key => $value ) {

				$fh = $value['fh'];
				$fm = $value['fm'];
				$th = $value['th'];
				$tm = $value['tm'];

				if ( 'weekdays' === $value['tv'] && gettype( json_decode( $value['dd'] ) ) === 'array' && count( json_decode( $value['dd'] ) ) > 0 ) {
					$dd = json_decode( $value['dd'] );

					if ( 'all' === $dd[0] &&
						$fh === $from_hour_new &&
						$fm === $from_minute_new &&
						$th === $to_hour_new &&
						$tm === $to_minute_new ) {
						$timeslot_present = 'yes';
						break;
					} else {
						foreach ( $dd as $id => $day ) {
							if ( isset( $_POST['orddd_litetime_slot_for_weekdays'] ) && in_array( $day, $_POST['orddd_lite_time_slot_for_weekdays'], true ) && $fh === $from_hour_new && $fm === $from_minute_new && $th === $to_hour_new && $tm === $to_minute_new ) { //phpcs:ignore
								$timeslot_present = 'yes';
								break;
							}
						}
					}
				}
			}

			if ( 'no' === $timeslot_present ) {
				if ( $from_hour_new !== $to_hour_new || $from_minute_new !== $to_minute_new ) {
					$timeslot_new_arr[] = array(
						'tv'                       => $time_slot_value,
						'dd'                       => $devel_dates,
						'lockout'                  => $lockouttime,
						'additional_charges'       => $additional_charges,
						'additional_charges_label' => $additional_charges_label,
						'fh'                       => $from_hour_new,
						'fm'                       => $from_minute_new,
						'th'                       => $to_hour_new,
						'tm'                       => $to_minute_new,
					);
				}
			}
		}
		$timeslot_jarr = wp_json_encode( $timeslot_new_arr );
		return $timeslot_jarr;
	}


	/******************** Block Time slot settings ***********************/

	/**
	 * Text to display on the Block Time Slots page
	 *
	 * @since 2.8.4
	 */
	public static function orddd_lite_disable_time_slot_callback() {
		echo 'Use this if you want to hide or block a Time Slot temporarily.';
	}

	/**
	 * Callback to add setting to block time slots
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.4
	 */
	public static function orddd_lite_disable_time_slot_for_delivery_days_callback( $args ) {
		global $orddd_weekdays;
		$orddd_disable_time_slot_for_weekdays = '';
		$orddd_disable_time_slot_for_dates    = 'checked';
		if ( 'weekdays' === get_option( 'orddd_lite_disable_time_slot_for_delivery_days' ) ) {
			$orddd_disable_time_slot_for_weekdays = 'checked';
			$orddd_disable_time_slot_for_dates    = '';
		} elseif ( 'dates' === get_option( 'orddd_lite_disable_time_slot_for_delivery_days' ) ) {
			$orddd_disable_time_slot_for_dates    = 'checked';
			$orddd_disable_time_slot_for_weekdays = '';
		}

		?>
		<p><label><input type="radio" name="orddd_lite_disable_time_slot_for_delivery_days" id="orddd_lite_disable_time_slot_for_delivery_days" value="dates" <?php echo esc_attr( $orddd_disable_time_slot_for_dates ); ?>/><?php esc_html_e( 'Dates', 'order-delivery-date' ); ?></label>
		<label><input type="radio" name="orddd_lite_disable_time_slot_for_delivery_days" id="orddd_lite_disable_time_slot_for_delivery_days" value="weekdays"<?php echo esc_attr( $orddd_disable_time_slot_for_weekdays ); ?>/><?php esc_html_e( 'Weekdays', 'order-delivery-date' ); ?></label></p>
		<label for="orddd_lite_disable_time_slot_for_delivery_days"><?php echo wp_kses_post( $args[0] ); ?></label>

		<script type='text/javascript'>
			jQuery( document ).ready( function(){
				if ( jQuery( "input[type=radio][id=\"orddd_lite_disable_time_slot_for_delivery_days\"][value=\"weekdays\"]" ).is(":checked") ) {
					jQuery( '.disable_time_slot_options' ).slideUp();
					jQuery( '.disable_time_slot_for_weekdays' ).slideDown();
				} else {
					jQuery( '.disable_time_slot_options' ).slideDown();
					jQuery( '.disable_time_slot_for_weekdays' ).slideUp();
				}
				jQuery( '.orddd_lite_disable_time_slot_for_weekdays' ).select2({'width': '300px' });
				jQuery( "input[type=radio][id=\"orddd_lite_disable_time_slot_for_delivery_days\"]" ).on( 'change', function() {
					if ( jQuery( this ).is(':checked') ) {
						var value = jQuery( this ).val();
						jQuery( '.disable_time_slot_options' ).slideUp();
						jQuery( '.disable_time_slot_for_' + value ).slideDown();
					}
				})
			});
		</script>
		<?php
	}


	/**
	 * Callback to add the setting for disabling time slots for weekdays
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.4
	 */
	public static function orddd_lite_disable_time_slot_for_weekdays_callback( $args ) {
		global $orddd_lite_weekdays;
		printf(
			'<div class="disable_time_slot_options disable_time_slot_for_weekdays">
            <select class="orddd_lite_disable_time_slot_for_weekdays" id="orddd_lite_disable_time_slot_for_weekdays" name="orddd_lite_disable_time_slot_for_weekdays[]" multiple="multiple" placeholder="Select Weekdays">
            <option name="all" value="all">All</option>'
		);
		$weekdays_arr = array();
		foreach ( $orddd_lite_weekdays as $n => $day_name ) {
			$weekdays[ $n ] = $day_name;
			printf( '<option name="' . esc_attr( $n ) . '" value="' . esc_attr( $n ) . '">' . esc_attr( $weekdays[ $n ] ) . '</option>' );
		}
		print( '</select></div>' );

		printf(
			'<div class="disable_time_slot_options disable_time_slot_for_dates">
            <textarea rows="4" cols="40" name="orddd_lite_disable_time_slot_for_dates" id="orddd_lite_disable_time_slot_for_dates" placeholder="Select Dates"></textarea></div>'
		);

		printf(
			'<script type="text/javascript">
            jQuery(document).ready(function() {
                var formats = [ "mm-dd-yy", "d.m.y", "d M, yy","MM d, yy" ];
                jQuery( "#orddd_lite_disable_time_slot_for_dates" ).datepick({dateFormat: formats[0], multiSelect: 999, monthsToShow: 1});
            });
			</script>'
		);
		?>
		<label for="orddd_lite_disable_time_slot_for_weekdays"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback to add the setting to select time slots to disable
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since
	 */
	public static function orddd_lite_selected_time_slots_to_be_disabled_callback( $args ) {

		printf( '<select class="orddd_lite_selected_time_slots_to_be_disabled" id="orddd_lite_selected_time_slots_to_be_disabled" name="orddd_lite_selected_time_slots_to_be_disabled[]" multiple="multiple" placeholder="Select Time slots">' );

		$time_slot_key_arr = self::get_all_timeslots();

		if ( isset( $time_slot_key_arr ) && is_array( $time_slot_key_arr ) && count( $time_slot_key_arr ) > 0 ) {
			foreach ( $time_slot_key_arr as $ts_key => $ts_value ) {
				echo "<option value='" . esc_attr( $ts_value ) . "'>" . esc_attr( $ts_value ) . "</option>\n";
			}
		}
		echo '</select>';

		?>
		<label for="orddd_lite_selected_time_slots_to_be_disabled"><?php echo wp_kses_post( $args[0] ); ?></label>
		<script type='text/javascript'>
			jQuery( document ).ready( function(){
				jQuery( '.orddd_lite_selected_time_slots_to_be_disabled' ).select2({'width': '300px' });
			});
		</script>
		<?php
	}

	/**
	 * Get all the saved time slots
	 *
	 * @param string $format_requested Time slot format.
	 * @return array
	 * @since
	 */
	public static function get_all_timeslots( $format_requested = '' ) {

		global $orddd_weekdays, $wpdb;

		$time_slot_arr       = array();
		$time_slot_key_arr   = array();
		$time_format_to_show = orddd_lite_common::orddd_lite_get_time_format();

		if ( 'on' === get_option( 'orddd_lite_enable_time_slot' ) ) {
			$time_slot_select = get_option( 'orddd_lite_delivery_time_slot_log' );

			if ( '' !== $time_slot_select &&
				'{}' !== $time_slot_select &&
				'[]' !== $time_slot_select &&
				'null' !== $time_slot_select ) {
				$time_slot_arr = json_decode( $time_slot_select );
			}
			if ( is_array( $time_slot_arr ) && count( $time_slot_arr ) > 0 ) {
				if ( 'null' === $time_slot_arr ) {
					$time_slot_arr = array();
				}
				foreach ( $time_slot_arr as $k => $v ) {
					$from_time = $v->fh . ':' . trim( $v->fm );
					// Send in format as requested.

					$ft = date( $time_format_to_show, strtotime( $from_time ) ); //phpcs:ignore
					if ( 0 != $v->th || ( 0 == $v->th && 0 != $v->tm ) ) { //phpcs:ignore
						$to_time       = $v->th . ':' . $v->tm;
						$tt            = date( $time_format_to_show, strtotime( $to_time ) ); //phpcs:ignore
						$time_slot_key = $ft . ' - ' . $tt;
					} else {
						$time_slot_key = $ft;
					}
					$time_slot_key_arr[] = $time_slot_key;
				}
			}
		}

		$time_slot_key_arr['asap'] = __( 'As Soon As Possible.', 'order-delivery-date' );
		return $time_slot_key_arr;
	}

	/**
	 * Callback to disable the selected time slots
	 *
	 * @return string $timeslot_jarr JSON Encoded values for selected time slots
	 * @since 2.8.4
	 */
	public static function orddd_lite_disable_time_slots_callback() {
		$disable_timeslot        = get_option( 'orddd_lite_disable_time_slot_log' );
		$disable_devel_dates     = array();
		$selected_time_slot      = '';
		$disable_time_slot_value = '';

		if ( isset( $_POST['orddd_lite_disable_time_slot_for_delivery_days'] ) ) { //phpcs:ignore
			$disable_time_slot_value = $_POST['orddd_lite_disable_time_slot_for_delivery_days']; //phpcs:ignore
			if ( 'weekdays' === $disable_time_slot_value ) {
				if ( isset( $_POST['orddd_lite_disable_time_slot_for_weekdays'] ) ) { //phpcs:ignore
					$disable_devel_dates = $_POST['orddd_lite_disable_time_slot_for_weekdays']; //phpcs:ignore
				}
			} elseif ( 'dates' === $disable_time_slot_value ) {
				if ( isset( $_POST['orddd_lite_disable_time_slot_for_dates'] ) ) { //phpcs:ignore
					$disable_devel_dates = explode( ',', $_POST['orddd_lite_disable_time_slot_for_dates'] ); //phpcs:ignore
				}
			}
		}

		if ( isset( $_POST['orddd_lite_selected_time_slots_to_be_disabled'] ) ) { //phpcs:ignore
			$selected_time_slot = wp_json_encode( $_POST['orddd_lite_selected_time_slots_to_be_disabled'] ); //phpcs:ignore
		}

		$disable_timeslot_new_arr = array();
		if ( 'null' === $disable_timeslot ||
			'' === $disable_timeslot ||
			'{}' === $disable_timeslot ||
			'[]' === $disable_timeslot ) {
			$timeslot_arr = array();
		} else {
			$timeslot_arr = json_decode( $disable_timeslot );
		}

		if ( isset( $timeslot_arr ) && is_array( $timeslot_arr ) && count( $timeslot_arr ) > 0 ) {
			foreach ( $timeslot_arr as $k => $v ) {
				$disable_timeslot_new_arr[] = array(
					'dtv' => $v->dtv,
					'dd'  => $v->dd,
					'ts'  => $v->ts,
				);
			}
		}

		if ( is_array( $disable_devel_dates ) && count( $disable_devel_dates ) > 0 && '' !== $selected_time_slot ) {
			foreach ( $disable_devel_dates as $key => $value ) {
				if ( 'dates' === $disable_time_slot_value ) {
					$disable_date          = explode( '-', $value );
					$delivery_disable_date = gmdate( 'n-j-Y', gmmktime( 0, 0, 0, $disable_date[0], $disable_date[1], $disable_date[2] ) );
				} else {
					$delivery_disable_date = $value;
				}
				$disable_timeslot_new_arr[] = array(
					'dtv' => $disable_time_slot_value,
					'dd'  => $delivery_disable_date,
					'ts'  => $selected_time_slot,
				);
			}
		}
		$timeslot_jarr = wp_json_encode( $disable_timeslot_new_arr );
		return $timeslot_jarr;
	}
}

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
		?>
		<section class="add-timeslot">
			<input type="text" name="orddd_lite_time_from_hours[]" id="orddd_lite_time_from_hours" value=""/>
			To
			<input type="text" name="orddd_lite_time_to_hours[]" id="orddd_lite_time_to_hours" value=""/>

			<a href="#" id="add_another_slot" role="button">+ Add another slot</a>
		</section>
		<?php
	}

	/**
	 * Callback for adding To hours for Time slot setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.4
	 */
	public static function orddd_lite_time_to_hours_callback( $args ) {}

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
	 * Callback for saving time slots.
	 *
	 * @param array $data Setting fields data in array.
	 * @return string
	 * @since 2.4
	 */
	public static function orddd_lite_delivery_time_slot_callback( $data ) {
		global $orddd_lite_weekdays;
		foreach ( $orddd_lite_weekdays as $n => $day_name ) {
			$alldays[ $n ] = get_option( $n );
		}
		$alldayskeys = array_keys( $alldays );

		$timeslot         = get_option( 'orddd_lite_delivery_time_slot_log' );
		$timeslot_new_arr = array();
		if ( 'null' === $timeslot || '' === $timeslot || '{}' === $timeslot || '[]' === $timeslot ) {
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

		$selected_dates           = '';
		$time_slot_value          = '';
		$lockouttime              = '';
		$additional_charges       = '';
		$additional_charges_label = '';

		if ( isset( $_POST['orddd_lite_bulk_time_slot_for_delivery_days'] ) && '' !== $_POST['orddd_lite_bulk_time_slot_for_delivery_days'] && isset( $_POST['orddd_lite_individual_or_bulk'] ) && 'bulk' === $_POST['orddd_lite_individual_or_bulk'] ) { // phpcs:ignore
			$time_slot_value = $_POST['orddd_lite_bulk_time_slot_for_delivery_days']; // phpcs:ignore
			if ( isset( $_POST['orddd_lite_bulk_time_slot_lockout'] ) ) { //phpcs:ignore
				$lockouttime = sanitize_text_field( wp_unslash( $_POST['orddd_lite_bulk_time_slot_lockout'] ) ); //phpcs:ignore
			}

			if ( isset( $_POST['orddd_lite_bulk_time_slot_additional_charges'] ) ) { //phpcs:ignore
				$additional_charges = sanitize_text_field( wp_unslash( $_POST['orddd_lite_bulk_time_slot_additional_charges'] ) ); //phpcs:ignore
			}

			if ( isset( $_POST['orddd_lite_bulk_time_slot_additional_charges_label'] ) ) { //phpcs:ignore
				$additional_charges_label = sanitize_text_field( wp_unslash( $_POST['orddd_lite_bulk_time_slot_additional_charges_label'] ) ); //phpcs:ignore
			}

			if ( 'weekdays' === $time_slot_value ) {
				if ( isset( $_POST['orddd_lite_time_slot_for_weekdays_bulk'] ) ) { // phpcs:ignore
					$orddd_time_slot_for_weekdays = $_POST['orddd_lite_time_slot_for_weekdays_bulk']; // phpcs:ignore

					// Add all the individual enabled weekdays if 'all' is selected.
					if ( in_array( 'all', $orddd_time_slot_for_weekdays, true ) ) {
						$weekdays = array();
						foreach ( $alldayskeys as $key ) {
							if ( 'checked' === $alldays[ $key ] ) {
								array_push( $weekdays, $key );
							}
						}
					} else {
						$weekdays = $_POST['orddd_lite_time_slot_for_weekdays_bulk']; // phpcs:ignore
					}

					$selected_dates = wp_json_encode( $weekdays );
				}
			}
		} elseif ( isset( $_POST['orddd_lite_time_slot_for_delivery_days'] ) && '' !== $_POST['orddd_lite_time_slot_for_delivery_days'] ) { // phpcs:ignore 
			$time_slot_value = $_POST['orddd_lite_time_slot_for_delivery_days']; // phpcs:ignore
			if ( isset( $_POST['orddd_lite_time_slot_lockout'] ) ) { //phpcs:ignore
				$lockouttime = sanitize_text_field( wp_unslash( $_POST['orddd_lite_time_slot_lockout'] ) ); //phpcs:ignore
			}

			if ( isset( $_POST['orddd_lite_time_slot_additional_charges'] ) ) { //phpcs:ignore
				$additional_charges = sanitize_text_field( wp_unslash( $_POST['orddd_lite_time_slot_additional_charges'] ) ); //phpcs:ignore
			}

			if ( isset( $_POST['orddd_lite_time_slot_additional_charges_label'] ) ) { //phpcs:ignore
				$additional_charges_label = sanitize_text_field( wp_unslash( $_POST['orddd_lite_time_slot_additional_charges_label'] ) ); //phpcs:ignore
			}

			if ( 'weekdays' === $time_slot_value ) {
				if ( isset( $_POST['orddd_lite_time_slot_for_weekdays'] ) ) { // phpcs:ignore
					$orddd_time_slot_for_weekdays = $_POST['orddd_lite_time_slot_for_weekdays']; // phpcs:ignore

					// Add all the individual enabled weekdays if 'all' is selected.
					if ( in_array( 'all', $orddd_time_slot_for_weekdays, true ) ) {
						$weekdays = array();
						foreach ( $alldayskeys as $key ) {
							if ( 'checked' === $alldays[ $key ] ) {
								array_push( $weekdays, $key );
							}
						}
					} else {
						$weekdays = $_POST['orddd_lite_time_slot_for_weekdays']; // phpcs:ignore
					}

					$selected_dates = wp_json_encode( $weekdays );
				}
			}
		}

		if ( ( ( ! isset( $_POST['orddd_lite_time_slot_for_weekdays'] ) ) && ( ! isset( $_POST['orddd_lite_time_slot_for_weekdays_bulk'] ) ) ) //phpcs:ignore
		&& ( ( ! empty( $_POST['orddd_lite_time_from_hours'] ) && '' !== $_POST['orddd_lite_time_from_hours'][0] ) //phpcs:ignore
		|| ( isset( $_POST['orddd_lite_time_slot_starts_from'] ) && '' !== $_POST['orddd_lite_time_slot_starts_from'] ) ) ) { //phpcs:ignore

			add_settings_error( 'orddd_delivery_time_slot_log_error', 'time_slot_save_error', 'Please Select Delivery Days/Dates for the Time slot', 'error' );

		} elseif ( isset( $_POST['orddd_lite_time_slot_starts_from'] ) && '' !== $_POST['orddd_lite_time_slot_starts_from'] ) { //phpcs:ignore
			$duration = isset( $_POST['orddd_lite_time_slot_duration'] ) && '' !== $_POST['orddd_lite_time_slot_duration'] ? wp_unslash( sanitize_text_field( wp_unslash( $_POST['orddd_lite_time_slot_duration'] ) ) ) : 60; //phpcs:ignore

			$frequency = isset( $_POST['orddd_lite_time_slot_interval'] ) && '' !== $_POST['orddd_lite_time_slot_interval'] ? sanitize_text_field( wp_unslash( $_POST['orddd_lite_time_slot_interval'] ) ) : 0; //phpcs:ignore

			$time_starts_from = isset( $_POST['orddd_lite_time_slot_starts_from'] ) && '' !== $_POST['orddd_lite_time_slot_starts_from'] ? sanitize_text_field( wp_unslash( $_POST['orddd_lite_time_slot_starts_from'] ) ) : ''; //phpcs:ignore
			$time_ends_at     = isset( $_POST['orddd_lite_time_slot_ends_at'] ) && '' !== $_POST['orddd_lite_time_slot_ends_at'] ? sanitize_text_field( wp_unslash( $_POST['orddd_lite_time_slot_ends_at'] ) ) : $time_starts_from; //phpcs:ignore

			if ( 0 === $duration ) {
				add_settings_error( 'orddd_delivery_time_slot_log_error', 'time_slot_save_error', 'Please Set the Time Slot Duration to be Greater than 0.', 'error' );
			} elseif ( '' !== $time_starts_from ) {
				$duration_in_secs  = $duration * 60;
				$frequency_in_secs = $frequency * 60;
				$array_of_time     = array();
				$start_time        = strtotime( $time_starts_from );
				$end_time          = strtotime( $time_ends_at );

				while ( $start_time <= $end_time ) {
					$from_hours  = gmdate( 'G:i', $start_time );
					$start_time += $duration_in_secs;

					if ( $start_time > $end_time ) {
						break;
					}
					$to_hours        = gmdate( 'G:i', $start_time );
					$array_of_time[] = $from_hours . ' - ' . $to_hours;
					if ( $frequency_in_secs > 0 ) {
						$start_time += $frequency_in_secs;
					}
				}

				$timeslot_new_arr = self::orddd_lite_save_timeslots( $array_of_time, $timeslot_new_arr, $time_slot_value, $selected_dates, $lockouttime, $additional_charges, $additional_charges_label );
			}
		} elseif ( isset( $_POST['orddd_lite_time_from_hours'] ) && '' !== $_POST['orddd_lite_time_from_hours'] ) { //phpcs:ignore
			$from_hours = isset( $_POST['orddd_lite_time_from_hours'] ) && '' !== $_POST['orddd_lite_time_from_hours'] ? wp_unslash( array_map( 'sanitize_text_field', wp_unslash( $_POST['orddd_lite_time_from_hours'] ) ) ) : ''; //phpcs:ignore
			$to_hours = isset( $_POST['orddd_lite_time_to_hours'] ) && '' !== $_POST['orddd_lite_time_to_hours'] ? wp_unslash( array_map( 'sanitize_text_field', wp_unslash( $_POST['orddd_lite_time_to_hours'] ) ) ) : $from_hours; //phpcs:ignore
			$array_of_time = array();
			if ( ! empty( $from_hours ) ) {
				foreach ( $from_hours as $key => $from_hour ) {

					if ( '' === $from_hour ) {
						continue;
					}
					if ( '' === $to_hours[ $key ] ) {
						$array_of_time[] = $from_hour;
					} else {
						$array_of_time[] = $from_hour . ' - ' . $to_hours[ $key ];
					}
				}

				$timeslot_new_arr = self::orddd_lite_save_timeslots( $array_of_time, $timeslot_new_arr, $time_slot_value, $selected_dates, $lockouttime, $additional_charges, $additional_charges_label );
			}
		}

		$timeslot_jarr = wp_json_encode( $timeslot_new_arr );
		return $timeslot_jarr;
	}

	/**
	 * Save the timeslots for weekdays or specific dates.
	 *
	 * @param array  $array_of_time Array of the time slots to save.
	 * @param array  $timeslot_new_arr Existing time slots array.
	 * @param string $time_slot_value Time slot for weekdays or specific dates.
	 * @param string $selected_dates Selected weekdays or specific dates.
	 * @param int    $lockouttime Maximum order deliveries for the time slot.
	 * @param string $additional_charges Additional charges for time slot.
	 * @param string $additional_charges_label Additional charges label.
	 * @return array
	 * @since 3.15.0
	 */
	public static function orddd_lite_save_timeslots( $array_of_time, $timeslot_new_arr, $time_slot_value, $selected_dates, $lockouttime, $additional_charges, $additional_charges_label ) {
		$time_format = Orddd_Lite_Common::orddd_lite_get_time_format();

		foreach ( $array_of_time as $timeslot ) {
			if ( 'h:i A' === $time_format ) {
				$timeslot = Orddd_Lite_Common::orddd_lite_change_time_slot_format( $timeslot );
			}

			$timeslot_array = explode( ' - ', $timeslot );
			$from_time      = explode( ':', $timeslot_array[0] );
			if ( isset( $timeslot_array[1] ) && '' !== $timeslot_array[1] ) {
				$to_time = explode( ':', $timeslot_array[1] );
			} else {
				$to_time = array( 0, 0 );
			}

			$from_hour   = $from_time[0];
			$from_minute = $from_time[1];
			$to_hour     = $to_time[0];
			$to_minute   = $to_time[1];

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

				if ( 'weekdays' === $value['tv'] &&
					gettype( json_decode( $value['dd'] ) ) === 'array' &
					count( json_decode( $value['dd'] ) ) > 0 ) {
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
							if ( isset( $_POST['orddd_lite_time_slot_for_weekdays'] ) && //phpcs:ignore
							in_array( $day, $_POST['orddd_lite_time_slot_for_weekdays'], true ) && //phpcs:ignore
							$fh === $from_hour_new &&
							$fm === $from_minute_new &&
							$th === $to_hour_new &&
							$tm === $to_minute_new ) {
								$timeslot_present = 'yes';
								break;

							}
						}
					}
				}
			}

			if ( 'no' === $timeslot_present ) {
				$timeslot_new_arr[] = array(
					'tv'                       => $time_slot_value,
					'dd'                       => $selected_dates,
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

		return $timeslot_new_arr;
	}


	/******************** Bulk Time slot settings ***********************/

	/**
	 * Callback for adding Time slot settings Extra arguments containing label & class for the field
	 */
	public static function orddd_lite_bulk_time_slot_admin_settings_callback() {}

	/**
	 * Callback to add time slots for weekday or specific dates
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 3.15.0
	 */
	public static function orddd_lite_bulk_time_slot_for_delivery_days_callback( $args ) {
		global $orddd_lite_weekdays;
		$orddd_time_slot_for_weekdays       = 'checked';
		$orddd_time_slot_for_specific_dates = '';
		if ( 'weekdays' === get_option( 'orddd_lite_bulk_time_slot_for_delivery_days' ) ) {
			$orddd_time_slot_for_weekdays       = 'checked';
			$orddd_time_slot_for_specific_dates = '';
		} elseif ( 'specific_dates' === get_option( 'orddd_lite_bulk_time_slot_for_delivery_days' ) ) {
			$orddd_time_slot_for_specific_dates = 'checked';
			$orddd_time_slot_for_weekdays       = '';
		}

		?>
		<p><label><input type="radio" name="orddd_lite_bulk_time_slot_for_delivery_days" id="orddd_lite_bulk_time_slot_for_delivery_days" value="weekdays"<?php echo esc_attr( $orddd_time_slot_for_weekdays ); ?>/><?php esc_html_e( 'Weekdays', 'order-delivery-date' ); ?></label>
		<label><input disabled='' type="radio" name="orddd_lite_bulk_time_slot_for_delivery_days" id="orddd_lite_bulk_time_slot_for_delivery_days" value="specific_dates"<?php echo esc_attr( $orddd_time_slot_for_specific_dates ); ?>/><?php esc_html_e( 'Specific Dates', 'order-delivery-date' ); ?></label>
		</p>

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
		<label for="orddd_lite_bulk_time_slot_for_delivery_days"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Weekdays for Time slot setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 3.15.0
	 */
	public static function orddd_lite_time_slot_for_weekdays_bulk_callback( $args ) {
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
			'<div class="time_slot_options_bulk time_slot_for_bulk_weekdays">
             <select class="orddd_lite_time_slot_for_weekdays" id="orddd_lite_time_slot_for_weekdays_bulk" name="orddd_lite_time_slot_for_weekdays_bulk[]" multiple="multiple" placeholder="Select Weekdays">
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
		<label for="orddd_lite_time_slot_for_weekdays_bulk"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback function for time duration in the bulk time slot.
	 *
	 * @param array $args Extra arguments.
	 * @since 3.15.0
	 * @return void
	 */
	public static function orddd_lite_time_slot_duration_callback( $args ) {
		?>
		<input type="number" min="0" step="1" name="orddd_lite_time_slot_duration" id="orddd_lite_time_slot_duration" value="<?php echo esc_attr( get_option( 'orddd_lite_time_slot_duration' ) ); ?>"/>
		<label for="orddd_lite_time_slot_duration"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback function for time interval in the bulk time slot.
	 *
	 * @param array $args Extra arguments.
	 * @since 3.15.0
	 * @return void
	 */
	public static function orddd_lite_time_slot_interval_callback( $args ) {
		?>
		<input type="number" min="0" step="1" name="orddd_lite_time_slot_interval" id="orddd_lite_time_slot_interval" value="<?php echo esc_attr( get_option( 'orddd_lite_time_slot_interval' ) ); ?>"/>
		<label for="orddd_lite_time_slot_interval"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback function for start time in the bulk time slot.
	 *
	 * @param array $args Extra arguments.
	 * @since 3.15.0
	 * @return void
	 */
	public static function orddd_lite_time_slot_starts_from_callback( $args ) {
		?>
		<input type="text" name="orddd_lite_time_slot_starts_from" id="orddd_lite_time_slot_starts_from" value="<?php echo esc_attr( get_option( 'orddd_lite_time_slot_starts_from' ) ); ?>"/>
		<label for="orddd_lite_time_slot_starts_from"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback function for end time in the bulk time slot.
	 *
	 * @param array $args Extra arguments.
	 * @return void
	 * @since 3.15.0
	 */
	public static function orddd_lite_time_slot_ends_at_callback( $args ) {
		?>
		<input type="text" name="orddd_lite_time_slot_ends_at" id="orddd_lite_time_slot_ends_at" value="<?php echo esc_attr( get_option( 'orddd_lite_time_slot_ends_at' ) ); ?>"/>
		<label for="orddd_lite_time_slot_ends_at"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Lockout Time slot after X orders setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.4
	 */
	public static function orddd_lite_bulk_time_slot_lockout_callback( $args ) {
		?>
		<input type="number" min="0" step="1" name="orddd_lite_bulk_time_slot_lockout" id="orddd_lite_bulk_time_slot_lockout"/>
		<label for="orddd_lite_bulk_time_slot_lockout"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback to add additional charges for a time slot
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.4
	 */
	public static function orddd_lite_bulk_time_slot_additional_charges_callback( $args ) {
		?>
		<input type="text" name="orddd_lite_bulk_time_slot_additional_charges" id="orddd_lite_bulk_time_slot_additional_charges" placeholder="Charges"/>
		<input type="text" name="orddd_lite_bulk_time_slot_additional_charges_label" id="orddd_lite_bulk_time_slot_additional_charges_label" placeholder="Time slot Charges Label" />
		<label for="orddd_lite_bulk_time_slot_additional_charges_label"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
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

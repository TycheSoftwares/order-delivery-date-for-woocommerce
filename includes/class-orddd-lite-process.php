<?php
/**
 * Order Delivery Date for WooCommerce Lite
 *
 * Processes performed on the frontend checkout page
 *
 * @author      Tyche Softwares
 * @package     Order-Delivery-Date-Lite-for-WooCommerce/Frontend/Checkout-Page-Processes
 * @since       1.5
 */

/**
 * Class for adding processes to be performed on the checkout page
 */
class Orddd_Lite_Process {

	/**
	 * Adds hidden fields and delivery date field on the frontend checkout page
	 *
	 * @hook woocommerce_after_checkout_billing_form
	 * @hook woocommerce_after_checkout_shipping_form
	 * @hook woocommerce_before_order_notes
	 * @hook woocommerce_after_order_notes
	 *
	 * @globals array $orddd_lite_weekdays Weekdays array
	 *
	 * @param resource $checkout WooCommerce checkout object.
	 * @since 1.5
	 */
	public static function orddd_lite_my_custom_checkout_field( $checkout = '' ) {
		global $orddd_lite_weekdays;
		if ( 'on' === get_option( 'orddd_lite_enable_delivery_date' ) ) {
			$var = '';

			$first_day_of_week = '1';
			if ( '' !== get_option( 'orddd_lite_start_of_week' ) ) {
				$first_day_of_week = get_option( 'orddd_lite_start_of_week' );
			}
			?>
				<input type="hidden" name="orddd_first_day_of_week" id="orddd_first_day_of_week" value="<?php echo esc_attr( $first_day_of_week ); ?>">
				<input type="hidden" name="orddd_lite_delivery_date_format" id="orddd_lite_delivery_date_format" value="<?php echo esc_attr( get_option( 'orddd_lite_delivery_date_format' ) ); ?>">
			<?php
			// phpcs:ignore
			$field_note_text = __( get_option( 'orddd_lite_delivery_date_field_note' ), 'order-delivery-date' );
			$field_note_text = str_replace( array( "\r\n", "\r", "\n" ), '<br/>', $field_note_text );
			if ( strpos( $field_note_text, '"' ) !== false ) {
				?>
				<input type="hidden" name="orddd_lite_field_note" id="orddd_lite_field_note" value="<?php echo esc_attr( $field_note_text ); ?>">
				<?php
			} else {
				?>
				<input type="hidden" name="orddd_lite_field_note" id="orddd_lite_field_note" value="<?php echo esc_attr( $field_note_text ); ?>">
				<?php
			}

			$alldays_orddd_lite = array();
			foreach ( $orddd_lite_weekdays as $n => $day_name ) {
				$alldays_orddd_lite[ $n ] = get_option( $n );
			}
			$alldayskeys_orddd_lite = array_keys( $alldays_orddd_lite );
			$checked                = 'No';
			foreach ( $alldayskeys_orddd_lite as $key ) {
				if ( 'checked' === $alldays_orddd_lite[ $key ] ) {
					$checked = 'Yes';
				}
			}

			if ( 'Yes' === $checked ) {
				foreach ( $alldayskeys_orddd_lite as $key ) {
					?>
						<input type="hidden" id="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $alldays_orddd_lite[ $key ] ); ?>">
					<?php
				}
			} elseif ( 'No' === $checked ) {
				foreach ( $alldayskeys_orddd_lite as $key ) {
					?>
					<input type="hidden" id="<?php echo esc_attr( $key ); ?>" value="checked">
					<?php
				}
			}

			$min_date        = '';
			$current_time    = current_time( 'timestamp' ); //phpcs:ignore
			$current_date    = date( 'j-n-Y', $current_time ); //phpcs:ignore
			$current_hour    = date( 'H', $current_time ); //phpcs:ignore
			$current_minute  = date( 'i', $current_time ); //phpcs:ignore
			$current_weekday = date( 'w', $current_time ); //phpcs:ignore

			if ( '' === get_option( 'orddd_lite_minimumOrderDays' ) ) {
				$minimum_delivery_time_orddd_lite = 0;
			} else {
				$minimum_delivery_time_orddd_lite = get_option( 'orddd_lite_minimumOrderDays' );
			}

			$delivery_time_seconds = $minimum_delivery_time_orddd_lite * 60 * 60;
			$cut_off_timestamp     = $current_time + $delivery_time_seconds;
			$cut_off_date          = gmdate( 'd-m-Y', $cut_off_timestamp );
			$min_date              = gmdate( 'j-n-Y', strtotime( $cut_off_date ) );

			?>

			<input type="hidden" name="orddd_lite_number_of_dates" id="orddd_lite_number_of_dates" value="<?php echo esc_attr( get_option( 'orddd_lite_number_of_dates' ) ); ?>">
			<input type="hidden" name="orddd_lite_date_field_mandatory" id="orddd_lite_date_field_mandatory" value="<?php echo esc_attr( get_option( 'orddd_lite_date_field_mandatory' ) ); ?>">
			<input type="hidden" name="orddd_lite_number_of_months" id="orddd_lite_number_of_months" value="<?php echo esc_attr( get_option( 'orddd_lite_number_of_months' ) ); ?>">
			<input type="hidden" name="h_deliverydate" id="h_deliverydate" value="">

			<?php
			$lockout_days_str = '';
			if ( get_option( 'orddd_lite_lockout_date_after_orders' ) > 0 ) {
				$lockout_days_arr = array();
				$lockout_days     = get_option( 'orddd_lite_lockout_days' );
				if ( '' !== $lockout_days && '{}' !== $lockout_days && '[]' !== $lockout_days ) {
					$lockout_days_arr = json_decode( get_option( 'orddd_lite_lockout_days' ) );
				}
				foreach ( $lockout_days_arr as $k => $v ) {
					if ( $v->o >= get_option( 'orddd_lite_lockout_date_after_orders' ) ) {
						$lockout_days_str .= '"' . $v->d . '",';
					}
				}
			}
			if ( 'on' === get_option( 'orddd_lite_enable_time_slot' ) ) {
				$booked_timeslot_days = Orddd_Lite_Common::orddd_lite_get_booked_timeslot_days();
				foreach ( $booked_timeslot_days as $booked_day ) {
					$lockout_days_str .= '"' . $booked_day . '",';
				}
			}

			if ( '' !== $lockout_days_str ) {
				$lockout_days_str = substr( $lockout_days_str, 0, strlen( $lockout_days_str ) - 1 );
			}
			?>

			<input type="hidden" name="orddd_lite_lockout_days" id="orddd_lite_lockout_days" value=" <?php echo esc_attr( $lockout_days_str ); ?>">
			<?php
			// fetch holidays.
			$holidays_arr = array();
			$holidays     = get_option( 'orddd_lite_holidays' );
			if ( '' !== $holidays &&
				'{}' !== $holidays &&
				'[]' !== $holidays &&
				null !== $holidays &&
				false !== $holidays ) {
				$holidays_arr = json_decode( get_option( 'orddd_lite_holidays' ) );
			}
			$holidays_str = '';
			foreach ( $holidays_arr as $k => $v ) {
				$name = str_replace( "'", '&apos;', $v->n );
				$name = str_replace( '"', '&quot;', $name );
				if ( isset( $v->r_type ) && 'on' === $v->r_type ) {
					$holiday_date_arr = explode( '-', $v->d );
					$recurring_date   = $holiday_date_arr[0] . '-' . $holiday_date_arr[1];
					$holidays_str    .= '"' . $name . ':' . $recurring_date . '",';
				} else {
					$holidays_str .= '"' . $name . ':' . $v->d . '",';
				}
			}

			$holidays_str   = substr( $holidays_str, 0, strlen( $holidays_str ) - 1 );
			$min_date_array = Orddd_Lite_Common::get_min_date( $delivery_time_seconds, $holidays_str, $lockout_days_str );

			// check mindate is today.. if yes, then check if all time slots are past, if yes, then set mindate to tomorrow.
			if ( 'on' === get_option( 'orddd_lite_enable_time_slot' ) ) {
				$last_slot_hrs          = 0;
				$last_slot_min          = 0;
				$current_date           = date( 'j-n-Y', $current_time ); //phpcs:ignore
				$existing_timeslots_arr = json_decode( get_option( 'orddd_lite_delivery_time_slot_log' ) );
				foreach ( $existing_timeslots_arr as $k => $v ) {
					$hours = $v->fh;
					$mins  = $v->fm;

					if ( gettype( json_decode( $v->dd ) ) === 'array' && count( json_decode( $v->dd ) ) > 0 ) {
						$dd             = json_decode( $v->dd );
						$check_min_date = date( 'n-j-Y', strtotime( $min_date_array['min_date'] ) ); //phpcs:ignore

						if ( is_array( $dd ) && count( $dd ) > 0 ) {
							$min_weekday = date( 'w', strtotime( $min_date_array['min_date'] ) ); //phpcs:ignore
							$min_weekday = 'orddd_lite_weekday_' . $min_weekday;
							if ( in_array( $min_weekday, $dd ) ) { //phpcs:ignore
								$current_slot_hrs  = $hours;
								$current_slot_mins = $mins;

								if ( $current_slot_hrs >= $last_slot_hrs || ( $current_slot_hrs == $last_slot_hrs && $current_slot_mins > $last_slot_min ) ) { //phpcs:ignore
									$last_slot_hrs = $current_slot_hrs;
									$last_slot_min = $current_slot_mins;
								}
							} elseif ( in_array( 'all', $dd, true ) ) {
								$current_slot_hrs  = $hours;
								$current_slot_mins = $mins;

								if ( $current_slot_hrs > $last_slot_hrs || ( $current_slot_hrs == $last_slot_hrs && $current_slot_mins > $last_slot_min ) ) { //phpcs:ignore
									$last_slot_hrs = $current_slot_hrs;
									$last_slot_min = $current_slot_mins;
								}
							}
						}
					} else {
						$current_slot_hrs  = $hours;
						$current_slot_mins = $mins;

						if ( $current_slot_hrs > $last_slot_hrs || ( $current_slot_hrs == $last_slot_hrs && $current_slot_mins > $last_slot_min ) ) { //phpcs:ignore
							$last_slot_hrs = $current_slot_hrs;
							$last_slot_min = $current_slot_mins;
						}
					}
				}

				if ( 0 != $last_slot_hrs ) { //phpcs:ignore
					$last_slot     = $last_slot_hrs . ':' . trim( $last_slot_min );
					$booking_date2 = $min_date_array['min_date'] . ' ' . $last_slot;
					$booking_date2 = date( 'Y-m-d G:i', strtotime( $booking_date2 ) ); //phpcs:ignore

					$date2              = new DateTime( $booking_date2 );
					$date_to_check      = date( 'n-j-Y', $current_time ); //phpcs:ignore
					$delivery_dates_arr = array();

					if ( 'checked' !== get_option( 'orddd_lite_weekday_' . $current_weekday ) ) {
						$current_time = strtotime( $current_date );
					}

					$booking_date1 = date( 'Y-m-d G:i', $current_time ); //phpcs:ignore
					$date1         = new DateTime( $booking_date1 );

					if ( '' !== $minimum_delivery_time_orddd_lite && 0 !== $minimum_delivery_time_orddd_lite ) {
						$calculated_date = $min_date_array['min_date'] . ' ' . $min_date_array['min_hour'] . ':' . $min_date_array['min_minute'];
					} else {
						$calculated_date = $current_date . ' ' . $current_hour . ':' . $current_minute;
					}

					$calculated_min_date   = new DateTime( $calculated_date );
					$calculated_difference = $date2->diff( $date1 );

					if ( $calculated_difference->days > 0 ) {
						$days_in_hour             = $calculated_difference->h + ( $calculated_difference->days * 24 );
						$calculated_difference->h = $days_in_hour;
					}

					if ( $calculated_difference->i > 0 ) {
						$min_in_hour                      = $calculated_difference->h + ( $calculated_difference->i / 60 );
						$calculated_minimum_delivery_time = $min_in_hour * 60 * 60;
					} else {
						$calculated_minimum_delivery_time = $calculated_difference->h * 60 * 60;
					}

					if ( 0 === $calculated_difference->invert || $calculated_minimum_delivery_time < $delivery_time_seconds ) {
						$min_date_array['min_date'] = date( 'j-n-Y', strtotime( $min_date_array['min_date'] . '+1 day' ) ); //phpcs:ignore
					}
				}
			}

			?>
			<input type="hidden" name="orddd_lite_minimumOrderDays" id="orddd_lite_minimumOrderDays" value="<?php echo esc_attr( $min_date_array['min_date'] ); ?>">
			<input type="hidden" name="orddd_lite_holidays" id="orddd_lite_holidays" value="<?php echo esc_attr( $holidays_str ); ?>">
			<input type="hidden" name="orddd_lite_auto_populate_first_available_date" id="orddd_lite_auto_populate_first_available_date" value="<?php echo esc_attr( get_option( 'orddd_lite_auto_populate_first_available_date' ) ); ?>">
			<input type="hidden" name="orddd_lite_calculate_min_time_disabled_days" id="orddd_lite_calculate_min_time_disabled_days" value="<?php echo esc_attr( get_option( 'orddd_lite_calculate_min_time_disabled_days' ) ); ?>">
			<?php
			$admin_url     = get_admin_url();
			$admin_url_arr = explode( '://', $admin_url );
			$home_url      = get_home_url();
			$home_url_arr  = explode( '://', $home_url );
			if ( $admin_url_arr[0] !== $home_url_arr[0] ) {
				$admin_url_arr[0] = $home_url_arr[0];
				$ajax_url         = implode( '://', $admin_url_arr );
			} else {
				$ajax_url = $admin_url;
			}
			?>
			<input type="hidden" name="orddd_admin_url" id="orddd_admin_url" value="<?php echo esc_attr( $ajax_url ); ?>">
			<?php
			$orddd_lite_disable_for_holidays = 'no';
			if ( has_filter( 'orddd_to_calculate_minimum_hours_for_holidays' ) ) {
				$orddd_lite_disable_for_holidays = apply_filters( 'orddd_to_calculate_minimum_hours_for_holidays', $orddd_lite_disable_for_holidays );
			}

			?>
			<input type="hidden" name="orddd_lite_disable_for_holidays" id="orddd_lite_disable_for_holidays" value="<?php echo esc_attr( $orddd_lite_disable_for_holidays ); ?>">
			<input type="hidden" name="orddd_lite_delivery_date_on_cart_page" id="orddd_lite_delivery_date_on_cart_page" value ="<?php echo esc_attr( get_option( 'orddd_lite_delivery_date_on_cart_page' ) ); ?>">
			<?php
			$current_hour   = gmdate( 'H', $current_time );
			$current_minute = gmdate( 'i', $current_time );
			?>
			<input type="hidden" name="orddd_lite_current_day" id="orddd_lite_current_day" value="<?php echo esc_attr( $current_date ); ?>">
			<input type="hidden" name="orddd_lite_current_hour" id="orddd_lite_current_hour" value="<?php echo esc_attr( $current_hour ); ?>">
			<input type="hidden" name="orddd_lite_current_minute" id="orddd_lite_current_minute" value="<?php echo esc_attr( $current_minute ); ?>">
			<?php
			$is_delivery_enabled = Orddd_Lite_Common::orddd_lite_is_delivery_enabled();

			if ( 'yes' === $is_delivery_enabled ) {
				$validate_wpefield = false;
				if ( get_option( 'orddd_lite_date_field_mandatory' ) === 'checked' ) {
					$validate_wpefield = true;
				}

				if ( '' === $checkout ) {
					woocommerce_form_field(
						'e_deliverydate',
						array(
							'type'              => 'text',
							// phpcs:ignore
							'label'             => __( get_option( 'orddd_lite_delivery_date_field_label' ), 'order-delivery-date' ),
							'required'          => $validate_wpefield,
							// phpcs:ignore
							'placeholder'       => __( get_option( 'orddd_lite_delivery_date_field_placeholder' ), 'order-delivery-date' ),
							'custom_attributes' => array( 'style' => 'cursor:text !important;' ),
							'class'             => array( 'form-row-cart' ),
						)
					);
				} else {
					woocommerce_form_field(
						'e_deliverydate',
						array(
							'type'              => 'text',
							// phpcs:ignore
							'label'             => __( get_option( 'orddd_lite_delivery_date_field_label' ), 'order-delivery-date' ),
							'required'          => $validate_wpefield,
							// phpcs:ignore
							'placeholder'       => __( get_option( 'orddd_lite_delivery_date_field_placeholder' ), 'order-delivery-date' ),
							'custom_attributes' => array( 'style' => 'cursor:text !important;' ),
							'class'             => array( 'form-row-wide' ),
						),
						$checkout->get_value( 'e_deliverydate' )
					);
				}

				if ( 'on' === get_option( 'orddd_lite_enable_time_slot' ) ) {
					$result            = array( __( 'Select a time slot', 'order-delivery-date' ) );
					$validate_wpefield = false;
					if ( get_option( 'orddd_lite_time_slot_mandatory' ) === 'checked' ) {
						$validate_wpefield = true;
					}

					$time_field_label = get_option( 'orddd_lite_delivery_timeslot_field_label' );
					if ( '' === $time_field_label ) {
						$time_field_label = 'Delivery Time';
					}

					if ( is_cart() ) {
						$custom_attributes = array(
							'disabled' => 'disabled',
							'style'    => 'cursor:not-allowed !important;max-width:300px;',
						);
					} else {
						$custom_attributes = array(
							'disabled' => 'disabled',
							'style'    => 'cursor:not-allowed !important;',
						);
					}

					if ( is_object( $checkout ) ) {
						woocommerce_form_field(
							'orddd_time_slot',
							array(
								'type'              => 'select',
								'label'             => __( $time_field_label, 'order-delivery-date' ), //phpcs:ignore
								'required'          => $validate_wpefield,
								'options'           => $result,
								'validate'          => array( 'required' ),
								'custom_attributes' => $custom_attributes,
								'class'             => array( 'form-row-wide' ),
							),
							$checkout->get_value( 'orddd_time_slot' )
						);
					} else {
						woocommerce_form_field(
							'orddd_time_slot',
							array(
								'type'              => 'select',
								'label'             => __( $time_field_label, 'order-delivery-date' ), //phpcs:ignore
								'required'          => $validate_wpefield,
								'options'           => $result,
								'validate'          => array( 'required' ),
								'custom_attributes' => $custom_attributes,
								'class'             => array( 'form-row-wide' ),
							)
						);
					}
				}
			}

			?>
			<input type="hidden" name="orddd_lite_enable_time_slot" id="orddd_lite_enable_time_slot" value="<?php echo esc_attr( get_option( 'orddd_lite_enable_time_slot' ) ); ?>">

			<input type="hidden" name="orddd_min_date_set" id="orddd_min_date_set" value="<?php echo esc_attr( $min_date_array['min_date'] ); ?>">
			<input type="hidden" name="orddd_is_cart" id="orddd_is_cart" value="<?php echo esc_attr( is_cart() ); ?>">
			<input type="hidden" name="orddd_lite_auto_populate_first_available_time_slot" id="orddd_lite_auto_populate_first_available_time_slot" value="<?php echo esc_attr( get_option( 'orddd_lite_auto_populate_first_available_time_slot' ) ); ?>">
			<?php
		}
	}

	/**
	 * Saves the selected delivery date into the post meta table
	 *
	 * @hook woocommerce_checkout_update_order_meta
	 *
	 * @param int $order_id Order ID.
	 * @since 1.5
	 */
	public static function orddd_lite_my_custom_checkout_field_update_order_meta( $order_id ) {
		if ( isset( $_POST['e_deliverydate'] ) && '' !== $_POST['e_deliverydate'] ) { //phpcs:ignore
			$delivery_date = '';
			$date_format   = 'dd-mm-y';

			if ( isset( $_POST['h_deliverydate'] ) ) { //phpcs:ignore
				$delivery_date = sanitize_text_field( wp_unslash( $_POST['h_deliverydate'] ) ); //phpcs:ignore
			}

			update_post_meta( $order_id, get_option( 'orddd_lite_delivery_date_field_label' ), sanitize_text_field( wp_unslash( $_POST['e_deliverydate'] ) ) ); //phpcs:ignore

			$timestamp = Orddd_Lite_Common::orddd_lite_get_timestamp( $delivery_date, $date_format );
			update_post_meta( $order_id, '_orddd_lite_timestamp', $timestamp );
			self::orddd_lite_update_lockout_days( $delivery_date );
		} else {
			$delivery_enabled    = Orddd_Lite_Common::orddd_lite_is_delivery_enabled();
			$is_delivery_enabled = 'yes';
			if ( 'no' === $delivery_enabled ) {
				$is_delivery_enabled = 'no';
			}

			if ( 'yes' === $is_delivery_enabled ) {
				update_post_meta( $order_id, get_option( 'orddd_delivery_date_field_label' ), '' );
			}
		}
	}

	/**
	 * Add selected time slot in the post meta
	 *
	 * @hook woocommerce_checkout_update_order_meta
	 * @globals resource $wpdb WordPress Object
	 * @globals resource $woocommerce WooCommerce Object
	 *
	 * @param int $order_id Order ID.
	 * @since 3.11.0
	 */
	public static function orddd_update_order_meta_time_slot( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( ! is_object( $order ) ) {
			return;
		}

		$time_slot_label = '' !== get_option( 'orddd_lite_delivery_timeslot_field_label' ) ? get_option( 'orddd_lite_delivery_timeslot_field_label' ) : 'Time Slot';

		if ( isset( $_POST['orddd_time_slot'] ) && '' != $_POST['orddd_time_slot'] ) { //phpcs:ignore
			$time_slot = $_POST['orddd_time_slot']; //phpcs:ignore

			if ( has_filter( 'orddd_before_timeslot_update' ) ) {
				$time_slot = apply_filters( 'orddd_before_timeslot_update', $time_slot );
			}

			$h_deliverydate = '';
			if ( isset( $_POST['h_deliverydate'] ) ) { //phpcs:ignore
				$h_deliverydate = $_POST['h_deliverydate']; //phpcs:ignore
			}

			if ( '' !== $time_slot && 'choose' !== $time_slot && 'NA' !== $time_slot && 'select' !== $time_slot ) {
				if ( 'asap' === $time_slot ) {
					update_post_meta( $order_id, $time_slot_label, esc_attr( __( 'As Soon As Possible.', 'order-delivery-date' ) ) );
					update_post_meta( $order_id, '_orddd_time_slot', esc_attr( __( 'As Soon As Possible.', 'order-delivery-date' ) ) );
				} else {
					$order_time_slot = $time_slot;
					$time_format     = get_option( 'orddd_lite_delivery_time_format' );
					$time_slot_arr   = explode( ' - ', $time_slot );

					if ( '1' === $time_format ) {
						$from_time = date( 'H:i', strtotime( $time_slot_arr[0] ) ); //phpcs:ignore
						if ( isset( $time_slot_arr[1] ) ) {
							$to_time         = date( 'H:i', strtotime( $time_slot_arr[1] ) ); //phpcs:ignore
							$order_time_slot = $from_time . ' - ' . $to_time;
						} else {
							$order_time_slot = $from_time;
						}
					} else {
						$from_time = date( 'H:i', strtotime( $time_slot_arr[0] ) ); //phpcs:ignore
					}
					update_post_meta( $order_id, $time_slot_label, esc_attr( $time_slot ) );
					update_post_meta( $order_id, '_orddd_time_slot', $order_time_slot );

					$delivery_date  = $h_deliverydate;
					$delivery_date .= ' ' . $from_time;
					$timestamp      = strtotime( $delivery_date );

					update_post_meta( $order_id, '_orddd_lite_timeslot_timestamp', $timestamp );

					self::orddd_lite_update_lockout_timeslot( $h_deliverydate, $order_time_slot );
				}
			}

			do_action( 'orddd_after_timeslot_update', $time_slot );
		}
	}


	/**
	 * Updates the lockout for the delivery date in the options table
	 *
	 * @param string $delivery_date Selected Delivery Date.
	 * @since 1.5
	 */
	public static function orddd_lite_update_lockout_days( $delivery_date ) {

		$lockout_date = gmdate( 'n-j-Y', strtotime( $delivery_date ) );
		$lockout_days = get_option( 'orddd_lite_lockout_days' );
		if ( '' === $lockout_days || '{}' === $lockout_days || '[]' === $lockout_days ) {
			$lockout_days_arr = array();
		} else {
			$lockout_days_arr = json_decode( $lockout_days );
		}
		// existing lockout days.
		$existing_days = array();
		foreach ( $lockout_days_arr as $k => $v ) {
			$orders = $v->o;
			if ( $lockout_date === $v->d ) {
				$orders = $v->o + 1;
			}
			$existing_days[]        = $v->d;
			$lockout_days_new_arr[] = array(
				'o' => $orders,
				'd' => $v->d,
			);
		}
		// add the currently selected date if it does not already exist.
		if ( ! in_array( $lockout_date, $existing_days, true ) ) {
			$lockout_days_new_arr[] = array(
				'o' => 1,
				'd' => $lockout_date,
			);
		}
		$lockout_days_jarr = wp_json_encode( $lockout_days_new_arr );
		update_option( 'orddd_lite_lockout_days', $lockout_days_jarr );
	}


	/**
	 * Update number of order for Delivery date and Time slot in options table
	 *
	 * @globals resource $wpdb WordPress Object
	 *
	 * @param string $delivery_date Selected Delivery date on the checkout page.
	 * @param string $timeslot Selected time slot on the checkout page.
	 *
	 * @since 3.11.0
	 */
	public static function orddd_lite_update_lockout_timeslot( $delivery_date, $timeslot ) {
		if ( '' === $timeslot || __( 'As Soon As Possible.', 'order-delivery-date' ) === $timeslot ) {
			return;
		}

		$lockout_date        = $delivery_date;
		$time_format_to_show = Orddd_Lite_Common::orddd_lite_get_time_format();
		$lockout_time        = get_option( 'orddd_lite_lockout_time_slot' );
		if ( '' == $lockout_time || '{}' == $lockout_time || '[]' == $lockout_time || 'null' == $lockout_time ) { //phpcs:ignore
			$lockout_time_arr = array();
		} else {
			$lockout_time_arr = json_decode( $lockout_time );
		}
		$existing_timeslots   = array();
		$existing_dates       = array();
		$lockout_time_new_arr = array();
		$total_quantities     = 1;

		foreach ( $lockout_time_arr as $k => $v ) {
			$orders = $v->o;
			if ( $timeslot == $v->t && $lockout_date == $v->d ) { //phpcs:ignore
				$orders = $v->o + $total_quantities;
			}
			$existing_timeslots[ $v->d ][] = $v->t;
			$existing_dates[]              = $v->d;
			$lockout_time_new_arr[]        = array(
				'o' => $orders,
				't' => $v->t,
				'd' => $v->d,
			);
		}

		$timeslot = Orddd_Lite_Common::orddd_lite_change_time_slot_format( $timeslot, $time_format_to_show );

		// add the currently selected date if it does not already exist.
		if ( ( isset( $existing_timeslots[ $lockout_date ] ) && ! in_array( $timeslot, $existing_timeslots[ $lockout_date ], true ) ) || ! in_array( $lockout_date, $existing_dates, true ) ) {
			$lockout_time_new_arr[] = array(
				'o' => $total_quantities,
				't' => $timeslot,
				'd' => $lockout_date,
			);
		}

		$lockout_time_jarr = wp_json_encode( $lockout_time_new_arr );
		update_option( 'orddd_lite_lockout_time_slot', $lockout_time_jarr );

	}

	/**
	 * Show delivery date in the email notification for the WooCommerce version below 2.3
	 *
	 * @hook woocommerce_email_order_meta_keys
	 *
	 * @param array $keys Array of custom fields to be included in notification email.
	 * @return array $keys
	 * @since 1.3
	 */
	public static function orddd_lite_add_delivery_date_to_order_woo_deprecated( $keys ) {
		// phpcs:ignore
		$label_name = __( get_option( 'orddd_lite_delivery_date_field_label' ), 'order-delivery-date' );
		$keys[]     = get_option( 'orddd_lite_delivery_date_field_label' );
		return $keys;
	}

	/**
	 * Show Time Slot in the email notification for the WooCommerce version below 2.3
	 *
	 * @hook woocommerce_email_order_meta_keys
	 *
	 * @param array $keys Array of custom fields to be included in notification email.
	 * @return array $keys
	 * @since
	 */
	public static function orddd_lite_add_time_slot_to_order_woo_deprecated( $keys ) {
		// phpcs:ignore
		$label_name = __( get_option( 'orddd_lite_delivery_timeslot_field_label' ), 'order-delivery-date' );
		$keys[]     = get_option( 'orddd_lite_delivery_timeslot_field_label' );
		return $keys;
	}

	/**
	 * Display Delivery Date in Customer notification email for WooCOmmerce version 2.3 and above
	 *
	 * @hook woocommerce_email_order_meta_fields
	 * @param array    $fields Custom fields to be added in the notification email.
	 * @param bool     $sent_to_admin Whether to send the email to admin or not.
	 * @param resource $order Order Object.
	 * @return array fields
	 * @since 1.3
	 */
	public static function orddd_lite_add_delivery_date_to_order_woo_new( $fields, $sent_to_admin, $order ) {
		if ( version_compare( get_option( 'woocommerce_version' ), '3.0.0', '>=' ) ) {
			$order_id = $order->get_id();
		} else {
			$order_id = $order->id;
		}
		$fields[ get_option( 'orddd_lite_delivery_date_field_label' ) ] = array(
			// phpcs:ignore
			'label' => __( get_option( 'orddd_lite_delivery_date_field_label' ), 'order-delivery-date' ),
			'value' => get_post_meta( $order_id, get_option( 'orddd_lite_delivery_date_field_label' ), true ),
		);
		return $fields;
	}

	/**
	 * Display Time Slot in Customer notification email for WooCOmmerce version 2.3 and above
	 *
	 * @hook woocommerce_email_order_meta_fields
	 * @param array    $fields Custom fields to be added in the notification email.
	 * @param bool     $sent_to_admin Whether to send the email to admin or not.
	 * @param resource $order Order Object.
	 * @return array fields
	 * @since 1.3
	 */
	public static function orddd_lite_add_time_slot_to_order_woo_new( $fields, $sent_to_admin, $order ) {
		if ( version_compare( get_option( 'woocommerce_version' ), '3.0.0', '>=' ) ) {
			$order_id = $order->get_id();
		} else {
			$order_id = $order->id;
		}

		$time_slot_label = ( '' !== get_option( 'orddd_lite_delivery_timeslot_field_label' ) && false !== get_option( 'orddd_lite_delivery_timeslot_field_label' ) ) ? get_option( 'orddd_lite_delivery_timeslot_field_label' ) : 'Time Slot';

		$fields[ $time_slot_label ] = array(
			// phpcs:ignore
			'label' => __( $time_slot_label, 'order-delivery-date' ),
			'value' => get_post_meta( $order_id, $time_slot_label, true ),
		);
		return $fields;
	}

	/**
	 * Validate delivery date field
	 *
	 * @hook woocommerce_checkout_process
	 * @since 1.4
	 **/
	public static function orddd_lite_validate_date_wpefield() {

		$is_delivery_enabled = Orddd_Lite_Common::orddd_lite_is_delivery_enabled();

		$delivery_date = '';
		if ( isset( $_POST['e_deliverydate'] ) ) { // phpcs:ignore
			$delivery_date = sanitize_text_field( wp_unslash( $_POST['e_deliverydate'] ) ); // phpcs:ignore
		}

		if ( isset( $_POST['orddd_time_slot'] ) ) { // phpcs:ignore
			$ts = wc_clean( wp_unslash( $_POST['orddd_time_slot'] ) ); // phpcs:ignore
		} else {
			$ts = '';
		}

		if ( 'yes' === $is_delivery_enabled ) {
			// Check if set, if its not set add an error.
			if ( 'checked' === get_option( 'orddd_lite_date_field_mandatory' ) && '' === $delivery_date ) {
				// phpcs:ignore
				$message                              = '<strong>' . __( get_option( 'orddd_lite_delivery_date_field_label' ), 'order-delivery-date' ) . '</strong>' . ' ' . __( 'is a required field.', 'order-delivery-date' );
				wc_add_notice( $message, $notice_type = 'error' );
			}

			if ( 'on' === get_option( 'orddd_lite_enable_time_slot' ) && 'checked' === get_option( 'orddd_lite_time_slot_mandatory' ) ) {
				if ( '' === $ts || 'choose' === $ts || 'select' === $ts || 'NA' === $ts ) {
					$message = '<strong>' . __( get_option( 'orddd_lite_delivery_timeslot_field_label' ), 'order-delivery-date' ) . '</strong> ' . __( 'is a required field.', 'order-delivery-date' ); //phpcs:ignore
					wc_add_notice( $message, $notice_type = 'error' );
				}
			}
		}
	}

	/**
	 * Display Delivery Date on Order Recieved Page
	 *
	 * @hook woocommerce_order_details_after_order_table
	 *
	 * @globals array orddd_lite_date_formats Date Format array
	 *
	 * @param resource $order Order Object.
	 * @since 1.0
	 */
	public static function orddd_lite_add_delivery_date_to_order_page_woo( $order ) {
		global $orddd_lite_date_formats;
		if ( version_compare( get_option( 'woocommerce_version' ), '3.0.0', '>=' ) ) {
			$order_id = $order->get_id();
		} else {
			$order_id = $order->id;
		}
		$delivery_date_formatted = Orddd_Lite_Common::orddd_lite_get_order_delivery_date( $order_id );
		if ( '' !== $delivery_date_formatted ) {
			// phpcs:ignore
			echo '<p><strong>' . __( get_option( 'orddd_lite_delivery_date_field_label' ), 'order-delivery-date' ) . ':</strong> ' . $delivery_date_formatted . '</p>';
		}
	}

	/**
	 * Display Time Slot on Order Recieved Page
	 *
	 * @hook woocommerce_order_details_after_order_table
	 *
	 * @globals array orddd_lite_date_formats Date Format array
	 *
	 * @param resource $order Order Object.
	 * @since
	 */
	public static function orddd_lite_add_time_slot_to_order_page_woo( $order ) {
		if ( version_compare( get_option( 'woocommerce_version' ), '3.0.0', '>=' ) ) {
			$order_id = $order->get_id();
		} else {
			$order_id = $order->id;
		}

		$order_page_time_slot = Orddd_Lite_Common::orddd_get_order_timeslot( $order_id );
		$time_field_label     = '' !== get_option( 'orddd_lite_delivery_timeslot_field_label' ) ? get_option( 'orddd_lite_delivery_timeslot_field_label' ) : 'Time Slot';
		if ( '' !== $order_page_time_slot ) {
			echo '<p><strong>' . __( $time_field_label, 'order-delivery-date' ) . ':</strong> ' . $order_page_time_slot . '</p>'; // phpcs:ignore
		}
	}

	/**
	 * Add Time slot drop down on select of the date on checkout page
	 *
	 * @hook wp_ajax_nopriv_check_for_time_slot_orddd
	 * @hook wp_ajax_check_for_time_slot_orddd
	 *
	 * @globals array $orddd_weekdays Weekdays array
	 * @globals resource $wpdb WordPress Object
	 *
	 * @since
	 */
	public static function check_for_time_slot_orddd() {

		$time_format_to_show = Orddd_Lite_Common::orddd_lite_get_time_format();

		// Time slot in Session variable.
		$session_time_slot = '';
		if ( isset( $_POST['time_slot_session'] ) && 'on' == get_option( 'orddd_lite_delivery_date_on_cart_page' ) ) { // phpcs:ignore
			$session_time_slot = $_POST['time_slot_session']; // phpcs:ignore
		}

		// Time slot selected for the order. This is for the edit order page in admin.
		$time_slot_for_order = '';
		if ( isset( $_POST['order_id'] ) ) { // phpcs:ignore
			$order_id            = $_POST['order_id']; // phpcs:ignore
			$time_slot_for_order = Orddd_Lite_Common::orddd_lite_get_order_timeslot( $order_id );
		}

		$time_slots_to_show_timestamp = Orddd_Lite_Common::orddd_lite_get_timeslot_display( $time_slot_for_order );

		$auto_populate_time_slot = get_option( 'orddd_auto_populate_first_available_time_slot' );

		asort( $time_slots_to_show_timestamp );

		if ( is_array( $time_slots_to_show_timestamp ) && count( $time_slots_to_show_timestamp ) > 1 ) {
			unset( $time_slots_to_show_timestamp['NA'] );
		}

		// Additional time slot to check.
		$additional_time_slot = '';
		if ( has_filter( 'orddd_populate_delivery_time' ) ) {
			$additional_time_slot_str = apply_filters( 'orddd_populate_delivery_time', '' );

			$additional_time_slot_arr = explode( ' - ', $additional_time_slot_str );
			$ft                       = '';
			if ( isset( $additional_time_slot_arr[0] ) ) {
				$ft = date( $time_format_to_show, strtotime( $additional_time_slot_arr[0] ) ); //phpcs:ignore
			}

			if ( isset( $additional_time_slot_arr[1] ) ) {
				$tt                   = date( $time_format_to_show, strtotime( $additional_time_slot_arr[1] ) ); //phpcs:ignore
				$additional_time_slot = $ft . ' - ' . $tt;
			} else {
				$additional_time_slot = $ft;
			}
		}

		$i             = 1;
		$time_slot_var = '';
		// Changing the seperator for time slots from comma(,) to backslash(/) to avoid conflicts with the
		// decimal seperator and thousand seperator.
		$time_slot_var .= 'select/';

		foreach ( $time_slots_to_show_timestamp as $key => $value ) {

			$sel = '';
			if ( '' !== $additional_time_slot && $key === $additional_time_slot ) {
				$sel = 'selected';
			} elseif ( '' !== $session_time_slot && $key === $session_time_slot ) {
				$sel = 'selected';
			} elseif ( ( '' !== $time_slot_for_order && $key === $time_slot_for_order ) ) {
				$sel = 'selected';
			} elseif ( 1 === $i && 'on' === get_option( 'orddd_lite_auto_populate_first_available_time_slot' ) ) {
				$sel = 'selected';
			}

			$current_date = isset( $_POST['current_date'] ) ? $_POST['current_date'] : false; //phpcs:ignore

			$timeslot_charges = Orddd_Lite_Common::orddd_lite_get_timeslot_charges( $key, $current_date );

			$timeslot_charges_label = '';
			if ( '' !== $timeslot_charges ) {
				$timeslot_charges_label = '(' . $timeslot_charges . ')';
				if ( has_filter( 'orddd_timeslot_charges_label' ) ) {
					$timeslot_charges_label = apply_filters( 'orddd_timeslot_charges_label', $timeslot_charges_label );
				}
			}

			$key_i18n = '';
			if ( 'asap' !== $key && 'NA' !== $key ) {

				if ( 'h:i A' === $time_format_to_show ) {
					$key = Orddd_Lite_Common::orddd_lite_change_time_slot_format( $key, $time_format_to_show );
				}

				$time_arr = explode( ' - ', $key );

				$from_time = date_i18n( $time_format_to_show, strtotime( $current_date . ' ' . $time_arr[0] ) );
				if ( isset( $time_arr[1] ) ) {
					$to_time  = date_i18n( $time_format_to_show, strtotime( $current_date . ' ' . $time_arr[1] ) );
					$key_i18n = $from_time . ' - ' . $to_time;
				} else {
					$key_i18n = $from_time;
				}
			}

			if ( '' !== $sel ) {
				$time_slot_var .= $key . '_' . $key_i18n . '_' . $timeslot_charges_label . '_' . $sel . '/';
			} else {
				$time_slot_var .= $key . '_' . $key_i18n . '_' . $timeslot_charges_label . '/';
			}

			$i++;
		}

		$time_slot_var = substr( $time_slot_var, 0, strlen( $time_slot_var ) - 1 );
		wp_send_json( $time_slot_var );
		die();
	}

	/**
	 * Add time slot charges on cart & checkout.
	 *
	 * @param WC_Cart $cart Cart object.
	 * @return void
	 */
	public static function orddd_lite_add_delivery_date_fee( $cart ) {
		global $woocommerce, $orddd_lite_weekdays, $wpdb;
		$gmt = false;
		if ( has_filter( 'orddd_gmt_calculations' ) ) {
			$gmt = apply_filters( 'orddd_gmt_calculations', '' );
		}

		$current_time  = current_time( 'timestamp', $gmt ); //phpcs:ignore
		$delivery_date = '';
		$time_slot     = '';
		$total_fees    = 0;

		if ( isset( $_POST['post_data'] ) ) { // phpcs:ignore

			$delivery_date_type = preg_match( '/h_deliverydate=(.*?)&/', $_POST['post_data'], $matches ); // phpcs:ignore
			if ( isset( $matches[1] ) ) {
				$delivery_date = $matches[1];
			}

			$time_slot_type = preg_match( '/&orddd_time_slot=(.*?)&/', $_POST['post_data'], $matches ); // phpcs:ignore

			if ( isset( $matches[1] ) ) {
				$time_slot = urldecode( $matches[1] );
			}
		}

		$delivery_on_cart = get_option( 'orddd_delivery_date_on_cart_page' );
		$is_cart          = is_cart();
		$is_ajax          = is_ajax();

		if ( '' === $delivery_date ) {
			if ( isset( $_POST['h_deliverydate'] ) ) { // phpcs:ignore
				 $delivery_date = $_POST['h_deliverydate']; // phpcs:ignore
				if ( $is_cart && 'on' === $delivery_on_cart ) {
					WC()->session->set( 'h_deliverydate', $delivery_date );
				}
			} elseif ( ( $is_cart || $is_ajax ) && 'on' === $delivery_on_cart && WC()->session->get( 'h_deliverydate' ) ) {
				$delivery_date = WC()->session->get( 'h_deliverydate' );
			} else {
				$delivery_date = '';
			}
		}

		if ( '' === $time_slot ) {
			if ( isset( $_POST['orddd_time_slot'] ) ) { // phpcs:ignore
				 $time_slot = $_POST['orddd_time_slot']; // phpcs:ignore
				if ( $is_cart && 'on' === $delivery_on_cart ) {
					WC()->session->set( 'orddd_time_slot', $time_slot );
				}
			} elseif ( ( $is_cart || $is_ajax ) && 'on' === $delivery_on_cart && WC()->session->get( 'orddd_time_slot' ) ) {
				$time_slot = WC()->session->get( 'orddd_time_slot' );
			} else {
				$time_slot = '';
			}
		}

		if ( '' !== $time_slot && 'choose' !== $time_slot && 'NA' !== $time_slot && 'select' !== $time_slot ) {
			$time_slot_arr = explode( ' - ', $time_slot );
			$from_time     = date( 'G:i', strtotime( $time_slot_arr[0] ) ); //phpcs:ignore
			if ( isset( $time_slot_arr[1] ) && '' !== $time_slot_arr[1] ) {
				$to_time           = date( 'G:i', strtotime( $time_slot_arr[1] ) ); //phpcs:ignore
				$timeslot_selected = $from_time . ' - ' . $to_time;
			} else {
				$timeslot_selected = $from_time;
			}

			$time_slot_fees_to_add = 0;

			$timeslot_log_arr   = array();
			$delivery_dates_arr = array();
			$temp_date_arr      = array();

			$timeslot_log_str = get_option( 'orddd_lite_delivery_time_slot_log' );
			if ( 'null' == $timeslot_log_str || '' == $timeslot_log_str || '{}' == $timeslot_log_str || '[]' == $timeslot_log_str ) { //phpcs:ignore
				$timeslot_log_arr = array();
			} else {
				$timeslot_log_arr = json_decode( $timeslot_log_str );
			}

			$date_to_check = date( 'n-j-Y', strtotime( $delivery_date ) ); //phpcs:ignore
			foreach ( $timeslot_log_arr as $k => $v ) {
				$ft = $v->fh . ':' . trim( $v->fm );
				if ( $v->th != 00 ) { //phpcs:ignore
					$tt            = $v->th . ':' . trim( $v->tm );
					$time_slot_key = $ft . ' - ' . $tt;
				} else {
					$time_slot_key = $ft;
				}

				$weekday = date( 'w', strtotime( $delivery_date ) ); //phpcs:ignore
				if ( gettype( json_decode( $v->dd ) ) === 'array' && count( json_decode( $v->dd ) ) > 0 ) {
					$dd = json_decode( $v->dd );
					foreach ( $dd as $dkey => $dval ) {
						if ( $timeslot_selected === $time_slot_key && ( 'orddd_lite_weekday_' . $weekday === $dval || 'all' === $dval ) ) {
							$additional_charges       = $v->additional_charges;
							$additional_charges_label = $v->additional_charges_label;
							if ( $additional_charges > 0 && '' !== $additional_charges ) {
								if ( $time_slot_fees_to_add < $additional_charges ) {
									$time_slot_fees_to_add = $additional_charges;
									if ( isset( $additional_charges_label ) && '' !== $additional_charges_label ) {
										$time_slot_charges_label = $additional_charges_label;
									} else {
										$time_slot_charges_label = 'Time Slot Charges';
									}
								}
							}
						}
					}
				} else {
					if ( $timeslot_selected === $time_slot_key && ( 'orddd_lite_weekday_' . $weekday === $v->dd || 'all' === $v->dd ) ) {
						$additional_charges       = $v->additional_charges;
						$additional_charges_label = $v->additional_charges_label;
						if ( $additional_charges > 0 && '' !== $additional_charges ) {
							if ( $time_slot_fees_to_add < $additional_charges ) {
								$time_slot_fees_to_add = $additional_charges;
								if ( isset( $additional_charges_label ) && '' !== $additional_charges_label ) {
									$time_slot_charges_label = $additional_charges_label;
								} else {
									$time_slot_charges_label = 'Time Slot Charges';
								}
							}
						}
					}
				}
			}

			if ( $time_slot_fees_to_add > 0 && '' !== $time_slot_fees_to_add ) {
				if ( is_object( $cart ) ) {
					$cart->add_fee( __( $time_slot_charges_label, 'order-delivery-date' ), $time_slot_fees_to_add, false ); //phpcs:ignore
				}
			}
		}
	}
}
$orddd_lite_process = new Orddd_Lite_Process();

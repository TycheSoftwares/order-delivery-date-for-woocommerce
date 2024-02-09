<?php
/**
 * Order Delivery Date for WooCommerce
 *
 * Processes performed on the frontend checkout page.
 *
 * @author      Tyche Softwares
 * @package     Order-Delivery-Date-Pro-for-WooCommerce/Frontend/Checkout-Page-Processes
 * @since       1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Order Delivery date in WooCommerce checkout block.
 */
class ORDDD_Lite_Delivery_Blocks {

	/**
	 * Constructor function
	 */
	public function __construct() {
		add_action( 'woocommerce_store_api_checkout_update_order_from_request', array( &$this, 'orddd_lite_update_block_order_meta_delivery_date' ), 10, 2 );
		add_action( 'woocommerce_store_api_checkout_update_order_from_request', array( &$this, 'orddd_lite_orddd_update_order_meta_time_slot' ), 10, 2 );
		add_action( 'woocommerce_store_api_checkout_update_order_from_request', array( &$this, 'orddd_lite_validate_date_wpefield_cart_block' ), 10, 2 );
	}

	/**
	 * Save the delivery date & time slot to order meta in WC Checkout block.
	 *
	 * @param object $order Order object.
	 * @param array  $request Order data to be saved.
	 * @return void
	 */
	public static function orddd_lite_update_block_order_meta_delivery_date( $order, $request ) {
		$order_id = $order->get_id();
		if ( isset( $request['extensions']['order-delivery-date'] ) && '' !== $request['extensions']['order-delivery-date'] ) { //phpcs:ignore
			$delivery_date = '';
			$date_format   = 'dd-mm-y';
			$data          = isset( $request['extensions']['order-delivery-date'] ) ? $request['extensions']['order-delivery-date'] : array();

			if ( isset( $data['h_deliverydate'] ) ) { //phpcs:ignore
				$delivery_date = sanitize_text_field( wp_unslash( $data['h_deliverydate'] ) ); //phpcs:ignore
			}

			Orddd_Lite_Common::update_order_meta( $order_id, get_option( 'orddd_lite_delivery_date_field_label' ), sanitize_text_field( wp_unslash( $data['e_deliverydate'] ) ), $order ); //phpcs:ignore

			$timestamp = Orddd_Lite_Common::orddd_lite_get_timestamp( $delivery_date, $date_format );
			Orddd_Lite_Common::update_order_meta( $order_id, '_orddd_lite_timestamp', $timestamp, $order );
			self::orddd_lite_update_lockout_days( $delivery_date );
		} else {
			$delivery_enabled    = Orddd_Lite_Common::orddd_lite_is_delivery_enabled();
			$is_delivery_enabled = 'yes';
			if ( 'no' === $delivery_enabled ) {
				$is_delivery_enabled = 'no';
			}

			if ( 'yes' === $is_delivery_enabled ) {
				Orddd_Lite_Common::update_order_meta( $order_id, get_option( 'orddd_delivery_date_field_label' ), '', $order );
			}
		}
		$order->save();
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
	 * Add selected time slot in the post meta
	 *
	 * @hook woocommerce_checkout_update_order_meta
	 * @globals resource $wpdb WordPress Object
	 * @globals resource $woocommerce WooCommerce Object
	 *
	 * @param int   $order Order.
	 * @param array $request Order.
	 * @since 3.11.0
	 */
	public static function orddd_lite_orddd_update_order_meta_time_slot( $order, $request ) {
		$order_id = $order->get_id();

		if ( ! is_object( $order ) ) {
			return;
		}
		$data = isset( $request['extensions']['order-delivery-date'] ) ? $request['extensions']['order-delivery-date'] : array();

		$time_slot_label = '' !== get_option( 'orddd_lite_delivery_timeslot_field_label' ) ? get_option( 'orddd_lite_delivery_timeslot_field_label' ) : 'Time Slot';

		if ( isset( $data['orddd_lite_time_slot'] ) && '' != $data['orddd_lite_time_slot'] ) { //phpcs:ignore
			$time_slot = $data['orddd_lite_time_slot']; //phpcs:ignore

			if ( has_filter( 'orddd_before_timeslot_update' ) ) {
				$time_slot = apply_filters( 'orddd_before_timeslot_update', $time_slot );
			}

			$h_deliverydate = '';
			if ( isset( $data['h_deliverydate'] ) ) { //phpcs:ignore
				$h_deliverydate = $data['h_deliverydate']; //phpcs:ignore
			}
			if ( '' !== $time_slot && 'choose' !== $time_slot && 'NA' !== $time_slot && 'select' !== $time_slot ) {
				if ( 'asap' === $time_slot ) {
					Orddd_Lite_Common::update_order_meta( $order_id, $time_slot_label, esc_attr( __( 'As Soon As Possible.', 'order-delivery-date' ) ), $order );
					Orddd_Lite_Common::update_order_meta( $order_id, '_orddd_time_slot', esc_attr( __( 'As Soon As Possible.', 'order-delivery-date' ) ), $order );
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
					Orddd_Lite_Common::update_order_meta( $order_id, $time_slot_label, esc_attr( $time_slot ), $order );
					Orddd_Lite_Common::update_order_meta( $order_id, '_orddd_time_slot', $order_time_slot, $order );

					$delivery_date  = $h_deliverydate;
					$delivery_date .= ' ' . $from_time;
					$timestamp      = strtotime( $delivery_date );

					Orddd_Lite_Common::update_order_meta( $order_id, '_orddd_lite_timeslot_timestamp', $timestamp, $order );

					$total_fees = WC()->session->get( '_total_delivery_charges' );
					if ( '' !== $total_fees && null !== $total_fees ) {
						Orddd_Lite_Common::update_order_meta( $order_id, '_total_delivery_charges', $total_fees, $order );
						WC()->session->__unset( '_total_delivery_charges' );
					} else {
						Orddd_Lite_Common::update_order_meta( $order_id, '_total_delivery_charges', '0', $order );
					}

					self::orddd_lite_update_lockout_timeslot( $h_deliverydate, $order_time_slot );
				}
				$order->save();
			}

			do_action( 'orddd_after_timeslot_update', $time_slot, $order_id );
		}
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
		$timeslot             = Orddd_Lite_Common::orddd_lite_change_time_slot_format( $timeslot, $time_format_to_show );

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
	 * Validate delivery date field
	 *
	 * @hook woocommerce_checkout_process
	 * @param object $order Order.
	 * @param array  $request Order.
	 * @since 1.4
	 **/
	public static function orddd_lite_validate_date_wpefield_cart_block( $order, $request ) {

		$is_delivery_enabled = Orddd_Lite_Common::orddd_lite_is_delivery_enabled();

		$delivery_date = '';
		$data          = isset( $request['extensions']['order-delivery-date'] ) ? $request['extensions']['order-delivery-date'] : array();

		if ( isset( $data['h_deliverydate'] ) && ! empty( $data['h_deliverydate'] ) ) {
			$delivery_date = sanitize_text_field( wp_unslash( $data['h_deliverydate'] ) );
		} elseif ( isset( $data['e_deliverydate'] ) && ! empty( $$data['e_deliverydate'] ) ) {
			$delivery_date = sanitize_text_field( wp_unslash( $data['e_deliverydate'] ) );
			$delivery_date = date( 'd-m-Y', strtotime( $delivery_date ) );// phpcs:ignore
		}

		if ( isset( $data['orddd_lite_time_slot'] ) ) { // phpcs:ignore
			$ts = wc_clean( wp_unslash( $data['orddd_lite_time_slot'] ) ); // phpcs:ignore
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
		} else {
			return;
		}
		$min_time_in_secs = '' !== get_option( 'orddd_lite_minimumOrderDays' ) ? get_option( 'orddd_lite_minimumOrderDays' ) * 60 * 60 : 0;
		if ( '' === $delivery_date ) {
			return;
		}

		$delivery_time = strtotime( $delivery_date );

		$gmt = false;
		if ( has_filter( 'orddd_gmt_calculations' ) ) {
			$gmt = apply_filters( 'orddd_gmt_calculations', '' );
		}
		$current_time = current_time( 'timestamp', $gmt );// phpcs:ignore

		if ( 'on' === get_option( 'orddd_lite_enable_time_slot' ) ) {

			$time_slot = sanitize_text_field( wp_unslash( $data['orddd_lite_time_slot'] ) );

			if ( ! ( '' === $time_slot || 'select' === $time_slot || 'asap' === $time_slot || 'NA' === $time_slot ) ) {
				$time_slot_arr = explode( ' - ', $time_slot );
				$from_time     = $time_slot_arr[0];
				if ( isset( $time_slot_arr[1] ) ) {
					$to_time = $time_slot_arr[1];
				} else {
					$to_time = '00';
				}

				$min_time_on_last_slot = apply_filters( 'orddd_min_delivery_on_last_slot', false );
				if ( $min_time_on_last_slot ) {
					$delivery_time = strtotime( $delivery_date . ' ' . $to_time );
				} else {
					$delivery_time = strtotime( $delivery_date . ' ' . $from_time );
				}
			} else {
				$delivery_time = $delivery_time + 24 * 60 * 60;
			}
		} else {
			$delivery_time = $delivery_time + 24 * 60 * 60;
		}

		if ( $min_time_in_secs > 0 ) {
			$delivery_time = $delivery_time - $min_time_in_secs;
		}

		if ( $current_time > $delivery_time ) {
			if ( empty( $time_slot_arr ) ) {
				$message = __( 'The cut-off time for the selected date has expired. Please select another delivery date.', 'order-delivery-date' );
			} else {
				$message = __( 'The selected time slot has expired. Please select another time slot for delivery.', 'order-delivery-date' );
			}
			wc_add_notice( $message, $notice_type = 'error' );
		}
	}
}

$orddd_delivery_blocks = new ORDDD_Lite_Delivery_Blocks();

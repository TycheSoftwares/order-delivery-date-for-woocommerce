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
			self::orddd_lite_validate_date_wpefield_cart_block( $order, $request );

			Orddd_Lite_Common::update_order_meta( $order_id, get_option( 'orddd_lite_delivery_date_field_label' ), sanitize_text_field( wp_unslash( $data['e_deliverydate'] ) ), $order ); //phpcs:ignore

			$timestamp = Orddd_Lite_Common::orddd_lite_get_timestamp( $delivery_date, $date_format );
			Orddd_Lite_Common::update_order_meta( $order_id, '_orddd_lite_timestamp', $timestamp, $order );
			Orddd_Lite_Process::orddd_lite_update_lockout_days( $delivery_date );
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
			self::orddd_lite_validate_date_wpefield_cart_block( $order, $request );
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

					Orddd_Lite_Process::orddd_lite_update_lockout_timeslot( $h_deliverydate, $order_time_slot );
				}
				$order->save();
			}

			do_action( 'orddd_after_timeslot_update', $time_slot, $order_id );
		}
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

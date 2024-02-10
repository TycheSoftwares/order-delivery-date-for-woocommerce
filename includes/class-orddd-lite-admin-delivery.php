<?php
/**
 * Order Delivery Date for WooCommerce Lite
 *
 * Handles the Add/Edit of the Delivery Date and Time in the admin order.
 *
 * @author      Tyche Softwares
 * @package     Order-Delivery-Date-Lite-for-WooCommerce/Admin/Edit-Order
 * @since       3.13.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Include Common function file.
require_once dirname( __FILE__ ) . '/class-orddd-lite-common.php';

/**
 * Main class which will handle the Add/Edit of Delivery information in the WooCommerce Add/Edit Orders page.
 *
 * @class orddd_admin_delivery_class
 */
class Orddd_Lite_Admin_Delivery {
	/**
	 * Default Constructor.
	 *
	 * @since 3.13.0
	 */
	public function __construct() {
		add_action( 'wp_ajax_save_delivery_dates', array( &$this, 'save_delivery_dates' ) );
		// Display Order Delivery Date meta box on Add/Edit Orders Page.
		if ( 'on' === get_option( 'orddd_lite_enable_delivery_date' ) ) {
			add_action( 'add_meta_boxes', array( &$this, 'orddd_admin_delivery_box' ) );
		}
	}

	/**
	 * Meta box for Delivery date and/or Time slot in WooCommerce Add/Edit Orders page.
	 *
	 * @hook add_meta_boxes
	 * @since 3.13.0
	 */
	public static function orddd_admin_delivery_box() {
		$screen = Orddd_Lite_Common::is_hpos_enabled() ? wc_get_page_screen_id( 'shop-order' ) : 'shop_order';
		add_meta_box( 'order-delivery-date', __( 'Edit Order Delivery Date and/or Time', 'order-delivery-date' ), array( 'Orddd_Lite_Admin_Delivery', 'orddd_lite_meta_box' ), $screen, 'normal', 'core' );
	}

	/**
	 * Delivery Date and/or Time slot fields in the Meta box
	 *
	 * @param resource $order - Order Details.
	 * @param array    $post - Post Details.
	 *
	 * @globals resource $wpdb
	 * @globals array $orddd_date_formats
	 * @globals resource $post
	 * @globals resource $woocommerce
	 * @globals array $orddd_languages
	 * @globals array $orddd_weekdays
	 * @since 3.13.0
	 */
	public static function orddd_lite_meta_box( $order, $post ) {
		global $wpdb, $orddd_date_formats, $post, $woocommerce, $orddd_languages, $orddd_weekdays;
		if ( 'on' === get_option( 'orddd_lite_enable_delivery_date' ) ) {
			$field_name      = 'e_deliverydate';
			
			$order_id = ( $order instanceof WP_Post ) ? $order->ID : $order->get_id();
			
			if ( ! Orddd_Lite_Common::is_hpos_enabled() ) {
				$order = wc_get_order( $order_id );
				$orddd_post_type = $post->post_type;
			} else {
				$orddd_post_type = $order->get_type();
			}
			
			$date_field_label = get_option( 'orddd_lite_delivery_date_field_label' );
			$time_field_label = get_option( 'orddd_lite_delivery_timeslot_field_label' );

			$get_order_item_ids_query = 'SELECT * FROM `' . $wpdb->prefix . 'woocommerce_order_items` WHERE order_id = %d';
			$results_order_item_ids   = $wpdb->get_results( $wpdb->prepare( $get_order_item_ids_query, $order_id ) ); // phpcs:ignore
			$product_id               = '';
			$shipping_method          = '';
			foreach ( $results_order_item_ids as $key => $value ) {
				$order_item_id      = $value->order_item_id;
				$get_itemmeta_query = 'SELECT * FROM `' . $wpdb->prefix . 'woocommerce_order_itemmeta` WHERE order_item_id = %d';
				$results            = $wpdb->get_results( $wpdb->prepare( $get_itemmeta_query, $order_item_id ) ); // phpcs:ignore
				foreach ( $results as $key => $value ) {
					if ( '_product_id' === $value->meta_key ) {
						$product_id = $value->meta_value;
					}
					if ( 'method_id' === $value->meta_key ) {
						$shipping_method = $value->meta_value;
					}
				}
			}

			$hidden_variables = Orddd_Lite_Common::orddd_lite_load_hidden_fields( '', $order_id );
			echo $hidden_variables; //phpcs:ignore

			// Default the fees.
			$fee = Orddd_Lite_Common::get_order_meta( $order_id, '_total_delivery_charges', true );
			if ( '' !== $fee || '{}' !== $fee || '[]' !== $fee ) {
				$fee_name = 'Delivery Charges:';
			} else {
				$fee      = 0;
				$fee_name = '';
				foreach ( $order->get_items( 'fee' ) as $item_id => $item_fee ) {
					if ( '' !== $item_fee->get_total() && $item_fee->get_total() > 0 ) {
						$fee_name = ( '' !== $item_fee->get_name() ) ? $item_fee->get_name() : __( 'Delivery Charges:', 'order-delivery-date' );
						$fee     += $item_fee->get_total();
					}
				}
				$fee_name = ( ( is_array( $order->get_items( 'fee' ) ) && count( $order->get_items( 'fee' ) ) ) > 1 || '' === $fee_name ) ? 'Delivery Charges:' : $fee_name;
			}

			$disabled = '';
			if ( 'auto-draft' === get_post_status( $order_id ) ) {
				$disabled = 'disabled';
			}

			$time_slot_enabled = false;

			// Add time slot values if enabled.
			if ( 'on' === get_option( 'orddd_lite_enable_time_slot' ) ) {
				$time_slot_enabled = true;
			}

			wc_get_template(
				'orddd-lite-admin-datepicker.php',
				array(
					'date_field_label'  => $date_field_label,
					'field_name'        => $field_name,
					'time_slot_enabled' => $time_slot_enabled,
					'time_field_label'  => $time_field_label,
					'fee_name'          => $fee_name,
					'fee'               => $fee,
				),
				'order-delivery-date-for-woocommerce/',
				ORDDD_LITE_TEMPLATE_PATH
			);

		}
	}

	/**
	 * Save Delivery date and/or Time slot
	 *
	 * @globals resource $wpdb
	 * @globals array $orddd_weekdays
	 * @since 3.13.0
	 */
	public static function save_delivery_dates() {
		global $wpdb, $orddd_weekdays;

		$delivery_details_updated = 'yes';
		$notes_array              = array();

		$order_id = isset( $_POST['order_id'] ) ? $_POST['order_id'] : ''; // phpcs:ignore

		if ( isset( $order_id ) && $order_id > 0 && false !== get_post_status( $order_id ) ) {
			$order      = new WC_Order( $order_id );
			$orddd_fees = ( isset( $_POST['orddd_charges'] ) && is_numeric( $_POST['orddd_charges'] ) ) ? $_POST['orddd_charges'] : 0; // phpcs:ignore

			$date_field_label = get_option( 'orddd_lite_delivery_date_field_label' );
			$time_field_label = get_option( 'orddd_lite_delivery_timeslot_field_label' );

			$date_selected            = 'no';
			$timeslot_selected        = 'no';
			$delivery_details_updated = 'no';

			$delivery_date = '';
			$time_slot     = '';

			$previous_order_date          = '';
			$previous_order_weekday_check = '';
			$previous_order_h_date        = '';
			$previous_order_timeslot      = '';
			$previous_charges_label       = '';
			$previous_order_date_check    = '';
			if ( ( isset( $_POST['e_deliverydate'] ) && '' !== $_POST['e_deliverydate'] ) ) { // phpcs:ignore
				$data = Orddd_Lite_Common::get_order_meta( $order_id );
				if ( strstr(  $_POST['e_deliverydate'], '/' ) ) {
					$_POST['e_deliverydate'] = implode( '-', explode( '/', $_POST['e_deliverydate'] ) );
				}

				if ( isset( $data['_orddd_lite_timestamp'][0] ) && '' !== $data['_orddd_lite_timestamp'][0] ) {
					$previous_order_h_date        = date( 'j-n-Y', $data['_orddd_lite_timestamp'][0] ); // phpcs:ignore
					$previous_order_date_check    = date( 'n-j-Y', $data['_orddd_lite_timestamp'][0] ); // phpcs:ignore
					$previous_order_weekday_check = date( 'w', $data['_orddd_lite_timestamp'][0] ); // phpcs:ignore
				}

				if ( isset( $data[ $date_field_label ][0] ) &&
					'' !== $data[ $date_field_label ][0] ) {

					$previous_order_date = $data[ $date_field_label ][0];

					if ( '' === $previous_order_h_date ) {
						$delivery_date_timestamp      = strtotime( str_replace( ',', ' ', $data[ $date_field_label ][0] ) );
						$previous_order_h_date        = date( 'j-n-Y', $delivery_date_timestamp ); // phpcs:ignore
						$previous_order_date_check    = date( 'n-j-Y', $delivery_date_timestamp ); // phpcs:ignore
						$previous_order_weekday_check = date( 'w', $delivery_date_timestamp ); // phpcs:ignore
					}
				}

				if ( isset( $data[ $time_field_label ][0] ) && '' !== $data[ $time_field_label ][0] ) {
					$previous_order_timeslot = $data[ $time_field_label ][0];
				}

				$order = wc_get_order( $order_id );

				if ( isset( $_POST['e_deliverydate'] ) && '' !== $_POST['e_deliverydate'] && $_POST['e_deliverydate'] !== $previous_order_date ) { // phpcs:ignore
					/* translators: %1s: date label, %2s old delivery date, %3s new delivery date */
					$notes_array[] = sprintf( __( '%1$s is updated from %2$s to %3$s', 'order-delivery-date' ), $date_field_label, $previous_order_date, $_POST['e_deliverydate'] ); // phpcs:ignore
					Orddd_Lite_Common::update_order_meta( $order_id, $date_field_label, $_POST['e_deliverydate'], $order ); // phpcs:ignore
					$delivery_details_updated = 'yes';
				}

				if ( isset( $_POST['h_deliverydate'] ) && '' !== $_POST['h_deliverydate'] ) { // phpcs:ignore
					$delivery_date = $_POST['h_deliverydate']; // phpcs:ignore
					$date_format   = 'dd-mm-y';
					if ( $previous_order_h_date !== $_POST['h_deliverydate'] || // phpcs:ignore
						( $previous_order_h_date == $_POST['h_deliverydate'] ) ) { // phpcs:ignore
						$timestamp = Orddd_Lite_Common::orddd_lite_get_timestamp( $delivery_date, $date_format );
						Orddd_Lite_Common::orddd_lite_cancel_delivery( $order_id );
						Orddd_Lite_Common::update_order_meta( $order_id, '_orddd_lite_timestamp', $timestamp, $order );
					}
				}
				$date_selected = 'yes';
			}

			if ( isset( $_POST['orddd_time_slot'] ) && '' !== $_POST['orddd_time_slot'] && 'select' !== $_POST['orddd_time_slot'] && 'No time slots are available.' !== $_POST['orddd_time_slot'] ) { // phpcs:ignore
				$time_slot = $_POST['orddd_time_slot']; // phpcs:ignore
				if ( $previous_order_h_date !== $_POST['h_deliverydate'] ) { // phpcs:ignore
					$delivery_details_updated = 'yes';
					Orddd_Lite_Common::update_order_meta( $order_id, $time_field_label, esc_attr( $time_slot ), $order );
					Orddd_Lite_Common::update_order_meta( $order_id, '_orddd_time_slot', esc_attr( $time_slot ), $order );
				} elseif ( $time_slot !== $previous_order_timeslot ) {
					$delivery_details_updated = 'yes';

					if ( 'asap' === $time_slot ) {
						Orddd_Lite_Common::update_order_meta( $order_id, $time_field_label, esc_attr( __( 'As Soon As Possible.', 'order-delivery-date' ) ), $order );
						Orddd_Lite_Common::update_order_meta( $order_id, '_orddd_time_slot', esc_attr( __( 'As Soon As Possible.', 'order-delivery-date' ) ), $order );
						$time_slot = __( 'As Soon As Possible.', 'order-delivery-date' );
					} else {
						Orddd_Lite_Common::update_order_meta( $order_id, $time_field_label, esc_attr( $time_slot ), $order );
						Orddd_Lite_Common::update_order_meta( $order_id, '_orddd_time_slot', esc_attr( $time_slot ), $order );
					}

				}
				$h_deliverydate = '';
				if ( isset( $_POST['h_deliverydate'] ) ) { //phpcs:ignore
					$h_deliverydate = $_POST['h_deliverydate']; //phpcs:ignore
				}
				$time_format   = get_option( 'orddd_lite_delivery_time_format' );
				$time_slot_arr = explode( ' - ', $time_slot );

				if ( '1' === $time_format ) {
					$from_time = date( 'H:i', strtotime( $time_slot_arr[0] ) ); //phpcs:ignore

				} else {
					$from_time = date( 'H:i', strtotime( $time_slot_arr[0] ) ); //phpcs:ignore
				}

				$delivery_date  = $h_deliverydate;
				$delivery_date .= ' ' . $from_time;
				$timestamp      = strtotime( $delivery_date );

				Orddd_Lite_Common::update_order_meta( $order_id, '_orddd_lite_timeslot_timestamp', $timestamp, $order );

				/* translators: %1s: time slot label, %2s old delivery time slot, %3s delivery time slot */
				$notes_array[]     = sprintf( __( '%1$s is updated from %2$s to %3$s', 'order-delivery-date' ), $time_field_label, $previous_order_timeslot, $time_slot );
				$timeslot_selected = 'yes';
			} elseif ( isset( $_POST['orddd_time_slot'] ) && ( 'select' === $_POST['orddd_time_slot'] || 'No time slots are available.' === $_POST['orddd_time_slot'] ) ) { // phpcs:ignore
				$timeslot_selected = 'no';
			} else {
				$timeslot_selected = 'yes';
			}
			// Update the Delivery Charges.
			Orddd_Lite_Common::update_order_meta( $order_id, '_total_delivery_charges', $orddd_fees, $order );
			$order->save();

			// Add order notes mentioning the same.
			if ( is_array( $notes_array ) && count( $notes_array ) > 0 ) {
				foreach ( $notes_array as $msg ) {
					$order->add_order_note( $msg );
				}
			}
			$delivery_details_updated = 'yes';
			if ( 'yes' === $delivery_details_updated && isset( $_POST['orddd_notify_customer'] ) && 'yes' === $_POST['orddd_notify_customer'] ) { // phpcs:ignore
				ORDDD_Lite_Email_Manager::orddd_lite_send_email_on_update( $order_id, 'admin' );
			}

			echo esc_attr( $date_selected . ',' . $timeslot_selected . ',' . $delivery_details_updated );			
		}
		die();
	}

	/**
	 * Send admin side data to JS.
	 */
	public static function orddd_lite_localize_admin_scripts() {
		global $wpdb, $orddd_date_formats, $post, $order, $woocommerce, $orddd_languages, $orddd_weekdays;
		$admin_lite_params = array();
		if ( ( ! isset( $post ) || empty( $post ) ) && ! Orddd_Lite_Common::is_hpos_enabled() ) {
			return $admin_lite_params;
		} elseif ( Orddd_Lite_Common::is_hpos_enabled() && ! ( isset( $_GET['page'] ) && 'wc-orders' === $_GET['page'] && isset( $_GET['id'] ) ) ) {
			return $admin_lite_params;
		}
		if ( 'on' === get_option( 'orddd_lite_enable_delivery_date' ) ) {
			$field_name = 'e_deliverydate';
			if ( ! Orddd_Lite_Common::is_hpos_enabled() ) {
				$orddd_post_type = $post->post_type;
				$order_id        = $post->ID;
			} else {
				$orddd_post_type = 'wc-orders';
				$order_id        = sanitize_text_field( wp_unslash( $_GET['id'] ) );
			}
			$current_date            = date( 'j-n-Y' ); // phpcs:ignore
			$data                    = Orddd_Lite_Common::get_order_meta( $order_id );
			$date_field_label        = get_option( 'orddd_lite_delivery_date_field_label' );
			$time_field_label        = get_option( 'orddd_lite_delivery_timeslot_field_label' );
			$delivery_date           = Orddd_Lite_Common::get_order_meta( $order_id, get_option( 'orddd_lite_delivery_date_field_label' ), true );
			$delivery_date_timestamp = Orddd_Lite_Common::get_order_meta( $order_id, '_orddd_lite_timestamp', true );
			$delivery_time           = Orddd_Lite_Common::get_order_meta( $order_id, get_option( 'orddd_lite_delivery_timeslot_field_label' ), true );
			$delivery_time_timestamp = Orddd_Lite_Common::get_order_meta( $order_id, '_orddd_lite_timeslot_timestamp', true );
			$delivery_date_format    = get_option( 'orddd_lite_delivery_date_format' );
			$holidays                = get_option( 'orddd_lite_holidays' );
			$holidays_str            = '';
			if ( '' !== $holidays && '{}' !== $holidays && '[]' !== $holidays && 'null' !== $holidays ) {
				$holidays_arr = json_decode( $holidays );
				if ( ! empty( $holidays_arr ) && is_array( $holidays_arr ) ) {
					foreach ( $holidays_arr as $k => $v ) {
						// Replace single quote in the holiday name with the html entities
						// @todo: Need to fix the double quotes issue in the holiday name.
						// An error comes in console when the holiday name contains double quotes in it.
						$name = str_replace( "'", '&apos;', $v->n );
						$name = str_replace( '"', '&quot;', $name );
						$name = str_replace( '/', ' ', $name );
						$name = str_replace( '-', ' ', $name );

						if ( isset( $v->r_type ) && 'on' === $v->r_type ) {
							$holiday_date_arr = explode( '-', $v->d );
							$recurring_date   = $holiday_date_arr[0] . '-' . $holiday_date_arr[1];
							$holidays_str    .= '"' . $name . ':' . $recurring_date . '",';
						} else {
							$holidays_str .= '"' . $name . ':' . $v->d . '",';
						}
					}
				}
				$holidays_str = apply_filters( 'ordd_add_to_holidays_str', $holidays_str );
				$holidays_str = substr( $holidays_str, 0, strlen( $holidays_str ) - 1 );
			}
			$first_day_of_week = '1';
			if ( '' !== get_option( 'orddd_lite_start_of_week' ) ) {
				$first_day_of_week = get_option( 'orddd_lite_start_of_week' );
			}
			if ( isset( $data['_orddd_lite_timestamp'][0] ) && '' !== $data['_orddd_lite_timestamp'][0] ) {
				$default_date           = date( 'd-m-Y', $data['_orddd_lite_timestamp'][0] ); // phpcs:ignore
				$default_h_deliverydate = date( 'j-n-Y', $data['_orddd_lite_timestamp'][0] ); // phpcs:ignore
			} elseif ( isset( $data[ $date_field_label ][0] ) && '' !== $data[ $date_field_label ][0] ) {
				$default_date           = date( 'd-m-Y', strtotime( str_replace( ',', ' ', $data[ $date_field_label ][0] ) ) ); // phpcs:ignore
				$default_h_deliverydate = date( 'j-n-Y', strtotime( str_replace( ',', ' ', $data[ $date_field_label ][0] ) ) ); // phpcs:ignore
			} else {
				$default_date           = '';
				$default_h_deliverydate = '';
			}
			// Default fees.
			$fee = Orddd_Lite_Common::get_order_meta( $order_id, '_total_delivery_charges', true );
			if ( '' !== $fee || '{}' !== $fee || '[]' !== $fee ) {
				$fee_name = 'Delivery Charges:';
			} else {
				$fee      = 0;
				$fee_name = '';
				foreach ( $order->get_items( 'fee' ) as $item_id => $item_fee ) {
					if ( '' !== $item_fee->get_total() && $item_fee->get_total() > 0 ) {
						$fee_name = ( '' !== $item_fee->get_name() ) ? $item_fee->get_name() : __( 'Delivery Charges:', 'order-delivery-date' );
						$fee     += $item_fee->get_total();
					}
				}
				$fee_name = ( ( is_array( $order->get_items( 'fee' ) ) && count( $order->get_items( 'fee' ) ) ) > 1 || '' === $fee_name ) ? 'Delivery Charges:' : $fee_name;
			}

			$disabled = '';
			if ( 'auto-draft' === get_post_status( $order_id ) ) {
				$disabled = 'disabled';
			}

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

			if ( '' === get_option( 'orddd_lite_minimumOrderDays' ) ) {
				$minimum_delivery_time_orddd_lite = 0;
			} else {
				$minimum_delivery_time_orddd_lite = get_option( 'orddd_lite_minimumOrderDays' );
			}

			$delivery_time_seconds = $minimum_delivery_time_orddd_lite * 60 * 60;
			$min_date_array        = Orddd_Lite_Common::get_min_date( $delivery_time_seconds, $holidays_str, $lockout_days_str );

			$admin_lite_params['orddd_lite_delivery_enabled']     = esc_attr( get_option( 'orddd_lite_enable_delivery_date' ) );
			$admin_lite_params['original_post_status']            = esc_attr( $disabled );
			$admin_lite_params['original_post_status']            = esc_attr( $disabled );
			$admin_lite_params['orddd_lite_default_date']         = esc_attr( $default_date );
			$admin_lite_params['orddd_lite_minimumOrderDays']     = esc_attr( $default_date );
			$admin_lite_params['orddd_lite_current_day']          = esc_attr( $current_date );
			$admin_lite_params['orddd_lite_current_date_set']     = esc_attr( $current_date );
			$admin_lite_params['orddd_lite_default_h_date']       = esc_attr( $default_h_deliverydate );
			$admin_lite_params['orddd_lite_holidays']             = esc_attr( $holidays_str );
			$admin_lite_params['orddd_lite_delivery_date_format'] = esc_attr( $delivery_date_format );
			$admin_lite_params['orddd_lite_first_day_of_week']    = esc_attr( $first_day_of_week );
			$admin_lite_params['orddd_lite_enable_time_slot']     = esc_attr( get_option( 'orddd_lite_enable_time_slot' ) );
			$admin_lite_params['orddd_lite_date_field_mandatory'] = esc_attr( get_option( 'orddd_lite_date_field_mandatory' ) );
			$admin_lite_params['orddd_lite_time_slot_mandatory']  = esc_attr( get_option( 'orddd_lite_time_slot_mandatory' ) );
			$admin_lite_params['orddd_lite_admin_url']            = esc_attr( get_admin_url() );
			$admin_lite_params['orddd_lite_order_id']             = esc_attr( $order_id );
			$admin_lite_params['orddd_lite_min_date_set']         = esc_attr( $min_date_array['min_date'] );
			$admin_lite_params['orddd_lite_field_label']          = esc_attr( $date_field_label );
			$admin_lite_params['orddd_lite_timeslot_field_label'] = esc_attr( $time_field_label );
			return $admin_lite_params;
		}
	}
}

$orddd_lite_admin_delivery = new Orddd_Lite_Admin_Delivery();

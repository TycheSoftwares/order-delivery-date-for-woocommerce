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
		add_action( 'woocommerce_saved_order_items', array( &$this, 'orddd_woocommerce_saved_order_items' ), 10, 2 );
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
		add_meta_box( 'order-delivery-date', __( 'Edit Order Delivery Date and/or Time', 'order-delivery-date' ), array( 'Orddd_Lite_Admin_Delivery', 'orddd_lite_meta_box' ), 'shop_order', 'normal', 'core' );
		if ( 'on' === get_option( 'orddd_enable_woo_subscriptions_compatibility' ) ) {
			add_meta_box( 'order-delivery-date', __( 'Edit Order Delivery Date and/or Time', 'order-delivery-date' ), array( 'Orddd_Lite_Admin_Delivery', 'orddd_lite_meta_box' ), 'shop_subscription', 'normal', 'core' );
		}
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
		if ( 'on' === get_option( 'orddd_enable_delivery_date' ) ) {
			$field_name              = 'e_deliverydate';
			$orddd_post_type         = $post->post_type;
			$order_id                = $order->ID;
			$current_date            = date( 'j-n-Y' ); // phpcs:ignore
			$data                    = get_post_meta( $order_id );
			$date_field_label        = get_option( 'orddd_lite_delivery_date_field_label' );
			$time_field_label        = get_option( 'orddd_delivery_timeslot_field_label' );
			$delivery_date           = get_post_meta( $order_id, get_option( 'orddd_lite_delivery_date_field_label' ), true );
			$delivery_date_timestamp = get_post_meta( $order_id, '_orddd_lite_timestamp', true );
			$delivery_time           = get_post_meta( $order_id, get_option( 'orddd_delivery_timeslot_field_label' ), true );
			$delivery_time_timestamp = get_post_meta( $order_id, '_orddd_lite_timeslot_timestamp', true );
			$delivery_date_format    = get_option( 'orddd_lite_delivery_date_format' );
			$holidays                = get_option( 'orddd_lite_holidays' );
			$holidays_str            = '';
			if ( '' !== $holidays && '{}' !== $holidays && '[]' !== $holidays && 'null' !== $holidays ) {
				$holidays_arr = json_decode( $holidays );
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
			$fee = get_post_meta( $order_id, '_total_delivery_charges', true );
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

			$min_date_array = Orddd_Lite_Common::get_min_date( $delivery_time_seconds, $holidays_str, $lockout_days_str );
			print ( '<table id="admin_delivery_fields" >
                <tr id="admin_delivery_date_field" >
                    <td><label class ="orddd_delivery_date_field_label">' . esc_attr( $date_field_label ) . ': </label></td>
                    <td>
                        <input type="text" id="' . esc_attr( $field_name ) . '" name="' . esc_attr( $field_name ) . '" class="' . esc_attr( $field_name ) . '" readonly/>
                    </td>
                </tr>' );
			if ( 'on' === get_option( 'orddd_lite_enable_time_slot' ) ) {
				print( '<tr id="admin_time_slot_field">
                        <td><label for="orddd_time_slot" class="">' . esc_attr( $time_field_label ) . ': </label></td>
                        <td><select name="orddd_time_slot" id="orddd_time_slot" class="orddd_admin_time_slot" disabled="disabled" placeholder="">
                                <option value="select">Select a time slot</option>
                            </select>
                        </td>
                    </tr>' );
			}
				print( "<tr id='delivery_charges'>
                    <td><label for='del_charges'>" . esc_attr( $fee_name ) . "</label></td>
                    <td><input type='number' min='0' value='" . esc_attr( $fee ) . "' step='0.001' id='del_charges' /></td></tr>" );

				print( "<tr>
                    <td colspan='2'>
					<small>" . esc_attr__( 'Any change in Delivery charges here will not change the order total. You will need to update the Item section above for delivery charges to reflect in order total.', 'order-delivery-date' ) . '</small>
					<br><small><em>' . esc_attr__( 'Note: If you are creating the order manually, you can update the delivery date & time after creating the order.', 'order-delivery-date' ) . '</em></small>
                    </td>
                    </tr>' );

				print( '<tr id="save_delivery_date_button">
                    <td><input type="button" value="' . esc_attr__( 'Update', 'order-delivery-date' ) . '" id="save_delivery_date" class="button button-primary"></td>
                    <td><input type="button" value="' . esc_attr__( 'Update & Notify Customer', 'order-delivery-date' ) . '" id="save_delivery_date_and_notify" class="button button-primary"' . esc_attr( $disabled ) . '></td>
                    <td><font id="orddd_update_notice"></font></td>
                </tr>
            </table>
            <div id="is_virtual_product"></div>' );
			print( '
				<input type="hidden" id="h_deliverydate" name="h_deliverydate" />
				<input type="hidden" id="orddd_is_admin" value="1" />
				<input type="hidden" id="original_post_status" value="' . esc_attr( $disabled ) . '" />
				<input type="hidden" id="orddd_lite_default_date" name="orddd_lite_default_date" value="' . esc_attr( $default_date ) . '" />
				<input type="hidden" id="orddd_lite_minimumOrderDays" name="orddd_lite_minimumOrderDays" value="' . esc_attr( $default_date ) . '" />
				<input type="hidden" id="orddd_lite_current_day" name="orddd_lite_current_day" value="' . esc_attr( $current_date ) . '" />
				<input type="hidden" id="orddd_lite_current_date_set" name="orddd_lite_current_date_set" value="' . esc_attr( $current_date ) . '" />
				<input type="hidden" id="orddd_lite_default_h_date" name="orddd_lite_default_h_date" value="' . esc_attr( $default_h_deliverydate ) . '" />
				<input type="hidden" id="orddd_lite_holidays" name="orddd_lite_holidays" value="' . esc_attr( $holidays_str ) . '" />
				<input type="hidden" id="orddd_lite_delivery_date_format" name="orddd_lite_delivery_date_format" value="' . esc_attr( $delivery_date_format ) . '" />
				<input type="hidden" id="orddd_lite_first_day_of_week" name="orddd_lite_first_day_of_week" value="' . esc_attr( $first_day_of_week ) . '" />
				<input type="hidden" name="orddd_lite_enable_time_slot" id="orddd_lite_enable_time_slot" value="' . esc_attr( get_option( 'orddd_lite_enable_time_slot' ) ) . '">
				<input type="hidden" name="orddd_lite_date_field_mandatory" id="orddd_lite_date_field_mandatory" value="' . esc_attr( get_option( 'orddd_lite_date_field_mandatory' ) ) . '">
				<input type="hidden" name="orddd_lite_time_slot_mandatory" id="orddd_lite_time_slot_mandatory" value="' . esc_attr( get_option( 'orddd_lite_time_slot_mandatory' ) ) . '">
				<input type="hidden" name="orddd_lite_admin_url" id="orddd_lite_admin_url" value="' . esc_attr( get_admin_url() ) . '">
				<input type="hidden" name="orddd_lite_order_id" id="orddd_lite_order_id" value="' . esc_attr( $order_id ) . '">
				<input type="hidden" name="orddd_lite_min_date_set" id="orddd_lite_min_date_set" value="' . esc_attr( $min_date_array )['min_date'] . '">
				<input type="hidden" name="orddd_lite_field_label" id="orddd_lite_field_label" value="' . esc_attr( $date_field_label ) . '">
			' );

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
			$time_field_label = get_option( 'orddd_delivery_timeslot_field_label' );

			$date_selected            = 'no';
			$timeslot_selected        = 'no';
			$delivery_details_updated = 'no';
			if ( ( isset( $_POST['e_deliverydate'] ) && '' !== $_POST['e_deliverydate'] ) ) { // phpcs:ignore
				$delivery_date = '';
				$time_slot     = '';

				$previous_order_date          = '';
				$previous_order_weekday_check = '';
				$previous_order_h_date        = '';
				$previous_order_timeslot      = '';
				$previous_charges_label       = '';
				$previous_order_date_check    = '';
				$data                         = get_post_meta( $order_id );

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

				if ( isset( $_POST['e_deliverydate'] ) && '' !== $_POST['e_deliverydate'] && $_POST['e_deliverydate'] !== $previous_order_date ) { // phpcs:ignore
					/* translators: %1s: date label, %2s old delivery date, %3s new delivery date */
					$notes_array[] = sprintf( __( '%1$s is updated from %2$s to %3$s', 'order-delivery-date' ), $date_field_label, $previous_order_date, $_POST['e_deliverydate'] ); // phpcs:ignore
					update_post_meta( $order_id, $date_field_label, $_POST['e_deliverydate'] ); // phpcs:ignore
					$delivery_details_updated = 'yes';
				}

				if ( isset( $_POST['h_deliverydate'] ) && '' !== $_POST['h_deliverydate'] ) { // phpcs:ignore
					$delivery_date = $_POST['h_deliverydate']; // phpcs:ignore
					$date_format   = 'dd-mm-y';
					if ( $previous_order_h_date !== $_POST['h_deliverydate'] || // phpcs:ignore
						( $previous_order_h_date == $_POST['h_deliverydate'] ) ) { // phpcs:ignore
						$timestamp = Orddd_Lite_Common::orddd_lite_get_timestamp( $delivery_date, $date_format );
						update_post_meta( $order_id, '_orddd_lite_timestamp', $timestamp );
					}
				}
				$date_selected = 'yes';
			}

			if ( isset( $_POST['orddd_time_slot'] ) && '' !== $_POST['orddd_time_slot'] && 'Select a time slot' !== $_POST['orddd_time_slot'] && 'No time slots are available.' !== $_POST['orddd_time_slot'] ) { // phpcs:ignore
				$time_slot = $_POST['orddd_time_slot']; // phpcs:ignore
				if ( $previous_order_h_date !== $_POST['h_deliverydate'] ) { // phpcs:ignore
					$delivery_details_updated = 'yes';
					update_post_meta( $order_id, $time_field_label, esc_attr( $time_slot ) );
					update_post_meta( $order_id, '_orddd_time_slot', esc_attr( $time_slot ) );
				} elseif ( $time_slot !== $previous_order_timeslot ) {
					$delivery_details_updated = 'yes';

					if ( 'asap' === $time_slot ) {
						update_post_meta( $order_id, $time_field_label, esc_attr( __( 'As Soon As Possible.', 'order-delivery-date' ) ) );
						update_post_meta( $order_id, '_orddd_time_slot', esc_attr( __( 'As Soon As Possible.', 'order-delivery-date' ) ) );
						$time_slot = __( 'As Soon As Possible.', 'order-delivery-date' );
					} else {
						update_post_meta( $order_id, $time_field_label, esc_attr( $time_slot ) );
						update_post_meta( $order_id, '_orddd_time_slot', esc_attr( $time_slot ) );
					}
				}
				/* translators: %1s: time slot label, %2s old delivery time slot, %3s delivery time slot */
				$notes_array[]     = sprintf( __( '%1$s is updated from %2$s to %3$s', 'order-delivery-date' ), $time_field_label, $previous_order_timeslot, $time_slot );
				$timeslot_selected = 'yes';
			} elseif ( isset( $_POST['orddd_time_slot'] ) && ( 'Select a time slot' === $_POST['orddd_time_slot'] || 'No time slots are available.' === $_POST['orddd_time_slot'] ) ) { // phpcs:ignore
				$timeslot_selected = 'no';
			} else {
				$timeslot_selected = 'yes';
			}

			// Update the Delivery Charges.
			update_post_meta( $order_id, '_total_delivery_charges', $orddd_fees );

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
}

$orddd_lite_admin_delivery = new Orddd_Lite_Admin_Delivery();

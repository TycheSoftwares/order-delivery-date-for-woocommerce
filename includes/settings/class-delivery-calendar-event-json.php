<?php //phpcs:disable
/**
 * Order Delivery Date Pro for WooCommerce
 *
 * Handles the JSON output for Delivery events to be displayed in Delivery Calendar.
 *
 * @author   Tyche Softwares
 * @package  Order-Delivery-Date-Pro-for-WooCommerce/Delivery-Calendar
 * @since    2.8.7
 * @since    9.28.3 renamed class file from adminend-events-jsons.php to class-delivery-calendar-event-json.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class for Delivery Calendar.
 *
 * @since 1.0
 */
class Delivery_Calendar_lite_Event_JSON {

	/**
	 * Default Constructor
	 *
	 * @since 8.1
	 */
	public function __construct() {
		add_action( 'init', array( &$this, 'handle_event_json' ) );
		add_action( 'admin_init', array( &$this, 'handle_event_json' ) );
	}

	/**
	 * This function is used for handling deliveries in the Calendar View.
	 *
	 * @since 1.0
	 */
	public static function handle_event_json() {

		$action    = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';
		$vendor_id = isset( $_GET['vendor_id'] ) ? sanitize_text_field( wp_unslash( $_GET['vendor_id'] ) ) : '';
		$is_vendor = '' !== $vendor_id && 0 !== $vendor_id;

		if ( 'orddd-delivery-calendar-event-json' === $action ) {
			global $wpdb;
			if ( isset( $_GET['orderStatus'] ) && ( sanitize_text_field( wp_unslash( $_GET['orderStatus'] ) ) != '' ) ) {
				$order_status1 = sanitize_text_field( wp_unslash( $_GET['orderStatus'] ) );
				$order_status  = explode( ',', $order_status1 );
			} else {
				$all_order_status = wc_get_order_statuses();
				$order_status     = array();
				foreach ( $all_order_status as $order_status_key => $order_status_name ) {
					if ( 'wc-pending' === $order_status_key || 'wc-processing' === $order_status_key || 'wc-on-hold' === $order_status_key || 'wc-completed' === $order_status_key ) {
						$order_status[] = $order_status_key;
					} elseif ( 'wc-cancelled' !== $order_status_key && 'wc-refunded' !== $order_status_key && 'wc-failed' !== $order_status_key ) {
						$order_status[] = $order_status_key;
					}
				}
			}

			$order_shipping = array();
			if ( isset( $_GET['orderShipping'] ) && ( sanitize_text_field( wp_unslash( $_GET['orderShipping'] ) ) != '' ) ) {
				$order_shipping1 = sanitize_text_field( wp_unslash( $_GET['orderShipping'] ) );
				$order_shipping  = explode( ',', $order_shipping1 );
			}

			$event_start           = '';
			$event_start_timestamp = '';
			$event_end             = '';
			$event_end_timestamp   = '';

			if ( isset( $_GET['start'] ) ) {
				$event_start           = sanitize_text_field( wp_unslash( $_GET['start'] ) );
				$event_start_timestamp = strtotime( $event_start );
			}

			if ( isset( $_GET['end'] ) ) {
				$event_end           = sanitize_text_field( wp_unslash( $_GET['end'] ) );
				$event_end_timestamp = strtotime( $event_end );
			}

			$date_str            = Orddd_Lite_Common::str_to_date_format();
			$delivery_date_label = esc_sql( get_option( 'orddd_lite_delivery_date_field_label' ) );

			
			$order_timestamp_key     = '_orddd_lite_timestamp';
			$orddd_delivery_date_key = $delivery_date_label;
			
			$order_table      = 'posts';
			$order_meta_table = 'postmeta';
			$post_type        = 'post_type';
			$post_status      = 'post_status';
			$post_id          = 'post_id';
			$id               = 'ID';

			if ( Orddd_Lite_Common::is_hpos_enabled() ) {
				$order_table      = 'wc_orders';
				$order_meta_table = 'wc_orders_meta';
				$post_type        = 'type';
				$post_status      = 'status';
				$post_id          = 'order_id';
				$id               = 'id';
			}

			$orddd_query = "SELECT DISTINCT wp.{$id}, {$post_status}, wpm1.meta_value AS orddd_timestamp , wpm2.meta_value AS delivery_date , wpm3.meta_value AS time_slot
			FROM `" . $wpdb->prefix . "$order_table` wp
			INNER JOIN `" . $wpdb->prefix . "$order_meta_table` wpm1 ON ( wp.{$id} = wpm1.{$post_id} AND wpm1.meta_key ='" . $order_timestamp_key . "' )
			LEFT JOIN `" . $wpdb->prefix . "$order_meta_table` wpm2 ON ( wp.{$id} = wpm2.{$post_id} AND ( wpm2.meta_key ='" . $orddd_delivery_date_key . "' ) )
			LEFT JOIN `" . $wpdb->prefix . "$order_meta_table` wpm3 ON ( wp.{$id} = wpm3.{$post_id} AND wpm3.meta_key ='_orddd_time_slot' ) ";

			$orddd_query = apply_filters( 'orddd_lite_calendar_join_filter', $orddd_query );

			$orddd_query .= "WHERE $post_type = 'shop_order' AND $post_status IN ( '" . implode( "','", $order_status ) . "')
			AND 
			(
			( wpm1.meta_key = '" . $order_timestamp_key . "' AND wpm1.meta_value >= '" . $event_start_timestamp . "' AND wpm1.meta_value <= '" . $event_end_timestamp . "' ) OR 
			( wpm2.meta_key = '" . $delivery_date_label . "' AND STR_TO_DATE( wpm2.meta_value, '" . $date_str . "' ) >= '" . $event_start . "' AND STR_TO_DATE( wpm2.meta_value, '" . $date_str . "' ) <= '" . $event_end . "' )
			OR ( wpm2.meta_key = '" . $delivery_date_label . "' AND STR_TO_DATE( wpm2.meta_value, '" . $date_str . "' ) >= '" . $event_start . "' AND STR_TO_DATE( wpm2.meta_value, '" . $date_str . "' ) <= '" . $event_end . "' ) 
			OR ( wpm2.meta_key = '" . $orddd_delivery_date_key . "' AND STR_TO_DATE( wpm2.meta_value, '" . $date_str . "' ) >= '" . $event_start . "' AND STR_TO_DATE( wpm2.meta_value, '" . $date_str . "' ) <= '" . $event_end . "' )
			)";

			$orddd_query = apply_filters( 'orddd_lite_calendar_where_filter', $orddd_query );
			$results     = $wpdb->get_results( $orddd_query );// nosemgrep:audit.php.wp.security.sqli.input-in-sinks
			$data        = array();

			$has_order_number = false;
			if ( has_filter( 'woocommerce_order_number' ) ) {
				$has_order_number = true;
			}
			$version_greater_than_three = version_compare( get_option( 'woocommerce_version' ), '3.0.0', '>=' );

			foreach ( $results as $key => $value ) {
				$delivery_date_timestamp = '';

				$order_id     = isset( $value->ID ) ? $value->ID : ( isset( $value->id ) ? $value->id  : '' ) ;
				$order_number = $order_id;
				$order        = wc_get_order( $order_id );
				if ( $has_order_number ) {
					$order        = wc_get_order( $order_id );
					if ( ! $order ) {
						continue;
					}
					$order_number = $order->get_order_number();
				}
				$delivery_date_formatted = isset( $value->delivery_date ) ? $value->delivery_date : orddd_common::orddd_get_order_delivery_date( $order_id );
				$delivery_date_timestamp = $value->orddd_timestamp;
				$time_slot               = $value->time_slot;
				$time_slider_enabled     = false;

				if ( ! empty( $order_shipping ) ) {
					if ( ! $order ) {
						$order = wc_get_order( $order_id );
					}
					$shipping_method_to_check = '';
					foreach ( $order->get_items( 'shipping' ) as $item_id => $item ) {
						$shipping_method_id          = $item->get_method_id();
						$shipping_method_instance_id = $item->get_instance_id();
						$shipping_method_to_check    = $shipping_method_id . ':' . $shipping_method_instance_id;
					}
					if ( ! in_array( $shipping_method_to_check, $order_shipping, true ) ) {
						continue;
					}
				}

				$time_settings = '';
				if ( isset( $delivery_date_timestamp ) && $delivery_date_timestamp != '' ) {
					$time_settings = date( 'H:i', $delivery_date_timestamp );
				}

				$end_date = '';
				if ( has_filter( 'orddd_lite_to_add_end_date' ) ) {
					$end_date = apply_filters( 'orddd_lite_to_add_end_date', $order_id );
				}

				$post_status = $version_greater_than_three ? ( isset( $value->post_status ) ? $value->post_status : ( isset( $value->status ) ? $value->status : '' ) ) : $order->post_status;

				// It will check if the woocommerce product addons plugin is active or not.
				$is_addon_plugin_active = is_plugin_active( 'woocommerce-product-addons/woocommerce-product-addons.php' );

				if ( in_array( $post_status, $order_status ) ) {
					if ( ( isset( $_GET['eventType'] ) && ( sanitize_text_field( wp_unslash( $_GET['eventType'] ) ) == '' || sanitize_text_field( wp_unslash( $_GET['eventType'] ) ) == 'product' ) ) ||
						! isset( $_GET['eventType'] ) ) {
						if ( ! $order ) {
							$order = new WC_Order( $order_id );
						}
						$order_items = $order->get_items();
						foreach ( $order_items as $item ) {
							$item_data    = $item->get_data();
							$product_name = html_entity_decode( $item['name'], ENT_COMPAT, 'UTF-8' );
							if ( $is_addon_plugin_active || ( isset( $item_data['variation_id'] ) && $item_data['variation_id'] != 0 ) ) {
								if ( isset( $item_data['variation_id'] ) && $item_data['variation_id'] ) {
									$_product       = new WC_Product_Variation( $item_data['variation_id'] );
									$product_name   = $_product->get_title();
									$variation_data = $_product->get_variation_attributes(); // variation data in array
								} else {
									$_product = wc_get_product( $data['product_id'] );
								}

								$meta_data = $item_data['meta_data'];

								if ( $is_addon_plugin_active || ( is_array( $variation_data ) && count( $variation_data ) > 0 ) ) {
									$product_name         .= ' - ';
									$variable_product_data = '';
									foreach ( $meta_data as $mkey => $mvalue ) {
										$mdata          = $mvalue->get_data();
										$taxonomy       = $mdata['key'];
										$attribute_name = wc_attribute_label( $taxonomy, $_product );
										if ( substr( $attribute_name, 0, 1 ) === '_' ) {
											continue;
										}
										if ( taxonomy_exists( $taxonomy ) ) {
											$attribute_value     = '';
											$get_attribute_value = get_term_by( 'slug', $mdata['value'], $taxonomy );
											if ( isset ( $get_attribute_value->name ) ) {
												$attribute_value = $get_attribute_value->name;
											}
										} else {
											$attribute_value = $mdata['value']; // For custom product attributes.
										}
										$product_name .= $attribute_name . ': ' . $attribute_value . ', ';
									}
								}
							}
							$product_name = rtrim( $product_name, ', ' );
							if ( ( $delivery_date_timestamp != '' ||
								$delivery_date_formatted != '' ) &&
								$delivery_date_timestamp >= $event_start_timestamp &&
								$delivery_date_timestamp <= $event_end_timestamp ) {
								if ( isset( $time_slot ) &&
									( $time_slot != 'select' &&
										$time_slot != '' &&
										$time_slot != 'null' &&
										false == strpos( $time_slot, 'Possible' )
									 )
								  ) {

									$time_arr            = explode( '-', $time_slot );
									$from_time           = $time_arr[0];
									$delivery_date       = date( 'Y-m-d', $delivery_date_timestamp ); 
									$delivery_date      .= ' ' . $from_time;
									$post_from_timestamp = strtotime( $delivery_date );
									$from_date           = date( 'Y-m-d H:i:s', $post_from_timestamp );

									if ( isset( $from_time ) && $from_time != '' ) {
										if ( isset( $time_arr[1] ) && $time_arr[1] != '' ) {
											$to_time           = $time_arr[1];
											$delivery_date     = date( 'Y-m-d', $delivery_date_timestamp );
											$delivery_date    .= ' ' . $to_time;
											$post_to_timestamp = strtotime( $delivery_date );
											if ( '' != $end_date ) {
												$to_date = $end_date;
											} else {
												$to_date = date( 'Y-m-d H:i:s', $post_to_timestamp );
											}

											// Modify delivery calendar data.
											array_push(
												$data,
												apply_filters(
													'orddd_lite_delivery_modify_calendar_data',
													array(
														'id'       => $order_id,
														'title'    => $product_name . ' x' . $item['quantity'],
														'product_name' => $product_name,
														'start'    => $from_date,
														'end'      => $to_date,
														'timeslot' => $time_slot,
														'eventtype' => 'product',
														'value'    => $value,
														'delivery_date' => '',
														'event_product_id' => $item['product_id'],
														'event_product_qty' => $item['quantity'],
													),
													$order_id
												)
											);
										} else {
											$to_time           = date( 'H:i', strtotime( '+30 minutes', $post_from_timestamp ) );
											$delivery_date     = date( 'Y-m-d', $delivery_date_timestamp );
											$delivery_date    .= ' ' . $to_time;
											$post_to_timestamp = strtotime( $delivery_date );
											if ( '' != $end_date ) {
												$to_date = $end_date;
											} else {
												$to_date = date( 'Y-m-d H:i:s', $post_to_timestamp );
											}

											// Modify delivery calendar data.
											array_push(
												$data,
												apply_filters(
													'orddd_lite_delivery_modify_calendar_data',
													array(
														'id'       => $order_id,
														'title'    => $product_name . ' x' . $item['quantity'],
														'product_name' => $product_name,
														'start'    => $from_date,
														'end'      => $to_date,
														'timeslot' => $time_slot,
														'eventtype' => 'product',
														'value'    => $value,
														'delivery_date' => '',
														'event_product_id' => $item['product_id'],
														'event_product_qty' => $item['quantity'],
													),
													$order_id
												)
											);
										}
									}
								} elseif ( $time_settings != '00:01' && $time_settings != '' && $time_settings != '00:00' ) {
									$time_format   = $time_slider_enabled ? 'Y-m-d H:i:s' : 'Y-m-d';
									$delivery_date = date( $time_format, $delivery_date_timestamp ); 
									$from_date     = date( $time_format, $delivery_date_timestamp );
									if ( '' != $end_date ) {
										$to_date = $end_date;
									} else {
										$to_date = date( $time_format, strtotime( '+30 minutes', $delivery_date_timestamp ) );
									}

									// Modify delivery calendar data.
									array_push(
										$data,
										apply_filters(
											'orddd_lite_delivery_modify_calendar_data',
											array(
												'id'    => $order_id,
												'title' => $product_name . ' x' . $item['quantity'],
												'product_name' => $product_name,
												'start' => $from_date,
												'end'   => $to_date,
												'eventtype' => 'product',
												'value' => $value,
												'delivery_date' => '',
												'event_product_id' => $item['product_id'],
												'event_product_qty' => $item['quantity'],
											),
											$order_id
										)
									);
								} else {
									if ( '' != $end_date ) {
										$delivery_date_formatted = $end_date;
									} else {
										$delivery_date_formatted = date( 'Y-m-d', $delivery_date_timestamp );
									}

									// Modify delivery calendar data.
									array_push(
										$data,
										apply_filters(
											'orddd_lite_delivery_modify_calendar_data',
											array(
												'id'    => $order_id,
												'title' => $product_name . ' x' . $item['quantity'],
												'product_name' => $product_name,
												'start' => $delivery_date_formatted,
												'end'   => $delivery_date_formatted,
												'eventtype' => 'product',
												'value' => $value,
												'delivery_date' => '',
												'event_product_id' => $item['product_id'],
												'event_product_qty' => $item['quantity'],
											),
											$order_id
										)
									);
								}
							}
						}
					} elseif ( isset( $_GET['eventType'] ) && $_GET['eventType'] == 'order' ) {

						if ( ( $delivery_date_timestamp != '' ||
							$delivery_date_formatted != '' ) &&
							$delivery_date_timestamp >= $event_start_timestamp &&
							$delivery_date_timestamp <= $event_end_timestamp ) {
							if ( isset( $time_slot ) &&
								( $time_slot != 'select' &&
									$time_slot != '' &&
									$time_slot != 'null' &&
									false == strpos( $time_slot, 'Possible' )
								 )
							  ) {
								$time_arr            = explode( '-', $time_slot );
								$from_time           = $time_arr[0];
								$delivery_date       = date( 'Y-m-d', $delivery_date_timestamp ); 
								$delivery_date      .= ' ' . $from_time;
								$post_from_timestamp = strtotime( $delivery_date );
								$from_date           = date( 'Y-m-d H:i:s', $post_from_timestamp );
								if ( isset( $from_time ) && $from_time != '' ) {
									if ( isset( $time_arr[1] ) && $time_arr[1] != '' ) {
										$to_time           = $time_arr[1];
										$delivery_date     = date( 'Y-m-d', $delivery_date_timestamp );
										$delivery_date    .= ' ' . $to_time;
										$post_to_timestamp = strtotime( $delivery_date );
										if ( '' != $end_date ) {
											$to_date = $end_date;
										} else {
											$to_date = date( 'Y-m-d H:i:s', $post_to_timestamp );
										}
										// Modify delivery calendar data.
										array_push(
											$data,
											apply_filters(
												'orddd_lite_delivery_modify_calendar_data',
												array(
													'id'  => $order_id,
													'title' => 'Order Number: ' . $order_number,
													'start' => $from_date,
													'end' => $to_date,
													'timeslot' => $time_slot,
													'eventtype' => 'order',
													'value' => $value,
													'delivery_date' => '',
													'event_product_id' => '',
													'event_product_qty' => '',
												),
												$order_id
											)
										);
									} else {
										$to_time           = date( 'H:i', strtotime( '+30 minutes', $post_from_timestamp ) );
										$delivery_date     = date( 'Y-m-d', $delivery_date_timestamp );
										$delivery_date    .= ' ' . $to_time;
										$post_to_timestamp = strtotime( $delivery_date );
										if ( '' != $end_date ) {
											$to_date = $end_date;
										} else {
											$to_date = date( 'Y-m-d H:i:s', $post_to_timestamp );
										}

										// Modify delivery calendar data.
										array_push(
											$data,
											apply_filters(
												'orddd_lite_delivery_modify_calendar_data',
												array(
													'id'  => $order_id,
													'title' => 'Order Number: ' . $order_number,
													'start' => $from_date,
													'end' => $to_date,
													'timeslot' => $time_slot,
													'eventtype' => 'order',
													'value' => $value,
													'delivery_date' => '',
													'event_product_id' => '',
													'event_product_qty' => '',
												),
												$order_id
											)
										);
									}
								}
							} elseif ( $time_settings != '00:01' && $time_settings != '' && $time_settings != '00:00' ) {
								$time_format   = $time_slider_enabled ? 'Y-m-d H:i:s' : 'Y-m-d';
								$delivery_date = date( $time_format, $delivery_date_timestamp );
								$from_date     = date( $time_format, $delivery_date_timestamp );
								if ( '' != $end_date ) {
									$to_date = $end_date;
								} else {
									$to_date = date( $time_format, strtotime( '+30 minutes', $delivery_date_timestamp ) );
								}
								// Modify delivery calendar data.
								array_push(
									$data,
									apply_filters(
										'orddd_lite_delivery_modify_calendar_data',
										array(
											'id'        => $order_id,
											'title'     => 'Order Number: ' . $order_number,
											'start'     => $from_date,
											'end'       => $to_date,
											'eventtype' => 'order',
											'value'     => $value,
											'delivery_date' => '',
											'event_product_id' => '',
											'event_product_qty' => '',
										),
										$order_id
									)
								);
							} else {
								if ( '' != $end_date ) {
									$delivery_date_formatted = $end_date;
								} else {
									$delivery_date_formatted = date( 'Y-m-d', $delivery_date_timestamp );
								}
								// Modify delivery calendar data.
								array_push(
									$data,
									apply_filters(
										'orddd_lite_delivery_modify_calendar_data',
										array(
											'id'        => $order_id,
											'title'     => 'Order Number: ' . $order_number,
											'start'     => $delivery_date_formatted,
											'end'       => $delivery_date_formatted,
											'eventtype' => 'order',
											'value'     => $value,
											'delivery_date' => '',
											'event_product_id' => '',
											'event_product_qty' => '',
										),
										$order_id
									)
								);
							}
						}
					}
				}
			}

			$orddd_query = 'SELECT ID FROM `' . $wpdb->prefix . "posts` WHERE post_type = 'shop_order' AND post_status NOT IN ('wc-cancelled', 'wc-refunded', 'trash', 'wc-failed') AND ID IN ( SELECT post_id FROM `" . $wpdb->prefix . "postmeta` WHERE meta_key LIKE '%_orddd_shipping_multiple_addresss_timestamp_%' AND meta_value >= '" . $event_start_timestamp . "' AND meta_value <= '" . $event_end_timestamp . "' ) ";
			$results     = $wpdb->get_results( $orddd_query );// nosemgrep:audit.php.wp.security.sqli.input-in-sinks
			if ( is_array( $results ) && count( $results ) > 0 ) {
				foreach ( $results as $key => $value ) {
					$order       = wc_get_order( $value->ID );
					$order_items = $order->get_items();
					$post_status = $version_greater_than_three ? ( isset( $value->post_status ) ? $value->post_status : ( isset( $value->status ) ? $value->status : '' ) ) : $order->post_status;

					if ( in_array( $post_status, $order_status ) ) {
						$shipping_packages = $order->get_meta( '_shipping_packages', true );
						$query             = 'SELECT meta_key, meta_value FROM `' . $wpdb->prefix . "postmeta` WHERE post_id='" . $value->ID . "' AND meta_key LIKE '%_orddd_shipping_multiple_addresss_%'";
						$results_array     = $wpdb->get_results( $query );// nosemgrep:audit.php.wp.security.sqli.input-in-sinks
						$delivery_dates    = array();
						foreach ( $results_array as $r_key => $r_value ) {
							$delivery_dates[ $r_value->meta_key ] = $r_value->meta_value;
						}
						foreach ( $delivery_dates as $d_key => $d_value ) {
							if ( preg_match( '/_orddd_shipping_multiple_addresss_e_deliverydate/', $d_key ) ) {
								$date_to_display = $d_value;
								$key_explode     = explode( '_', $d_key );
								$timestamp_key   = '_orddd_shipping_multiple_addresss_timestamp_' . $key_explode[7] . '_' . $key_explode[8] . '_' . $key_explode[9];
								$time_slot_key   = '_orddd_shipping_multiple_addresss_time_slot_' . $key_explode[7] . '_' . $key_explode[8] . '_' . $key_explode[9];
								if ( isset( $delivery_dates[ $timestamp_key ] ) ) {
									$delivery_date_timestamp = $delivery_dates[ $timestamp_key ];
								} else {
									$delivery_date_timestamp = '';
								}

								if ( isset( $delivery_dates[ $time_slot_key ] ) ) {
									$time_slot = $delivery_dates[ $time_slot_key ];
								} else {
									$time_slot = '';
								}
								if ( isset( $delivery_date_timestamp ) && $delivery_date_timestamp != '' ) {
									$time_settings_arr = explode( ' ', $d_value );
									array_pop( $time_settings_arr );
									$time_settings = date( 'H:i', strtotime( end( $time_settings_arr ) ) );
								} else {
									$time_settings = '';
								}

								if ( ( isset( $_GET['eventType'] ) && ( $_GET['eventType'] == '' || $_GET['eventType'] == 'product' ) ) || ! isset( $_GET['eventType'] ) ) {
									foreach ( $order_items as $item_key => $item ) {
										if ( $item['product_id'] == $key_explode[8] ) {
											$product_name = html_entity_decode( $item['name'], ENT_COMPAT, 'UTF-8' );
											if ( isset( $time_slot ) && $time_slot != 'select' && $time_slot != '' && $delivery_date_timestamp != '' && false == strpos( $time_slot, 'Possible' ) ) {
												$time_arr            = explode( '-', $time_slot );
												$from_time           = $time_arr[0];
												$delivery_date       = date( 'Y-m-d', $delivery_date_timestamp );
												$delivery_date      .= ' ' . $from_time;
												$post_from_timestamp = strtotime( $delivery_date );
												$from_date           = date( 'Y-m-d H:i:s', $post_from_timestamp );
												if ( isset( $from_time ) && $from_time != '' ) {
													if ( isset( $time_arr[1] ) && $time_arr[1] != '' ) {
														$to_time               = $time_arr[1];
														$delivery_date         = date( 'Y-m-d', $delivery_date_timestamp );
														$delivery_date_to_pass = $delivery_date;
														$delivery_date        .= ' ' . $to_time;
														$post_to_timestamp     = strtotime( $delivery_date );
														if ( '' != $end_date ) {
															$to_date = $end_date;
														} else {
															$to_date = date( 'Y-m-d H:i:s', $post_to_timestamp );
														}
														// Modify delivery calendar data.
														array_push(
															$data,
															apply_filters(
																'orddd_lite_delivery_modify_calendar_data',
																array(
																	'id'       => $value->ID,
																	'title'    => $product_name . ' x' . $item['quantity'],
																	'product_name' => $product_name,
																	'start'    => $from_date,
																	'end'      => $to_date,
																	'timeslot' => $time_slot,
																	'eventtype' => 'product',
																	'value'    => $value,
																	'delivery_date' => $date_to_display,
																	'time_slot' => $time_slot,
																	'event_product_id' => $item['product_id'],
																	'event_product_qty' => $item['quantity'],
																),
																$order_id
															)
														);
													} else {
														$to_time               = date( 'H:i', strtotime( '+30 minutes', $post_from_timestamp ) );
														$delivery_date         = date( 'Y-m-d', $delivery_date_timestamp );
														$delivery_date_to_pass = $delivery_date;
														$delivery_date        .= ' ' . $to_time;
														$post_to_timestamp     = strtotime( $delivery_date );
														if ( '' != $end_date ) {
															$to_date = $end_date;
														} else {
															$to_date = date( 'Y-m-d H:i:s', $post_to_timestamp );
														}
														// Modify delivery calendar data.
														array_push(
															$data,
															apply_filters(
																'orddd_lite_delivery_modify_calendar_data',
																array(
																	'id'       => $value->ID,
																	'title'    => $product_name . ' x' . $item['quantity'],
																	'product_name' => $product_name,
																	'start'    => $from_date,
																	'end'      => $to_date,
																	'timeslot' => $time_slot,
																	'eventtype' => 'product',
																	'value'    => $value,
																	'delivery_date' => $date_to_display,
																	'time_slot' => $time_slot,
																	'event_product_id' => $item['product_id'],
																	'event_product_qty' => $item['quantity'],
																),
																$order_id
															)
														);
													}
												}
											} elseif ( $time_settings != '00:01' && $time_settings != '' && $time_settings != '00:00' && $delivery_date_timestamp != '' ) {
												$delivery_date = date( 'Y-m-d', $delivery_date_timestamp );
												$from_date     = date( 'Y-m-d H:i:s', $delivery_date_timestamp );
												if ( '' != $end_date ) {
													$to_date = $end_date;
												} else {
													$to_date = date( 'Y-m-d H:i:s', strtotime( '+30 minutes', $delivery_date_timestamp ) );
												}
												// Modify delivery calendar data.
												array_push(
													$data,
													apply_filters(
														'orddd_lite_delivery_modify_calendar_data',
														array(
															'id'    => $value->ID,
															'title' => $product_name . ' x' . $item['quantity'],
															'product_name' => $product_name,
															'start' => $from_date,
															'end'   => $to_date,
															'eventtype' => 'product',
															'value' => $value,
															'delivery_date' => $date_to_display,
															'time_slot' => $time_slot,
															'event_product_id' => $item['product_id'],
															'event_product_qty' => $item['quantity'],
														),
														$order_id
													)
												);
											} elseif ( $delivery_date_timestamp != '' ) {
												if ( '' != $end_date ) {
													$delivery_date_formatted = $end_date;
												} else {
													$delivery_date_formatted = date( 'Y-m-d', $delivery_date_timestamp );
												}
												// Modify delivery calendar data.
												array_push(
													$data,
													apply_filters(
														'orddd_lite_delivery_modify_calendar_data',
														array(
															'id'    => $value->ID,
															'title' => $product_name . ' x' . $item['quantity'],
															'product_name' => $product_name,
															'start' => $delivery_date_formatted,
															'end'   => $delivery_date_formatted,
															'eventtype' => 'product',
															'value' => $value,
															'delivery_date' => $date_to_display,
															'time_slot' => $time_slot,
															'event_product_id' => $item['product_id'],
															'event_product_qty' => $item['quantity'],
														),
														$order_id
													)
												);
											}
										}
									}
								} elseif ( isset( $_GET['eventType'] ) && $_GET['eventType'] == 'order' ) {

									if ( $delivery_date_timestamp != '' && $delivery_date_formatted != '' && $delivery_date_timestamp >= $event_start_timestamp && $delivery_date_timestamp <= $event_end_timestamp ) {
										if ( isset( $time_slot ) && ( $time_slot != 'select' && $time_slot != '' && $time_slot != 'null' && false == strpos( $time_slot, 'Possible' ) ) ) {
											$time_arr            = explode( '-', $time_slot );
											$from_time           = $time_arr[0];
											$delivery_date       = date( 'Y-m-d', $delivery_date_timestamp );
											$delivery_date      .= ' ' . $from_time;
											$post_from_timestamp = strtotime( $delivery_date );
											$from_date           = date( 'Y-m-d H:i:s', $post_from_timestamp );
											if ( isset( $from_time ) && $from_time != '' ) {
												if ( isset( $time_arr[1] ) && $time_arr[1] != '' ) {
													$to_time           = $time_arr[1];
													$delivery_date     = date( 'Y-m-d', $delivery_date_timestamp );
													$delivery_date    .= ' ' . $to_time;
													$post_to_timestamp = strtotime( $delivery_date );
													$to_date           = date( 'Y-m-d H:i:s', $post_to_timestamp );
													// Modify delivery calendar data.
													array_push(
														$data,
														apply_filters(
															'orddd_lite_delivery_modify_calendar_data',
															array(
																'id'       => $value->ID,
																'title'    => 'Order Number: ' . $order_number,
																'start'    => $from_date,
																'end'      => $to_date,
																'timeslot' => $time_slot,
																'eventtype' => 'order',
																'value'    => $value,
																'delivery_date' => $date_to_display,
																'time_slot' => $time_slot,
																'event_product_id' => $key_explode[8],
																'event_product_qty' => '',
															),
															$order_id
														)
													);
												} else {
													$to_time           = date( 'H:i', strtotime( '+30 minutes', $post_from_timestamp ) );
													$delivery_date     = date( 'Y-m-d', $delivery_date_timestamp );
													$delivery_date    .= ' ' . $to_time;
													$post_to_timestamp = strtotime( $delivery_date );
													$to_date           = date( 'Y-m-d H:i:s', $post_to_timestamp );
													// Modify delivery calendar data.
													array_push(
														$data,
														apply_filters(
															'orddd_lite_delivery_modify_calendar_data',
															array(
																'id'       => $value->ID,
																'title'    => 'Order Number: ' . $order_number,
																'start'    => $from_date,
																'end'      => $to_date,
																'timeslot' => $time_slot,
																'eventtype' => 'order',
																'value'    => $value,
																'delivery_date' => $date_to_display,
																'time_slot' => $time_slot,
																'event_product_id' => $key_explode[8],
																'event_product_qty' => '',
															),
															$order_id
														)
													);
												}
											}
										} elseif ( $time_settings != '00:01' && $time_settings != '' && $time_settings != '00:00' ) {
											$delivery_date = date( 'Y-m-d', $delivery_date_timestamp );
											$from_date     = date( 'Y-m-d H:i:s', $delivery_date_timestamp );
											$to_date       = date( 'Y-m-d H:i:s', strtotime( '+30 minutes', $delivery_date_timestamp ) );
											// Modify delivery calendar data.
											array_push(
												$data,
												apply_filters(
													'orddd_lite_delivery_modify_calendar_data',
													array(
														'id'    => $value->ID,
														'title' => 'Order Number: ' . $order_number,
														'start' => $from_date,
														'end'   => $to_date,
														'eventtype' => 'order',
														'value' => $value,
														'delivery_date' => $date_to_display,
														'time_slot' => $time_slot,
														'event_product_id' => $key_explode[8],
														'event_product_qty' => '',
													),
													$order_id
												)
											);
										} else {
											$delivery_date_formatted = date( 'Y-m-d', $delivery_date_timestamp );
											// Modify delivery calendar data.
											array_push(
												$data,
												apply_filters(
													'orddd_lite_delivery_modify_calendar_data',
													array(
														'id'    => $value->ID,
														'title' => 'Order Number: ' . $order_number,
														'start' => $delivery_date_formatted,
														'end'   => $delivery_date_formatted,
														'eventtype' => 'order',
														'value' => $value,
														'delivery_date' => $date_to_display,
														'time_slot' => $time_slot,
														'event_product_id' => $key_explode[8],
														'event_product_qty' => '',
													),
													$order_id
												)
											);
										}
									}
								}
							}
						}
					}
				}
			}
			wp_send_json( $data );
		}
	}
}
$delivery_calendar_event_json = new Delivery_Calendar_lite_Event_JSON();

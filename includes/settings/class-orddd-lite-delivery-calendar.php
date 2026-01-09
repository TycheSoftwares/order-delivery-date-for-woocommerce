<?php //phpcs:disable
/**
 * Order Delivery Date Pro for WooCommerce
 *
 * Display the events in the Delivery Calendar in admin.
 *
 * @author      Tyche Softwares
 * @package     Order-Delivery-Date-Pro-for-WooCommerce/Delivery-Calendar
 * @since       2.8.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Display the events in the Delivery Calendar.
 *
 * @class orddd_lite_class_view_deliveries
 */
class orddd_lite_class_view_deliveries { 

	/**
	 * Default constructor
	 *
	 * @since 8.2
	 */
	public function __construct() {
		add_action( 'admin_init', array( &$this, 'orddd_data_export' ) );
	}

	/**
	 * Called during AJAX request for qtip content for a calendar item
	 *
	 * @hook wp_ajax_nopriv_orddd_order_calendar_content , wp_ajax_orddd_order_calendar_content
	 * @since 2.8.7
	 */
	public static function orddd_order_calendar_content() {
		$import_nonce = isset( $_POST['security'] ) ? sanitize_text_field( wp_unslash( $_POST['security'] ) ) : '';
		if ( ! wp_verify_nonce( $import_nonce, 'orddd-delivery-calendar-event-json' ) || ! ( current_user_can( 'manage_options' ) || current_user_can( 'manage_woocommerce' ) ) ) {
			esc_html_e( 'Authentication has failed.', 'order-delivery-date' );
			return;
		}
	    global $orddd_date_formats, $wpdb;
	    $content = $delivery_date_timestamp = '';
	    if ( ! empty( $_REQUEST['order_id'] ) && ! empty( $_REQUEST['event_value'] ) ) {
	        $order              = new WC_Order( $_REQUEST['order_id'] );
	        $order_items        = $order->get_items();
	        $billing_first_name = ( version_compare( get_option( 'woocommerce_version' ), '3.0.0', '>=' ) ) ? $order->get_billing_first_name() : $order->billing_first_name;
	        $billing_last_name  = ( version_compare( get_option( 'woocommerce_version' ), '3.0.0', '>=' ) ) ? $order->get_billing_last_name() : $order->billing_last_name;
	        $order_id           = ( version_compare( get_option( 'woocommerce_version' ), '3.0.0', '>=' ) ) ? $order->get_id() : $order->id;
	        $edit_order_url     = 'post.php?post=' . $order_id . '&action=edit';

	        if ( $_REQUEST['event_type'] == 'product' ) {
	            $value[]                 = sanitize_text_field( $_REQUEST['event_value'] );
	            $product_id              = sanitize_text_field( $_REQUEST['event_product_id'] );
	            $product_name            = sanitize_text_field( $_REQUEST['product_name'] );
	            $product_quantity        = intval( $_REQUEST['event_product_qty'] );
	            $delivery_date_formatted = Orddd_Lite_Common::orddd_lite_get_order_delivery_date( $order_id );
	            $order_page_time_slot    = Orddd_Lite_Common::orddd_get_order_timeslot( $order_id );

	            $content = '<table>
	                <tr><td> <strong>Order:</strong></td><td><a href="' . esc_url( $edit_order_url ) . '">#' . $order->get_order_number() . ' </a></td></tr>
	                <tr><td> <strong>Product Name:</strong></td><td> ' . esc_html( $product_name ) . ' x' . esc_html( $product_quantity ) . '</td></tr>
	                <tr><td> <strong>Customer Name:</strong></td><td> ' . esc_html( $billing_first_name ) . ' ' . esc_html( $billing_last_name ) . '</td></tr>';

	            if ( isset( $delivery_date_formatted ) && $delivery_date_formatted != '0000-00-00' && isset( $_REQUEST['event_date'] ) && $_REQUEST['event_date'] == '' ) {
	                $content .= '<tr> <td> <strong>Delivery Date:</strong></td><td> ' . esc_html( $delivery_date_formatted ) . '</td></tr>';
	            } elseif ( isset( $_REQUEST['event_date'] ) && $_REQUEST['event_date'] != '' ) {
	                $content .= '<tr> <td> <strong>Delivery Date:</strong></td><td> ' . esc_html( $_REQUEST['event_date'] ) . '</td></tr>';
	            }

	            if ( isset( $order_page_time_slot ) && $order_page_time_slot != '' && isset( $_REQUEST['event_date'] ) && $_REQUEST['event_date'] == '' ) {
	                $content .= '<tr> <td> <strong>Time Slot:</strong></td><td> ' . esc_html( $order_page_time_slot ) . '</td></tr>';
	            } elseif ( isset( $_REQUEST['event_timeslot'] ) && $_REQUEST['event_timeslot'] != '' ) {
	                $content .= '<tr> <td> <strong>Time Slot:</strong></td><td> ' . esc_html( $_REQUEST['event_timeslot'] ) . '</td></tr>';
	            }

	            $custom_fields = '';
	            if ( has_filter( 'orddd_lite_add_custom_field_value_to_qtip' ) ) {
	                $custom_fields = apply_filters( 'orddd_lite_lite_add_custom_field_value_to_qtip', $order_id );
	            }

	            if ( $custom_fields != '' ) {
	                $content .= esc_html( $custom_fields );
	            }

	            $content .= '</table>';

	            if ( $product_id ) {
	                $post_image = get_the_post_thumbnail( $product_id, array( 100, 100 ) );
	                if ( ! empty( $post_image ) ) {
	                    $content = '<div class="orddd_product_image">' . $post_image . '</div>' . $content;
	                }
	            }
	        } elseif ( $_REQUEST['event_type'] == 'order' ) {
	        	$delivery_date_formatted = Orddd_Lite_Common::orddd_lite_get_order_delivery_date( $order_id );
	            $order_page_time_slot    = Orddd_Lite_Common::orddd_get_order_timeslot( $order_id );

	            $value[] = sanitize_text_field( $_REQUEST['event_value'] );
	            $content = '<table>
	                <tr> <td> <strong>Order:</strong></td><td><a href="'. esc_url( $edit_order_url ) . '">#' . $order->get_order_number() . ' </a> </td> </tr>
	                <tr> <td> <strong>Customer Name:</strong></td><td> ' . esc_html( $billing_first_name ) . ' ' . esc_html( $billing_last_name ) . '</td> </tr>';

	            if ( isset( $delivery_date_formatted ) && $delivery_date_formatted != '0000-00-00' && isset( $_REQUEST['event_date'] ) && $_REQUEST['event_date'] == '' ) {
	                $content .= '<tr> <td> <strong>Delivery Date:</strong></td><td> ' . esc_html( $delivery_date_formatted ) . '</td></tr>';
	            } elseif ( isset( $_REQUEST['event_date'] ) && $_REQUEST['event_date'] != '' ) {
	                $content .= '<tr> <td> <strong>Delivery Date:</strong></td><td> ' . esc_html( $_REQUEST['event_date'] ) . '</td></tr>';
	            }

	            if ( isset( $order_page_time_slot ) && $order_page_time_slot != '' && isset( $_REQUEST['event_date'] ) && $_REQUEST['event_date'] == '' ) {
	                $content .= '<tr> <td> <strong>Time Slot:</strong></td><td> ' . esc_html( $order_page_time_slot ) . '</td></tr>';
	            } elseif ( isset( $_REQUEST['event_timeslot'] ) && $_REQUEST['event_timeslot'] != '' ) {
	                $content .= '<tr> <td> <strong>Time Slot:</strong></td><td> ' . esc_html( $_REQUEST['event_timeslot'] ) . '</td></tr>';
	            }

	            $custom_fields = '';
	            if ( has_filter( 'orddd_lite_add_custom_field_value_to_qtip' ) ) {
	                $custom_fields = apply_filters( 'orddd_lite_add_custom_field_value_to_qtip', $order_id );
	            }

	            if ( $custom_fields != '' ) {
	                $content .= esc_html( $custom_fields );
	            }

	            $product_name = '';
	            if ( isset( $_REQUEST['event_product_id'] ) && $_REQUEST['event_product_id'] != '' ) {
	                $product_name = get_the_title( sanitize_text_field( $_REQUEST['event_product_id'] ) );
	            } else {
	                $variable_product_name = '';
	                foreach ( $order_items as $item ) {
	                    $data = $item->get_data();
	                    if ( isset( $data['variation_id'] ) && $data['variation_id'] != 0 ) {
	                        $_product              = new WC_Product_Variation( $data['variation_id'] );
	                        $variable_product_name = '<a href="' . esc_url( get_edit_post_link( $data['product_id'] ) ) . '" target="_blank">' . esc_html( $_product->get_title() );
	                        $variation_data        = $_product->get_variation_attributes(); // variation data in array.
	                        $meta_data             = $data['meta_data'];

	                        if ( is_array( $variation_data ) && count( $variation_data ) > 0 ) {
	                            $variable_product_data = '';

	                            foreach( $meta_data as $key => $value ) {
	                                $mdata =  $value->get_data();
	                                $taxonomy = $mdata['key'];
	                                $attribute_name = wc_attribute_label( $taxonomy, $_product );
	                                if ( taxonomy_exists( $taxonomy ) ) {
	                                    $attribute_value = get_term_by( 'slug', $mdata['value'], $taxonomy )->name;
	                                } else {
	                                    $attribute_value = $mdata['value']; // For custom product attributes.
	                                }
	                                $variable_product_data .=  $attribute_name . ': ' . $attribute_value . ', ';
	                            }
	                            $variable_product_name = rtrim( $variable_product_name . ' - ' . $variable_product_data, ', ' ) . ' x' . esc_html( $item['quantity'] ) . '</a><br><br>';
	                            $product_name          .= $variable_product_name;
	                        }
	                    } else {
	                        $product_name .= '<a href="' . esc_url( get_edit_post_link( $data['product_id'] ) ) . '" target="_blank">' . esc_html( $item['name'] ) . ' x' . esc_html( $item['quantity'] ) . '</a><br><br>';
	                    }
	                    $product_name = apply_filters( 'orddd_lite_modify_calendar_product_info', $product_name, $item );
	                }
	            }
	            $content .= '<tr> <td> <strong>Item Details:</strong></td><td> ' . wp_kses_post( $product_name ) . '</td> </tr>';
	            $content .= '</table>';
	        }
	    }
	    echo wp_kses_post( $content );
	    die();
	}


	/**
	 * This function will download CSV or Print Deliveries based on the CSV on Print button is clicked
	 *
	 * @since 2.0
	 * @global $wpdb Global wpdb object
	 */

	public static function orddd_data_export() {
		global $wpdb;
		if ( isset( $_GET['download'] ) && ( $_GET['download'] == 'orddd_data.csv' ) && ( ( isset( $_GET['page'] ) && $_GET['page'] = 'orddd_view_orders' ) ) ) {
			$report = self::orddd_generate_data();
			$csv    = self::orddd_generate_csv( $report );

			header( 'Content-type: application/x-msdownload' );
			header( 'Content-Disposition: attachment; filename=orddd_data.csv' );
			header( 'Pragma: no-cache' );
			header( 'Expires: 0' );
			echo "\xEF\xBB\xBF";
			echo $csv;
			exit;
		} elseif ( isset( $_GET['download'] ) && ( $_GET['download'] == 'orddd_data.print' ) && ( ( isset( $_GET['page'] ) && $_GET['page'] = 'orddd_view_orders' ) ) ) {
			$report = self::orddd_generate_data();

			$print_data_columns  = "
                                    <tr>
                                        <th style='border:1px solid black;padding:5px;'>" . __( 'Order ID', 'order-delivery-date' ) . "</th>
                                        <th style='border:1px solid black;padding:5px;'>" . __( 'Products', 'order-delivery-date' ) . "</th>
                                        <th style='border:1px solid black;padding:5px;'>" . __( 'Billing Address', 'order-delivery-date' ) . "</th>
                                        <th style='border:1px solid black;padding:5px;'>" . __( 'Shipping Address', 'order-delivery-date' ) . "</th>
                                        <th style='border:1px solid black;padding:5px;'>" . __( 'Shipping Method', 'order-delivery-date' ) . "</th>
                                        <th style='border:1px solid black;padding:5px;'>" . __( 'Delivery Date', 'order-delivery-date' ) . "</th>
                                        <th style='border:1px solid black;padding:5px;'>" . __( 'Delivery Time', 'order-delivery-date' ) . "</th>
                                        <th style='border:1px solid black;padding:5px;'>" . __( 'Order Date', 'order-delivery-date' ) . '</th>
                                    </tr>';
			$print_data_row_data = '';

			foreach ( $report as $key => $value ) {
				// Currency Symbol.
				// The order currency is fetched to ensure the correct currency is displayed if the site uses multi-currencies.
				$the_order       = wc_get_order( $value->order_id );
				$currency        = ( version_compare( WOOCOMMERCE_VERSION, '3.0.0' ) < 0 ) ? $the_order->get_order_currency() : $the_order->get_currency();
				$currency_symbol = get_woocommerce_currency_symbol( $currency );

				$print_data_row_data .= "<tr>
                                        <td style='border:1px solid black;padding:5px;'>" . $value->order_id . "</td>
                                        <td style='border:1px solid black;padding:5px;'>";

				foreach ( $value->product_name as $id => $data ) {
					$print_data_row_data .= $data['product'] . ' x ' . $data['quantity'] . '<br>';
				}

				$print_data_row_data .= "</td>
                                        <td style='border:1px solid black;padding:5px;'>" . $value->billing_address . "</td>
                                        <td style='border:1px solid black;padding:5px;'>" . $value->shipping_address . "</td>
                                        <td style='border:1px solid black;padding:5px;'>" . $value->shipping_method . "</td>
                                        <td style='border:1px solid black;padding:5px;'>" . $value->delivery_date . "</td>
                                        <td style='border:1px solid black;padding:5px;'>" . $value->delivery_time . "</td>
                                        <td style='border:1px solid black;padding:5px;'>" . $value->order_date . '</td>
                                        </tr>';
			}
			$print_data_columns  = apply_filters( 'orddd_lite_print_columns', $print_data_columns );
			$print_data_row_data = apply_filters( 'orddd_lite_print_rows', $print_data_row_data, $report );
			$print_data          = "<table style='border:1px solid black;border-collapse:collapse;'>" . $print_data_columns . $print_data_row_data . '</table>';
			$print_data          = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"></head><body><table style='border:1px solid black;border-collapse:collapse;'>" . $print_data_columns . $print_data_row_data . '</table></body></html>';
			echo $print_data;
			exit;
		}

		do_action( 'orddd_lite_print_summary_data' );
	}

	/**
	 * This function will generate the data require for CSV and Print of bookings
	 *
	 * @since 2.0
	 * @param string $tab_status selected filter E.g status for Booking from today onwards is 'future'
	 * @global object $wpdb Global wpdb object
	 * @return array $report All booking details required to show on old View Bookings page.
	 */
	public static function orddd_generate_data() {
		global $wpdb;
		if ( isset( $_GET['orderStatus'] ) && ( $_GET['orderStatus'] != '' ) ) { // phpcs:ignore
			$order_status1 = $_GET['orderStatus']; // phpcs:ignore
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
		if ( isset( $_GET['orderShipping'] ) && ( $_GET['orderShipping'] != '' ) ) { // phpcs:ignore
			$order_shipping1 = $_GET['orderShipping']; // phpcs:ignore
			$order_shipping  = explode( ',', $order_shipping1 );
		}

		$event_start = $event_start_timestamp = $event_end = $event_end_timestamp = '';

		if ( isset( $_GET['start'] ) ) { // phpcs:ignore
			$event_start           = $_GET['start']; // phpcs:ignore
			$event_start_timestamp = strtotime( $_GET['start'] ); // phpcs:ignore
		}

		if ( isset( $_GET['end'] ) ) { // phpcs:ignore
			$event_end           = $_GET['end']; // phpcs:ignore
			$event_end_timestamp = strtotime( $_GET['end'] ); // phpcs:ignore
		}

		// Start and end date is same then check for same date with end time ie 10th Nov 2018 23:59:59.
		if ( $event_start_timestamp != '' && $event_start_timestamp == $event_end_timestamp ) { // phpcs:ignore
			$event_end_timestamp += 86399;
		}

		$date_str                  = Orddd_Lite_Common::str_to_date_format();
		$delivery_date_field_label = esc_sql( get_option( 'orddd_lite_delivery_date_field_label' ) );

		
		$order_timestamp_key     = '_orddd_lite_timestamp';
		$orddd_delivery_date_key = $delivery_date_field_label;

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
			( wpm2.meta_key = '" . $delivery_date_field_label . "' AND STR_TO_DATE( wpm2.meta_value, '" . $date_str . "' ) >= '" . $event_start . "' AND STR_TO_DATE( wpm2.meta_value, '" . $date_str . "' ) <= '" . $event_end . "' )
			OR ( wpm2.meta_key = '" . $delivery_date_field_label . "' AND STR_TO_DATE( wpm2.meta_value, '" . $date_str . "' ) >= '" . $event_start . "' AND STR_TO_DATE( wpm2.meta_value, '" . $date_str . "' ) <= '" . $event_end . "' ) 
			OR ( wpm2.meta_key = '" . $orddd_delivery_date_key . "' AND STR_TO_DATE( wpm2.meta_value, '" . $date_str . "' ) >= '" . $event_start . "' AND STR_TO_DATE( wpm2.meta_value, '" . $date_str . "' ) <= '" . $event_end . "' )
			)";

			$orddd_query = apply_filters( 'orddd_lite_calendar_where_filter', $orddd_query );
			$results     = $wpdb->get_results( $orddd_query );// nosemgrep:audit.php.wp.security.sqli.input-in-sinks

		$report = array();
		$i      = 0;

		// It will check if the woocommerce product addons plugin is active or not.
		$is_addon_plugin_active = is_plugin_active( 'woocommerce-product-addons/woocommerce-product-addons.php' );

		foreach ( $results as $rkey => $rval ) {
			$order_id    = isset( $rval->ID ) ? $rval->ID : ( isset( $rval->id ) ? $rval->id  : '' ) ;
			$order       = wc_get_order( $order_id );
			$order_items = $order->get_items();

			if ( $order->get_items( 'shipping' ) ) {
				foreach ( $order->get_items( 'shipping' ) as $item_id => $item ) {
					$shipping_method_id          = $item->get_method_id();
					$shipping_method_instance_id = $item->get_instance_id();
					$shipping_method_to_check    = $shipping_method_id . ':' . $shipping_method_instance_id;
				}
				if ( ! empty( $order_shipping ) && ! in_array( $shipping_method_to_check, $order_shipping, true ) ) {
					continue;
				}
			}

			if ( 'order' === $_GET['eventType'] ) { // phpcs:ignore
				$i = $order_id;
			}
			$products = array();
			foreach ( $order_items as $item ) {
				if ( 'product' == $_GET['eventType'] ) { // phpcs:ignore
					$products = array();
				}

				$report[ $i ] = new stdClass();

				// Order ID.
				$report[ $i ]->order_id = $order_id;

				// Product Name.
				$product_name = html_entity_decode( $item['name'], ENT_COMPAT, 'UTF-8' );
				$data         = $item->get_data();

				if ( $is_addon_plugin_active || ( isset( $data['variation_id'] ) && $data['variation_id'] != 0 ) ) {
					if ( isset( $data['variation_id'] ) && $data['variation_id'] ) {
						$_product       = new WC_Product_Variation( $data['variation_id'] );
						$product_name   = $_product->get_title();
						$variation_data = $_product->get_variation_attributes(); // variation data in array
					} else {
						$_product = wc_get_product( $data['product_id'] );
					}
					$meta_data = $data['meta_data'];

					if ( $is_addon_plugin_active || ( is_array( $variation_data ) && count( $variation_data ) > 0 ) ) {
						$product_name .= ' - ';
						if ( isset( $_product ) && ! empty( $_product ) ) {
							foreach( $meta_data as $key => $value ) {
								$mdata =  $value->get_data();
								$taxonomy = $mdata['key'];
								$attribute_name = wc_attribute_label( $taxonomy, $_product );

								if( substr( $attribute_name, 0, 1 ) === '_' ) {
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
								
								$product_name .=  $attribute_name . ': ' . $attribute_value . ', ';
							}
						}
						$product_name = rtrim( $product_name, ', ' );
					}
				}

				array_push(
					$products,
					array(
						'product'  => $product_name,
						'quantity' => $item['quantity'],
					)
				);
				$report[ $i ]->product_name = $products;

				// Quantity
				$report[ $i ]->quantity = $item['quantity'];

				// Billing Address
				$billing                       = $order->get_formatted_billing_address();
				$billing                       = str_replace( '\n', ',', $billing );
				$billing                       = str_replace( PHP_EOL, ',', $billing );
				$billing                       = str_replace( '<br/>', ',', $billing );
				$report[ $i ]->billing_address = $billing;

				// Shipping Address
				$shipping                       = $order->get_formatted_shipping_address();
				$shipping                       = str_replace( '\n', ',', $shipping );
				$shipping                       = str_replace( PHP_EOL, ',', $shipping );
				$shipping                       = str_replace( '<br/>', ',', $shipping );
				$report[ $i ]->shipping_address = $shipping;

				// Shipping Method
				$report[ $i ]->shipping_method = $order->get_shipping_method();

				// Delivery Date
				$report[ $i ]->delivery_date = Orddd_Lite_Common::orddd_lite_get_order_delivery_date( $order_id );

				// Delivery Time
				$report[ $i ]->delivery_time = Orddd_Lite_Common::orddd_get_order_timeslot( $order_id );


				// Order Date
				$order_date = '';
				if ( version_compare( WOOCOMMERCE_VERSION, '3.0.0' ) < 0 ) {
					$order_date = $order->completed_date;
				} else {
					if ( Orddd_Lite_Common::is_hpos_enabled() ) {
						$order_post = wc_get_order( $order_id );
						$order_date = gmdate( 'Y-m-d H:i:s', $order->get_date_created( 'edit' )->getOffsetTimestamp() );
					} else {
						$order_post = get_post( $order_id );
						$post_date  = strtotime( $order_post->post_date );
						$order_date = date( 'Y-m-d H:i:s', $post_date );
					}					
				}

				$report[ $i ]->order_date = $order_date;
				if ( 'product' == $_GET['eventType'] ) {
					$i++;
				}
			}
		}
		return apply_filters( 'orddd_lite_export_data', $report );
	}

	/**
	 * This function will create the string to be required for CSV download
	 *
	 * @since 8.2
	 * @param array $report Array of all delivery details
	 * @return string $csv Returns the strings which is created based on the delivery details
	 */

	public static function orddd_generate_csv( $report, $event_type = '' ) {
		// Column Names
		$csv  = __( 'Order ID', 'order-delivery-date' ) . ',' . __( 'Products', 'order-delivery-date' ) . ',' . __( 'Billing Address', 'order-delivery-date' ) . ',' . __( 'Shipping Address', 'order-delivery-date' ) . ',' . __( 'Shipping Method', 'order-delivery-date' ) . ',' . __( 'Delivery Date', 'order-delivery-date' ) . ',' . __( 'Delivery Time', 'order-delivery-date' ) . ',' . __( 'Order Date', 'order-delivery-date' );
		$csv .= "\n";
		foreach ( $report as $key => $value ) {

			// Order ID
			$order_id         = $value->order_id;
			$product_name     = $value->product_name;
			$quantity         = $value->quantity;
			$billing_address  = $value->billing_address;
			$shipping_address = $value->shipping_address;
			$shipping_method  = $value->shipping_method;
			$delivery_date    = $value->delivery_date;
			$delivery_time    = $value->delivery_time;
			$order_date       = $value->order_date;

			if ( 'product' == $_GET['eventType'] || 'product' == $event_type ) {
				$break = '';
			} else {
				$break = "\n";
			}
			// Create the data row
			$csv .= $order_id . ',"';
			foreach ( $product_name as $id => $data ) {
				$data['product'] = str_replace( '"', '""', $data['product'] );
				$name            = str_replace( '<br>', "\n", $data['product'] );
				$csv            .= strip_tags( $name ) . ' x ' . $data['quantity'] . $break;
			}

			// Create the data row
			$csv .= '","' . $billing_address . '","' . $shipping_address . '","' . $shipping_method . '","' . $delivery_date . '","' . $delivery_time . '","' . $order_date . '"';

			$csv .= "\n";
		}
		$csv = apply_filters( 'orddd_lite_csv_data', $csv, $report );
		return $csv;
	}
}

$orddd_class_view_deliveries = new orddd_lite_class_view_deliveries();

<?php
/**
 * Order Delivery Date for WooCommerce Lite
 *
 * Common functions used in multiple files are added
 *
 * @author      Tyche Softwares
 * @package     Order-Delivery-Date-Lite-for-WooCommerce/Common-Functions
 * @since       3.5
 */
 
 use Automattic\WooCommerce\Utilities\OrderUtil;

/**
 * Class for the common functions used in the plugin
 */
class Orddd_Lite_Common {
	/**
	 * Tracking data to send when No, thanks. button is clicked.
	 *
	 * @hook ts_tracker_opt_out_data
	 *
	 * @param array $params Parameters to pass for tracking data.
	 *
	 * @return array Data to track when opted out.
	 */
	public static function orddd_lite_get_data_for_opt_out( $params ) {
		$plugin_data['ts_meta_data_table_name'] = 'ts_tracking_orddd_lite_meta_data';
		$plugin_data['ts_plugin_name']          = 'Order Delivery Date for WooCommerce (Lite version)';
		$params['plugin_data']                  = $plugin_data;
		return $params;
	}

	/**
	 * Plugin's data to be tracked when Allow option is choosed.
	 *
	 * @hook ts_tracker_data
	 *
	 * @param array $data Contains the data to be tracked.
	 *
	 * @return array Plugin's data to track.
	 */
	public static function orddd_lite_ts_add_plugin_tracking_data( $data ) {
		// phpcs:ignore WordPress.Security.NonceVerification
		$plugin_data['ts_meta_data_table_name'] = 'ts_tracking_orddd_lite_meta_data';
		$plugin_data['ts_plugin_name']          = 'Order Delivery Date for WooCommerce (Lite version)';

		// Store count info.
		$plugin_data['deliveries_count'] = self::orddd_lite_ts_get_order_counts();

		// Get all plugin options info.
		$plugin_data['deliveries_settings']       = self::orddd_lite_ts_get_all_plugin_options_values();
		$plugin_data['orddd_lite_plugin_version'] = self::orddd_get_version();
		$plugin_data['orddd_lite_allow_tracking'] = get_option( 'orddd_lite_allow_tracking' );
		$data['plugin_data']                      = $plugin_data;
		return $data;
	}

	/**
	 * Get order counts based on order status.
	 *
	 * @globals resource WordPress object
	 *
	 * @return int $order_count Number of Deliveries
	 */
	public static function orddd_lite_ts_get_order_counts() {
		global $wpdb;
		$order_count = 0;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$results = $wpdb->get_results(
			$wpdb->prepare(
				'SELECT count(ID) AS delivery_orders_count FROM `' . $wpdb->prefix . 'posts` WHERE post_type = "shop_order" AND post_status NOT IN ("wc-cancelled", "wc-refunded", "trash", "wc-failed" ) AND ID IN ( SELECT post_id FROM `' . $wpdb->prefix . 'postmeta` WHERE meta_key IN ( %s, %s ) )',
				'_orddd_lite_timestamp',
				get_option( 'orddd_lite_delivery_date_field_label' )
			)
		);
		if ( isset( $results[0] ) ) {
			$order_count = $results[0]->delivery_orders_count;
		}
		return $order_count;
	}

	/**
	 * Get all plugin options starting with orddd_ prefix.
	 *
	 * @globals resource WordPress object
	 *
	 * @return array Plugin Settings
	 */
	public static function orddd_lite_ts_get_all_plugin_options_values() {
		return array(
			'enable_delivery'                          => get_option( 'orddd_lite_enable_delivery_date' ),
			'date_mandatory'                           => get_option( 'orddd_lite_date_field_mandatory' ),
			'populate_first_delivery_date'             => get_option( 'orddd_lite_auto_populate_first_available_date' ),
			'allow_minimum_hours_for_non_working_days' => get_option( 'orddd_lite_calculate_min_time_disabled_days' ),
			'no_fields_for'                            => array(
				'virtual_product'  => get_option( 'orddd_lite_no_fields_for_virtual_product' ),
				'featured_product' => get_option( 'orddd_lite_no_fields_for_featured_product' ),
			),
			'cart_page_delivery'                       => get_option( 'orddd_lite_delivery_date_on_cart_page' ),
		);
	}


	/**
	 * It will add the question for the deactivate popup modal
	 *
	 * @return array $orddd_lite_add_questions All questions.
	 */
	public static function orddd_lite_deactivate_add_questions() {

		$orddd_lite_add_questions = array(
			0 => array(
				'id'                => 4,
				'text'              => __( 'Minimum Delivery Time (in hours) is not working as expected.', 'order-delivery-date' ),
				'input_type'        => '',
				'input_placeholder' => '',
			),
			1 => array(
				'id'                => 5,
				'text'              => __( 'I need delivery time along with the delivery date.', 'order-delivery-date' ),
				'input_type'        => '',
				'input_placeholder' => '',
			),
			2 => array(
				'id'                => 6,
				'text'              => __( 'The plugin is not compatible with another plugin.', 'order-delivery-date' ),
				'input_type'        => 'textfield',
				'input_placeholder' => 'Which Plugin?',
			),
			3 => array(
				'id'                => 7,
				'text'              => __( 'I have purchased the Pro version of the Plugin.', 'order-delivery-date' ),
				'input_type'        => '',
				'input_placeholder' => '',
			),

		);
		return $orddd_lite_add_questions;
	}

	/**
	 * Return the date with the selected langauge in Appearance tab
	 *
	 * @param string $delivery_date_formatted Default Delivery Date.
	 * @param string $delivery_date_timestamp Delivery Date Timestamp.
	 *
	 * @return string Translated Delivery Date
	 *
	 * @globals array $orddd_lite_languages Languages array
	 * @globals array $orddd_lite_languages_locale Locale of all languages array
	 *
	 * @since 1.9
	 */
	public static function delivery_date_lite_language( $delivery_date_formatted, $delivery_date_timestamp ) {
		global $orddd_lite_languages, $orddd_lite_languages_locale, $orddd_lite_date_formats;
		require_once ABSPATH . 'wp-admin/includes/translation-install.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		$date_language = get_option( 'orddd_lite_language_selected' );
		if ( '' !== $delivery_date_timestamp ) {
			if ( 'en-GB' !== $date_language ) {
				$locale_format = $orddd_lite_languages[ $date_language ];
				$time          = setlocale( LC_ALL, $orddd_lite_languages_locale[ $locale_format ] );
				$date_format   = get_option( 'orddd_lite_delivery_date_format' );

				$lang = explode( '.', $orddd_lite_languages_locale[ $locale_format ][0] );

				// Load the language selected in the Appearance settings.
				$loaded_language = wp_download_language_pack( $lang[0] );
				if ( $loaded_language ) {
					load_default_textdomain( $loaded_language );
					$GLOBALS['wp_locale'] = new WP_Locale(); //phpcs:ignore
				}
				switch ( $date_format ) {
					case 'd M, y':
						$date_str  = str_replace( 'd', '%d', $date_format );
						$month_str = str_replace( 'M', '%b', $date_str );
						$year_str  = str_replace( 'y', '%y', $month_str );
						break;
					case 'd M, yy':
						$date_str  = str_replace( 'd', '%d', $date_format );
						$month_str = str_replace( 'M', '%b', $date_str );
						$year_str  = str_replace( 'yy', '%Y', $month_str );
						break;
					case 'd MM, y':
						$date_str  = str_replace( 'd', '%d', $date_format );
						$month_str = str_replace( 'MM', '%B', $date_str );
						$year_str  = str_replace( 'y', '%y', $month_str );
						break;
					case 'd MM, yy':
						$date_str  = str_replace( 'd', '%d', $date_format );
						$month_str = str_replace( 'MM', '%B', $date_str );
						$year_str  = str_replace( 'yy', '%Y', $month_str );
						break;
					case 'DD, d MM, yy':
						$day_str   = str_replace( 'DD', '%A', $date_format );
						$date_str  = str_replace( 'd', '%d', $day_str );
						$month_str = str_replace( 'MM', '%B', $date_str );
						$year_str  = str_replace( 'yy', '%Y', $month_str );
						break;
					case 'D, M d, yy':
						$day_str   = str_replace( 'D', '%a', $date_format );
						$date_str  = str_replace( 'd', '%d', $day_str );
						$month_str = str_replace( 'M', '%b', $date_str );
						$year_str  = str_replace( 'yy', '%Y', $month_str );
						break;
					case 'DD, M d, yy':
						$day_str   = str_replace( 'DD', '%A', $date_format );
						$date_str  = str_replace( 'd', '%d', $day_str );
						$month_str = str_replace( 'M', '%b', $date_str );
						$year_str  = str_replace( 'yy', '%Y', $month_str );
						break;
					case 'DD, MM d, yy':
						$day_str   = str_replace( 'DD', '%A', $date_format );
						$date_str  = str_replace( 'd', '%d', $day_str );
						$month_str = str_replace( 'MM', '%B', $date_str );
						$year_str  = str_replace( 'yy', '%Y', $month_str );
						break;
					case 'D, MM d, yy':
						$day_str   = str_replace( 'D', '%a', $date_format );
						$date_str  = str_replace( 'd', '%d', $day_str );
						$month_str = str_replace( 'MM', '%B', $date_str );
						$year_str  = str_replace( 'yy', '%Y', $month_str );
						break;
				}

				if ( isset( $year_str ) ) {
					$delivery_date_formatted = strftime( $year_str, $delivery_date_timestamp );
					$delivery_date_formatted = date_i18n( $orddd_lite_date_formats[ $date_format ], $delivery_date_timestamp );
				}
				setlocale( LC_ALL, 'en_GB.utf8' );
				// Load the default language again after setting the delivery date to required language.
				load_default_textdomain( 'en_GB' );
				$GLOBALS['wp_locale'] = new WP_Locale(); //phpcs:ignore
			}
		}
		return $delivery_date_formatted;
	}

	/**
	 * Return the delivery date selected for the order
	 *
	 * @param int $order_id Order ID.
	 * @return string Delivery Date for the order.
	 * @globals array $orddd_lite_date_formats Date Format array
	 * @since 1.9
	 */
	public static function orddd_lite_get_order_delivery_date( $order_id ) {
		global $orddd_lite_date_formats;
		$data                    = Orddd_Lite_Common::get_order_meta( $order_id );
		$field_date_label        = get_option( 'orddd_lite_delivery_date_field_label' );
		$delivery_date_formatted = '';
		$delivery_date_timestamp = '';
		$date_format             = get_option( 'orddd_lite_delivery_date_format' );

		if ( isset( $data['_orddd_lite_timestamp'] ) || isset( $data[ get_option( 'orddd_lite_delivery_date_field_label' ) ] ) ) {
			if ( isset( $data['_orddd_lite_timestamp'] ) ) {
				$delivery_date_timestamp = $data['_orddd_lite_timestamp'][0];
			}
			
			$delivery_date_formatted = '';
			if ( '' !== $delivery_date_timestamp ) {
				$delivery_date_formatted = gmdate( $orddd_lite_date_formats[ get_option( 'orddd_lite_delivery_date_format' ) ], $delivery_date_timestamp );

				$delivery_date_timestamp += 0; // this will convert to long type.
				$delivery_date_formatted  = date_i18n( $orddd_lite_date_formats[ $date_format ], $delivery_date_timestamp );
				$delivery_date_formatted = self::delivery_date_lite_language( $delivery_date_formatted, $delivery_date_timestamp );
			} else {
				if ( array_key_exists( get_option( 'orddd_lite_delivery_date_field_label' ), $data ) ) {
					if ( '' !== $data[ get_option( 'orddd_lite_delivery_date_field_label' ) ][0] ) {
						$delivery_date_formatted = $data[ get_option( 'orddd_lite_delivery_date_field_label' ) ][0];
					}
				} elseif ( array_key_exists( ORDDD_LITE_DELIVERY_DATE_FIELD_LABEL, $data ) ) {
					$delivery_date_formatted = $data[ ORDDD_LITE_DELIVERY_DATE_FIELD_LABEL ][0];
				}
			}
		}
		return $delivery_date_formatted;
	}


	/**
	 * Returns timestamp for the selected Delivery date
	 *
	 * @param string $delivery_date Selected Delivery Date.
	 * @param string $date_format Date Format.
	 * @return string Timestamp for the selected delivery date.
	 * @since 1.7
	 */
	public static function orddd_lite_get_timestamp( $delivery_date, $date_format ) {
		$hour     = 0;
		$min      = 1;
		$date_str = '';
		$m        = 0;
		$d        = 0;
		$y        = 0;
		if ( '' !== $delivery_date ) {
			switch ( $date_format ) {
				case 'mm/dd/y':
					$date_arr = explode( '/', $delivery_date );
					$m        = $date_arr[0];
					$d        = $date_arr[1];
					$y        = $date_arr[2];
					break;
				case 'dd/mm/y':
					$date_arr = explode( '/', $delivery_date );
					$m        = $date_arr[1];
					$d        = $date_arr[0];
					$y        = $date_arr[2];
					break;
				case 'y/mm/dd':
					$date_arr = explode( '/', $delivery_date );
					$m        = $date_arr[1];
					$d        = $date_arr[2];
					$y        = $date_arr[0];
					break;
				case 'dd.mm.y':
					$date_arr = explode( '.', $delivery_date );
					$m        = $date_arr[1];
					$d        = $date_arr[0];
					$y        = $date_arr[2];
					break;
				case 'y.mm.dd':
					$date_arr = explode( '.', $delivery_date );
					$m        = $date_arr[1];
					$d        = $date_arr[2];
					$y        = $date_arr[0];
					break;
				case 'yy-mm-dd':
					$date_arr = explode( '-', $delivery_date );
					$m        = $date_arr[1];
					$d        = $date_arr[2];
					$y        = $date_arr[0];
					break;
				case 'dd-mm-y':
					$date_arr = explode( '-', $delivery_date );
					$m        = $date_arr[1];
					$d        = $date_arr[0];
					$y        = $date_arr[2];
					break;
				case 'd M, y':
					$date_str = str_replace( ',', '', $delivery_date );
					break;
				case 'd M, yy':
					$date_str = str_replace( ',', '', $delivery_date );
					break;
				case 'd MM, y':
					$date_str = str_replace( ',', '', $delivery_date );
					break;
				case 'd MM, yy':
					$date_str = str_replace( ',', '', $delivery_date );
					break;
				case 'DD, d MM, yy':
					$date_str = str_replace( ',', '', $delivery_date );
					break;
				case 'D, M d, yy':
					$date_str = str_replace( ',', '', $delivery_date );
					break;
				case 'DD, M d, yy':
					$date_str = str_replace( ',', '', $delivery_date );
					break;
				case 'DD, MM d, yy':
					$date_str = str_replace( ',', '', $delivery_date );
					break;
				case 'D, MM d, yy':
					$date_str = str_replace( ',', '', $delivery_date );
					break;
			}
			if ( isset( $date_str ) && '' !== $date_str ) {
				$timestamp = strtotime( $date_str );
			} else {
				$timestamp = mktime( 0, 0, 0, $m, $d, $y );
			}
		} else {
			$timestamp = '';
		}

		return $timestamp;
	}

	/**
	 * Free up the delivery date and time if an order is moved to trashed
	 *
	 * @hook wp_trash_post
	 *
	 * @param int $order_id Order ID.
	 * @globals string typenow
	 * @since 2.5
	 */
	public static function orddd_lite_cancel_delivery_for_trashed( $order_id ) {
		global $typenow;

		if ( self::is_hpos_enabled() ) {
			$order       = wc_get_order( $order_id );
			if ( ! $order ) {
				return;
			}
			$post_status = $order->get_status();
			$post_type   = $order->get_type();		
		} else {
			$post_obj    = get_post( $order_id );
			if ( ! $post_obj ) {
				return;
			}
			$post_status = $post_status->post_status;
			$post_type   = $post_obj->post_type;
		}

		if ( 'shop_order' !== $post_type ) {
			return;
		} else {
			if ( 'wc-cancelled' !== $post_status &&
			'wc-refunded' !== $post_status &&
			'wc-failed' !== $post_status ) {
				self::orddd_lite_cancel_delivery( $order_id );
			}
		}
	}


	/**
	 * Free up the delivery date and time if an order is cancelled, refunded or failed
	 *
	 * @hook woocommerce_order_status_cancelled
	 * @hook woocommerce_order_status_refunded
	 * @hook woocommerce_order_status_failed
	 *
	 * @param int $order_id Order ID.
	 * @globals string typenow
	 * @since 2.5
	 */
	public static function orddd_lite_cancel_delivery( $order_id ) {
		global $wpdb, $typenow;
		$order                   = wc_get_order( $order_id );
		$post_meta               = $order->get_meta( '_orddd_lite_timestamp', true );
		$delivery_date_timestamp = '';
		if ( isset( $post_meta ) && '' !== $post_meta && null !== $post_meta ) {
			$delivery_date_timestamp = $post_meta;
		}

		$timeslot            = '';
		$total_quantities    = 1;
		$time_field_label    = '' !== get_option( 'orddd_lite_delivery_timeslot_field_label' ) ? get_option( 'orddd_lite_delivery_timeslot_field_label' ) : 'Time Slot';
		$timeslot_post_meta  = Orddd_Lite_Common::get_order_meta( $order_id, $time_field_label );
		$time_format_to_show = self::orddd_lite_get_time_format();

		if ( isset( $timeslot_post_meta[0] ) && '' !== $timeslot_post_meta[0] && null !== $timeslot_post_meta[0] ) {
			$timeslot = $timeslot_post_meta[0];
		}

		$delivery_date = '';
		if ( '' !== $delivery_date_timestamp ) {
			$delivery_date = gmdate( ORDDD_LITE_LOCKOUT_DATE_FORMAT, $delivery_date_timestamp );
		}

		$lockout_days = get_option( 'orddd_lite_lockout_days' );
		if ( '' === $lockout_days || '{}' === $lockout_days || '[]' === $lockout_days || 'null' === $lockout_days ) {
			$lockout_days_arr = array();
		} else {
			$lockout_days_arr = (array) json_decode( $lockout_days );
		}
		foreach ( $lockout_days_arr as $k => $v ) {
			$orders = $v->o;
			if ( $delivery_date === $v->d ) {
				if ( '1' === $v->o ) {
					unset( $lockout_days_arr[ $k ] );
				} else {
					$orders                 = $v->o - 1;
					$lockout_days_arr[ $k ] = array(
						'o' => $orders,
						'd' => $v->d,
					);
				}
			}
		}

		$lockout_days_jarr = wp_json_encode( $lockout_days_arr );
		update_option( 'orddd_lite_lockout_days', $lockout_days_jarr );

		if ( '' !== $timeslot ) {
			if ( '' !== $delivery_date_timestamp ) {
				$lockout_date = date( 'j-n-Y', $delivery_date_timestamp ); //phpcs:ignore
			} else {
				$lockout_date = '';
			}

			$lockout_time = get_option( 'orddd_lite_lockout_time_slot' );
			if ( '' == $lockout_time || '{}' == $lockout_time || '[]' == $lockout_time || 'null' == $lockout_time ) { //phpcs:ignore
				$lockout_time_arr = array();
			} else {
				$lockout_time_arr = (array) json_decode( $lockout_time );
			}

			foreach ( $lockout_time_arr as $k => $v ) {
				$orders        = $v->o;
				$time_to_check = self::orddd_lite_change_time_slot_format( $v->t, $time_format_to_show );
				if ( $timeslot == $v->t && $lockout_date == $v->d ) { //phpcs:ignore
					if ( $v->o == $total_quantities ) { //phpcs:ignore
						unset( $lockout_time_arr[ $k ] );
					} else {
						$orders                 = $v->o - $total_quantities;
						$lockout_time_arr[ $k ] = array(
							'o' => $orders,
							't' => $v->t,
							'd' => $v->d,
						);
					}
				}
			}
			$lockout_time_jarr = wp_json_encode( $lockout_time_arr );
			update_option( 'orddd_lite_lockout_time_slot', $lockout_time_jarr );
		}
	}


	/**
	 * Checks if there is a Virtual product in cart
	 *
	 * @globals resource $woocommerce WooCommerce Object
	 * @return string yes if virtual product is there in the cart else no
	 * @since 1.7
	 */
	public static function orddd_lite_is_delivery_enabled() {

		$delivery_enabled = wp_cache_get( 'orddd_lite_delivery_enabled' );
		if ( false === $delivery_enabled ) {
			global $woocommerce;
			$delivery_enabled            = 'yes';
			$fields_for_virtual_product  = get_option( 'orddd_lite_no_fields_for_virtual_product' );
			$fields_for_featured_product = get_option( 'orddd_lite_no_fields_for_featured_product' );

			$orddd_lite_enable_delivery_date = get_option( 'orddd_lite_enable_delivery_date' );
			if ( 'on' !== $orddd_lite_enable_delivery_date ) {
				return 'no';
			}
			if ( 'on' === $fields_for_virtual_product && 'on' === $fields_for_featured_product ) {
				foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
					$product_id = $values['product_id'];
					$_product   = wc_get_product( $product_id );
					if ( $_product->is_virtual() === false && $_product->is_featured() === false ) {
						$delivery_enabled = 'yes';
						break;
					} else {
						$delivery_enabled = 'no';
					}
				}
			} elseif ( 'on' === $fields_for_virtual_product && 'on' !== $fields_for_featured_product ) {
				foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
					$_product = $values['data'];
					if ( $_product->is_virtual() === false ) {
						$delivery_enabled = 'yes';
						break;
					} else {
						$delivery_enabled = 'no';
					}
				}
			} elseif ( 'on' !== $fields_for_virtual_product && 'on' === $fields_for_featured_product ) {
				foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
					$product_id = $values['product_id'];
					$_product   = wc_get_product( $product_id );
					if ( $_product->is_featured() === false ) {
						$delivery_enabled = 'yes';
						break;
					} else {
						$delivery_enabled = 'no';
					}
				}
			} else {
				$delivery_enabled = 'yes';
			}
			wp_cache_set( 'orddd_lite_delivery_enabled', $delivery_enabled );
		}
		return $delivery_enabled;
	}

	/**
	 * This function returns the Order Delivery Date Lite plugin version number.
	 *
	 * @return string Version of the plugin
	 * @since 3.3
	 */
	public static function orddd_get_version() {

		$plugin_version = wp_cache_get( 'orddd_lite_get_version' );
		if ( false === $plugin_version ) {

			$orddd_plugin_dir  = dirname( dirname( __FILE__ ) );
			$orddd_plugin_dir .= '/order-delivery-date-for-woocommerce/order_delivery_date.php';

			$plugin_data = get_file_data( $orddd_plugin_dir, array( 'Version' => 'Version' ) );
			if ( ! empty( $plugin_data['Version'] ) ) {
				$plugin_version = $plugin_data['Version'];
			}
			wp_cache_set( 'orddd_lite_get_version', $plugin_version );
		}
		return $plugin_version;
	}

	/**
	 * This function returns the plugin url
	 *
	 * @return string Base URL of the plugin
	 * @since 3.3
	 */
	public static function orddd_get_plugin_url() {
		return plugins_url() . '/order-delivery-date-for-woocommerce/';
	}

	/**
	 * Returns between days from a start date till end date
	 *
	 * @param string $from_date Start date of the range.
	 * @param string $to_date End date of the range.
	 * @return array Dates between the start and the end date.
	 * @since 3.9
	 */
	public static function orddd_lite_get_betweendays( $from_date, $to_date ) {
		$days[]             = $from_date;
		$fromdate_timestamp = strtotime( $from_date );
		$todate_timestamp   = strtotime( $to_date );
		if ( $fromdate_timestamp !== $todate_timestamp ) {
			while ( $fromdate_timestamp < $todate_timestamp ) {
				$from_date          = gmdate( 'd-n-Y', strtotime( '+1 day', strtotime( $from_date ) ) );
				$fromdate_timestamp = $fromdate_timestamp + 86400;
				$days[]             = $from_date;
			}
		}
		return $days;
	}

	/**
	 * Return the selected time format under Appearance link.
	 *
	 * @return string Time format.
	 * @since 3.11.0
	 */
	public static function orddd_lite_get_time_format() {
		$time_format_to_show = 'H:i';
		$time_format         = get_option( 'orddd_lite_delivery_time_format' );
		if ( '1' === $time_format ) {
			$time_format_to_show = 'h:i A';
		}
		return $time_format_to_show;
	}

	/**
	 * Returns time slot for an order
	 *
	 * @param  int $order_id Order ID.
	 * @return string Time slot for an order
	 * @since 3.11.0
	 */
	public static function orddd_get_order_timeslot( $order_id ) {
		$order_time_slot     = '';
		$time_format_to_show = self::orddd_lite_get_time_format();

		$data  = Orddd_Lite_Common::get_order_meta( $order_id );
		$order = new WC_Order( $order_id );
		$items = $order->get_items();

		global $typenow;
		$field_label = '' !== get_option( 'orddd_lite_delivery_timeslot_field_label' ) ? get_option( 'orddd_lite_delivery_timeslot_field_label' ) : 'Time Slot';

		if ( isset( $data['_orddd_time_slot'] ) && '' !== $data['_orddd_time_slot'] ) {
			$order_time_slot = $data['_orddd_time_slot'][0];
		} elseif ( isset( $data[ $field_label ] ) && array_key_exists( $field_label, $data ) ) {
			if ( isset( $data[ $field_label ][0] ) && 'select' !== $data[ $field_label ][0] && '' !== $data[ $field_label ][0] ) {
				if ( false != strpos( $data[ $field_label ][0], 'Possible' ) ) { //phpcs:ignore
					$order_time_slot = $data[ $field_label ][0];
				} else {
					$order_time_slot = $data[ $field_label ][0];
				}
			}
		}

		if ( '' !== $order_time_slot && __( 'As Soon As Possible.', 'order-delivery-date' ) !== $order_time_slot ) {
			$time_slot_arr = explode( ' - ', $order_time_slot );
			$from_time     = date( $time_format_to_show, strtotime( $time_slot_arr[0] ) ); //phpcs:ignore

			if ( isset( $time_slot_arr[1] ) ) {
				$to_time         = date( $time_format_to_show, strtotime( $time_slot_arr[1] ) ); //phpcs:ignore
				$order_time_slot = $from_time . ' - ' . $to_time;
			} else {
				$order_time_slot = $from_time;
			}
		}

		return $order_time_slot;
	}

	/**
	 * Checks if all the added specific dates are past dates or not.
	 *
	 * @globals resource $wpdb WordPress object
	 * @globals array $orddd_lite_weekdays Weekdays array
	 *
	 * @param string $time_slot_for_order Already added time slot if accessed from edit order page.
	 *
	 * @return array All time slots for the selected delivery date
	 * @since 3.11.0
	 */
	public static function orddd_lite_get_timeslot_display( $time_slot_for_order ) {
		global $wpdb, $orddd_lite_weekdays;
		$gmt = false;
		if ( has_filter( 'orddd_gmt_calculations' ) ) {
			$gmt = apply_filters( 'orddd_gmt_calculations', '' );
		}

		$current_time = current_time( 'timestamp', $gmt ); //phpcs:ignore

		$asap_option             = false;
		$all_timeslots           = array();
		$timeslot_lockout        = array();
		$selected_date_timeslots = array();
		$current_weekday         = '';
		$delivery_date           = '';
		$today                   = '';
		$ordd_mindate            = '';
		$days_in_hour            = 0;
		$current_day             = date( 'j-n-Y', $current_time ); //phpcs:ignore
		$current_date            = '';
		$time_format_to_show     = self::orddd_lite_get_time_format();
		$holidays_str            = '';
		$lockout_str             = '';

		if ( isset( $_POST['holidays_str'] ) ) { //phpcs:ignore
			$holidays_str = $_POST['holidays_str']; //phpcs:ignore
		}

		if ( isset( $_POST['lockout_str'] ) ) { //phpcs:ignore
			$lockout_str = $_POST['lockout_str']; //phpcs:ignore
		}

		if ( isset( $_POST['current_date'] ) ) { //phpcs:ignore
			$current_date    = $_POST['current_date']; //phpcs:ignore
			$current_weekday = date( 'w', strtotime( $current_date ) ); //phpcs:ignore
			$delivery_date   = date( 'n-j-Y', strtotime( $current_date ) ); //phpcs:ignore
		}

		$min_hour_in_sec = '' !== get_option( 'orddd_lite_minimumOrderDays' ) ? get_option( 'orddd_lite_minimumOrderDays' ) * 60 * 60 : 0;
		$min_date_array  = orddd_lite_common::get_min_date( $min_hour_in_sec, $holidays_str, $lockout_str );

		if ( 'checked' === get_option( 'orddd_lite_time_slot_asap' ) ) {
			$asap_option = true;
		}

		if ( isset( $_POST ['current_date_to_check'] ) && $_POST ['current_date_to_check'] == $current_day && $min_hour_in_sec != '' && $min_hour_in_sec != 0 ) { //phpcs:ignore
			$today = date( 'Y-m-d G:i', $current_time ); //phpcs:ignore
		} else {
			if ( isset( $_POST ['current_date_to_check'] ) && $min_hour_in_sec != '' && $min_hour_in_sec != 0 ) { //phpcs:ignore
				$date_arr = explode( '-', $_POST ['current_date_to_check'] ); //phpcs:ignore
				$today    = date( 'Y-m-d', strtotime( $date_arr[2] . '-' . $date_arr[1] . '-' . $date_arr[0] ) ); //phpcs:ignore
			} else {
				$today = date( 'Y-m-d', $current_time ); //phpcs:ignore
			}
		}

		$today_current = new DateTime( $today );

		if ( isset( $_POST ['current_date'] ) && $_POST ['current_date'] == $current_day && 0 == $min_hour_in_sec ) { //phpcs:ignore
			$last_slot = date( 'G:i', $current_time ); //phpcs:ignore
		} else {
			$last_slot = $min_date_array['min_hour'] . ':' . $min_date_array['min_minute'];
		}

		$ordd_date_two = $min_date_array['min_date'] . ' ' . $last_slot;
		$ordd_date_two = date( 'Y-m-d G:i', strtotime( $ordd_date_two ) ); //phpcs:ignore
		$today_last    = new DateTime( $ordd_date_two );
		$difference    = $today_last->diff( $today_current );

		if ( $difference->days > 0 ) {
			$days_in_hour  = $difference->h + ( $difference->days * 24 );
			$difference->h = $days_in_hour;
		}

		if ( $difference->i > 0 ) {
			$min_in_hour              = $difference->h + ( $difference->i / 60 );
			$diff_min_hour_in_seconds = $min_in_hour * 60 * 60;
		} else {
			$diff_min_hour_in_seconds = $difference->h * 60 * 60;
		}

		$min_hour_in_sec = $diff_min_hour_in_seconds > $min_hour_in_sec ? ( $diff_min_hour_in_seconds ) : $min_hour_in_sec;

		$time_slots_to_show_timestamp = array( 'NA' => __( 'No time slots are available', 'order-delivery-date' ) );
		if ( $asap_option ) {
			$time_slots_to_show_timestamp['asap'] = __( 'As Soon As Possible', 'order-delivery-date' );
		}

		$blocked_timeslots     = self::orddd_lite_get_disabled_timeslot();
		$time_slots_to_disable = self::get_timeslot_to_disable( $current_weekday, $delivery_date, $blocked_timeslots );
		$all_timeslots         = self::orddd_get_timeslots( $current_date, $current_weekday, $delivery_date );
		$timeslot_lockout      = self::orddd_get_timeslot_lockout();

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

		if ( array_key_exists( 'orddd_lite_weekday_' . $current_weekday, $all_timeslots ) || array_key_exists( 'all', $all_timeslots ) ) {
			if ( array_key_exists( 'orddd_lite_weekday_' . $current_weekday, $all_timeslots ) ) {
				$selected_date_timeslots = array_merge( $selected_date_timeslots, $all_timeslots[ 'orddd_lite_weekday_' . $current_weekday ] );
			}
			if ( array_key_exists( 'all', $all_timeslots ) ) {
				$selected_date_timeslots = array_merge( $selected_date_timeslots, $all_timeslots['all'] );
			}
		}

		$dmy = date( 'd' ) . ' ' . date( 'M' ) . ' ' . date( 'Y' ); //phpcs:ignore

		foreach ( $selected_date_timeslots as $key => $lockout ) {

			$include_time_slot = false;
			$time_slot_locked  = false;
			$time_arr          = explode( ' - ', $key );
			$tstamp_from       = strtotime( $dmy . ' ' . $time_arr[0] );
			$tstamp_to         = '';
			if ( isset( $time_arr[1] ) ) {
				$tstamp_to = strtotime( $dmy . ' ' . $time_arr[1] );
			}

			$key = self::orddd_lite_change_time_slot_format( $key, $time_format_to_show );

			if ( '' !== $lockout && '0' != $lockout && ':' !== $lockout ) { //phpcs:ignore
				if ( isset( $timeslot_lockout[ $current_date ][ $key ] ) && $timeslot_lockout[ $current_date ][ $key ] >= $lockout ) {
					// if it comes here, then it means the time slot for the selected date is full.
					if ( ( '' !== $time_slot_for_order && $key === $time_slot_for_order ) ) {
						if ( ! in_array( $key, $time_slots_to_disable, true ) ) {
							$include_time_slot = true;
						}
					}
					$time_slot_locked = true;
				}
			}

			if ( ! in_array( $key, $time_slots_to_disable ) && ! $time_slot_locked ) { //phpcs:ignore
				$date  = $current_date . ' ' . $time_arr[0];
				$date3 = new DateTime( $date );

				if ( version_compare( phpversion(), '5.3.0', '>' ) ) {
					$difference = $date3->diff( $today_current );
				} else {
					$difference = self::dateTimeDiff( $date3, $today_current );
				}

				if ( $difference->days > 0 ) {
					$days_in_hour  = $difference->h + ( $difference->days * 24 );
					$difference->h = $days_in_hour;
				}

				if ( $difference->i > 0 ) {
					$min_in_hour          = $difference->h + ( $difference->i / 60 );
					$diff_hour_in_seconds = $min_in_hour * 60 * 60;
				} else {
					$diff_hour_in_seconds = $difference->h * 60 * 60;
				}

				if ( 0 == $difference->invert || $diff_hour_in_seconds < $min_hour_in_sec ) { //phpcs:ignore
					$include_time_slot = false;
				} else {
					$include_time_slot = true;
				}
			}

			if ( $include_time_slot ) {
				$time_slots_to_show_timestamp[ $key ] = $tstamp_from;
			}
		}
		return $time_slots_to_show_timestamp;
	}

	/**
	 * Get the first available date after MDT is applied.
	 *
	 * @param string $delivery_time_seconds Minimum Delivery Time.
	 * @param string $holidays_str Holidays.
	 * @param string $lockout_str Lockout dates.
	 * @return array
	 */
	public static function get_min_date( $delivery_time_seconds, $holidays_str, $lockout_str ) {
		global $orddd_lite_weekdays;
		$gmt = false;
		if ( has_filter( 'orddd_gmt_calculations' ) ) {
			$gmt = apply_filters( 'orddd_gmt_calculations', '' );
		}
		$current_time = current_time( 'timestamp', $gmt ); //phpcs:ignore

		$min_hour                   = 0;
		$min_minute                 = 0;
		$current_date               = date( 'j-n-Y', $current_time ); //phpcs:ignore
		$current_time_check         = strtotime( $current_date );
		$date_to_check              = date( 'n-j-Y', $current_time ); //phpcs:ignore
		$current_hour               = date( 'H', $current_time ); //phpcs:ignore
		$current_minute             = date( 'i', $current_time ); //phpcs:ignore
		$current_weekday            = date( 'w', $current_time ); //phpcs:ignore
		$current_date_time          = strtotime( $current_date );
		$current_date_time_to_check = strtotime( $current_date );
		$current_weekday_to_check   = date( 'w', $current_time ); //phpcs:ignore

		$weekdays_to_check       = array();
		$is_all_disable_weekdays = true;
		$holidays                = array();

		$holidays_arr = explode( ',', $holidays_str );
		foreach ( $holidays_arr as $hkey => $hval ) {
			$hval           = str_replace( '"', '', $hval );
			$hval           = str_replace( '\\', '', $hval );
			$holidays_arr_1 = explode( ':', $hval );
			if ( isset( $holidays_arr_1[1] ) ) {
				$holidays[] = $holidays_arr_1[1];
			}
		}

		// Global Settings.
		$delivery_dates_arr = array();
		foreach ( $orddd_lite_weekdays as $n => $day_name ) {
			$weekdays_to_check[ $n ] = get_option( $n );
			if ( 'checked' === $weekdays_to_check[ $n ] ) {
				$is_all_disable_weekdays = false;
			}
		}

		$orddd_min_hours_for_holidays = 'no';
		if ( has_filter( 'orddd_to_calculate_minimum_hours_for_holidays' ) ) {
			$orddd_min_hours_for_holidays = apply_filters( 'orddd_to_calculate_minimum_hours_for_holidays', $orddd_min_hours_for_holidays );
		}

		if ( ! $is_all_disable_weekdays && ( ( in_array( $date_to_check, $holidays, true ) && 'yes' !== $orddd_min_hours_for_holidays ) ) ) {
			$current_time = strtotime( $current_date );
		} elseif ( $is_all_disable_weekdays ) {
			$current_time = strtotime( $current_date );
		}

		$calculate_min = false;

		if ( 0 != $delivery_time_seconds && '' != $delivery_time_seconds ) { //phpcs:ignore
			$calculate_min = true;
		}
		$apply_disabled_weekdays = get_option( 'orddd_lite_calculate_min_time_disabled_days' );
		// Min Date calculation.
		if ( $calculate_min ) {
			$cut_off_timestamp = $current_time + $delivery_time_seconds;
			$cut_off_date      = date( 'd-m-Y', $cut_off_timestamp ); //phpcs:ignore
			$cut_off_date_time = strtotime( $cut_off_date );

			for ( $i = $current_weekday; $current_time_check <= $cut_off_date_time; $i++ ) {
				if ( $i >= 0 ) {
					$day = 'orddd_lite_weekday_' . $current_weekday;

					$weekday_disabled = 'no';
					if ( '' === get_option( $day ) ) {
						$weekday_disabled = 'yes';
					}

					if ( 'on' === $apply_disabled_weekdays ) {
						$weekday_disabled = 'no';
					}

					if ( 'yes' === $weekday_disabled && ! $is_all_disable_weekdays ) {
						$current_date_time_to_check = strtotime( '+1 day', $current_date_time_to_check );
						$current_weekday_to_check   = date( 'w', $current_date_time_to_check ); //phpcs:ignore

						$cut_off_date_time = strtotime( '+1 day', $cut_off_date_time );
						$cut_off_timestamp = strtotime( '+1 day', $cut_off_timestamp );

						$current_time_check = strtotime( '+1 day', $current_time_check );
						$current_weekday    = date( 'w', $current_time_check ); //phpcs:ignore
					} else {
						if ( $current_time_check <= $cut_off_date_time ) {
							$m = date( 'n', $current_time_check ); //phpcs:ignore
							$d = date( 'j', $current_time_check ); //phpcs:ignore
							$y = date( 'Y', $current_time_check ); //phpcs:ignore

							$orddd_disable_for_holidays = 'no';
							if ( has_filter( 'orddd_to_calculate_minimum_hours_for_holidays' ) ) {
								$orddd_disable_for_holidays = apply_filters( 'orddd_to_calculate_minimum_hours_for_holidays', $orddd_disable_for_holidays );
							}

							if ( 'yes' !== $orddd_disable_for_holidays && in_array( $m . '-' . $d . '-' . $y, $holidays, true ) ) {
								$cut_off_date_time = strtotime( '+1 day', $cut_off_date_time );
								$cut_off_timestamp = strtotime( '+1 day', $cut_off_timestamp );
							}

							$current_time_check = strtotime( '+1 day', $current_time_check );
							$current_weekday    = date( 'w', $current_time_check ); //phpcs:ignore
						}
					}
				}
			}

			$min_date              = date( 'j-n-Y', $cut_off_date_time ); //phpcs:ignore
			$min_hour              = date( 'H', $cut_off_timestamp ); //phpcs:ignore
			$min_minute            = date( 'i', $cut_off_timestamp ); //phpcs:ignore
			$current_date_to_check = date( 'j-n-Y', $current_date_time_to_check ); //phpcs:ignore
		} else {
			$min_date              = date( 'j-n-Y', $current_time ); //phpcs:ignore
			$current_date_to_check = $current_date;
		}

		return array(
			'min_date'              => $min_date,
			'min_hour'              => $min_hour,
			'min_minute'            => $min_minute,
			'current_date_to_check' => $current_date_to_check,
		);
	}

	/**
	 * Converts timeslot from one format to another. If no format is specified, it will convert
	 * timeslot to 24 hour format. We convert all timeslots to 24 hour format so they can be
	 * compared properly for their lockout values.
	 *
	 * @param string $timeslot Timeslot to format, example: 05:00 PM - 06:00 PM.
	 * @param string $timeslot_format Timeslot format, default = H:i, or else it can be: h:i A.
	 * @return string Returns updated timeslot in the new format.
	 *
	 * @since 3.11.0
	 */
	public static function orddd_lite_change_time_slot_format( $timeslot, $timeslot_format = 'H:i' ) {
		$timeslot_new    = '';
		$dmy             = date( 'd' ) . ' ' . date( 'M' ) . ' ' . date( 'Y' ); //phpcs:ignore
		$time_arr        = explode( ' - ', $timeslot );
		$tstamp_from     = strtotime( $dmy . ' ' . $time_arr[0] );
		$start_time_slot = date( $timeslot_format, $tstamp_from ); //phpcs:ignore
		$tstamp_to       = '';
		$end_time_slot   = '';

		if ( isset( $time_arr[1] ) ) {
			$tstamp_to     = strtotime( $dmy . ' ' . $time_arr[1] );
			$end_time_slot = date( $timeslot_format, $tstamp_to ); //phpcs:ignore
		}

		if ( '' !== $end_time_slot ) {
			$timeslot_new = $start_time_slot . ' - ' . $end_time_slot;
		} else {
			$timeslot_new = $start_time_slot;
		}

		return $timeslot_new;
	}

	/**
	 * Return all the time slots disabled for a date/weekday
	 *
	 * @param string $current_weekday Weekday of the selected delivery date.
	 * @param string $delivery_date Selected delivery date.
	 * @param array  $disable_days All the disabled time slots.
	 *
	 * @return array
	 * @since 3.11.0
	 */
	public static function get_timeslot_to_disable( $current_weekday, $delivery_date, $disable_days ) {
		$time_slots_to_disable = array();
		if ( array_key_exists( 'orddd_lite_weekday_' . $current_weekday, $disable_days ) ) {
			foreach ( $disable_days[ 'orddd_lite_weekday_' . $current_weekday ] as $dw_key => $dw_value ) {
				$time_slots_to_disable[] = $dw_value;
			}
		}

		if ( array_key_exists( 'all', $disable_days ) ) {
			foreach ( $disable_days['all'] as $all_key => $all_value ) {
				$time_slots_to_disable[] = $all_value;
			}
		}

		if ( array_key_exists( $delivery_date, $disable_days ) ) {
			foreach ( $disable_days[ $delivery_date ] as $date_key => $date_value ) {
				$time_slots_to_disable[] = $date_value;
			}
		}
		return $time_slots_to_disable;
	}

	/**
	 * Return all the time slots that are blocked or disabled from the admin interface
	 *
	 * @return array
	 *
	 * @since 3.11.0
	 */
	public static function orddd_lite_get_disabled_timeslot() {
		$disable_days = array();

		$time_format_to_show = self::orddd_lite_get_time_format();

		$existing_timeslots_str = get_option( 'orddd_lite_disable_time_slot_log' );
		$existing_timeslots_arr = array();
		if ( 'null' == $existing_timeslots_str || '' == $existing_timeslots_str || '{}' == $existing_timeslots_str || '[]' == $existing_timeslots_str ) { //phpcs:ignore
			$existing_timeslots_arr = array();
		} else {
			$existing_timeslots_arr = json_decode( $existing_timeslots_str );
		}

		if ( is_array( $existing_timeslots_arr ) && count( $existing_timeslots_arr ) > 0 ) {
			foreach ( $existing_timeslots_arr as $k => $v ) {
				if ( isset( $v->dtv ) && 'dates' === $v->dtv ) {
					$date_explode  = explode( '-', $v->dd );
					$date_to_check = date( 'n-j-Y', gmmktime( 0, 0, 0, $date_explode[0], $date_explode[1], $date_explode[2] ) ); //phpcs:ignore
				} else {
					$date_to_check = $v->dd;
				}
				$time_slots = json_decode( $v->ts );
				foreach ( $time_slots as $time_key => $time_value ) {
					$time_slot_arr = explode( ' - ', $time_value );
					$from_time     = date( $time_format_to_show, strtotime( $time_slot_arr[0] ) ); //phpcs:ignore
					if ( isset( $time_slot_arr[1] ) ) {
						$to_time       = date( $time_format_to_show, strtotime( $time_slot_arr[1] ) ); //phpcs:ignore
						$time_slot_val = $from_time . ' - ' . $to_time;
					} else {
						$time_slot_val = $from_time;
					}
					$disable_days[ $date_to_check ][] = $time_slot_val;
				}
			}
		}
		return $disable_days;
	}


	/**
	 * Return all the time slots added for a day/date
	 *
	 * @globals resource $wpdb WordPress object
	 *
	 * @param string $current_date Selected date.
	 * @param int    $current_weekday Weekday of current selected date.
	 * @param string $delivery_date Selected date in n-j-Y format.
	 *
	 * @return array
	 *
	 * @since
	 */
	public static function orddd_get_timeslots( $current_date, $current_weekday, $delivery_date ) {
		global $wpdb;

		$arr1               = array();
		$time_slots         = array();
		$all_time_slots     = array();
		$delivery_dates_str = '';
		$min_lockout        = 0;

		$time_format_for_lockout = 'H:i';
		$time_format_to_show     = self::orddd_lite_get_time_format();

		$delivery_dates_arr = array();
		$temp_arr           = array();

		$existing_timeslots_str = get_option( 'orddd_lite_delivery_time_slot_log' );
		$existing_timeslots_arr = json_decode( $existing_timeslots_str );
		if ( isset( $existing_timeslots_arr ) ) {
			foreach ( $existing_timeslots_arr as $k => $v ) {
				$from_time = $v->fh . ':' . $v->fm;
				$ft        = date( $time_format_for_lockout, strtotime( $from_time ) ); //phpcs:ignore
				if ( 0 != $v->th || ( 00 == $v->th && 0 != $v->tm ) ) { //phpcs:ignore
					$to_time = $v->th . ':' . $v->tm;
					$tt      = date( $time_format_for_lockout, strtotime( $to_time ) ); //phpcs:ignore
					$key     = $ft . ' - ' . $tt;
				} else {
					$key = $ft;
				}

				if ( gettype( json_decode( $v->dd ) ) === 'array' && count( json_decode( $v->dd ) ) > 0 ) {
					$dd = json_decode( $v->dd );
					foreach ( $dd as $dkey => $dval ) {
						if ( 'orddd_lite_weekday_' . $current_weekday === $dval || 'all' === $dval ) {
							$arr1[ $dval ][ $key ] = $v->lockout;
						}
					}
				} else {
					if ( 'orddd_lite_weekday_' . $current_weekday === $v->dd || 'all' === $v->dd ) {
						$arr1[ $v->dd ][ $key ] = $v->lockout;
					}
				}
			}
		}

		return $arr1;
	}

	/**
	 * Return the number of orders placed for a time slot for a date
	 *
	 * @globals resource $wpdb WordPress object
	 *
	 * @return array
	 *
	 * @since 3.11.0
	 */
	public static function orddd_get_timeslot_lockout() {
		global $wpdb;
		$arr2                = array();
		$time_format_to_show = self::orddd_lite_get_time_format();

		$lockout_time     = get_option( 'orddd_lite_lockout_time_slot' );
		$lockout_time_arr = json_decode( $lockout_time );
		if ( is_array( $lockout_time_arr ) && count( $lockout_time_arr ) > 0 ) {
			foreach ( $lockout_time_arr as $k => $v ) {
				// add the timeslot in the array with the set time format.
				$v->t                   = self::orddd_lite_change_time_slot_format( $v->t, $time_format_to_show );
				$arr2[ $v->d ][ $v->t ] = $v->o;
			}
		}

		return $arr2;
	}

	/**
	 * Return time slot charges added for a time slot
	 *
	 * @globals resource $wpdb WordPress object
	 *
	 * @param string $time_slot Time slot.
	 * @param mixed  $current_date Selected date.
	 *
	 * @return string
	 * @since 3.11.0
	 */
	public static function orddd_lite_get_timeslot_charges( $time_slot, $current_date = false ) {
		$timeslot_charges            = 0;
		$currency_symbol             = get_woocommerce_currency_symbol();
		$time_format_to_show         = self::orddd_lite_get_time_format();
		$time_slot_charges_lable_str = '';

		$existing_timeslots_str = get_option( 'orddd_lite_delivery_time_slot_log' );
		$existing_timeslots_arr = json_decode( $existing_timeslots_str );

		if ( isset( $existing_timeslots_arr ) ) {
			foreach ( $existing_timeslots_arr as $k => $v ) {
				$from_time = $v->fh . ':' . $v->fm;
				$ft        = date( $time_format_to_show, strtotime( $from_time ) ); //phpcs:ignore
				if ( 0 != $v->th ) { //phpcs:ignore
					$to_time = $v->th . ':' . $v->tm;
					$tt      = date( $time_format_to_show, strtotime( $to_time ) ); //phpcs:ignore
					$key     = $ft . ' - ' . $tt;
				} else {
					$key = $ft;
				}

				$date_to_check    = date( 'n-j-Y', strtotime( $current_date ) ); //phpcs:ignore
				$current_weekday  = date( 'w', strtotime( $current_date ) ); //phpcs:ignore
				$selected_weekday = 'orddd_lite_weekday_' . $current_weekday;

				// Check for Multiple values of specific dates or weekdays and fetch the time slot charges
				// and labels for them.
				if ( gettype( json_decode( $v->dd ) ) === 'array' && count( json_decode( $v->dd ) ) > 0 ) {
					$dd = json_decode( $v->dd );

					if ( is_array( $dd ) && count( $dd ) > 0 ) {
						foreach ( $dd as $dkey => $dval ) {
							if ( $time_slot === $key && ( $dval === $selected_weekday || 'all' === $dval ) ) {
								$timeslot_charges = $v->additional_charges;
								if ( '' === $v->additional_charges_label ) {
									$time_slot_charges_lable_str = 'Time Slot Charges';
								} else {
									$time_slot_charges_lable_str = $v->additional_charges_label;
								}
							}
						}
					}
				} elseif ( $time_slot === $key && ( $v->dd === $selected_weekday || 'all' === $v->dd ) ) {
					$timeslot_charges = $v->additional_charges;
					if ( '' === $v->additional_charges_label ) {
						$time_slot_charges_lable_str = 'Time Slot Charges';
					} else {
						$time_slot_charges_lable_str = $v->additional_charges_label;
					}
				}
			}
		}

		$timeslot_charges_str = '';
		if ( '' !== $timeslot_charges ) {
			// Format the time slot charges as per the WooCommerce Decimal Seperator, Thousand Seperator and Number of Decimals.
			$timeslot_charges = number_format(
				$timeslot_charges,
				wc_get_price_decimals(),
				wc_get_price_decimal_separator(),
				wc_get_price_thousand_separator()
			);

			if ( '' !== $time_slot_charges_lable_str ) {
				$timeslot_charges_str = $time_slot_charges_lable_str;
			}
			if ( 0 != $timeslot_charges ) { //phpcs:ignore
				$timeslot_charges_str .= ': ' . $currency_symbol . '' . $timeslot_charges;
			}
		}

		return $timeslot_charges_str;
	}

	/**
	 * Return the dates where all timeslots have been booked.
	 *
	 * @return array
	 */
	public static function orddd_lite_get_booked_timeslot_days() {
		$gmt = false;
		if ( has_filter( 'orddd_gmt_calculations' ) ) {
			$gmt = apply_filters( 'orddd_gmt_calculations', '' );
		}

		$current_time           = current_time( 'timestamp', $gmt ); //phpcs:ignore
		$current_date           = date( 'j-n-Y', $current_time );//phpcs:ignore
		$booked_dates           = array();
		$existing_timeslots_arr = json_decode( get_option( 'orddd_lite_delivery_time_slot_log' ) );
		$time_format_to_show    = self::orddd_lite_get_time_format();
		$delivery_days          = array();
		$lockout_arr            = array();

		foreach ( $existing_timeslots_arr as $k => $v ) {
			$from_time = date( $time_format_to_show, strtotime( $v->fh . ':' . trim( $v->fm, ' ' ) ) ); //phpcs:ignore
			$to_time   = date( $time_format_to_show, strtotime( $v->th . ':' . trim( $v->tm, ' ' ) ) ); //phpcs:ignore
			if ( '' != $v->th && '00' != $v->th && '' != $v->tm && '00' != $v->tm ) { //phpcs:ignore
				$timeslot = $from_time . ' - ' . $to_time;
			} else {
				$timeslot = $from_time;
			}
			$dd = json_decode( $v->dd );

			if ( is_array( $dd ) && count( $dd ) > 0 ) {
				foreach ( $dd as $dkey => $dval ) {
					if ( isset( $delivery_days[ $dval ] ) ) {
						$delivery_days[ $dval ][ $timeslot ] = $v->lockout;
					} else {
						$delivery_days[ $dval ] = array( $timeslot => $v->lockout );
					}
				}
			}
		}

		$timeslot_dates = self::orddd_lite_get_timeslot_availability();
		$lockout_arr    = self::orddd_lite_get_booked_timeslots( $timeslot_dates, $current_date );

		// For time slot lockout the date format saved in the database is j-n-Y. And the date format we add in the booked days array is n-j-Y.
		foreach ( $lockout_arr as $date => $timeslot ) {
			$lockout_date_split = explode( '-', $date );
			$date_lockout       = $lockout_date_split[1] . '-' . $lockout_date_split[0] . '-' . $lockout_date_split[2];

			if ( is_array( $delivery_days ) && count( $delivery_days ) > 0 ) {
				$weekday = date( 'w', strtotime( $date ) ); //phpcs:ignore
				if ( isset( $delivery_days[ 'orddd_lite_weekday_' . $weekday ] ) && is_array( $lockout_arr[ $date ] ) && count( $lockout_arr[ $date ] ) >= count( $delivery_days[ 'orddd_lite_weekday_' . $weekday ] ) ) {
					array_push( $booked_dates, $date_lockout );
				}
			}
		}

		return $booked_dates;
	}

	/**
	 * Get the general settings timeslots availability.
	 *
	 * @return array
	 */
	public static function orddd_lite_get_timeslot_availability() {
		$gmt = false;
		if ( has_filter( 'orddd_gmt_calculations' ) ) {
			$gmt = apply_filters( 'orddd_gmt_calculations', '' );
		}

		$current_time          = current_time( 'timestamp', $gmt ); //phpcs:ignore
		$lockout_timeslots_arr = array();
		$current_date          = date( 'j-n-Y', $current_time ); //phpcs:ignore
		$current_weekday       = date( 'w', $current_time ); //phpcs:ignore
		$time_format_to_show   = self::orddd_lite_get_time_format();

		$lockout_timeslots_days = get_option( 'orddd_lite_lockout_time_slot' );
		if ( '' != $lockout_timeslots_days && '{}' != $lockout_timeslots_days && '[]' != $lockout_timeslots_days && 'null' != $lockout_timeslots_days ) { //phpcs:ignore
			$lockout_timeslots_arr = json_decode( get_option( 'orddd_lite_lockout_time_slot' ) );
		}

		$existing_timeslots_arr = json_decode( get_option( 'orddd_lite_delivery_time_slot_log' ) );

		$dates         = array();
		$delivery_days = array();

		foreach ( $existing_timeslots_arr as $k => $v ) {
			$from_time = date( $time_format_to_show, strtotime( $v->fh . ':' . trim( $v->fm, ' ' ) ) ); //phpcs:ignore
			$to_time   = date( $time_format_to_show, strtotime( $v->th . ':' . trim( $v->tm, ' ' ) ) ); //phpcs:ignore
			if ( '' != $v->th && '00' != $v->th && '' != $v->tm && '00' != $v->tm ) { //phpcs:ignore
				$timeslot = $from_time . ' - ' . $to_time;
			} else {
				$timeslot = $from_time;
			}
			$dd = json_decode( $v->dd );

			if ( is_array( $dd ) && count( $dd ) > 0 ) {
				foreach ( $dd as $dkey => $dval ) {
					if ( isset( $delivery_days[ $dval ] ) ) {
						$delivery_days[ $dval ][ $timeslot ] = $v->lockout;
					} else {
						$delivery_days[ $dval ] = array( $timeslot => $v->lockout );
					}
				}
			}
		}

		// Get all time slots for current date so that we can determine the past time slots as well.
		foreach ( $delivery_days as $key => $value ) {
			if ( 'orddd_lite_weekday_' . $current_weekday === $key && isset( $delivery_days[ 'orddd_lite_weekday_' . $current_weekday ] ) ) {
				$dates[ $current_date ] = $delivery_days[ 'orddd_lite_weekday_' . $current_weekday ];
			}
		}

		foreach ( $lockout_timeslots_arr as $k => $v ) {
			$date    = date( 'j-n-Y', strtotime( $v->d ) ); //phpcs:ignore
			$weekday = date( 'w', strtotime( $v->d ) ); //phpcs:ignore

			$lockout_date_split = explode( '-', $date );
			$date_lockout       = $lockout_date_split[1] . '-' . $lockout_date_split[0] . '-' . $lockout_date_split[2];
			$date_lockout_time  = strtotime( $date_lockout );
			$timeslot           = self::orddd_lite_change_time_slot_format( $v->t, $time_format_to_show );

			if ( isset( $delivery_days[ 'orddd_lite_weekday_' . $weekday ][ $timeslot ] ) ) {
				if ( '' !== $delivery_days[ 'orddd_lite_weekday_' . $weekday ][ $timeslot ] &&
					 '0' != $delivery_days[ 'orddd_lite_weekday_' . $weekday ][ $timeslot ] ) { //phpcs:ignore
					$dates[ $date ][ $timeslot ] = $delivery_days[ 'orddd_lite_weekday_' . $weekday ][ $timeslot ] - $v->o;
				} else {
					$dates[ $date ][ $timeslot ] = '';
				}
			}
		}
		return $dates;
	}

	/**
	 *
	 * Gets the lockout dates by checking certain conditions
	 *
	 * @param array $timeslot_dates Array of available time slots along with their dates.
	 * @param array $current_date   Current / Today's date.
	 *
	 * @return array $lockout_arr
	 *
	 * @since
	 */
	public static function orddd_lite_get_booked_timeslots( $timeslot_dates, $current_date ) {
		$lockout_arr = array();
		foreach ( $timeslot_dates as $key => $value ) {
			foreach ( $value as $time => $available_lockout ) {

				// Below we are fetching the timestamp of the first time slot for current date in $check_time variable.
				$time_arr   = explode( ' - ', $time );
				$check_time = strtotime( $current_date . ' ' . $time_arr[0] );

				if ( '' !== $available_lockout && $available_lockout <= 0 ) {
					if ( isset( $lockout_arr[ $key ] ) ) {
						array_push( $lockout_arr[ $key ], $time );
					} else {
						$lockout_arr[ $key ] = array( $time );
					}
				}
			}
		}
		return $lockout_arr;
	}

	/**
	 * Compares the timeslots added.
	 *
	 * @param array $a First time slot of the array to compare.
	 * @param array $b Second time slot of the array to compare.
	 * @return bool Return true id the time slot 1 is greater than time slot 2 else false.
	 *
	 * @since 3.11.0
	 */
	public static function orddd_lite_custom_sort( $a, $b ) {
		$tstamp_from_1 = 0;
		$tstamp_from_2 = 0;
		if ( isset( $a->fh ) ) {
			$tstamp_from_1 = strtotime( date( 'd' ) . ' ' . date( 'M' ) . ' ' . date( 'Y' ) . ' ' . $a->fh . ':' . $a->fm ); //phpcs:ignore
			$tstamp_from_2 = strtotime( date( 'd' ) . ' ' . date( 'M' ) . ' ' . date( 'Y' ) . ' ' . $b->fh . ':' . $b->fm ); //phpcs:ignore
		}

		return (int) ( $tstamp_from_1 > $tstamp_from_2 );
	}

	/**
	 * Restoring the deliveries when the order status is changed from cancelled/refunded/failed.
	 *
	 * @hook woocommerce_order_status_changed
	 * @param int    $order_id Order ID.
	 * @param string $old_status Previous status of the order.
	 * @param string $new_status New status of the order.
	 * @since 3.11.0
	 */
	public static function orddd_lite_restore_deliveries( $order_id, $old_status, $new_status ) {
		$old_status_arr = array( 'cancelled', 'refunded', 'trashed', 'failed' );
		if ( in_array( $old_status, $old_status_arr, true ) && ! in_array( $new_status, $old_status_arr, true ) ) {
			$data = Orddd_Lite_Common::get_order_meta( $order_id );

			$time_field_label = '' !== get_option( 'orddd_lite_delivery_timeslot_field_label' ) ? get_option( 'orddd_lite_delivery_timeslot_field_label' ) : 'Time Slot';

			if ( isset( $data['_orddd_lite_timestamp'][0] ) && '' !== $data['_orddd_lite_timestamp'][0] ) {
				$delivery_date = date( 'j-n-Y', $data['_orddd_lite_timestamp'][0] ); //phpcs:ignore
				Orddd_Lite_Process::orddd_lite_update_lockout_days( $delivery_date );
				if ( isset( $data[ $time_field_label ][0] ) && '' !== $data[ $time_field_label ][0] ) {
					$time_slot = $data[ $time_field_label ][0];
					Orddd_Lite_Process::orddd_lite_update_lockout_timeslot( $delivery_date, $time_slot );
				}
			}
		}
	}

	/**
	 * Block the delivery date & time again when the order is restored from the trash
	 *
	 * @hook untrash_post
	 * @param int $post_id Order ID.
	 * @since 3.11.0
	 */
	public static function orddd_lite_untrash_order( $post_id ) {
		if ( self::is_hpos_enabled() ) {
			$order       = wc_get_order( $order_id );
			$post_status = $order->get_status();
			$post_type   = $order->get_type();			
		} else {
			$post_obj    = get_post( $order_id );
			$post_status = $post_status->post_status;
			$post_type   = $post_obj->post_type;
		}
		$status   = array( 'wc-pending', 'wc-cancelled', 'wc-refunded', 'wc-failed' );

		if ( 'shop_order' === $post_type && ( ! in_array( $post_status, $status, true ) ) ) {
			// untrash the delivery dates as well.
			self::orddd_lite_restore_deliveries( $post_id, 'trashed', $post_status );
		}
	}

	/**
	 * Load hidden fields on the checkout page
	 *
	 * @return string
	 * @since 3.16.0
	 */
	public static function orddd_lite_load_hidden_fields() {
		$var  = '';
		$var .= '<input type="hidden" name="h_deliverydate" id="h_deliverydate" value="">';
		return apply_filters( 'orddd_lite_hidden_variables', $var );
	}

	/**
	 * Send the data to JS through localize_script.
	 *
	 * @param string $order_id - Order id.
	 * @return array
	 */
	public static function orddd_lite_localize_data_script( $order_id = '' ) {
		global $orddd_lite_weekdays;
		$orddd_lite_settings = array();
		$additional_data     = array();
		if ( 'on' === get_option( 'orddd_lite_enable_delivery_date' ) ) {
			$var = '';

			$first_day_of_week = '1';
			if ( '' !== get_option( 'orddd_lite_start_of_week' ) ) {
				$first_day_of_week = get_option( 'orddd_lite_start_of_week' );
			}
			$orddd_lite_settings['orddd_first_day_of_week']         = $first_day_of_week;
			$orddd_lite_settings['orddd_lite_delivery_date_format'] = esc_attr( get_option( 'orddd_lite_delivery_date_format' ) );

			$field_note_text = get_option( 'orddd_lite_delivery_date_field_note' );
			$field_note_text = str_replace( array( "\r\n", "\r", "\n" ), '<br/>', $field_note_text );
			if ( strpos( $field_note_text, '"' ) !== false ) {
				$orddd_lite_settings['orddd_lite_field_note'] = esc_attr( $field_note_text );
			} else {
				$orddd_lite_settings['orddd_lite_field_note'] = esc_attr( $field_note_text );
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
					$orddd_lite_settings[ $key ] = $alldays_orddd_lite[ $key ];
				}
			} elseif ( 'No' === $checked ) {
				foreach ( $alldayskeys_orddd_lite as $key ) {
					$orddd_lite_settings[ $key ] = 'checked';
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

			// It will check if the below filter returns true then it will shows the exact number of dates added in `Number of dates to choose` option even if there are holidays present within the date range.
			$is_holiday_exclude = apply_filters( 'orddd_is_holidays_excluded_from_dates_to_choose', false );

			$orddd_lite_settings['orddd_lite_number_of_dates']      = get_option( 'orddd_lite_number_of_dates' );
			$orddd_lite_settings['orddd_lite_date_field_mandatory'] = get_option( 'orddd_lite_date_field_mandatory' );
			$orddd_lite_settings['orddd_lite_number_of_months']     = get_option( 'orddd_lite_number_of_months' );
			$orddd_lite_settings['h_deliverydate']                  = '';
			$orddd_lite_settings['is_holiday_exclude']              = $is_holiday_exclude;

			$orddd_lite_settings['orddd_lite_show_partially_booked_dates'] = get_option( 'orddd_lite_show_partially_booked_dates' );

			$lockout_days_str = '';
			$partial_days_str = '';
			if ( '' !== get_option( 'orddd_lite_show_partially_booked_dates' ) ) {
				$partial_days_arr = array();
				$lockout_days     = get_option( 'orddd_lite_lockout_days' );
				if ( '' !== $lockout_days && '{}' !== $lockout_days && '[]' !== $lockout_days ) {
					$partial_days_arr = json_decode( get_option( 'orddd_lite_lockout_days' ) );
				}
				foreach ( $partial_days_arr as $k => $v ) {
					if ( $v->o < get_option( 'orddd_lite_lockout_date_after_orders' ) ) {
						$partial_days_str .= '"' . $v->d . '",';
					}
				}
			}
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
				$booked_timeslot_days = self::orddd_lite_get_booked_timeslot_days();
				foreach ( $booked_timeslot_days as $booked_day ) {
					$lockout_days_str .= '"' . $booked_day . '",';
				}
			}

			if ( '' !== $lockout_days_str ) {
				$lockout_days_str = substr( $lockout_days_str, 0, strlen( $lockout_days_str ) - 1 );
			}
			$orddd_lite_settings['orddd_lite_lockout_days'] = $lockout_days_str;
			if ( '' !== $partial_days_str ) {
				$partial_days_str = substr( $partial_days_str, 0, strlen( $partial_days_str ) - 1 );
			}
			$orddd_lite_settings['orddd_lite_partial_days'] = $partial_days_str;
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
				$name = str_replace( '"', '&apos;', $v->n );
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
			$min_date_array = self::get_min_date( $delivery_time_seconds, $holidays_str, $lockout_days_str );

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

					if ( 0 === $calculated_difference->invert || $calculated_minimum_delivery_time < $delivery_time_seconds || $calculated_min_date > $date2 ) {
						$min_date_array['min_date'] = date( 'j-n-Y', strtotime( $min_date_array['min_date'] . '+1 day' ) ); //phpcs:ignore
					}
				}
			}
			$orddd_lite_settings['orddd_lite_minimumOrderDays']                   = $min_date_array['min_date'];
			$orddd_lite_settings['orddd_lite_holidays']                           = $holidays_str;
			$orddd_lite_settings['orddd_lite_auto_populate_first_available_date'] = get_option( 'orddd_lite_auto_populate_first_available_date' );
			$orddd_lite_settings['orddd_lite_calculate_min_time_disabled_days']   = get_option( 'orddd_lite_calculate_min_time_disabled_days' );

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
			$orddd_lite_settings['orddd_lite_admin_url'] = $ajax_url;
			$orddd_lite_disable_for_holidays             = 'no';
			if ( has_filter( 'orddd_to_calculate_minimum_hours_for_holidays' ) ) {
				$orddd_lite_disable_for_holidays = apply_filters( 'orddd_to_calculate_minimum_hours_for_holidays', $orddd_lite_disable_for_holidays );
			}
			$orddd_lite_settings['orddd_lite_disable_for_holidays']       = $orddd_lite_disable_for_holidays;
			$orddd_lite_settings['orddd_lite_delivery_date_on_cart_page'] = get_option( 'orddd_lite_delivery_date_on_cart_page' );

			$current_hour   = gmdate( 'H', $current_time );
			$current_minute = gmdate( 'i', $current_time );

			$orddd_lite_settings['orddd_lite_current_day']    = $current_date;
			$orddd_lite_settings['orddd_lite_current_hour']   = $current_hour;
			$orddd_lite_settings['orddd_lite_current_minute'] = $current_minute;

			$orddd_lite_settings['orddd_lite_enable_time_slot']                        = get_option( 'orddd_lite_enable_time_slot' );
			$orddd_lite_settings['orddd_min_date_set']                                 = $min_date_array['min_date'];
			$orddd_lite_settings['orddd_is_cart']                                      = is_cart();
			$orddd_lite_settings['orddd_lite_auto_populate_first_available_time_slot'] = get_option( 'orddd_lite_auto_populate_first_available_time_slot' );
			$orddd_lite_settings['orddd_is_cart_block']                                = has_block( 'woocommerce/checkout' ) || has_block( 'woocommerce/cart' );
			$orddd_lite_settings['is_enable_delivery_date']                            = 'on' === get_option( 'orddd_lite_enable_delivery_date' ) ? true : false;
			$orddd_lite_settings['orddd_lite_time_slot_mandatory']                     = get_option( 'orddd_lite_time_slot_mandatory' );
			$orddd_lite_settings['orddd_lite_delivery_date_field_label']               = get_option( 'orddd_lite_delivery_date_field_label' );
			$orddd_lite_settings['orddd_lite_delivery_timeslot_field_label']           = get_option( 'orddd_lite_delivery_timeslot_field_label' );
			$orddd_lite_settings['orddd_lite_delivery_date_field_placeholder']         = get_option( 'orddd_lite_delivery_date_field_placeholder' );
			return apply_filters( 'orddd_lite_hidden_variables_array', $orddd_lite_settings, $additional_data );
		}
	}

	/**
	 * Returns if HPOS is enabled
	 *
	 * @return bool
	 * @since 3.19.0
	 */
	public static function is_hpos_enabled() {

		if ( version_compare( get_option( 'woocommerce_version' ), '7.1.0' ) < 0 ) {
				return false;
		}

		if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
			return true;
		}

		return false;
	}

	/**
	 * Returns Post meta data
	 *
	 * @param int    $order_id order id.
	 * @param string $key meta key.
	 * @param bool   $single get single or all meta.
	 * @param object $order order object
	 * @return array
	 * @since 3.19.0
	 */
	public static function get_order_meta( $order_id, $key = '', $single = false, $order = null ) {
		$data = array();
		if ( self::is_hpos_enabled() ) {
			if ( ! $order ) {
				$order = wc_get_order( $order_id );
			}
			if ( ! $order ) {
				return $data;
			}
			if ( '' !== $key ) {
				return $order->get_meta( $key );
			}

			$meta_data = $order->get_meta_data();
			$data      = array();
			foreach ( $meta_data  as $key => $meta_value ) {
				$meta_key            = $meta_value->key;
				$data[ $meta_key ][] = $meta_value->value;
			}

		} else {		
			$data  = get_post_meta( $order_id, $key, $single );
		}

		return $data;
	}

	/**
	 * Update Post meta data
	 *
	 * @param int    $order_id order id.
	 * @param string $key meta key.
	 * @param bool   $single get single or all meta.
	 * @param object $order order object
	 * @return array
	 * @since 3.19.0
	 */
	public static function update_order_meta( $order_id, $key, $value = false, $order = null, $save = false ) {
		$data = array();
		if ( self::is_hpos_enabled() ) {
			if ( ! $order ) {
				$order = wc_get_order( $order_id );
			}
			if ( ! $order ) {
				return false;
			}

			$order->update_meta_data( $key, $value );

			if ( $save ) {
				$order->save();
			}

		} else {
			$data  = update_post_meta( $order_id, $key, $value );
		}

		return $data;
	}

	/**
	 * Delivery date column orderby.
	 *
	 * @param string $args  - Query string.
	 *
	 * @hook request
	 * @since 3.19.0
	 */
	public static function modify_query_for_sort_by_date( $args ) {

		if ( isset( $_GET['page'] ) && 'wc-orders' === $_GET['page'] && ( ( isset( $_GET['orderby'] ) && '_orddd_lite_timestamp' === $_GET['orderby'] ) || ( ! isset( $_GET['orderby'] ) && 'on' === get_option( 'orddd_show_column_on_orders_page_check' ) && 'on' === get_option( 'orddd_enable_default_sorting_of_column' ) ) ) ) {// phpcs:ignore WordPress.Security.NonceVerification
			$order              = ( isset( $_GET['order'] ) && 'asc' === $_GET['order'] ) ? 'ASC' : 'DESC';// phpcs:ignore WordPress.Security.NonceVerification
			$meta_query         = array(
				'relation'                    => 'OR',
				'orddd_lite_timestamp_clause' => array(
					'key'     => '_orddd_lite_timestamp',
					'compare' => 'EXISTS',
				),
				'orddd_lite_timeslot_clause'  => array(
					'key'     => '_orddd_timestamp',
					'compare' => 'EXISTS',
				),
				'orddd_lite_timeslot_clause'  => array(
					'key'     => '_orddd_lite_timestamp',
					'compare' => 'NOT EXISTS',
				),
			);
			$args['orderby']    = 'meta_value_num';
			$args['meta_query'] = $meta_query;// phpcs:ignore
			$args['order']      = $order;
			return $args;
		}
		return $args;
	}

}


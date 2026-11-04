<?php
/**
 * Order Delivery Date Lite – Settings REST API endpoints.
 *
 * GET  /orddd/v1/settings                  — all settings groups
 * POST /orddd/v1/settings                  — save by key (default_schedule | additional)
 *
 * GET    /orddd/v1/settings/block-time-slot — list blocked time slots
 * POST   /orddd/v1/settings/block-time-slot — add blocked time slot
 * DELETE /orddd/v1/settings/block-time-slot — remove blocked time slot
 *
 * GET    /orddd/v1/settings/disable-shipping — Pro only (returns 403)
 * POST   /orddd/v1/settings/disable-shipping — Pro only (returns 403)
 * DELETE /orddd/v1/settings/disable-shipping — Pro only (returns 403)
 *
 * @package Order-Delivery-Date-Lite-for-WooCommerce/API
 * @since   4.7.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ORDDD_Settings_API' ) ) {

	/**
	 * Class ORDDD_Settings_API
	 */
	class ORDDD_Settings_API extends ORDDD_API {

		/**
		 * REST base for this resource.
		 *
		 * @var string
		 */
		protected $rest_base = 'settings';

		/**
		 * Constructor — registers routes on rest_api_init.
		 */
		public function __construct() {
			add_action( 'rest_api_init', array( $this, 'register_routes' ) );
		}

		/**
		 * Register REST routes.
		 */
		public function register_routes() {
			$ns   = self::endpoint();
			$base = $this->rest_base;

			// GET / POST /orddd/v1/settings
			register_rest_route(
				$ns,
				'/' . $base,
				array(
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, 'get_all_settings' ),
						'permission_callback' => array( $this, 'check_admin_permission' ),
					),
					array(
						'methods'             => WP_REST_Server::CREATABLE,
						'callback'            => array( $this, 'update_settings' ),
						'permission_callback' => array( $this, 'check_admin_permission' ),
					),
				)
			);

			// GET / POST / DELETE /orddd/v1/settings/block-time-slot
			register_rest_route(
				$ns,
				'/' . $base . '/block-time-slot',
				array(
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, 'get_blocked_time_slots' ),
						'permission_callback' => array( $this, 'check_admin_permission' ),
					),
					array(
						'methods'             => WP_REST_Server::CREATABLE,
						'callback'            => array( $this, 'add_blocked_time_slot' ),
						'permission_callback' => array( $this, 'check_admin_permission' ),
					),
					array(
						'methods'             => WP_REST_Server::DELETABLE,
						'callback'            => array( $this, 'delete_blocked_time_slot' ),
						'permission_callback' => array( $this, 'check_admin_permission' ),
					),
				)
			);

			// GET / POST / DELETE /orddd/v1/settings/disable-shipping — Pro only
			register_rest_route(
				$ns,
				'/' . $base . '/disable-shipping',
				array(
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, 'pro_only_response' ),
						'permission_callback' => array( $this, 'check_admin_permission' ),
					),
					array(
						'methods'             => WP_REST_Server::CREATABLE,
						'callback'            => array( $this, 'pro_only_response' ),
						'permission_callback' => array( $this, 'check_admin_permission' ),
					),
					array(
						'methods'             => WP_REST_Server::DELETABLE,
						'callback'            => array( $this, 'pro_only_response' ),
						'permission_callback' => array( $this, 'check_admin_permission' ),
					),
				)
			);
		}

		// =========================================================================
		// GET /orddd/v1/settings
		// =========================================================================

		/**
		 * Return all settings groups.
		 *
		 * @return WP_REST_Response
		 */
		public function get_all_settings() {
			return self::return_response(
				array(
					'default_schedule' => $this->get_default_schedule_data(),
					'block_time_slot'  => $this->get_blocked_time_slots_data(),
					'additional'       => $this->get_additional_data(),
					// Pro-only groups: return null so React knows to show upgrade prompts.
					'business_days'    => null,
					'common_schedule'  => null,
					'order_type'       => null,
					'pickup_locations' => null,
				)
			);
		}

		// =========================================================================
		// POST /orddd/v1/settings
		// =========================================================================

		/**
		 * Save one settings group by key.
		 *
		 * @param WP_REST_Request $request Request object.
		 * @return WP_REST_Response|WP_Error
		 */
		public function update_settings( WP_REST_Request $request ) {
			$key = sanitize_text_field( $request->get_param( 'key' ) ?? '' );

			$lite_handlers = array(
				'default_schedule' => array( $this, 'save_default_schedule' ),
				'additional'       => array( $this, 'save_additional' ),
			);

			$pro_only_keys = array( 'business_days', 'common_schedule', 'order_type', 'pickup_locations' );

			if ( in_array( $key, $pro_only_keys, true ) ) {
				return $this->pro_only_response();
			}

			if ( ! isset( $lite_handlers[ $key ] ) ) {
				return new WP_Error(
					'invalid_key',
					__( 'Invalid or missing key. Accepted: default_schedule, additional.', 'order-delivery-date' ),
					array( 'status' => 400 )
				);
			}

			return call_user_func( $lite_handlers[ $key ], $request );
		}

		// =========================================================================
		// Savers
		// =========================================================================

		/**
		 * Save Default Schedule settings (delivery days + time slots).
		 *
		 * @param WP_REST_Request $request Request object.
		 * @return WP_REST_Response
		 */
		private function save_default_schedule( WP_REST_Request $request ) {
			global $orddd_lite_weekdays;

			$body = $request->get_json_params() ?: array();

			// Scalar delivery-date options.
			$scalar_fields = array(
				'orddd_lite_enable_delivery_date',
				'orddd_lite_minimumOrderDays',
				'orddd_lite_number_of_dates',
				'orddd_lite_date_field_mandatory',
				'orddd_lite_lockout_date_after_orders',
				'orddd_lite_calculate_min_time_disabled_days',
				'orddd_lite_enable_time_slot',
				'orddd_lite_time_slot_mandatory',
				'orddd_lite_time_slot_asap',
				'orddd_lite_auto_populate_first_available_time_slot',
			);

			foreach ( $scalar_fields as $field ) {
				if ( array_key_exists( $field, $body ) ) {
					update_option( $field, sanitize_text_field( $body[ $field ] ) );
				}
			}

			// orddd_lockout_date_quantity_based has no _lite_ prefix.
			if ( array_key_exists( 'orddd_lockout_date_quantity_based', $body ) ) {
				update_option( 'orddd_lockout_date_quantity_based', sanitize_text_field( $body['orddd_lockout_date_quantity_based'] ) );
			}

			// Weekday checkboxes.
			if ( array_key_exists( 'weekdays', $body ) && is_array( $body['weekdays'] ) ) {
				for ( $i = 0; $i <= 6; $i++ ) {
					$key = 'orddd_lite_weekday_' . $i;
					if ( array_key_exists( $key, $body['weekdays'] ) ) {
						update_option( $key, sanitize_text_field( $body['weekdays'][ $key ] ) );
					}
				}
			}

			// Time slots JSON array.
			if ( array_key_exists( 'time_slots', $body ) && is_array( $body['time_slots'] ) ) {
				$slot_log = array();
				foreach ( $body['time_slots'] as $slot ) {
					if ( ! is_array( $slot ) ) {
						continue;
					}
					$fh     = (int) ( $slot['fh'] ?? 0 );
					$fm     = (int) ( $slot['fm'] ?? 0 );
					$th     = (int) ( $slot['th'] ?? 0 );
					$tm     = (int) ( $slot['tm'] ?? 0 );
					$dd_raw = $slot['dd'] ?? '[""]';
					if ( is_array( $dd_raw ) ) {
						$dd_arr = $dd_raw;
					} else {
						$dd_arr = json_decode( (string) $dd_raw, true );
						if ( ! is_array( $dd_arr ) ) {
							$dd_arr = array( (string) $dd_raw );
						}
					}
					$dd_arr = array_map( 'sanitize_text_field', $dd_arr );
					$slot_log[] = array(
						'tv'                       => in_array( $slot['tv'] ?? '', array( 'weekdays', 'dates' ), true ) ? $slot['tv'] : 'weekdays',
						'dd'                       => wp_json_encode( $dd_arr ),
						'fh'                       => gmdate( 'G', gmmktime( $fh, $fm, 0, gmdate( 'm' ), gmdate( 'd' ), gmdate( 'Y' ) ) ),
						'fm'                       => gmdate( 'i', gmmktime( $fh, $fm, 0, gmdate( 'm' ), gmdate( 'd' ), gmdate( 'Y' ) ) ),
						'th'                       => gmdate( 'G', gmmktime( $th, $tm, 0, gmdate( 'm' ), gmdate( 'd' ), gmdate( 'Y' ) ) ),
						'tm'                       => gmdate( 'i', gmmktime( $th, $tm, 0, gmdate( 'm' ), gmdate( 'd' ), gmdate( 'Y' ) ) ),
						'lockout'                  => sanitize_text_field( $slot['lockout']                  ?? '' ),
						'additional_charges'       => sanitize_text_field( $slot['additional_charges']       ?? '' ),
						'charges_type'             => sanitize_text_field( $slot['charges_type']             ?? 'fixed' ),
						'additional_charges_label' => sanitize_text_field( $slot['additional_charges_label'] ?? '' ),
					);
				}
				update_option( 'orddd_lite_delivery_time_slot_log', wp_json_encode( $slot_log ) );
			}

			return self::return_response(
				array(
					'status'  => 'success',
					'message' => __( 'Default schedule settings saved successfully.', 'order-delivery-date' ),
					'data'    => array( 'default_schedule' => $this->get_default_schedule_data() ),
				)
			);
		}

		/**
		 * Save Additional Settings.
		 *
		 * @param WP_REST_Request $request Request object.
		 * @return WP_REST_Response
		 */
		private function save_additional( WP_REST_Request $request ) {
			$body   = $request->get_json_params() ?: array();
			$fields = array(
				'orddd_lite_show_column_on_orders_page_check',
				'orddd_lite_enable_default_sorting_of_column',
				'orddd_lite_auto_populate_first_available_date',
				'orddd_lite_show_filter_on_orders_page_check',
				'orddd_lite_no_fields_for_virtual_product',
				'orddd_lite_no_fields_for_featured_product',
				'orddd_lite_show_partially_booked_dates',
			);

			foreach ( $fields as $field ) {
				if ( array_key_exists( $field, $body ) ) {
					update_option( $field, sanitize_text_field( $body[ $field ] ) );
				}
			}

			return self::return_response(
				array(
					'status'  => 'success',
					'message' => __( 'Additional settings saved successfully.', 'order-delivery-date' ),
					'data'    => array( 'additional' => $this->get_additional_data() ),
				)
			);
		}

		// =========================================================================
		// GET /orddd/v1/settings/block-time-slot
		// =========================================================================

		/**
		 * Return all blocked time slot entries + available slots list.
		 *
		 * @return WP_REST_Response
		 */
		public function get_blocked_time_slots() {
			// Build available slots from the time slot log.
			$slot_log = get_option( 'orddd_lite_delivery_time_slot_log', '' );
			$slots    = array();

			if ( ! in_array( $slot_log, array( '', 'null', '[]', '{}', false ), true ) ) {
				$items = json_decode( $slot_log, true ) ?? array();
				foreach ( $items as $item ) {
					$fh    = $item['fh'] ?? '0';
					$fm    = $item['fm'] ?? '00';
					$th    = $item['th'] ?? '0';
					$tm    = $item['tm'] ?? '00';
					$label = sprintf( '%02d:%02d - %02d:%02d', (int) $fh, (int) $fm, (int) $th, (int) $tm );
					if ( ! in_array( $label, $slots, true ) ) {
						$slots[] = $label;
					}
				}
			}

			return self::return_response(
				array(
					'blocked_slots'   => $this->get_blocked_time_slots_data(),
					'available_slots' => $slots,
				)
			);
		}

		/**
		 * Add a blocked time slot entry.
		 *
		 * Body params:
		 *   block_time_slot_for  "weekdays" | "dates"  (required)
		 *   delivery_days        comma-separated weekday keys or Y-m-d dates (required)
		 *   timeslot             comma-separated time slot strings (required)
		 *
		 * @param WP_REST_Request $request Request object.
		 * @return WP_REST_Response|WP_Error
		 */
		public function add_blocked_time_slot( WP_REST_Request $request ) {
			$block_for     = sanitize_text_field( $request->get_param( 'block_time_slot_for' ) ?? '' );
			$delivery_days = sanitize_text_field( $request->get_param( 'delivery_days' ) ?? '' );
			$timeslot_raw  = sanitize_text_field( $request->get_param( 'timeslot' ) ?? '' );

			if ( ! in_array( $block_for, array( 'weekdays', 'dates' ), true ) ) {
				return new WP_Error(
					'invalid_block_for',
					__( 'block_time_slot_for must be "weekdays" or "dates".', 'order-delivery-date' ),
					array( 'status' => 400 )
				);
			}

			if ( empty( $delivery_days ) || empty( $timeslot_raw ) ) {
				return new WP_Error(
					'missing_params',
					__( 'delivery_days and timeslot are required.', 'order-delivery-date' ),
					array( 'status' => 400 )
				);
			}

			$days        = array_map( 'trim', explode( ',', $delivery_days ) );
			$selected_ts = wp_json_encode( array_map( 'trim', explode( ',', $timeslot_raw ) ) );

			$raw      = get_option( 'orddd_lite_disable_time_slot_log', '' );
			$existing = in_array( $raw, array( '', 'null', '[]', '{}', false ), true )
				? array()
				: ( json_decode( $raw, true ) ?? array() );

			foreach ( $days as $day ) {
				if ( 'dates' === $block_for ) {
					$ts = strtotime( $day );
					if ( false === $ts ) {
						continue;
					}
					$delivery_date = gmdate( 'n-j-Y', $ts );
				} else {
					$delivery_date = $day;
				}

				$found = false;
				foreach ( $existing as $item ) {
					if ( ( $item['dtv'] ?? '' ) === $block_for && ( $item['dd'] ?? '' ) === $delivery_date && ( $item['ts'] ?? '' ) === $selected_ts ) {
						$found = true;
						break;
					}
				}

				if ( ! $found ) {
					$existing[] = array(
						'dtv' => $block_for,
						'dd'  => $delivery_date,
						'ts'  => $selected_ts,
					);
				}
			}

			update_option( 'orddd_lite_disable_time_slot_log', wp_json_encode( $existing ) );

			return self::return_response(
				array(
					'status'  => 'success',
					'message' => __( 'Time slot blocked successfully.', 'order-delivery-date' ),
					'data'    => $this->get_blocked_time_slots_data(),
				)
			);
		}

		/**
		 * Remove a blocked time slot entry.
		 *
		 * Params (single): delivery_days, timeslot
		 * Params (bulk):   key="bulk", timeslot=JSON{"timeslots":[["HH:MM - HH:MM","day"],...]}
		 *
		 * @param WP_REST_Request $request Request object.
		 * @return WP_REST_Response|WP_Error
		 */
		public function delete_blocked_time_slot( WP_REST_Request $request ) {
			$key      = sanitize_text_field( $request->get_param( 'key' ) ?? 'single' );
			$days_raw = sanitize_text_field( $request->get_param( 'delivery_days' ) ?? '' );
			$slot_raw = $request->get_param( 'timeslot' ) ?? '';

			$raw          = get_option( 'orddd_lite_disable_time_slot_log', '' );
			$timeslot_arr = in_array( $raw, array( '', 'null', '[]', '{}', false ), true )
				? array()
				: ( json_decode( $raw, true ) ?? array() );

			if ( 'bulk' === $key ) {
				$bulk = json_decode( wp_unslash( $slot_raw ), true );
				if ( ! isset( $bulk['timeslots'] ) ) {
					return new WP_Error(
						'invalid_bulk_payload',
						__( 'For bulk deletion, timeslot must be JSON with a "timeslots" array.', 'order-delivery-date' ),
						array( 'status' => 400 )
					);
				}
				foreach ( $bulk['timeslots'] as $entry ) {
					if ( ! isset( $entry[0], $entry[1] ) ) {
						continue;
					}
					$t_slot = $entry[0];
					if ( strpos( $entry[1], '-' ) !== false ) {
						$ts    = strtotime( $entry[1] );
						$t_day = ( false !== $ts ) ? gmdate( 'n-j-Y', $ts ) : $entry[1];
					} else {
						$t_day = $entry[1];
					}
					$timeslot_arr = $this->unblock_time_slot( $timeslot_arr, $t_slot, $t_day );
				}
			} else {
				if ( empty( $days_raw ) ) {
					return new WP_Error(
						'missing_delivery_days',
						__( 'delivery_days is required.', 'order-delivery-date' ),
						array( 'status' => 400 )
					);
				}
				if ( strpos( $days_raw, '-' ) !== false ) {
					$ts           = strtotime( $days_raw );
					$day_to_check = ( false !== $ts ) ? gmdate( 'n-j-Y', $ts ) : $days_raw;
				} else {
					$day_to_check = $days_raw;
				}
				$timeslot_arr = $this->unblock_time_slot( $timeslot_arr, sanitize_text_field( $slot_raw ), $day_to_check );
			}

			update_option( 'orddd_lite_disable_time_slot_log', wp_json_encode( $timeslot_arr ) );

			return self::return_response(
				array(
					'status'  => 'success',
					'message' => __( 'Time slot unblocked successfully.', 'order-delivery-date' ),
					'data'    => $this->get_blocked_time_slots_data(),
				)
			);
		}

		// =========================================================================
		// Pro-only stub
		// =========================================================================

		/**
		 * Returns a 403 "Pro only" response for endpoints not available in Lite.
		 *
		 * @return WP_Error
		 */
		public function pro_only_response() {
			return new WP_Error(
				'pro_only',
				__( 'This feature is available in the Pro version. Upgrade at tychesoftwares.com.', 'order-delivery-date' ),
				array( 'status' => 403 )
			);
		}

		// =========================================================================
		// Private data helpers
		// =========================================================================

		/**
		 * Default schedule data.
		 *
		 * @return array
		 */
		private function get_default_schedule_data() {
			$weekdays = array();
			for ( $i = 0; $i <= 6; $i++ ) {
				$key              = 'orddd_lite_weekday_' . $i;
				$weekdays[ $key ] = get_option( $key, '' );
			}

			$slot_log   = get_option( 'orddd_lite_delivery_time_slot_log', '' );
			$time_slots = array();
			if ( ! in_array( $slot_log, array( '', 'null', '[]', '{}', false ), true ) ) {
				$time_slots = json_decode( $slot_log, true ) ?? array();
			}

			return array(
				'orddd_lite_enable_delivery_date'                  => get_option( 'orddd_lite_enable_delivery_date', '' ),
				'weekdays'                                         => $weekdays,
				'orddd_lite_minimumOrderDays'                      => get_option( 'orddd_lite_minimumOrderDays', '' ),
				'orddd_lite_number_of_dates'                       => get_option( 'orddd_lite_number_of_dates', '' ),
				'orddd_lite_date_field_mandatory'                  => get_option( 'orddd_lite_date_field_mandatory', '' ),
				'orddd_lite_lockout_date_after_orders'             => get_option( 'orddd_lite_lockout_date_after_orders', '' ),
				'orddd_lockout_date_quantity_based'                => get_option( 'orddd_lockout_date_quantity_based', '' ),
				'orddd_lite_calculate_min_time_disabled_days'      => get_option( 'orddd_lite_calculate_min_time_disabled_days', '' ),
				'orddd_lite_enable_time_slot'                      => get_option( 'orddd_lite_enable_time_slot', '' ),
				'orddd_lite_time_slot_mandatory'                   => get_option( 'orddd_lite_time_slot_mandatory', '' ),
				'orddd_lite_time_slot_asap'                        => get_option( 'orddd_lite_time_slot_asap', '' ),
				'orddd_lite_auto_populate_first_available_time_slot' => get_option( 'orddd_lite_auto_populate_first_available_time_slot', '' ),
				'time_slots'                                       => $time_slots,
			);
		}

		/**
		 * Additional settings data.
		 *
		 * @return array
		 */
		private function get_additional_data() {
			return array(
				'orddd_lite_show_column_on_orders_page_check'  => get_option( 'orddd_lite_show_column_on_orders_page_check', '' ),
				'orddd_lite_enable_default_sorting_of_column'  => get_option( 'orddd_lite_enable_default_sorting_of_column', '' ),
				'orddd_lite_auto_populate_first_available_date' => get_option( 'orddd_lite_auto_populate_first_available_date', '' ),
				'orddd_lite_show_filter_on_orders_page_check'  => get_option( 'orddd_lite_show_filter_on_orders_page_check', '' ),
				'orddd_lite_no_fields_for_virtual_product'     => get_option( 'orddd_lite_no_fields_for_virtual_product', '' ),
				'orddd_lite_no_fields_for_featured_product'    => get_option( 'orddd_lite_no_fields_for_featured_product', '' ),
				'orddd_lite_show_partially_booked_dates'       => get_option( 'orddd_lite_show_partially_booked_dates', '' ),
			);
		}

		/**
		 * Blocked time slots data (normalised list).
		 *
		 * @return array
		 */
		private function get_blocked_time_slots_data() {
			$raw = get_option( 'orddd_lite_disable_time_slot_log', '' );

			if ( in_array( $raw, array( '', 'null', '[]', '{}', false ), true ) ) {
				return array();
			}

			$items  = json_decode( $raw, true ) ?? array();
			$result = array();

			foreach ( $items as $item ) {
				$dtv        = $item['dtv'] ?? '';
				$dd         = $item['dd'] ?? '';
				$ts_decoded = json_decode( $item['ts'] ?? '[]', true ) ?? array();

				if ( 'dates' === $dtv ) {
					$parts = explode( '-', $dd );
					if ( 3 === count( $parts ) ) {
						$delivery_date = gmdate( 'Y-m-d', gmmktime( 0, 0, 0, (int) $parts[0], (int) $parts[1], (int) $parts[2] ) );
					} else {
						$delivery_date = $dd;
					}
				} else {
					$delivery_date = $dd;
				}

				foreach ( $ts_decoded as $time_value ) {
					$result[] = array(
						'block_time_slot_for' => $dtv,
						'delivery_days'       => $delivery_date,
						'time_slot'           => $time_value,
					);
				}
			}

			return $result;
		}

		/**
		 * Remove a single time-slot entry from the array.
		 *
		 * @param array  $arr      Existing blocked slots.
		 * @param string $slot     Time slot string to remove.
		 * @param string $day      Day key or date string.
		 * @return array
		 */
		private function unblock_time_slot( array $arr, $slot, $day ) {
			$updated = array();

			foreach ( $arr as $item ) {
				$dd         = $item['dd'] ?? '';
				$ts_decoded = json_decode( $item['ts'] ?? '[]', true ) ?? array();

				if ( $dd === $day ) {
					$ts_decoded = array_values( array_filter( $ts_decoded, function( $t ) use ( $slot ) {
						return trim( $t ) !== trim( $slot );
					} ) );

					if ( empty( $ts_decoded ) ) {
						continue; // Remove entire row if no slots left.
					}
					$item['ts'] = wp_json_encode( $ts_decoded );
				}

				$updated[] = $item;
			}

			return $updated;
		}
	}

	new ORDDD_Settings_API();
}

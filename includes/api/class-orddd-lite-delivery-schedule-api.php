<?php
/**
 * Order Delivery Date Lite – Delivery Schedule REST API endpoint.
 *
 * GET  /orddd/v1/delivery_schedule       – list (single Default Schedule)
 * GET  /orddd/v1/delivery_schedule/0     – single Default Schedule
 * PUT  /orddd/v1/delivery_schedule/0     – save Default Schedule settings
 *
 * All keys in the response / request body use the actual Lite option names
 * so the JS form and PHP layer share the same key identifiers.
 *
 * @package Order-Delivery-Date-Lite-for-WooCommerce/API
 * @since   4.7.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ORDDD_Lite_Delivery_Schedule_API' ) ) {

	/**
	 * Class ORDDD_Lite_Delivery_Schedule_API
	 */
	class ORDDD_Lite_Delivery_Schedule_API extends ORDDD_API {

		/**
		 * REST base for this resource.
		 *
		 * @var string
		 */
		protected $rest_base = 'delivery_schedule';

		/**
		 * Simple text options — key is both the JS form key and the WP option name.
		 *
		 * @var array
		 */
		private static $text_options = array(
			'orddd_lite_enable_delivery_date',
			'orddd_lite_delivery_checkout_options',
			'orddd_lite_lockout_date_after_orders',
			'orddd_lite_date_field_mandatory',
			'orddd_lite_number_of_dates',
			'orddd_lite_delivery_date_field_label',
			'orddd_lite_minimumOrderDays',
			'orddd_lite_calculate_min_time_disabled_days',
			'orddd_lite_enable_time_slot',
			'orddd_lite_time_slot_mandatory',
			'orddd_lite_time_slot_asap',
			'orddd_lite_auto_populate_first_available_time_slot',
			'orddd_lite_delivery_timeslot_field_label',
		);

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

			// GET  /orddd/v1/delivery_schedule
			// POST /orddd/v1/delivery_schedule
			register_rest_route(
				$ns,
				'/' . $base,
				array(
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, 'get_schedules' ),
						'permission_callback' => array( $this, 'check_admin_permission' ),
					),
					array(
						'methods'             => WP_REST_Server::CREATABLE,
						'callback'            => array( $this, 'create_schedule' ),
						'permission_callback' => array( $this, 'check_admin_permission' ),
					),
				)
			);

			// GET  /orddd/v1/delivery_schedule/0
			// PUT  /orddd/v1/delivery_schedule/0
			register_rest_route(
				$ns,
				'/' . $base . '/(?P<id>\d+)',
				array(
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, 'get_schedule' ),
						'permission_callback' => array( $this, 'check_admin_permission' ),
						'args'                => array(
							'id' => array( 'validate_callback' => 'rest_validate_request_arg' ),
						),
					),
					array(
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => array( $this, 'update_schedule' ),
						'permission_callback' => array( $this, 'check_admin_permission' ),
						'args'                => array(
							'id' => array( 'validate_callback' => 'rest_validate_request_arg' ),
						),
					),
				)
			);
		}

		/**
		 * Build the Default Schedule data object.
		 * Keys use the actual Lite option names so the JS form can use them directly.
		 *
		 * @return array
		 */
		private function build_default_schedule() {
			$weekdays = array();
			for ( $i = 0; $i <= 6; $i++ ) {
				$key              = 'orddd_lite_weekday_' . $i;
				$weekdays[ $key ] = array(
					'enable'     => get_option( $key, '' ),
					'max_orders' => '',
				);
			}

			$slot_log   = get_option( 'orddd_lite_delivery_time_slot_log', '' );
			$time_slots = array();
			if ( ! in_array( $slot_log, array( '', 'null', '[]', '{}', false ), true ) ) {
				$raw = json_decode( $slot_log, true ) ?? array();
				foreach ( $raw as $slot ) {
					// Support both old storage format {tv,dd,fh,fm,th,tm,lockout,...}
					// and new display format {time_slot_for,delivery_days,time,...}.
					if ( isset( $slot['tv'] ) ) {
						$days = array();
						if ( ! empty( $slot['dd'] ) ) {
							$decoded = json_decode( $slot['dd'], true );
							$days    = is_array( $decoded ) ? $decoded : array( $slot['dd'] );
						}
						if ( empty( $days ) ) {
							$days = array( '' );
						}
						$fh        = str_pad( (string) ( $slot['fh'] ?? '0' ), 2, '0', STR_PAD_LEFT );
						$fm        = str_pad( trim( (string) ( $slot['fm'] ?? '0' ) ), 2, '0', STR_PAD_LEFT );
						$th        = str_pad( (string) ( $slot['th'] ?? '0' ), 2, '0', STR_PAD_LEFT );
						$tm        = str_pad( trim( (string) ( $slot['tm'] ?? '0' ) ), 2, '0', STR_PAD_LEFT );
						foreach ( $days as $day ) {
							$time_slots[] = array(
								'time_slot_for' => $slot['tv'] ?? 'weekdays',
								'delivery_days' => $day,
								'time'          => array(
									'from_time' => $fh . ':' . $fm,
									'to_time'   => $th . ':' . $tm,
								),
								'max_orders'    => $slot['lockout']                  ?? '',
								'charges'       => $slot['additional_charges']       ?? '',
								'charges_type'  => $slot['charges_type']             ?? 'fixed',
								'charges_label' => $slot['additional_charges_label'] ?? '',
								'blocked'       => false,
							);
						}
					} else {
						// Already in display format — pass through.
						$time_slots[] = $slot;
					}
				}
			}

			return array(
				'delivery_schedule_id'                => 0,
				'orddd_shipping_settings_status'      => 'on',
				'status'                              => 'on',
				// ── Delivery settings ─────────────────────────────────────────
				'orddd_lite_enable_delivery_date'     => get_option( 'orddd_lite_enable_delivery_date', '' ),
				'orddd_lite_delivery_checkout_options'=> get_option( 'orddd_lite_delivery_checkout_options', 'delivery_calendar' ),
				'orddd_lite_lockout_date_after_orders'=> get_option( 'orddd_lite_lockout_date_after_orders', '' ),
				'orddd_lite_date_field_mandatory'     => get_option( 'orddd_lite_date_field_mandatory', '' ),
				'orddd_lite_number_of_dates'          => get_option( 'orddd_lite_number_of_dates', '10' ),
				'orddd_lite_delivery_date_field_label'=> get_option( 'orddd_lite_delivery_date_field_label', '' ),
				'orddd_lite_delivery_date_field_note' => get_option( 'orddd_lite_delivery_date_field_note', '' ),
				'orddd_lite_minimumOrderDays'                  => get_option( 'orddd_lite_minimumOrderDays', '0' ),
				'orddd_lite_calculate_min_time_disabled_days'  => get_option( 'orddd_lite_calculate_min_time_disabled_days', '' ),
				// ── Time slot settings ────────────────────────────────────────
				'orddd_lite_enable_time_slot'         => get_option( 'orddd_lite_enable_time_slot', '' ),
				'orddd_lite_time_slot_mandatory'      => get_option( 'orddd_lite_time_slot_mandatory', '' ),
				'orddd_lite_time_slot_asap'           => get_option( 'orddd_lite_time_slot_asap', '' ),
				'orddd_lite_delivery_timeslot_field_label' => get_option( 'orddd_lite_delivery_timeslot_field_label', '' ),
				// ── Nested / complex ─────────────────────────────────────────
				'weekdays'                            => $weekdays,
				'time_slots'                          => $time_slots,
				'same_day'                            => array(
					'after_hours'        => '',
					'after_minutes'      => '',
					'additional_charges' => '',
					'charges_type'       => '',
				),
				'next_day'                            => array(
					'after_hours'        => '',
					'after_minutes'      => '',
					'additional_charges' => '',
					'charges_type'       => '',
				),
				'title'                               => array(),
				'schedule_type'                       => 'delivery',
				'appearance'                          => array(
					'time_format' => get_option( 'orddd_lite_delivery_time_format', '1' ),
				),
			);
		}

		/**
		 * GET /orddd/v1/delivery_schedule — returns an array with the Default Schedule.
		 *
		 * @return WP_REST_Response
		 */
		public function get_schedules() {
			return self::return_response( array( $this->build_default_schedule() ) );
		}

		/**
		 * GET /orddd/v1/delivery_schedule/0 — returns the Default Schedule object.
		 *
		 * @param WP_REST_Request $request
		 * @return WP_REST_Response|WP_Error
		 */
		public function get_schedule( $request ) {
			if ( (int) $request['id'] !== 0 ) {
				return new WP_Error( 'not_found', __( 'Schedule not found.', 'order-delivery-date' ), array( 'status' => 404 ) );
			}
			return self::return_response( $this->build_default_schedule() );
		}

		/**
		 * PUT /orddd/v1/delivery_schedule/0 — saves the Default Schedule settings.
		 *
		 * @param WP_REST_Request $request
		 * @return WP_REST_Response|WP_Error
		 */
		public function update_schedule( $request ) {
			if ( (int) $request['id'] !== 0 ) {
				return new WP_Error( 'not_found', __( 'Schedule not found.', 'order-delivery-date' ), array( 'status' => 404 ) );
			}

			$body     = $request->get_json_params();
			$settings = isset( $body['shipping_settings'] ) ? $body['shipping_settings'] : array();

			// ── Simple text options (key = WP option name) ────────────────────────
			foreach ( self::$text_options as $option ) {
				if ( array_key_exists( $option, $settings ) ) {
					update_option( $option, sanitize_text_field( $settings[ $option ] ) );
				}
			}

			// ── Delivery date field note (textarea) ───────────────────────────────
			if ( array_key_exists( 'orddd_lite_delivery_date_field_note', $settings ) ) {
				update_option( 'orddd_lite_delivery_date_field_note', sanitize_textarea_field( $settings['orddd_lite_delivery_date_field_note'] ) );
			}

			// ── Weekdays ──────────────────────────────────────────────────────────
			if ( ! empty( $settings['weekdays'] ) && is_array( $settings['weekdays'] ) ) {
				foreach ( $settings['weekdays'] as $key => $value ) {
					if ( preg_match( '/^orddd_lite_weekday_[0-6]$/', $key ) ) {
						update_option( $key, sanitize_text_field( $value['enable'] ?? '' ) );
					}
				}
			}

			// ── Time slots — convert display format back to storage format ───────────
			if ( array_key_exists( 'time_slots', $settings ) ) {
				$display_slots = is_array( $settings['time_slots'] ) ? $settings['time_slots'] : array();
				$storage_slots = array();
				foreach ( $display_slots as $slot ) {
					$time       = isset( $slot['time'] ) && is_array( $slot['time'] ) ? $slot['time'] : array();
					$from_parts = explode( ':', $time['from_time'] ?? '00:00' );
					$to_parts   = explode( ':', $time['to_time']   ?? '00:00' );
					$storage_slots[] = array(
						'tv'                       => sanitize_text_field( $slot['time_slot_for']  ?? 'weekdays' ),
						'dd'                       => wp_json_encode( array( sanitize_text_field( $slot['delivery_days'] ?? '' ) ) ),
						'fh'                       => sanitize_text_field( $from_parts[0] ?? '00' ),
						'fm'                       => sanitize_text_field( $from_parts[1] ?? '00' ),
						'th'                       => sanitize_text_field( $to_parts[0]   ?? '00' ),
						'tm'                       => sanitize_text_field( $to_parts[1]   ?? '00' ),
						'lockout'                  => sanitize_text_field( $slot['max_orders']    ?? '' ),
						'additional_charges'       => sanitize_text_field( $slot['charges']       ?? '' ),
						'charges_type'             => sanitize_text_field( $slot['charges_type']  ?? 'fixed' ),
						'additional_charges_label' => sanitize_text_field( $slot['charges_label'] ?? '' ),
					);
				}
				update_option( 'orddd_lite_delivery_time_slot_log', wp_json_encode( $storage_slots ) );
			}

			return self::return_response( array( 'success' => true ) );
		}

		/**
		 * POST /orddd/v1/delivery_schedule — creating custom schedules requires Pro.
		 *
		 * @return WP_Error
		 */
		public function create_schedule() {
			return new WP_Error(
				'orddd_pro_required',
				__( 'Creating custom delivery schedules requires Order Delivery Date Pro.', 'order-delivery-date' ),
				array( 'status' => 403 )
			);
		}
	}

	new ORDDD_Lite_Delivery_Schedule_API();
}

<?php
/**
 * Order Delivery Date Lite – Appearance Settings REST API endpoints.
 *
 * GET  /orddd/v1/settings/appearance          — returns field & calendar appearance
 * POST /orddd/v1/settings/appearance          — save by key ("field" | "calendar")
 * GET  /orddd/v1/settings/appearance/field    — field appearance only
 * POST /orddd/v1/settings/appearance/field    — save field appearance
 * GET  /orddd/v1/settings/appearance/calendar — calendar appearance only
 * POST /orddd/v1/settings/appearance/calendar — save calendar appearance
 *
 * @package Order-Delivery-Date-Lite-for-WooCommerce/API
 * @since   4.7.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ORDDD_Lite_Appearance_API' ) ) {

	/**
	 * Class ORDDD_Lite_Appearance_API
	 */
	class ORDDD_Lite_Appearance_API extends ORDDD_Lite_API {

		/**
		 * REST base for this resource.
		 *
		 * @var string
		 */
		protected $rest_base = 'settings/appearance';

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

			// GET / POST /orddd/v1/settings/appearance
			register_rest_route(
				$ns,
				'/' . $base,
				array(
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, 'get_all_appearance' ),
						'permission_callback' => array( $this, 'check_admin_permission' ),
					),
					array(
						'methods'             => WP_REST_Server::CREATABLE,
						'callback'            => array( $this, 'update_appearance' ),
						'permission_callback' => array( $this, 'check_admin_permission' ),
					),
				)
			);

			// GET / POST /orddd/v1/settings/appearance/field
			register_rest_route(
				$ns,
				'/' . $base . '/field',
				array(
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, 'get_field_appearance' ),
						'permission_callback' => array( $this, 'check_admin_permission' ),
					),
					array(
						'methods'             => WP_REST_Server::CREATABLE,
						'callback'            => array( $this, 'save_field_appearance' ),
						'permission_callback' => array( $this, 'check_admin_permission' ),
					),
				)
			);

			// GET / POST /orddd/v1/settings/appearance/calendar
			register_rest_route(
				$ns,
				'/' . $base . '/calendar',
				array(
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, 'get_calendar_appearance' ),
						'permission_callback' => array( $this, 'check_admin_permission' ),
					),
					array(
						'methods'             => WP_REST_Server::CREATABLE,
						'callback'            => array( $this, 'save_calendar_appearance' ),
						'permission_callback' => array( $this, 'check_admin_permission' ),
					),
				)
			);
		}

		// =========================================================================
		// GET /orddd/v1/settings/appearance
		// =========================================================================

		/**
		 * Return both field and calendar appearance settings.
		 *
		 * @return WP_REST_Response
		 */
		public function get_all_appearance() {
			return self::return_response(
				array(
					'field'    => $this->get_field_appearance_data(),
					'calendar' => $this->get_calendar_appearance_data(),
				)
			);
		}

		// =========================================================================
		// POST /orddd/v1/settings/appearance  (route by key)
		// =========================================================================

		/**
		 * Save a specific appearance group routed by the `key` body param.
		 *
		 * @param WP_REST_Request $request Request object.
		 * @return WP_REST_Response|WP_Error
		 */
		public function update_appearance( WP_REST_Request $request ) {
			$key = sanitize_text_field( $request->get_param( 'key' ) ?? '' );

			$handlers = array(
				'field'    => array( $this, 'save_field_appearance' ),
				'calendar' => array( $this, 'save_calendar_appearance' ),
			);

			if ( ! isset( $handlers[ $key ] ) ) {
				return new WP_Error(
					'invalid_key',
					__( 'Invalid or missing key. Accepted: "field", "calendar".', 'order-delivery-date' ),
					array( 'status' => 400 )
				);
			}

			return call_user_func( $handlers[ $key ], $request );
		}

		// =========================================================================
		// GET /orddd/v1/settings/appearance/field
		// =========================================================================

		/**
		 * Return field appearance settings.
		 *
		 * @return WP_REST_Response
		 */
		public function get_field_appearance() {
			return self::return_response( $this->get_field_appearance_data() );
		}

		// =========================================================================
		// GET /orddd/v1/settings/appearance/calendar
		// =========================================================================

		/**
		 * Return calendar appearance settings.
		 *
		 * @return WP_REST_Response
		 */
		public function get_calendar_appearance() {
			return self::return_response( $this->get_calendar_appearance_data() );
		}

		// =========================================================================
		// POST /orddd/v1/settings/appearance/field
		// =========================================================================

		/**
		 * Save field appearance settings.
		 *
		 * @param WP_REST_Request $request Request object.
		 * @return WP_REST_Response
		 */
		public function save_field_appearance( WP_REST_Request $request ) {
			$body         = $request->get_json_params() ?: array();
			$plain_fields = array(
				'orddd_lite_delivery_date_fields_on_checkout_page',
				'orddd_lite_delivery_date_on_cart_page',
				'orddd_lite_delivery_date_field_label',
				'orddd_lite_delivery_timeslot_field_label',
				'orddd_lite_delivery_date_field_placeholder',
				'orddd_lite_auto_populate_first_available_date',
				'orddd_lite_auto_populate_first_available_time_slot',
			);

			foreach ( $plain_fields as $field ) {
				if ( array_key_exists( $field, $body ) ) {
					update_option( $field, sanitize_text_field( $body[ $field ] ) );
				}
			}

			// Allow limited HTML in the field note (mirrors Pro behaviour).
			if ( array_key_exists( 'orddd_lite_delivery_date_field_note', $body ) ) {
				update_option(
					'orddd_lite_delivery_date_field_note',
					wp_filter_post_kses( wp_unslash( $body['orddd_lite_delivery_date_field_note'] ) )
				);
			}

			return self::return_response(
				array(
					'status'  => 'success',
					'message' => __( 'Field appearance settings saved successfully.', 'order-delivery-date' ),
					'data'    => array( 'field' => $this->get_field_appearance_data() ),
				)
			);
		}

		// =========================================================================
		// POST /orddd/v1/settings/appearance/calendar
		// =========================================================================

		/**
		 * Save calendar appearance settings.
		 *
		 * @param WP_REST_Request $request Request object.
		 * @return WP_REST_Response
		 */
		public function save_calendar_appearance( WP_REST_Request $request ) {
			$body         = $request->get_json_params() ?: array();
			$plain_fields = array(
				'orddd_lite_language_selected',
				'orddd_lite_delivery_date_format',
				'orddd_lite_delivery_time_format',
				'orddd_lite_start_of_week',
				'orddd_lite_number_of_months',
				'orddd_lite_calendar_theme',
				'orddd_lite_holiday_color',
				'orddd_lite_available_dates_color',
				'orddd_lite_show_partially_booked_dates',
			);

			foreach ( $plain_fields as $field ) {
				if ( array_key_exists( $field, $body ) ) {
					update_option( $field, sanitize_text_field( $body[ $field ] ) );
				}
			}

			// orddd_booked_color in the request maps to orddd_lite_booked_dates_color in storage.
			if ( array_key_exists( 'orddd_booked_color', $body ) ) {
				update_option( 'orddd_lite_booked_dates_color', sanitize_text_field( $body['orddd_booked_color'] ) );
			}

			return self::return_response(
				array(
					'status'  => 'success',
					'message' => __( 'Calendar appearance settings saved successfully.', 'order-delivery-date' ),
					'data'    => array( 'calendar' => $this->get_calendar_appearance_data() ),
				)
			);
		}

		// =========================================================================
		// Private data helpers
		// =========================================================================

		/**
		 * Field appearance data.
		 *
		 * @return array
		 */
		private function get_field_appearance_data() {
			return array(
				'orddd_lite_delivery_date_fields_on_checkout_page'  => get_option( 'orddd_lite_delivery_date_fields_on_checkout_page' ),
				'orddd_lite_delivery_date_on_cart_page'             => get_option( 'orddd_lite_delivery_date_on_cart_page' ),
				'orddd_lite_delivery_date_field_label'              => get_option( 'orddd_lite_delivery_date_field_label' ),
				'orddd_lite_delivery_timeslot_field_label'          => get_option( 'orddd_lite_delivery_timeslot_field_label' ),
				'orddd_lite_delivery_date_field_placeholder'        => get_option( 'orddd_lite_delivery_date_field_placeholder' ),
				'orddd_lite_delivery_date_field_note'               => get_option( 'orddd_lite_delivery_date_field_note' ),
				'orddd_lite_auto_populate_first_available_date'     => get_option( 'orddd_lite_auto_populate_first_available_date' ),
				'orddd_lite_auto_populate_first_available_time_slot' => get_option( 'orddd_lite_auto_populate_first_available_time_slot' ),
			);
		}

		/**
		 * Calendar appearance data.
		 *
		 * @return array
		 */
		private function get_calendar_appearance_data() {
			return array(
				'orddd_lite_language_selected'    => get_option( 'orddd_lite_language_selected' ),
				'orddd_lite_delivery_date_format' => get_option( 'orddd_lite_delivery_date_format' ),
				'orddd_lite_delivery_time_format' => get_option( 'orddd_lite_delivery_time_format' ),
				'orddd_lite_start_of_week'        => get_option( 'orddd_lite_start_of_week' ),
				'orddd_lite_number_of_months'     => get_option( 'orddd_lite_number_of_months' ),
				'orddd_lite_calendar_theme'       => get_option( 'orddd_lite_calendar_theme' ),
				'orddd_lite_holiday_color'        => get_option( 'orddd_lite_holiday_color' ),
				// Returned as orddd_booked_color to match Pro response shape.
				'orddd_booked_color'                      => get_option( 'orddd_lite_booked_dates_color' ),
				'orddd_lite_available_dates_color'        => get_option( 'orddd_lite_available_dates_color' ),
				'orddd_lite_show_partially_booked_dates'  => get_option( 'orddd_lite_show_partially_booked_dates' ),
			);
		}
	}

	new ORDDD_Lite_Appearance_API();
}

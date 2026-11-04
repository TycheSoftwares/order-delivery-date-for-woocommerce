<?php
/**
 * Order Delivery Date Lite – Holidays REST API endpoints.
 *
 * GET    /orddd/v1/holidays — list all holidays
 * POST   /orddd/v1/holidays — add a holiday
 * DELETE /orddd/v1/holidays — remove a holiday by date
 *
 * Internal storage: orddd_lite_holidays (JSON array, dates in n-j-Y format).
 * The API converts to/from Y-m-d for React.
 *
 * @package Order-Delivery-Date-Lite-for-WooCommerce/API
 * @since   4.7.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ORDDD_Holidays_API' ) ) {

	/**
	 * Class ORDDD_Holidays_API
	 */
	class ORDDD_Holidays_API extends ORDDD_API {

		/**
		 * REST base for this resource.
		 *
		 * @var string
		 */
		protected $rest_base = 'holidays';

		/**
		 * Internal date format used by the Lite plugin option.
		 *
		 * @var string
		 */
		const INTERNAL_DATE_FORMAT = 'n-j-Y';

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

			register_rest_route(
				$ns,
				'/' . $base,
				array(
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, 'get_all' ),
						'permission_callback' => array( $this, 'check_admin_permission' ),
					),
					array(
						'methods'             => WP_REST_Server::CREATABLE,
						'callback'            => array( $this, 'add_holiday' ),
						'permission_callback' => array( $this, 'check_admin_permission' ),
					),
					array(
						'methods'             => WP_REST_Server::DELETABLE,
						'callback'            => array( $this, 'delete_holiday' ),
						'permission_callback' => array( $this, 'check_admin_permission' ),
					),
				)
			);
		}

		// =========================================================================
		// GET /orddd/v1/holidays
		// =========================================================================

		/**
		 * Return all holidays, converting internal n-j-Y dates to Y-m-d.
		 *
		 * @return WP_REST_Response
		 */
		public function get_all() {
			return self::return_response( $this->get_holidays_data() );
		}

		// =========================================================================
		// POST /orddd/v1/holidays
		// =========================================================================

		/**
		 * Add one or more holidays.
		 *
		 * Body params:
		 *   holiday_name (string, required)
		 *   holiday_date (Y-m-d, for single date)
		 *   holiday_date_from + holiday_date_to (Y-m-d, for date range)
		 *   holiday_type ("current" | "recurring", default "current")
		 *
		 * @param WP_REST_Request $request Request object.
		 * @return WP_REST_Response|WP_Error
		 */
		public function add_holiday( WP_REST_Request $request ) {
			$name      = sanitize_text_field( $request->get_param( 'holiday_name' ) ?? '' );
			$date      = sanitize_text_field( $request->get_param( 'holiday_date' ) ?? '' );
			$date_from = sanitize_text_field( $request->get_param( 'holiday_date_from' ) ?? '' );
			$date_to   = sanitize_text_field( $request->get_param( 'holiday_date_to' ) ?? '' );
			$type      = sanitize_text_field( $request->get_param( 'holiday_type' ) ?? 'current' );
			$r_type    = ( 'recurring' === $type ) ? 'on' : 'off';

			if ( empty( $name ) ) {
				return new WP_Error(
					'missing_holiday_name',
					__( 'holiday_name is required.', 'order-delivery-date' ),
					array( 'status' => 400 )
				);
			}

			$existing = $this->load_raw_holidays();
			$dates    = array();

			if ( ! empty( $date ) ) {
				$dates[] = $date;
			} elseif ( ! empty( $date_from ) && ! empty( $date_to ) ) {
				$current = strtotime( $date_from );
				$end     = strtotime( $date_to );
				while ( $current <= $end ) {
					$dates[] = gmdate( 'Y-m-d', $current );
					$current  = strtotime( '+1 day', $current );
				}
			} else {
				return new WP_Error(
					'missing_holiday_date',
					__( 'Provide holiday_date or holiday_date_from + holiday_date_to.', 'order-delivery-date' ),
					array( 'status' => 400 )
				);
			}

			foreach ( $dates as $d ) {
				$internal_date = gmdate( self::INTERNAL_DATE_FORMAT, strtotime( $d ) );
				// Skip duplicates.
				$already_exists = false;
				foreach ( $existing as $h ) {
					if ( $h['d'] === $internal_date ) {
						$already_exists = true;
						break;
					}
				}
				if ( ! $already_exists ) {
					$existing[] = array(
						'n'      => $name,
						'd'      => $internal_date,
						'r_type' => $r_type,
					);
				}
			}

			update_option( 'orddd_lite_holidays', wp_json_encode( $existing ) );

			return self::return_response(
				array(
					'status'   => 'success',
					'message'  => __( 'Holiday added successfully.', 'order-delivery-date' ),
					'data'     => $this->get_holidays_data(),
				)
			);
		}

		// =========================================================================
		// DELETE /orddd/v1/holidays
		// =========================================================================

		/**
		 * Remove a holiday by date.
		 *
		 * Query param: holiday_date (Y-m-d)
		 *
		 * @param WP_REST_Request $request Request object.
		 * @return WP_REST_Response|WP_Error
		 */
		public function delete_holiday( WP_REST_Request $request ) {
			$date = sanitize_text_field( $request->get_param( 'holiday_date' ) ?? '' );

			if ( empty( $date ) ) {
				return new WP_Error(
					'missing_holiday_date',
					__( 'holiday_date query param is required.', 'order-delivery-date' ),
					array( 'status' => 400 )
				);
			}

			$internal_date = gmdate( self::INTERNAL_DATE_FORMAT, strtotime( $date ) );
			$existing      = $this->load_raw_holidays();
			$updated       = array_values(
				array_filter(
					$existing,
					function( $h ) use ( $internal_date ) {
						return $h['d'] !== $internal_date;
					}
				)
			);

			update_option( 'orddd_lite_holidays', wp_json_encode( $updated ) );

			return self::return_response(
				array(
					'status'  => 'success',
					'message' => __( 'Holiday removed successfully.', 'order-delivery-date' ),
					'data'    => $this->get_holidays_data(),
				)
			);
		}

		// =========================================================================
		// Private helpers
		// =========================================================================

		/**
		 * Load raw holidays array from the option.
		 *
		 * @return array
		 */
		private function load_raw_holidays() {
			$raw = get_option( 'orddd_lite_holidays', '' );
			if ( in_array( $raw, array( '', 'null', '[]', '{}', false ), true ) ) {
				return array();
			}
			return json_decode( $raw, true ) ?? array();
		}

		/**
		 * Return holidays formatted for React (Y-m-d dates).
		 *
		 * @return array
		 */
		private function get_holidays_data() {
			$raw    = $this->load_raw_holidays();
			$result = array();

			foreach ( $raw as $h ) {
				if ( empty( $h['d'] ) ) {
					continue;
				}
				$date_parts = explode( '-', $h['d'] );
				if ( count( $date_parts ) < 3 ) {
					continue;
				}
				$date = gmdate( 'Y-m-d', gmmktime( 0, 0, 0, (int) $date_parts[0], (int) $date_parts[1], (int) $date_parts[2] ) );

				$result[] = array(
					'holiday_name' => $h['n'] ?? '',
					'holiday_date' => $date,
					'holiday_type' => ( isset( $h['r_type'] ) && 'on' === $h['r_type'] ) ? 'recurring' : 'current',
				);
			}

			return $result;
		}
	}

	new ORDDD_Holidays_API();
}

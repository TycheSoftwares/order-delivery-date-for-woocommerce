<?php
/**
 * ORDDD Lite Base API Class
 *
 * Defines the ORDDD_Lite_API base class used by all Lite REST API resource classes.
 *
 * @package Order-Delivery-Date-Lite-for-WooCommerce/API
 * @since   4.7.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class ORDDD_Lite_API
 *
 * Base REST controller providing shared helpers used by every Lite API resource class.
 */
class ORDDD_Lite_API extends WP_REST_Controller {

	/**
	 * REST API namespace / base endpoint.
	 *
	 * @var string
	 */
	public static $base_endpoint = 'orddd/v1';

	/**
	 * Returns the REST API namespace string.
	 *
	 * @return string
	 */
	public static function endpoint() {
		return self::$base_endpoint;
	}

	/**
	 * Wraps a response value in a WP_REST_Response (or returns it raw).
	 *
	 * @param mixed $response   Data to return.
	 * @param bool  $return_raw When true, returns $response without wrapping.
	 * @return WP_REST_Response|mixed
	 */
	public static function return_response( $response, $return_raw = false ) {
		return $return_raw ? $response : rest_ensure_response( $response );
	}

	/**
	 * Returns a generic success response.
	 *
	 * @return WP_REST_Response
	 */
	public static function success() {
		return self::return_response( 'success' );
	}

	/**
	 * Returns a generic error response.
	 *
	 * @return WP_REST_Response
	 */
	public static function error() {
		return self::return_response( 'error' );
	}

	/**
	 * Returns $value, or $fallback when $value is an empty string.
	 *
	 * @param string $value   Target value.
	 * @param string $fallback Value to use when target is empty.
	 * @return string
	 */
	public static function return_value_if_empty( $value, $fallback ) {
		return '' === $value ? $fallback : $value;
	}

	/**
	 * Permission callback: allows shop managers and admins.
	 *
	 * @return bool
	 */
	public function check_admin_permission() {
		return current_user_can( 'manage_options' ) || current_user_can( 'manage_woocommerce' );
	}
}

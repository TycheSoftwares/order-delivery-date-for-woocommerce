<?php
/**
 * Register the inner blocks for checkout.
 *
 * @package order-delivery-date/blocks
 */

use Automattic\WooCommerce\StoreApi\StoreApi;
use Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema;
use Automattic\WooCommerce\StoreApi\Schemas\V1\CheckoutSchema;

add_action(
	'woocommerce_blocks_loaded',
	function () {
		require_once __DIR__ . '/class-order-delivery-date-blocks-integration.php';
		require_once __DIR__ . '/class-order-delivery-date-cart-block-integration.php';
		$delivery_on_cart = get_option( 'orddd_lite_delivery_date_on_cart_page' );
		if ( 'on' === $delivery_on_cart ) {
			add_action(
				'woocommerce_blocks_cart_block_registration',
				function ( $integration_registry ) {
					$integration_registry->register( new Order_DeliveryDate_Lite_Cart_Block_Integration() );
				}
			);
		}
		add_action(
			'woocommerce_blocks_checkout_block_registration',
			function ( $integration_registry ) {
				$integration_registry->register( new Order_DeliveryDate_Lite_Blocks_Integration() );
			}
		);

		if ( function_exists( 'woocommerce_store_api_register_endpoint_data' ) ) {
			woocommerce_store_api_register_endpoint_data(
				array(
					'endpoint'        => CheckoutSchema::IDENTIFIER,
					'namespace'       => 'order-delivery-date',
					'data_callback'   => 'orddd_lite_data_callback',
					'schema_callback' => 'orddd_lite_schema_callback',
					'schema_type'     => ARRAY_A,
				)
			);
		}

		if ( function_exists( 'woocommerce_store_api_register_update_callback' ) ) {
			woocommerce_store_api_register_update_callback(
				array(
					'namespace' => 'order-delivery-date',
					'callback'  => 'orddd_lite_update_cart_fees',
				)
			);
		}
	}
);

/**
 * Callback function to register endpoint data for blocks.
 *
 * @return array
 */
function orddd_lite_data_callback() {
	return array(
		'e_deliverydate'       => '',
		'h_deliverydate'       => '',
		'orddd_lite_time_slot' => '',
		'delivery_schedule'    => '',
		'time_slot_mandatory'  => '',
		'time_slot_label'      => '',
	);
}

/**
 * Callback function to register schema for data.
 *
 * @return array
 */
function orddd_lite_schema_callback() {
	return array(
		'e_deliverydate'       => array(
			'description' => __( 'Delivery Date', 'order-delivery-date' ),
			'type'        => array( 'string', 'null' ),
			'readonly'    => true,
		),
		'h_deliverydate'       => array(
			'description' => __( 'Delivery Date (d-m-y format)', 'order-delivery-date' ),
			'type'        => array( 'string', 'null' ),
			'readonly'    => true,
		),
		'orddd_lite_time_slot' => array(
			'description' => __( 'Time Slot', 'order-delivery-date' ),
			'type'        => array( 'string', 'null' ),
			'readonly'    => true,
		),
		'delivery_schedule'    => array(
			'description' => __( 'Delivery Schedule', 'order-delivery-date' ),
			'type'        => array( 'string', 'null' ),
			'readonly'    => true,
		),
		'time_slot_mandatory'  => array(
			'description' => __( 'Time Slot Mandatory', 'order-delivery-date' ),
			'type'        => array( 'bool', 'null' ),
			'readonly'    => true,
		),
		'time_slot_label'      => array(
			'description' => __( 'Time Slot Label', 'order-delivery-date' ),
			'type'        => array( 'string', 'null' ),
			'readonly'    => true,
		),
	);
}

/**
 * Update delivery charges for date & time slot.
 *
 * @param array $data Updated data from cart.
 * @return void
 */
function orddd_lite_update_cart_fees( $data ) {
	if ( isset( $data['orddd_time_slot'] ) ) {
		WC()->session->set( 'orddd_lite_time_slot', $data['orddd_lite_time_slot'] );
	}

	if ( isset( $data['h_deliverydate'] ) ) {
		WC()->session->set( 'h_deliverydate', $data['h_deliverydate'] );
	}

	WC()->cart->calculate_totals();
}

/**
 * Registers the slug as a block category with WordPress.
 *
 * @param array $categories Categories array.
 * @return array
 */
function orddd_lite_register_delivery_date_block_category( $categories ) {
	return array_merge(
		$categories,
		array(
			array(
				'slug'  => 'order-delivery-date',
				'title' => __( 'Order Delivery Date Blocks', 'delivery_date' ),
			),
		)
	);
}
add_action( 'block_categories_all', 'orddd_lite_register_delivery_date_block_category', 10, 2 );

add_action( 'wp_footer', 'orddd_lite_add_calendar_styles' );

/**
 * Add the colors for the dates based on settings
 *
 * @return void
 */
function orddd_lite_add_calendar_styles() {
	$orddd_holiday_color         = get_option( 'orddd_lite_holiday_color' );
	$orddd_booked_dates_color    = get_option( 'orddd_lite_booked_dates_color' );
	$orddd_available_dates_color = get_option( 'orddd_lite_available_dates_color' );

	echo '<style type="text/css">
			.holidays {
				background-color: ' . $orddd_holiday_color . ' !important;
			}

			.booked_dates {
				background-color: ' . $orddd_booked_dates_color . ' !important;
			}

			.available-deliveries, .available-deliveries a {
				background: ' . $orddd_available_dates_color . ' !important;
			}

			.partially-booked, .partially-booked a {
				background: linear-gradient(to bottom right, ' . $orddd_booked_dates_color . '59 0%, ' . $orddd_booked_dates_color . '59 50%, ' . $orddd_available_dates_color . ' 50%, ' . $orddd_available_dates_color . ' 100%) !important;
			}
		</style>';
}

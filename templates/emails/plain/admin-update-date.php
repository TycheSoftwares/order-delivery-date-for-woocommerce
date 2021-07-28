<?php
/**
 * Order Delivery Date for WooCommerce Lite
 *
 * Plain template for an email sent to the admin or customer when the delivery details are edited.
 *
 * @author      Tyche Softwares
 * @package     Order-Delivery-Date-Lite-for-WooCommerce/Templates/Emails/Plain/Admin-Update-Date
 * @since       3.13.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$order_instance = new WC_order( $order_id );

echo '= ' . esc_attr( $email_heading ) . ' =\n\n';

if ( 'admin' === $updated_by ) {
	$opening_paragraph = __( 'The Delivery Date & Time has been updated by the Administrator. The details of the order and the updated delivery details are as follows:', 'order-delivery-date' );
} else {
	$opening_paragraph = __( 'The Delivery Date & Time has been updated by the customer. The details of the order and the updated delivery details are as follows:', 'order-delivery-date' );
}


if ( $order_instance && $order_instance->billing_first_name && $order_instance->billing_last_name ) {
	echo sprintf( esc_attr( $opening_paragraph ), esc_attr( $order_instance->billing_first_name ) . ' ' . esc_attr( $order_instance->billing_last_name ) ) . '\n\n';
}

echo '=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n';

/**
 * Adds delivery date and time to email.
 *
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Emails::order_schema_markup() Adds Schema.org markup.
 * @since 3.13.0
 */
do_action( 'woocommerce_email_order_details', $order_instance, $sent_to_admin, $plain_text, $email );

echo '\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n';

if ( '' !== $delivery_date_formatted ) {
	echo '<p><strong>' . esc_attr( get_option( 'orddd_lite_delivery_date_field_label' ) ) . ':</strong> ' . esc_attr( $delivery_date_formatted ) . '</p>';
}

if ( '' !== $order_page_time_slot ) {
	echo '<p><strong>' . esc_attr( get_option( 'orddd_delivery_timeslot_field_label' ) ) . ': </strong>' . esc_attr( $order_page_time_slot ) . '</p>';
}

echo esc_attr( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );

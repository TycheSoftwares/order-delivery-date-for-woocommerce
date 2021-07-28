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
    exit; // Exit if accessed directly
}

$order = new WC_order( $order_id );

echo "= " . $email_heading . " =\n\n";

if ( 'admin' == $updated_by ) {
	$opening_paragraph = __( 'The Delivery Date & Time has been updated by the Administrator. The details of the order and the updated delivery details are as follows:', 'woocommerce-booking' );
} else {
	$opening_paragraph = __( 'The Delivery Date & Time has been updated by the customer. The details of the order and the updated delivery details are as follows:', 'woocommerce-booking' );		
}


if ( $order && $order->billing_first_name && $order->billing_last_name ) {
	echo sprintf( $opening_paragraph, $order->billing_first_name . ' ' . $order->billing_last_name ) . "\n\n";
}

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

/**
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Emails::order_schema_markup() Adds Schema.org markup.
 * @since 3.13.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

if ( '' !== $delivery_date_formatted ) {
   echo '<p><strong>' . __( get_option( 'orddd_lite_delivery_date_field_label' ), 'order-delivery-date' ) . ':</strong> ' . $delivery_date_formatted . '</p>';
}

if ( '' !== $order_page_time_slot && '' !== $order_page_time_slot ) {
    echo '<p><strong>' . __( get_option( 'orddd_delivery_timeslot_field_label' ), 'order-delivery-date' ) . ': </strong>' . $order_page_time_slot . '</p>';
}

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );

<?php
/**
 * Order Delivery Date for WooCommerce Lite
 *
 * HTML template for an email sent to the admin or customer when the delivery details are edited.
 *
 * @author      Tyche Softwares
 * @package     Order-Delivery-Date-Lite-for-WooCommerce/Templates/Emails/Admin-Update-Date
 * @since       3.13.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$order_instance          = new WC_order( $order_id );
$delivery_date_formatted = Orddd_Lite_Common::orddd_lite_get_order_delivery_date( $order_id );
$order_page_time_slot    = Orddd_Lite_Common::orddd_get_order_timeslot( $order_id );
if ( 'admin' === $updated_by ) {
	$opening_paragraph = __( 'The Delivery Date & Time has been updated by the Administrator. The details of the order and the updated delivery details are as follows:', 'order-delivery-date' );
} else {
	$opening_paragraph = __( 'The Delivery Date & Time has been updated by the customer. The details of the order and the updated delivery details are as follows:', 'order-delivery-date' );
}

$date_field_label = get_option( 'orddd_lite_delivery_date_field_label' );
$time_field_label = get_option( 'orddd_lite_delivery_timeslot_field_label' );

do_action( 'woocommerce_email_header', $email_heading );
?>
<p><?php echo esc_attr( $opening_paragraph ); ?></p>
<?php

do_action( 'woocommerce_email_order_details', $order_instance, $sent_to_admin, $plain_text, $email );

if ( '' !== $delivery_date_formatted ) {
	?>
	<p><strong><?php echo esc_attr( $date_field_label ); ?> </strong><?php echo esc_attr( $delivery_date_formatted ); ?></p>
	<?php
}

if ( '' !== $order_page_time_slot && '' !== $order_page_time_slot ) {
	?>
	<p><strong><?php echo esc_attr( $time_field_label ); ?> </strong><?php echo esc_attr( $order_page_time_slot ); ?></p>
	<?php
}

do_action( 'woocommerce_email_footer' );

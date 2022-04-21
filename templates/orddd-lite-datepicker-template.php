<?php
/**
 * Template for Date & time fields on Cart, Checkout & My Account page.
 *
 * @package Order-Delivery-Date-Lite-for-WooCommerce/Templates
 */

?>
<div id="orddd-lite-checkout-fields">

<?php
/** Delivery Date Field */
do_action( 'orddd_lite_before_checkout_delivery_date', $checkout );

if ( is_cart() ) {
	$custom_attributes = array(
		'style'    => 'cursor:text !important;max-width:300px;  background:transparent;',
		'readonly' => 'readonly',
	);
} else {
	$custom_attributes = array(
		'style'    => 'cursor:text !important; background:transparent;',
		'readonly' => 'readonly',
	);
}

woocommerce_form_field(
	'e_deliverydate',
	array(
		'type'              => 'text',
		'label'             => $date_field_label,
		'required'          => $validate_date_field,
		'placeholder'       => get_option( 'orddd_lite_delivery_date_field_placeholder' ),
		'custom_attributes' => $custom_attributes,
		'autocomplete'      => 'off',
		'class'             => array( 'form-row-wide' ),
	)
);

do_action( 'orddd_lite_after_checkout_delivery_date', $checkout );

/** Time Slot Feild */

if ( $time_slot_enabled ) {
	if ( is_cart() ) {
		$custom_attributes = array(
			'disabled' => 'disabled',
			'style'    => 'cursor:not-allowed !important;max-width:300px;',
		);
	} else {
		$custom_attributes = array(
			'disabled' => 'disabled',
			'style'    => 'cursor:not-allowed !important;',
		);
	}

	do_action( 'orddd_lite_before_checkout_time_slot', $checkout );

	woocommerce_form_field(
		'orddd_lite_time_slot',
		array(
			'type'              => 'select',
			'label'             => $time_field_label,
			'required'          => $validate_time_field,
			'options'           => $time_slot_options,
			'validate'          => array( 'required' ),
			'custom_attributes' => $custom_attributes,
			'class'             => array( 'form-row-wide' ),
		)
	);

	do_action( 'orddd_lite_after_checkout_time_slot', $checkout );

}
?>

</div>

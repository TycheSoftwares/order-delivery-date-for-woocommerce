<?php
/**
 * Template for Date & time fields on Admin side.
 *
 * @package Order-Delivery-Date-Lite-for-WooCommerce/Templates
 */

?>
<table id="admin_delivery_fields" >
	<tr id="admin_delivery_date_field" >
		<td><label class ="orddd_lite_delivery_date_field_label"><?php echo esc_attr__( $date_field_label , 'order-delivery-date' ); //phpcs:ignore ?>: </label></td>
		<td>
			<input type="text" id="<?php echo esc_attr( $field_name ); ?>" name="<?php echo esc_attr( $field_name ); ?>" class="<?php echo esc_attr( $field_name ); ?>" readonly/>
		</td>
	</tr>

	<?php if ( $time_slot_enabled ) { ?>

		<tr id="admin_time_slot_field">
			<td><label for="orddd_lite_time_slot" class=""><?php echo esc_attr__( $time_field_label , 'order-delivery-date' ); //phpcs:ignore ?>: </label></td>
			<td><select name="orddd_lite_time_slot" id="orddd_lite_time_slot" class="orddd_lite_admin_time_slot" disabled="disabled" placeholder="">
					<option value="select"><?php echo esc_attr__( 'Select a time slot', 'order-delivery-date' ); ?></option>
				</select>
			</td>
		</tr>

	<?php } ?>

	<tr id='delivery_charges'>
		<td><label for='del_charges'><?php echo esc_attr__( $fee_name , 'order-delivery-date' ); //phpcs:ignore ?></label></td>
		<td><input type='number' min='0' value='<?php echo esc_attr( $fee ); ?>' step='0.001' id='del_charges' /></td>
	</tr>

	<tr id="delivery_charges_notes">
		<td colspan='2'>
			<small>
				<?php echo esc_attr__( 'Any change in Delivery charges here will not change the order total. You will need to update the Item section above for delivery charges to reflect in order total.', 'order-delivery-date' ); ?>
			</small>
			<br>
			<small>
				<em><?php echo esc_attr__( 'Note: If you are creating the order manually, you can update the delivery date & time after creating the order.', 'order-delivery-date' ); ?></em>
			</small>
		</td>
	</tr>

	<tr id="save_delivery_date_button">
		<td><input type="button" value="<?php echo esc_attr__( 'Update', 'order-delivery-date' ); ?>" id="save_delivery_date" class="button button-primary"></td>
		<td><input type="button" value="<?php echo esc_attr__( 'Update & Notify Customer', 'order-delivery-date' ); ?>" id="save_delivery_date_and_notify" class="button button-primary"></td>
		<td><font id="orddd_lite_update_notice"></font></td>
	</tr>

</table>
<div id="is_virtual_product"></div>

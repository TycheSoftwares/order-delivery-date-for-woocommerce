<?php

?>

<div id="content" class="orddd-shipping-based">
	<div class="container-fluid pl-info-wrap">
		<div class="row">
			<h2><?php _e( 'Delivery Calendar', 'order-delivery-date' ); ?></h2>
			<table class="orddd_delivery_table">
				<tr>
					<td>
							<label class="orddd_label" for="orddd_filter_delivery_calendar"><?php esc_html_e('Select a view:', 'order-delivery-date'); ?></label>
							<select class="orddd_filter_delivery_calendar" id="orddd_filter_delivery_calendar">
								<optgroup label="<?php _e( 'Filter Deliveries by', 'order-delivery-date' ); ?>">
									<option name="product" value="product"><?php _e( 'Products', 'order-delivery-date' ); ?></option>
									<option name="order" value="order"><?php _e( 'Orders', 'order-delivery-date' ); ?></option>
								</optgroup>
							</select>
							<input type="hidden" id="prev_event_type" value="product"/>
						</td>
						<td>
							<?php
							$order_status         = wc_get_order_statuses();
							$default_order_status = '';
							?>
							<label class="orddd_label" for="orddd_filter_by_order_status"><?php esc_html_e('Select status:', 'order-delivery-date'); ?></label>
							<select class="orddd_filter_by_order_status" id="orddd_filter_by_order_status" multiple="multiple">
								<?php
								foreach ( $order_status as $order_status_key => $order_status_name ) {
									if ( $order_status_key == 'wc-pending' || $order_status_key == 'wc-processing' || $order_status_key == 'wc-on-hold' || $order_status_key == 'wc-completed' ) {
										?>
										<option name="<?php echo $order_status_name; ?>" value="<?php echo $order_status_key; ?>" selected><?php echo $order_status_name; ?></option>
										<?php
										$default_order_status .= $order_status_key . ',';
									} elseif ( $order_status_key != 'wc-cancelled' && $order_status_key != 'wc-refunded' && $order_status_key != 'wc-failed' ) {
										?>
										<option name="<?php echo $order_status_name; ?>" value="<?php echo $order_status_key; ?>" selected><?php echo $order_status_name; ?></option>
										<?php
										$default_order_status .= $order_status_key . ',';
									}
								}
								?>
							</select>
							<?php $default_order_status = substr( $default_order_status, 0, strlen( $default_order_status ) - 1 ); ?>
							<input type="hidden" id="prev_order_status" value="<?php echo $default_order_status; ?>"/>
						</td>
						<?php if ( class_exists( 'WC_Shipping_Zones' ) ) { ?>
						<td>
							<?php
							if ( class_exists( 'WC_Shipping' ) ) {
								$shipping_methods = Orddd_Lite_Common::get_all_wc_shipping_methods();
									?>
									<label class="orddd_label" for="orddd_filter_by_order_shipping">
										<?php esc_html_e( 'Select shipping method:', 'order-delivery-date' ); ?>
									</label>

									<select class="orddd_filter_by_order_shipping" id="orddd_filter_by_order_shipping" multiple="multiple">
										<?php
										foreach ( $shipping_methods as $method_key => $method ) {
											?>
											<option value="<?php echo esc_attr( $method['method_key'] ); ?>">
												<?php echo esc_html( $method['title'] ); ?>
											</option>
											<?php
										}
										?>
									</select>
								<?php
							}
							?>
							<input type="hidden" id="prev_order_shipping" value=""/>
						</td>
						<?php } ?>
						<td style="width: 100px;">
							<label class="orddd_label">&nbsp;</label>
							<a href="javascript:;" id="orddd_filter_calendar_data" class="button button-primary orddd_filter_btn"><?php _e( 'Apply filters', 'order-delivery-date' ); ?></a>
						</td>
						<td>
							<?php do_action( 'orddd_before_export_actions' ); ?>

							<a href="<?php echo esc_url( add_query_arg( 'download', 'orddd_data.print' ) ); ?>" target="_blank" style="float:right;margin:5px;" class="" id="orddd_print_orders"><?php _e( 'Print', 'order-delivery-date' ); ?></a>

							<a href="<?php echo esc_url( add_query_arg( 'download', 'orddd_data.csv' ) ); ?>" style="float:right;margin:5px;" class="" id="orddd_csv_orders"><?php _e( 'CSV', 'order-delivery-date' ); ?></a>


						</td>
					</tr>
			</table>
			<div id="orddd_events_loader">
			<div class="orddd_events_loader_wrapper">
				Loading delivery events...<img src=<?php echo plugins_url() . '/order-delivery-date/images/ajax-loader.gif'; ?>>
			</div>
		</div>
		<div id='calendar' style="padding:10px"></div>
		</br>
	</div>
</div>


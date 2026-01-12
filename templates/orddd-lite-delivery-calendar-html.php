<?php

?>

<div id="content" class="orddd-shipping-based">
	<div class="container-fluid pl-info-wrap">
		<div class="row">
			<h2 style="padding-left: 16px;"><?php _e( 'Filters', 'order-delivery-date' ); ?></h2>
			<div class="orddd-filters-card">

				<div class="orddd-filters-row">

					<!-- View -->
					<div class="orddd-filter-field">
						<label class="orddd_label" for="orddd_filter_delivery_calendar">
							<?php esc_html_e('Select a view:', 'order-delivery-date'); ?>
						</label>
						<select class="orddd_filter_delivery_calendar" id="orddd_filter_delivery_calendar">
							<optgroup label="<?php _e( 'Filter Deliveries by', 'order-delivery-date' ); ?>">
								<option value="product"><?php _e( 'Products', 'order-delivery-date' ); ?></option>
								<option value="order"><?php _e( 'Orders', 'order-delivery-date' ); ?></option>
							</optgroup>
						</select>
						<input type="hidden" id="prev_event_type" value="product"/>
					</div>

					<!-- Status -->
					<div class="orddd-filter-field">
						<label class="orddd_label" for="orddd_filter_by_order_status">
							<?php esc_html_e('Select status:', 'order-delivery-date'); ?>
						</label>
						<select class="orddd_filter_by_order_status" id="orddd_filter_by_order_status" multiple="multiple">
							<?php
							$order_status         = wc_get_order_statuses();
							$default_order_status = '';

							foreach ( $order_status as $key => $label ) {
								if ( ! in_array( $key, array( 'wc-cancelled', 'wc-refunded', 'wc-failed' ), true ) ) {
									echo '<option value="' . esc_attr( $key ) . '" selected>' . esc_html( $label ) . '</option>';
									$default_order_status .= $key . ',';
								}
							}
							?>
						</select>
						<input type="hidden" id="prev_order_status" value="<?php echo esc_attr( rtrim( $default_order_status, ',' ) ); ?>"/>
					</div>

					<!-- Shipping -->
					<?php if ( class_exists( 'WC_Shipping_Zones' ) ) : ?>
					<div class="orddd-filter-field">
						<label class="orddd_label" for="orddd_filter_by_order_shipping">
							<?php esc_html_e( 'Select shipping method:', 'order-delivery-date' ); ?>
						</label>
						<select class="orddd_filter_by_order_shipping" id="orddd_filter_by_order_shipping" multiple="multiple">
							<?php
							$shipping_methods = Orddd_Lite_Common::get_all_wc_shipping_methods();
							foreach ( $shipping_methods as $method ) {
								echo '<option value="' . esc_attr( $method['method_key'] ) . '">' . esc_html( $method['title'] ) . '</option>';
							}
							?>
						</select>
						<input type="hidden" id="prev_order_shipping" value=""/>
					</div>
					<?php endif; ?>

					<!-- Actions -->
					<div class="orddd-filter-actions">
						<a href="javascript:;" id="orddd_filter_calendar_data"
						   class="button button-primary orddd_filter_btn">
							<?php _e( 'Apply Filters', 'order-delivery-date' ); ?>
						</a>
					</div>
				</div>

				<div class="orddd-export-actions">
					<a href="<?php echo esc_url( add_query_arg( 'download', 'orddd_data.csv' ) ); ?>"
							   id="orddd_csv_orders" class="button">
						<i class="fa-solid fa-file-csv" style="margin-right:6px;"></i>
						<?php _e( 'CSV', 'order-delivery-date' ); ?>
					</a>

					<a href="<?php echo esc_url( add_query_arg( 'download', 'orddd_data.print' ) ); ?>"
							   target="_blank" id="orddd_print_orders" class="button">
						<i class="fa-solid fa-print" style="margin-right:6px;"></i>
						<?php _e( 'Print', 'order-delivery-date' ); ?>
					</a>
				</div>

			</div>
		</div>

			<div id="orddd_events_loader">
			<div class="orddd_events_loader_wrapper">
				Loading delivery events...<img src=<?php echo plugins_url() . '/order-delivery-date-for-woocommerce/images/ajax-loader.gif'; ?>>
			</div>
		</div>
		<div id='calendar' style="padding:10px"></div>
		</br>
	</div>
</div>

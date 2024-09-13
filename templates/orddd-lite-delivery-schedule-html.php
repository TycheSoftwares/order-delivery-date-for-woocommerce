<?php

?>

<div id="content" class="orddd-shipping-based">
	<div class="orddd-shipping-based-inner-section">
		<div class="container cw-wide">
			<div class="container-fluid pl-info-wrap">
				<div class="row">
					<div class="col-md-12">
						<div class="ordd-delivery-schedule">
							<div class="ordd-ds-top">
								<div class="ordd-ds-search">
									<div class="search-box">
										<h1>Delivery Schedule</h1>
									</div>
									<div class="ordd-doc-link">
										<a href="https://www.tychesoftwares.com/docs/woocommerce-order-delivery-date-pro-new/delivery-schedule/" target="_blank" class="go-link">Documentation</a>
									</div>
								</div>
							</div>
							<div class="ordd-ds-content">
								<div class="tbl-delivery-schedule tbl-responsive">
									<table class="table">
										<thead>
											<tr>
												<th width="5%">
													<div class="el-checkbox el-checkbox-green d-uni-col">
														<input type="checkbox" id="cb_opt_nm_9A" value="cb_opt_val_9" class="ckbCheckAll">
														<label for="cb_opt_nm_9A" class="el-checkbox-style mb-0"></label>
													</div>
												</th>
												<th>Status</th>
												<th>Settings based on</th>
												<th>Delivery Settings</th>
												<th>Same/Next day Settings</th>
												<th>Timeslots Settings</th>
												<th>Holidays</th>
												<th>Actions</th>
											</tr>
										</thead>
										<tbody>
											<tr class="cloned-row">
												<td></td>
												<td>
													-
												</td>
												<td>
													<ul>
														<li><strong>Default Delivery Schedule</strong></li>
														<li>Delivery Date: <strong>Enabled</strong></li>
													</ul>
												</td>
												<td>
													<ul>
														<li>
															<label>Delivery Checkout option:</label>Delivery Calendar</li>
														<li>
															<label>Delivery Days/Dates:</label>
															<br> <span>Monday</span>
															<br> <span>Tuesday</span>
															<br> <span>Wednesday</span>
															<br> <span>Thursday</span>
															<br> <span>Friday</span>
															<br>
														</li>
														<li>
															<label>Minimum Delivery Time(in hours):</label>12</li>
														<li>
															<label>Maximum Order Deliveries per day: </label>4</li>
														<li>
															<label>Number of dates to choose:</label>30</li>
													</ul>
												</td>
												<td>
													<ul>
														<li></li>
														<li></li>
													</ul>
												</td>
												<td class="schedule-time">
													<div class="section-with-autoscroll">
														<ul>
															<li><strong>Time Settings: </strong></li>
															<li style="font-size: smaller;border-bottom: 1px solid #ccc;">Monday: 09:00 - 10:00
																<br>Maximum deliveries: 4</li>
															<li style="font-size: smaller;border-bottom: 1px solid #ccc;">Monday: 09:00 - 10:00
																<br>Maximum deliveries: 4</li>
															<li style="font-size: smaller;border-bottom: 1px solid #ccc;">Monday: 09:00 - 10:00
																<br>Maximum deliveries: 4</li>
															<li style="font-size: smaller;border-bottom: 1px solid #ccc;">Monday: 09:00 - 10:00
																<br>Maximum deliveries: 4</li>
															<li style="font-size: smaller;">Monday: 09:00 - 10:00
																<br>Maximum deliveries: 4</li>
														</ul>
													</div>
												</td>
												<td class="schedule-time">
													<div class="section-with-autoscroll"></div>
												</td>
												<td class="tbl-action-col"><a data-toggle="collapse" href="#edit-acds_pc_div" id="shiping-method111" role="button" aria-expanded="false" aria-controls="collapseExample" class="edit-delvry-sche"><span title="Edit" class="dashicons dashicons-edit"></span></a>                        <a title="Duplicate" class="Duplicate"><span title="Duplicate" class="dashicons dashicons-admin-page"></span></a></td>
											</tr>
											<tr class="alt-in-tbl">
												<td colspan="9">
													<div role="alert" class="alert alert-dark alert-dismissible fade show"><img src="<?php echo plugins_url() . '/order-delivery-date-for-woocommerce/includes/component/upgrade-to-pro/assets/images/icon-info-grey.svg'; ?>" alt="Logo" class="msg-icon"> Hey, don't forget - The custom settings you add below will override the default settings.
														</div>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<div class="add-more-link">
									<a id="shiping-method" data-toggle="collapse" href="#acds-collapse-opt" role="button" aria-expanded="false" aria-controls="collapseExample"><img src="<?php echo plugins_url() . '/order-delivery-date-for-woocommerce/includes/component/upgrade-to-pro/assets/images/icon-plus.svg'; ?>" alt="Icon"> Add custom delivery schedule</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php do_action( 'orddd_lite_after_settings_page_form' ); ?>
</div>


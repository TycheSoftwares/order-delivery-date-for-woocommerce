<?php

$icon_info_grey = plugins_url() . '/order-delivery-date-for-woocommerce/includes/component/upgrade-to-pro/assets/images/icon-info.svg';
$icon_trash     = plugins_url() . '/order-delivery-date-for-woocommerce/includes/component/upgrade-to-pro/assets/images/icon-trash.svg';
?>

<div id="content" class="orddd-calendar-sync">
	<div class="orddd-shipping-based-inner-section">
		<div id="secondary-nav-wrap" class="ordd-content-area">
			<div class="container cw-full secondary-nav">
				<div class="row">
					<div class="col-md-12">
						<div class="secondary-nav-wrap">
							<ul>
								<li class="current-menu-item"><a href="#/" aria-current="page" class="router-link-exact-active router-link-active">Google Sync</a></li>
								<li class=""><a href="#/integration-settings" class="">Integration Settings</a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<div class="container fas-page-wrap">
				<div id="save_message" class="container-fluid pl-info-wrap" style="display: none;">
					<div class="row">
						<div class="col-md-12">
							<div role="alert" class="alert alert-success alert-dismissible fade show">
								Settings Saved.
								<button type="button" data-dismiss="alert" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
							</div>
						</div>
					</div>
				</div>
				<div class="container-fluid pl-info-wrap" style="display: none;">
					<div class="row">
						<div class="col-md-12">
							<div role="alert" class="alert alert-danger alert-dismissible fade show">
								Please Upload a valid JSON key file.
								<button type="button" data-dismiss="alert" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="ordd-page-head phw-btn">
							<div class="col-left">
								<h1>Google Calendar Sync</h1>
								<p>Stay on top of your delivery schedule by syncing it with the Google Calendar. And your customers can have their own fair share by adding the delivery info to their own GC.</p>
							</div>
							<div class="col-right">
								<button type="button" class="orddd-save save-gcal-settings">Save Settings</button>
							</div>
						</div>
						<div class="wbc-accordion">
							<div id="wbc-accordion" class="panel-group ordd-accordian">
								<div class="panel panel-default">
									<div class="panel-heading">
										<h2 data-toggle="collapse" data-target="#collapseOne" aria-expanded="false" class="panel-title">
										General Settings							</h2></div>
									<div id="collapseOne" class="panel-collapse collapse show">
										<div class="panel-body">
											<div class="tbl-mod-1">
												<div class="tm1-row flx-center">
													<div class="col-left">
														<label>Event Location</label>
													</div>
													<div class="col-right">
														<div class="rc-flx-wrap flx-aln-center"><img src="<?php echo $icon_info_grey; ?>" alt="Info" data-toggle="tooltip" data-placement="top" title="Enter the text that will be used in the location field of the calendar event. If left empty, the website description will be used. Note: You can use PICKUP_LOCATION, ADDRESS, FULL_ADDRESS, ADDRESS_SHIP, FULL_ADDRESS_SHIP and CITY placeholders which will be replaced by their real values."
															class="tt-info">
															<input type="text" name="orddd_calendar_event_location" id="orddd_calendar_event_location" class="ib-xl">
														</div>
													</div>
												</div>
												<div class="tm1-row flx-center">
													<div class="col-left">
														<label>Event summary (name)</label>
													</div>
													<div class="col-right">
														<div class="rc-flx-wrap flx-aln-center"><img src="<?php echo $icon_info_grey; ?>" alt="Info" data-toggle="tooltip" data-placement="top" title="Enter the text to be used in the Event summary in the Google calendar event."
															class="tt-info">
															<input type="text" id="orddd_calendar_event_summary" name="orddd_calendar_event_summary" class="ib-xl">
														</div>
													</div>
												</div>
												<div class="tm1-row">
													<div class="col-left">
														<label>Event Description</label>
													</div>
													<div class="col-right">
														<div class="rc-flx-wrap"><img src="<?php echo $icon_info_grey; ?>" alt="Info" title="Use the following placeholders which will be replaced by their real values: SITE_NAME, CLIENT, PRODUCTS, PRODUCT_WITH_QTY, PRODUCT_WITH_CATEGORY, ORDER_DATE_TIME, ORDER_DATE, ORDER_NUMBER, PRICE, PHONE, NOTE, ADDRESS, FULL_ADDRESS, CLIENT_SHIP, FULL_ADDRESS_SHIP, SHIPPING_METHOD_TITLE, PAYMENT_METHOD_TITLE, PICKUP_LOCATION, ORDER_WEBLINK, ORDER_STATUS, EMAIL (Client's email)"
															class="tt-info aw-text">
															<textarea id="orddd_calendar_event_description" name="orddd_calendar_event_description" cols="90" rows="4" class="ta-sm"></textarea>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<h2 data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" class="panel-title">
										Customer Add to Calendar Button Settings							</h2></div>
									<div id="collapseTwo" class="panel-collapse collapse show">
										<div class="panel-body">
											<div class="tbl-mod-1">
												<div class="tm1-row">
													<div class="col-left">
														<label>Show Add to Calendar button on Order received page</label>
													</div>
													<div class="col-right">
														<div class="rc-flx-wrap flx-aln-center"><img src="<?php echo $icon_info_grey; ?>" alt="Info" data-toggle="tooltip" data-placement="top" title="Show Add to Calendar button on the Order Received page for the customers."
															class="tt-info">
															<label class="el-switch el-switch-green">
																<input type="checkbox" name="orddd_add_to_calendar_order_received_page" id="orddd_add_to_calendar_order_received_page" true-value="on" false-value=""> <span class="el-switch-style"></span></label>
														</div>
													</div>
												</div>
												<div class="tm1-row">
													<div class="col-left">
														<label>Show Add to Calendar button in the Customer notification email</label>
													</div>
													<div class="col-right">
														<div class="rc-flx-wrap flx-aln-center"><img src="<?php echo $icon_info_grey; ?>" alt="Info" data-toggle="tooltip" data-placement="top" title="Show Add to Calendar button in the Customer notification email."
															class="tt-info">
															<label class="el-switch el-switch-green">
																<input type="checkbox" name="orddd_add_to_calendar_customer_email" id="orddd_add_to_calendar_customer_email" value="on" true-value="on" false-value=""> <span class="el-switch-style"></span></label>
														</div>
													</div>
												</div>
												<div class="tm1-row">
													<div class="col-left">
														<label>Show Add to Calendar button on My account</label>
													</div>
													<div class="col-right">
														<div class="rc-flx-wrap flx-aln-center"><img src="<?php echo $icon_info_grey; ?>" alt="Info" data-toggle="tooltip" data-placement="top" title="Show Add to Calendar button on My account page for the customers."
															class="tt-info">
															<label class="el-switch el-switch-green">
																<input type="checkbox" name="orddd_add_to_calendar_my_account_page" id="orddd_add_to_calendar_my_account_page" value="on" true-value="on" false-value=""> <span class="el-switch-style"></span></label>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<h2 data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" class="panel-title">
										Admin Calendar Sync Settings							</h2></div>
									<div id="collapseThree" class="panel-collapse collapse show google-calendar-sync">
										<div class="panel-body">
											<div class="tbl-mod-1">
												<div class="tm1-row">
													<div class="col-left">
														<label>Integration Mode</label>
													</div>
													<div class="col-right">
														<div class="rc-flx-wrap flx-aln-center ro-wrap"><img src="<?php echo $icon_info_grey; ?>" alt="Info" data-toggle="tooltip" data-placement="top" title="Select method of integration. `Sync Automatically` will add the delivery events to the Google calendar, which is set in the `Calendar to be used` field, automatically when a customer places an order. Also, an `Add to Calendar` button is added on the Delivery Calendar page in admin to Sync past orders.
		`Sync Manually` will add an `Add to Google Calendar` button in emails received by admin and New customer order.
		`Disabled` will disable the integration with Google Calendar.
		Note: Import of the events will work manually using .ics link." class="tt-info">
															<div class="rb-flx-style">
																<div class="el-radio el-radio-green">
																	<input type="radio" id="7_0" value="oauth_sync">
																	<label for="7_0" class="el-radio-style"></label>
																</div>
																<label for="7_0">OAuth Sync(Recommended)</label>
															</div>
															<div class="rb-flx-style">
																<div class="el-radio el-radio-green">
																	<input type="radio" id="7_1" value="directly">
																	<label for="7_1" class="el-radio-style"></label>
																</div>
																<label for="7_1">Service Account Sync</label>
															</div>
															<div class="rb-flx-style">
																<div class="el-radio el-radio-green">
																	<input type="radio" id="7_2" value="manually">
																	<label for="7_2" class="el-radio-style"></label>
																</div>
																<label for="7_2">Sync Manually</label>
															</div>
															<div class="rb-flx-style">
																<div class="el-radio el-radio-green">
																	<input type="radio" id="7_3" value="disabled">
																	<label for="7_3" class="el-radio-style"></label>
																</div>
																<label for="7_3">Disabled</label>
															</div>
														</div>
													</div>
												</div>
												<div class="rb_opt_val_3" style="display: none;">
													<div class="tm1-row">
														<div class="col-full">
															<label>Instructions</label>
															<p>To set up Client ID and Client Secret click on the "Show me how" link and follow the steps: <a><span class="link-wul">Show me how</span></a></p>
															<p></p>
														</div>
													</div>
													<div class="description orddd-info_target api-instructions" style="display: none;">
														<ul style="list-style-type: decimal;">
															<li>Google Calendar OAuth Sync requires PHP 7.4. </li>
															<li>
																Go to the <strong><a href="https://code.google.com/apis/console/" target="_blank">Google Developers Console</a></strong> and select a project, or create a new one. Login to your Google account if
																you are not already logged in. </li>
															<li>If creating a new project, give the Project name. eg 'My Deliveries' and click on the Create button. </li>
															<li>Once the project is created, the Calendar API needs to be enabled. To do so, click on <strong>ENABLE API AND SERVICES</strong> link and search for <strong>Google Calendar API</strong>, and enable it
																by clicking the ENABLE button. </li>
															<li>On the left, click <strong>Credentials</strong>. If this is your first time creating a client ID, you'll be prompted to configure the consent screen. Click on <strong>Configure Consent Screen</strong></li>
															<li>Go to the <strong>OAuth consent screen</strong>.Select User Type as <strong>Internal</strong> and click on the CREATE button. After that, set the <strong>Application name</strong> and click on the Create
																button. </li>
															<li>Go back to the <strong>Credentials</strong> tab, click <strong>Create credentials</strong>, then select <strong>OAuth client ID</strong>. </li>
															<li>Select <strong>Web application</strong> under Application type and provide the necessary information to create your project's credentials. </li>
															<li>For <strong>Authorized redirect URIs</strong> enter the Redirect URI (Can be found in Order Delivery Date &gt; Settings &gt; Integrations &gt; Google Sync ). Then click Create button. </li>
															<li>On the dialog that appears, you'll see your <strong>Client ID</strong> and <strong>Client Secret</strong>. Fill the details in below fields and click on Save Settings. </li>
															<li>Once the Successful Connection to Google, <strong>Calendar to be used</strong> option will appear. Here select the calendar to which the event should get created for the delivery. </li>
														</ul>
													</div>
													<div class="tm1-row flx-center">
														<div class="col-left">
															<label>Client ID</label>
														</div>
														<div class="col-right">
															<div class="rc-flx-wrap flx-aln-center"><img src="<?php echo $icon_info_grey; ?>" alt="Info" data-toggle="tooltip" data-placement="top" title="Upload the JSON key file downloaded from your Google account."
																class="tt-info">
																<input type="text" name="gcal_oauth_client_id" id="gcal_oauth_client_id" class="ib-xl">
															</div>
														</div>
													</div>
													<div class="tm1-row">
														<div class="col-left">
															<label>Client Secret</label>
														</div>
														<div class="col-right">
															<div class="row-box-1">
																<div class="rb1-left"><img src="<?php echo $icon_info_grey; ?>" alt="Info" data-toggle="tooltip" data-placement="top" title="Enter the ID of the calendar in which your deliveries will be saved, e.g. abcdefg1234567890@group.calendar.google.com."
																	class="tt-info"></div>
																<div class="rb1-right">
																	<div class="rb1-row">
																		<input type="text" name="gcal_oauth_client_secret" id="gcal_oauth_client_secret" class="ib-xl">
																	</div>
																</div>
															</div>
														</div>
													</div>
													<div class="tm1-row">
														<div class="col-left">
															<label>Redirect URI</label>
														</div>
														<div class="col-right">
															<div class="row-box-1">
																<div class="rb1-left"><img src="<?php echo $icon_info_grey; ?>" alt="Info" data-toggle="tooltip" data-placement="top" title="Enter the ID of the calendar in which your deliveries will be saved, e.g. abcdefg1234567890@group.calendar.google.com."
																	class="tt-info"></div>
																<div class="rb1-right">
																	<div class="rb1-row">
																		<input type="text" name="gcal_oauth_redirect_uri" id="gcal_oauth_redirect_uri" value="https://bkapui-temp.instawp.xyz/wp-admin/admin.php?page=order_delivery_date&amp;action=integrations&amp;orddd-google-oauth=1"
																		readonly="readonly" class="ib-xl gcal_oauth_redirect_uri"> <a href="javascript:void(0)" id="orddd_copy_redirect_uri" data-selector-to-copy="#gcal_oauth_redirect_uri" data-tip="Redirect URI has been copied!" class="dashicons dashicons-admin-page copy-to-clipboard">&nbsp;</a>
																		<span
																		id="orddd_redirect_uri_copied"></span>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
												<div class="rb_opt_val_3" style="display: none;">
													<div class="tm1-row">
														<div class="col-full">
															<label>Instructions</label>
															<p>Set up Google Calendar API by clicking on the "Show me how" link and following these steps: <a><span class="link-wul">Show me how</span></a></p>
															<p></p>
														</div>
													</div>
													<div class="description orddd-info_target api-instructions" style="display: none;">
														<ul style="list-style-type: decimal;">
															<li>Google Calendar API requires PHP V5.3+ and some PHP extensions. </li>
															<li>Go to Google APIs console by clicking <a href="https://code.google.com/apis/console/" target="_blank">https://code.google.com/apis/console/</a>. Login to your Google account if you are not already logged
																in.</li>
															<li>Click on 'Create Project'. Name the project 'Deliveries' (or use your chosen name instead) and create the project.</li>
															<li>Click on APIs &amp; Services from the left side panel. Select the Project created. </li>
															<li>Click on 'Enable APIs and services' on the dashboard. Search for 'Google Calendar API' and enable this API.</li>
															<li>Go to 'Credentials' menu in the left side pane and click on 'CREATE CREDENTIALS' link and from the dropdown that appears select 'Service account.'</li>
															<li>Enter Service account name, id, and description and Create the service account.</li>
															<li>In the next step assign Owner role under Service account permissions, keep options in the third optional step empty and click on Done button.</li>
															<li>Now edit the Service account that you have created and under the 'Keys' section click on Add Key&gt;&gt; Create New Key, in the popup that opens select 'JSON' option and click on the CREATE button. A
																file with extension .json will be downloaded.</li>
															<li>
																The JSON file is required as you will grant access to your Google Calendar account. So this file serves as a proof of your consent to access to your Google calendar account. </li>
															<li>Open your Google Calendar by clicking this link: <a href="https://www.google.com/calendar/render" target="_blank">https://www.google.com/calendar/render</a></li>
															<li>Create a new Calendar by clicking on '+' sign next to 'Other Calendars' section on left side pane. Try NOT to use your primary calendar.</li>
															<li>Give a name to the new calendar, e.g. Order Delivery Date calendar. Check that Calendar Time Zone setting matches with time zone setting of your WordPress website. Otherwise there will be a time shift.</li>
															<li>Create the calendar and once it is created click on the Configure link which will appear at the end of the page, this will redirect you to Calendar Settings section. Paste already copied 'Service Account
																ID' from Manage service account of Google APIs console to 'Add People' field under 'Share with specific people'.</li>
															<li>Set 'Permission Settings' of this person as 'Make changes to events' and add the person.</li>
															<li>Now copy 'Calendar ID' value from Integrate Calendar section and paste the value to 'Calendar to be used' field of Order Delivery Date settings.</li>
															<li>After saving the settings, you can test the connection by clicking on the 'Test Connection' link.</li>
															<li>If you get a success message, you should see a test event inserted into the Google Calendar and you are ready to go. If you get an error message, double check your settings.</li>
														</ul>
													</div>
													<div class="tm1-row flx-center">
														<div class="col-left">
															<label>Upload Key File</label>
														</div>
														<div class="col-right">
															<div class="rc-flx-wrap flx-aln-center"><img src="<?php echo $icon_info_grey; ?>" alt="Info" data-toggle="tooltip" data-placement="top" title="Upload the JSON key file downloaded from your Google account."
																class="tt-info">
																<input type="file" name="gcal_key_file" id="gcal_key_file" class="ib-xl">
															</div>
														</div>
													</div>
													<div class="tm1-row">
														<div class="col-left">
															<label>Calendar to be used</label>
														</div>
														<div class="col-right">
															<div class="row-box-1">
																<div class="rb1-left"><img src="<?php echo $icon_info_grey; ?>" alt="Info" data-toggle="tooltip" data-placement="top" title="Enter the ID of the calendar in which your deliveries will be saved, e.g. abcdefg1234567890@group.calendar.google.com."
																	class="tt-info"></div>
																<div class="rb1-right">
																	<div class="rb1-row">
																		<input type="text" class="ib-xl">
																	</div>
																	<div class="rb1-row flx-center">
																		<span class="link-wul">
																		Test Connection	
																		</span>
																	</div>
																	<div class="rb1-row"><span class="success-msg"></span></div>
																</div>
															</div>
														</div>
													</div>
													<div class="tm1-row">
														<div class="col-left">
															<label>Show "Export to Google Calendar" button on Delivery Calendar page</label>
														</div>
														<div class="col-right">
															<div class="rc-flx-wrap flx-aln-center"><img src="<?php echo $icon_info_grey; ?>" alt="Info" data-toggle="tooltip" data-placement="top" title="Show Export to Google Calendar button on the Order Delivery Date -> Delivery Calendar page. Note: This button can be used to export the already placed orders with future deliveries from the current date to the calendar used above."
																class="tt-info">
																<label class="el-switch el-switch-green">
																	<input type="checkbox" name="orddd_admin_add_to_calendar_delivery_calendar" id="orddd_admin_add_to_calendar_delivery_calendar" value="on" true-value="on" false-value=""> <span class="el-switch-style"></span></label>
															</div>
														</div>
													</div>
												</div>
												<div class="rb_opt_val_4" style="display: none;">
													<div class="tm1-row">
														<div class="col-left">
															<label>Show Add to Calendar button in New Order email notification</label>
														</div>
														<div class="col-right">
															<div class="rc-flx-wrap flx-aln-center"><img src="<?php echo $icon_info_grey; ?>" alt="Info" data-toggle="tooltip" data-placement="top" title="Show Add to Calendar button in the New Order email notification."
																class="tt-info">
																<label class="el-switch el-switch-green">
																	<input type="checkbox" name="orddd_admin_add_to_calendar_email_notification" id="orddd_admin_add_to_calendar_email_notification" value="on" true-value="on" false-value=""> <span class="el-switch-style"></span></label>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<h2 data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" class="panel-title">
										Import Events							</h2>
										<p>Import the events with the ICS Feed url. Yep, each event equals a new WooCommerce order.</p>
									</div>
									<div id="collapseFour" class="panel-collapse collapse show">
										<div class="panel-body">
											<div class="tbl-mod-1">
												<div class="tm1-row">
													<div class="col-full">
														<label>Instructions:</label> To set up Google Calendar API, please click on "Show me how" link and carefully follow these steps: <span class="link-wul">Show me how</span></div>
													<div class="ics-feed-description orddd_ics_feed-info_target api-instructions"
													style="display: none;">
														<ul style="list-style-type: decimal;">
															<li>Open your Google Calendar by clicking this link:<a href="https://www.google.com/calendar/render" target="_blank">https://www.google.com/calendar/render</a></li>
															<li>Select the calendar to be imported and click "Calendar settings".</li>
															<li>Click on "ICAL" button in Calendar Address option.</li>
															<li>Copy the basic.ics file URL. If you are importing events from a private calendar please copy the basic.ics file URL for private calendar.</li>
															<li>Paste this link in the text box under Google Calendar Sync tab -&gt; Import Events section.</li>
															<li>Save the URL.</li>
															<li>Click on "Import Events" button to import the events from the calendar.</li>
															<li>You can import multiple calendars by using ics feeds. Add them using the Add New Ics Feed url button.</li>
														</ul>
													</div>
												</div>
												<div class="tm1-row">
													<div class="col-left">
														<label>iCalendar/.ics Feed URL</label>
													</div>
													<div class="col-right">
														<div class="row-box-1">
															<div class="rb1-left"><img src="<?php echo $icon_info_grey; ?>" alt="Info" data-toggle="tooltip" data-placement="top" title="Add the Calendar .ics feed URL to import events from it."
																class="tt-info"></div>
															<div class="rb1-right"><span class="success-msg"></span>
																<div class="rb1-row flx-center">
																	<div class="rb-col">
																		<input type="text" id="orddd_ics_fee_url_0" size="60" class="ib-md">
																	</div>
																	<div class="rb-col">
																		<input type="submit" value="Save" class="secondary-btn">
																	</div>
																	<div class="rb-col">
																		<input type="submit" name="import_ics" value="Import Events" disabled="disabled" class="secondary-btn disabled">
																	</div>
																	<div class="rb-col">
																		<a role="button"><img src="<?php echo $icon_trash; ?>" alt="Delete"></a>
																	</div>
																</div>
																<div class="rb1-row"><a role="button" class="link-vol-ul">Add New Ics feed url</a></div>
															</div>
														</div>
													</div>
												</div>
												<div class="tm1-row">
													<div class="col-left">
														<label>Import frequency</label>
													</div>
													<div class="col-right">
														<div class="rc-flx-wrap flx-aln-center"><img src="<?php echo $icon_info_grey; ?>" alt="Info" data-toggle="tooltip" data-placement="top" title="Import events from Google calendar based on the time set below. By default, all events from the Google calendar will be imported once every 24 hours."
															class="tt-info">
															<label class="el-switch el-switch-green">
																<input type="checkbox" name="orddd_real_time_import" id="orddd_real_time_import" value="on" true-value="on" false-value="on"> <span class="el-switch-style"></span></label>
														</div>
													</div>
												</div>
												<div class="tm1-row">
													<div class="col-left">
														<label>Enter Import frequency (in minutes)</label>
													</div>
													<div class="col-right">
														<div class="rc-flx-wrap flx-aln-center"><img src="<?php echo $icon_info_grey; ?>" alt="Info" data-toggle="tooltip" data-placement="top" title="The duration in minutes at which events from the Google Calendar ICS feeds will be imported automatically in the store. Note: Setting this to a lower value then 10 minutes may impact the performance of your store."
															class="tt-info">
															<input type="text" id="orddd_wp_cron_minutes" name="orddd_wp_cron_minutes" class="ib-md">
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="ss-foot">
							<button type="button" class="orddd-save save-gcal-settings">Save Settings</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php do_action( 'orddd_lite_after_settings_page_form' ); ?>
</div>
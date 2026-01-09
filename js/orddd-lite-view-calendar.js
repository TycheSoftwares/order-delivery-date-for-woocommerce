/**
 * File for configuring Full Calendar
 *
 * @namespace orddd_delivery_calendar
 * @since 2.8.7
 * @since Updated 9.28.3
 */

let calendar_element;

(function () {

	// Stop if either tyche object or orddd parameters are missing.
	if ('undefined' === typeof tyche || 'undefined' === typeof orddd_calendar_js) {
		return;
	}

	// Stop if vendor_id is empty or undefined.
	if ('undefined' === typeof orddd_calendar_js.vendor_id || ('undefined' !== typeof orddd_calendar_js.vendor_id && '' === orddd_calendar_js.vendor_id)) {
		return;
	}

	tyche.extend(tyche.orddd, {

		/**
		 * Gets the Vendor ID.
		 * @since 9.28.3
		 */
		get_vendor_id: function () {
			return orddd_calendar_js.vendor_id;
		},

		orddd_set_buttons_href_value: function () {
			jQuery('#orddd_print_orders, #orddd_csv_orders').attr('href', function () {
				let start_date = jQuery(this).data('start'),
					end_date = jQuery(this).data('end'),
					orddd_this_href = this.href;
					cpurl = orddd_calendar_js.admin_url;
					if ( 'undefined' !== typeof orddd_calendar_js.vendor_url ) {
						cpurl = orddd_calendar_js.vendor_url;
					}

				if (orddd_this_href.includes("orddd_data.print")) {
					orddd_this_href = cpurl + '/admin.php?page=order_delivery_date_lite&action=view-orders&download=orddd_data.print';
				} else {
					orddd_this_href = cpurl + '/admin.php?page=order_delivery_date_lite&action=view-orders&download=orddd_data.csv';
				}

				return orddd_this_href + '&eventType=' + jQuery( ".orddd_filter_delivery_calendar" ).val() + '&orderStatus=' + jQuery( ".orddd_filter_by_order_status" ).val() + '&orderShipping=' + jQuery( ".orddd_filter_by_order_shipping" ).val() + '&start=' + start_date + "&end=" + end_date;
			});
		},

		/**
		 * Initializes JS events.
		 * @since 9.28.3
		 */
		init_events: function () {

			jQuery('.orddd_filter_delivery_calendar').on('change', function () {

				let delivery_calendar_last_view;

				if ('order' === jQuery(this).val()) {
					jQuery("#prev_event_type").val("order");
					delivery_calendar_last_view = jQuery(this).val();
				} else {
					jQuery("#prev_event_type").val("product");
					delivery_calendar_last_view = 'product';
				}

				localStorage.setItem("delivery_calendar_last_view_" + tyche.orddd.get_vendor_id(), delivery_calendar_last_view);

				// Changes the href value of CSV and print buttons.
				tyche.orddd.orddd_set_buttons_href_value();
			});

			jQuery('.orddd_filter_by_order_status').on('change', function () {

				let order_status = jQuery(this).val();
				jQuery("#prev_order_status").val(order_status);

				// Store the selected statuses in the local storage.
				localStorage.setItem("delivery_calendar_last_order_statuses_" + tyche.orddd.get_vendor_id(), order_status);

				// Changes the href value of CSV and print buttons.
				tyche.orddd.orddd_set_buttons_href_value();
			});

			jQuery('.orddd_filter_by_order_shipping').on('change', function () {

				let order_shipping = jQuery(this).val();
				jQuery("#prev_order_shipping").val(order_shipping);

				// Store the selected shipping in the local storage.
				localStorage.setItem("delivery_calendar_last_order_shipping_" + tyche.orddd.get_vendor_id(), order_shipping);

				// Changes the href value of CSV and print buttons.
				tyche.orddd.orddd_set_buttons_href_value();
			});

			jQuery('#orddd_filter_calendar_data').on('click', function () {
				let order_shipping = jQuery('.orddd_filter_by_order_shipping').val();
				event_type = jQuery("#prev_event_type").val(),
					order_status = jQuery('.orddd_filter_by_order_status').val(),
					eventSource = calendar_element.getEventSources();

				eventSource[0].remove();
				calendar_element.addEventSource( orddd_calendar_js.pluginurl + "&eventType=" + event_type + "&orderStatus=" + order_status + "&orderShipping=" + order_shipping );
				//calendar_element.refetchEvents();
				tyche.orddd.orddd_set_buttons_href_value();
			});
		},

		/**
		 * Init.
		 * @since 9.28.3
		 */
		init: function () {

			// Prevent the right click event on the Print button click on Delivery Calendar Page. 
			jQuery("#orddd_print_orders").on("contextmenu", function (e) {
				e.preventDefault();
			});

			// The auxclick event is fired at an Element when a non-primary pointing device button (any mouse button other than the primary—usually leftmost—button) 
			// has been pressed and released both within the same element. This is added for the mouse roller click.
			// This event is not supported on the Safari browser and IE. 
			jQuery("#orddd_print_orders").on("auxclick", function (e) {
				e.preventDefault();
			});

			setTimeout(function () {
				jQuery('.orddd_filter_delivery_calendar').select2({
					minimumResultsForSearch: -1,
					width: '90%'
				});
				jQuery('.orddd_filter_by_order_status').select2();
				jQuery('.orddd_filter_by_order_status').select2({
					width: '90%',
					closeOnSelect: false
				});
				jQuery('.orddd_filter_by_order_shipping').select2();
				jQuery('.orddd_filter_by_order_shipping').select2({
					width: '90%',
					closeOnSelect: false,
					allowClear: true
				});
				jQuery('.orddd_filter_by_order_shipping').select2();
				jQuery('div#wpfooter').css('display', 'none');
			})
		},

		/**
		 * Gets the View URL for the Calendar element.
		 * @since 9.28.3
		 */
		get_calendar_view_url: function () {

			let load_previous_view_url = '',
				plugin_url = orddd_calendar_js.pluginurl;

			// Fields with id prev_order_status and prev_event_type are hidden fields.

			// Check for the selected status and pre populate that statuses in the dropdown
			// when the admin visits again. By default, Processing, Completed, On-hold and Pending payment will come.
			let prev_order_status = document.getElementById('prev_order_status').value;

			if (null !== localStorage.getItem("delivery_calendar_last_order_statuses_" + tyche.orddd.get_vendor_id())) {

				// Remove all selected options from the dropdown.
				jQuery(".orddd_filter_by_order_status option:selected").removeAttr("selected");

				//Reselect the previously selected options in the dropdown.
				let prev_order_status = localStorage.getItem("delivery_calendar_last_order_statuses_" + tyche.orddd.get_vendor_id());
				if ('undefined' !== typeof prev_order_status && null !== prev_order_status && '' !== prev_order_status) {
					let prev_order_status_arr = prev_order_status.split(",");
					for (let i = 0; i < prev_order_status_arr.length; i++) {
						jQuery(".orddd_filter_by_order_status option[value='" + prev_order_status_arr[i] + "']").prop('selected', true);
					}

					//Set the previous order status as the previously selected status.
					jQuery("#prev_order_status").val(prev_order_status);
				}
			}

			// Check for the selected shipping method and pre populate that shipping method in the dropdown
			let prev_order_shipping = document.getElementById('prev_order_shipping').value;

			if (null !== localStorage.getItem("delivery_calendar_last_order_shipping_" + tyche.orddd.get_vendor_id())) {

				// Remove all selected options from the shipping method dropdown.
				jQuery(".orddd_filter_by_order_shipping option:selected").removeAttr("selected");

				//Reselect the previously selected options in the dropdown.
				prev_order_shipping = localStorage.getItem("delivery_calendar_last_order_shipping_" + tyche.orddd.get_vendor_id());
				if ('undefined' !== typeof prev_order_shipping && null !== prev_order_shipping && '' !== prev_order_shipping) {
					let prev_order_shipping_arr = prev_order_shipping.split(",");
					for (let i = 0; i < prev_order_shipping_arr.length; i++) {
						jQuery(".orddd_filter_by_order_shipping option[value='" + prev_order_shipping_arr[i] + "']").prop('selected', true);
					}

					//Set the previous shipping method as the previously selected shipping method.
					jQuery("#prev_order_shipping").val(prev_order_shipping);
				}
			}

			//Fetch the last view of the Delivery Calendar from the Local Storage.
			if ('order' === localStorage.getItem("delivery_calendar_last_view_" + tyche.orddd.get_vendor_id())) {

				// Pass the order type as the previous order statuses and event type as order to fetch events by default on page load.
				// Pass the previous order statuses for which the events should be displayed in the Calendar. 
				// This is required as when the default type is Orders and when an admin changes the order statuses,
				// the url for refetching events does not have statues and hence it displays the default events and not 
				// with the selected statuses.
				load_previous_view_url = plugin_url + '&eventType=order' + '&orderStatus=' + prev_order_status + '&orderStatus=' + prev_order_status;

				//Set the filter value to Orders.
				jQuery('.orddd_filter_delivery_calendar').val('order');

				//Set the previous event type as orders.
				jQuery("#prev_event_type").val("order");
			} else {
				// Pass the order type as the previous order statuses and event type as order to fetch events by default on page load.
				load_previous_view_url = plugin_url + '&eventType=product' + '&orderStatus=' + prev_order_status;

				//Set the filter value to product.
				jQuery('.orddd_filter_delivery_calendar').val('product');

				//Set the previous event type as products.
				jQuery("#prev_event_type").val("product");
			}

			if ('' !== prev_order_shipping) {
				//load_previous_view_url = plugin_url + '&eventType=product' + '&orderStatus=' + prev_order_status + '&orderShipping=' + prev_order_shipping;
			}

			return load_previous_view_url;
		},

		/**
		 * Initializes the Calendar element..
		 * @since 9.28.3
		 */
		init_calendar: function () {
			let calendarEl = document.getElementById('calendar');

			calendar_element = new FullCalendar.Calendar(calendarEl, {
				headerToolbar: {
					left: 'prev,next today',
					center: 'title',
					right: 'dayGridMonth,timeGridWeek,timeGridDay'
				},
				selectable: false,
				events: tyche.orddd.get_calendar_view_url(),
				navLinks: true,
				locale: orddd_calendar_js.calendar_language,
				dayMaxEvents: true,
				eventDidMount: function (info) {

					let lines = info.event.title.split('\\n');
					let eventTitleElement = info.el.querySelector('.fc-event-title');
    				eventTitleElement.innerHTML = '';
    				lines.forEach(( line, index ) => {
						let textNode = document.createTextNode( line );
						eventTitleElement.appendChild( textNode );
						if ( index < lines.length - 1 ) {
				            eventTitleElement.appendChild( document.createElement( 'br' ) );
				        }
				    });

					let event_data = {
						action: 'orddd_order_calendar_content',
						event_product_id: info.event.extendedProps.event_product_id,
						product_name: info.event.extendedProps.product_name,
						event_product_qty: info.event.extendedProps.event_product_qty,
						order_id: info.event.id,
						event_value: info.event.extendedProps.value,
						event_date: info.event.extendedProps.delivery_date,
						event_timeslot: info.event.extendedProps.time_slot,
						event_type: info.event.extendedProps.eventtype,
						security: orddd_calendar_js.security
					};

					jQuery(info.el).qtip({
						content: {
							text: 'Loading...',
							button: 'Close', // It will disply Close button on the tool tip.
							ajax: {
								url: orddd_calendar_js.ajax_url,
								type: "POST",
								data: event_data
							}
						},
						show: {
							event: 'click', // Show tooltip only on click of the vent
							solo: true // Disply only One tool tip at time, hide other all tool tip
						},
						position: {
							my: 'bottom right', // this is for the botton v shape icon position.
							at: 'top right' // this is for the content box position
						},
						hide: 'unfocus', //this is used to keep the hover effect untill click outside on calender. For clickingthe order number
						style: {
							classes: 'qtip-light qtip-shadow'
						}
					});
				},

				dayCellDidMount: function (args) {
					let holidays = eval('[' + orddd_calendar_js.orddd_holidays + ']'),
						date_obj = args.date,
						m = date_obj.getMonth(),
						d = date_obj.getDate(),
						y = date_obj.getFullYear();

					if (jQuery.inArray((m + 1) + '-' + d + '-' + y, holidays) != -1 || jQuery.inArray((m + 1) + '-' + d, holidays) != -1) {
						args.el.style.background = orddd_calendar_js.orddd_holiday_color;
					}
				},
				loading: function (bool) {
					if (bool == true) {
						jQuery("#orddd_events_loader").show();
					} else if (bool == false) {
						jQuery("#orddd_events_loader").hide();
					}
				},
				datesSet: function (dateInfo) {
					jQuery('#orddd_print_orders, #orddd_csv_orders').attr('href', function () {
						let start_date_obj = dateInfo.view.currentStart,
							start_date = moment(start_date_obj).format('YYYY-MM-DD'),
							end_date_obj = dateInfo.view.currentEnd,
							end_date = moment(end_date_obj).subtract('1', 'days').format('YYYY-MM-DD');

						jQuery(this).data('start', start_date);
						jQuery(this).data('end', end_date);

						let orddd_this_href = this.href;
						cpurl = orddd_calendar_js.admin_url;
						if ( 'undefined' !== typeof orddd_calendar_js.vendor_url ) {
							cpurl = orddd_calendar_js.vendor_url;
						}
						if (orddd_this_href.includes("orddd_data.print")) {
							orddd_this_href = cpurl + '/admin.php?page=order_delivery_date_lite&action=view-orders&download=orddd_data.print';
						} else {
							orddd_this_href = cpurl + '/admin.php?page=order_delivery_date_lite&action=view-orders&download=orddd_data.csv';
						}

						return orddd_this_href + '&eventType=' + jQuery(".orddd_filter_delivery_calendar").val() + '&orderStatus=' + jQuery(".orddd_filter_by_order_status").val() + '&orderShipping=' + jQuery(".orddd_filter_by_order_shipping").val() + '&start=' + start_date + "&end=" + end_date;
					});
				}
			});

			calendar_element.render();
		}
	});

	jQuery(document).ready(function () {
		tyche.orddd.init();
	});

	document.addEventListener('DOMContentLoaded', function () {
		tyche.orddd.init_calendar();
		tyche.orddd.init_events();
	});
}());

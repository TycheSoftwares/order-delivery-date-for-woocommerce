/**
 * Allows to initiliaze/load the settings in the calendar.
 *
 * @namespace orddd_lite_initialize
 * @since 1.0
 */
 jQuery( document ).ready( function() {

	if ( '1' === jsL10n.is_admin && jQuery('#orddd_lite_holiday_color').length > 0 ) {
		// Add Color Picker to all inputs that have 'color-field' class
		jQuery( '.cpa-color-picker' ).wpColorPicker();
	}

	// Clear local storage for the selected delivery date in next 2 hours.
	var orddd_last_check_date = localStorage.getItem( "orddd_lite_storage_next_time" );
	var current_date          = orddd_lite_params.orddd_lite_current_day

	if ( '1' === jsL10n.is_admin && jQuery('#orddd_lite_time_slot').length > 0 ) {
		// Add Color Picker to all inputs that have 'color-field' class
		e_deliverydate_width = jQuery( '#e_deliverydate' ).outerWidth();
		jQuery("#orddd_lite_time_slot").css( 'width', e_deliverydate_width );
		jQuery( '#orddd_lite_time_slot' ).select2({ width: e_deliverydate_width + 'px' });
	} else {
		jQuery("#orddd_lite_time_slot").select2();
	}

	if ( current_date != '' && typeof( current_date ) != 'undefined' ) {
		  var split_current_date = current_date.split( '-' );
		  var ordd_next_date     = new Date( split_current_date[ 2 ], ( split_current_date[ 1 ] - 1 ), split_current_date[ 0 ], orddd_lite_params.orddd_lite_current_hour, orddd_lite_params.orddd_lite_current_minute );
	} else {
		var ordd_next_date = new Date();
	}

	if ( null != orddd_last_check_date ) {
		if ( ordd_next_date.getTime() > orddd_last_check_date ) {
			localStorage.removeItem( "orddd_lite_storage_next_time" );
			localStorage.removeItem( "orddd_deliverydate_lite_session" );
			localStorage.removeItem( "h_deliverydate_lite_session" );
		}
	}

		jQuery( document ).on(
			"ajaxComplete",
			function( event, xhr, options ) {
				if ( options.url.indexOf( "wc-ajax=checkout" ) !== -1 ) {
					if ( xhr.statusText != "abort" ) {
						localStorage.removeItem( "orddd_lite_storage_next_time" );
						localStorage.removeItem( "orddd_deliverydate_lite_session" );
						localStorage.removeItem( "h_deliverydate_lite_session" );
					}
				}
			}
		);

		var formats = ["MM d, yy","MM d, yy"];

	jQuery.extend(
		jQuery.datepicker,
		{ afterShow: function( event ) {
			jQuery.datepicker._getInst( event.target ).dpDiv.css( "z-index", 9999 );
			if ( orddd_lite_params.orddd_lite_number_of_months == "1" ) {
					jQuery.datepicker._getInst( event.target ).dpDiv.css( "width", "300px" );
			} else {
					jQuery.datepicker._getInst( event.target ).dpDiv.css( "width", "40em" );
			}
		} }
	);
	if ( '1' !== jsL10n.is_admin ) {
		jQuery( "#e_deliverydate" ).val( "" ).datepicker(
			{
				dateFormat: orddd_lite_params.orddd_lite_delivery_date_format,
				firstDay: parseInt( orddd_lite_params.orddd_first_day_of_week ),
				beforeShow: avd,
				beforeShowDay: chd,
				showButtonPanel: true,
				closeText: jsL10n.clearText,
				onSelect: orddd_on_select_date,
				onClose:function( dateStr, inst ) {
					if ( dateStr != "" ) {
						var monthValue = inst.selectedMonth + 1;
						var dayValue   = inst.selectedDay;
						var yearValue  = inst.selectedYear;
						var all        = dayValue + "-" + monthValue + "-" + yearValue;							
					}
					jQuery( "#e_deliverydate" ).blur();
				}
			}).focus(
			function ( event ) {
				jQuery( this ).trigger( "blur" );
				jQuery.datepicker.afterShow( event );
			}
		);
	}
	if ( orddd_lite_params.orddd_lite_field_note != '' ) {
		jQuery( "#e_deliverydate_field" ).append( "<small class='orddd_lite_field_note'>" + orddd_lite_params.orddd_lite_field_note + "</small>" );
	}
	jQuery( document ).on( "click", '.ui-datepicker-close', function() {
		jQuery( document ).trigger( "orddd_on_clear_text" );
		jQuery( "#e_deliverydate" ).val( "" );
		jQuery( "#h_deliverydate" ).val( '' );
	})

	jQuery( document ).on( "change", "#orddd_lite_time_slot", function() {
		var selected_val = jQuery(this).val();
		jQuery(this).find('option[value="'+ selected_val + '"]').prop( 'selected', true );
		localStorage.setItem( "orddd_deliverydate_lite_session", jQuery( "#e_deliverydate" ).val() );
		localStorage.setItem( "h_deliverydate_lite_session", jQuery( "#h_deliverydate" ).val() );
		localStorage.setItem( "orddd_lite_time_slot", selected_val );

		var current_date = orddd_lite_params.orddd_lite_current_day
		if ( typeof( current_date ) != 'undefined' && current_date != '' ) {
			var split_current_date = current_date.split( '-' );
			var ordd_next_date     = new Date( split_current_date[ 2 ], ( split_current_date[ 1 ] - 1 ), split_current_date[ 0 ], orddd_lite_params.orddd_lite_current_hour, orddd_lite_params.orddd_lite_current_minute );
		} else {
			var ordd_next_date = new Date();
		}

		ordd_next_date.setHours( ordd_next_date.getHours() + 2 );
		localStorage.setItem( "orddd_lite_storage_next_time", ordd_next_date.getTime() );

		jQuery( "body" ).trigger( "update_checkout" );
		if ( 'on' == orddd_lite_params.orddd_lite_delivery_date_on_cart_page && orddd_lite_params.orddd_is_cart == '1' ) {
			jQuery( "#hidden_timeslot" ).val( jQuery( "#orddd_lite_time_slot" ).find(":selected").val() );
			jQuery( "body" ).trigger( "wc_update_cart" );
		}
		jQuery( "body" ).trigger( "change_orddd_time_slot", [ jQuery( this ) ] );
	});
	if ( '1' === jsL10n.is_admin ) {
		window.onload = orddd_lite_init();
	} else if ( '1' !== orddd_lite_params.orddd_is_cart_block ) {
		window.onload = load_lite_functions();
	}

	if( '1' == jsL10n.is_admin ) {
		jQuery( "#save_delivery_date" ).click(function() {
			save_delivery_dates( 'no' );
		}); 

		jQuery( "#save_delivery_date_and_notify" ).click(function() {
			save_delivery_dates( 'yes' );
		});        
	}
}
);

/**
* Adds the Delivery information on Admin order page load.
*
* @function orddd_lite_init
* @memberof orddd_initialize_functions
* @since 3.13.0
*/
function orddd_lite_init() {
var default_date_str = orddd_lite_admin_params.orddd_lite_default_date;
		jQuery.extend(
		jQuery.datepicker,
		{ afterShow: function( event ) {
			jQuery.datepicker._getInst( event.target ).dpDiv.css( "z-index", 9999 );
			jQuery.datepicker._getInst( event.target ).dpDiv.css( "width", "300px" );
		} }
	);
	jQuery( '#e_deliverydate' ).datepicker({
		dateFormat: orddd_lite_params.orddd_lite_delivery_date_format,
		firstDay: parseInt( orddd_lite_params.orddd_first_day_of_week ),
		showButtonPanel: true,
		closeText: jsL10n.clearText,
		onSelect: show_admin_times,
		onClose:function( dateStr, inst ) {
			if ( dateStr != "" ) {
				var monthValue = inst.selectedMonth + 1;
				var dayValue   = inst.selectedDay;
				var yearValue  = inst.selectedYear;
				var all        = dayValue + "-" + monthValue + "-" + yearValue;
			}
			jQuery( "#e_deliverydate" ).blur();
		}
	}).focus(
	function ( event ) {
		jQuery( this ).trigger( "blur" );
		jQuery.datepicker.afterShow( event );
	});

if( default_date_str != "" && typeof( default_date_str ) != 'undefined' ) {
	if( default_date_str != '' && typeof( default_date_str ) != 'undefined' ) {
		var default_date_arr = default_date_str.split( "-" );
		var default_date = new Date( default_date_arr[ 1 ] + "/" + default_date_arr[ 0 ] + "/" + default_date_arr[ 2 ] );
	} else {
		var default_date = new Date();
	}

	jQuery( '#e_deliverydate' ).datepicker( "setDate", default_date );
	jQuery( "#h_deliverydate" ).val( orddd_lite_admin_params.orddd_lite_default_h_date );
	var default_date_inst = jQuery.datepicker._getInst( jQuery( "#e_deliverydate" )[0] );
	show_admin_times( default_date_str, default_date_inst );
}

if( 'no' == orddd_lite_admin_params.orddd_lite_delivery_enabled ) {
	jQuery( "#admin_time_slot_field" ).remove();
	jQuery( "#admin_delivery_date_field" ).remove()
	jQuery( "#save_delivery_date_button" ).remove();
	jQuery( "#delivery_charges" ).remove();
	jQuery( "#delivery_charges_notes" ).remove();
	jQuery( "#is_virtual_product" ).html( "Delivery date settings are not enabled for the products." );                    
} 

jQuery('.ui-datepicker-close').on( 'click', function(){
	jQuery( '#e_deliverydate' ).val( "" );
	jQuery( '#h_deliverydate' ).val( "" );
})

}

/**
* Shows the Time Slots in the admin Orders page
*
* @function show_admin_times
* @memberof orddd_initialize_functions
* @param {date} date - Date
* @param {object} inst 
* @since 3.13.0
*/
function show_admin_times( date, inst ) {

jQuery( document ).trigger( 'on_select_additional_action', [ date, inst ] )

var monthValue = inst.selectedMonth+1;
var dayValue = inst.selectedDay;
var yearValue = inst.selectedYear;
var all = dayValue + "-" + monthValue + "-" + yearValue;
jQuery( "#h_deliverydate" ).val( all );
// jQuery( "#e_deliverydate" ).val(  jQuery('#' + jQuery( "#orddd_field_name" ).val() ).val() );
if( orddd_lite_params.orddd_lite_enable_time_slot == "on" ) {
	if( typeof( inst.id ) !== "undefined" ) {
		var data = {
			current_date: all,
			order_id: orddd_lite_admin_params.orddd_lite_order_id,
			min_date: orddd_lite_admin_params.orddd_lite_min_date_set,
			current_date_to_check: orddd_lite_admin_params.orddd_lite_current_date_set,
			holidays_str: orddd_lite_params.orddd_lite_holidays,
			lockout_str: orddd_lite_params.orddd_lite_lockout_days,
			action: "check_for_time_slot_orddd",
			admin: true,
		};

		jQuery( "#orddd_lite_time_slot" ).attr( "disabled", "disabled" );
		jQuery( "#admin_time_slot_field" ).attr( "style", "opacity: 0.5" );
		if( orddd_lite_params.orddd_lite_admin_url != '' && typeof( orddd_lite_params.orddd_lite_admin_url ) != 'undefined' ) {
			jQuery.post( orddd_lite_params.orddd_lite_admin_url + "admin-ajax.php", data, function( response ) {
				jQuery( "#admin_time_slot_field" ).attr( "style" ,"opacity:1" );
				if( orddd_lite_params.orddd_is_cart == 1 ) {
					jQuery( "#orddd_lite_time_slot" ).attr( "style", "cursor: pointer !important;max-width:300px" );
				} else {
					jQuery( "#orddd_lite_time_slot" ).attr( "style", "cursor: pointer !important" );
				}
				jQuery( "#orddd_lite_time_slot" ).removeAttr( "disabled" ); 
				// If there is weglot plugin is active then it will return the response with non-translatable div with id time_slot_var so we will check for that below and if it has that div then it will use its html() as response text.
				response_text = jQuery( jQuery.parseHTML( response ) ).filter("#time_slot_var");
				if ( response_text.length ) {
					response = response_text.html();
				}
				orddd_load_time_slots( response );
			});
		}
	}
}
}

/**
* This function is called when the date is selected from the calendar.
*
* @function orddd_on_select_date
* @memberof orddd_lite_initialize
* @param {object} inst
* @param {string} date
* @since 3.1
*/
function orddd_on_select_date( date, inst ) {
var monthValue = inst.selectedMonth + 1;
var dayValue   = inst.selectedDay;
var yearValue  = inst.selectedYear;
var all        = dayValue + "-" + monthValue + "-" + yearValue;

localStorage.setItem( "orddd_deliverydate_lite_session", jQuery( "#e_deliverydate" ).val() );
localStorage.setItem( "h_deliverydate_lite_session", all );

jQuery('input[name="e_deliverydate"]').each( function( index ) {
	id = jQuery(this).attr('id');
	if ( id !=='e_deliverydate'){
		jQuery(this).val(dayValue + '-' + monthValue + '-' + yearValue );
	}
});

var current_date = orddd_lite_params.orddd_lite_current_day
if ( typeof( current_date ) != 'undefined' && current_date != '' ) {
	var split_current_date = current_date.split( '-' );
	var ordd_next_date     = new Date( split_current_date[ 2 ], ( split_current_date[ 1 ] - 1 ), split_current_date[ 0 ], orddd_lite_params.orddd_lite_current_hour, orddd_lite_params.orddd_lite_current_minute );
} else {
	var ordd_next_date = new Date();
}

ordd_next_date.setHours( ordd_next_date.getHours() + 2 );
localStorage.setItem( "orddd_lite_storage_next_time", ordd_next_date.getTime() );
show_times( date, inst );
}


function show_times( date, inst ) {
var monthValue = inst.selectedMonth+1;
var dayValue = inst.selectedDay;
var yearValue = inst.selectedYear;
var all = dayValue + "-" + monthValue + "-" + yearValue;
jQuery( "#h_deliverydate" ).val( all );
if( orddd_lite_params.orddd_lite_enable_time_slot == "on" ) {
	if( typeof( inst.id ) !== "undefined" ) {
		var data = {
			current_date: all,
			order_id: jQuery( "#orddd_my_account_order_id" ).val(),
			min_date: orddd_lite_params.orddd_min_date_set,
			current_date_to_check: jQuery( "#orddd_current_date_set" ).val(),
			time_slot_session: localStorage.getItem( "orddd_lite_time_slot" ),
			holidays_str: orddd_lite_params.orddd_lite_holidays,
			lockout_str: orddd_lite_params.orddd_lite_lockout_days,
			action: "check_for_time_slot_orddd"
		};

		var option_selected = orddd_lite_params.orddd_lite_auto_populate_first_available_time_slot;
		jQuery( "#orddd_lite_time_slot" ).attr( "disabled", "disabled" );
		jQuery( "#orddd_time_slot_field" ).attr( "style", "opacity: 0.5" );
		if( orddd_lite_params.orddd_lite_admin_url != '' && typeof( orddd_lite_params.orddd_lite_admin_url ) != 'undefined' ) {
			jQuery.post( orddd_lite_params.orddd_lite_admin_url + "admin-ajax.php", data, function( response ) {

				jQuery( "#orddd_time_slot_field" ).attr( "style", "opacity: 1" );
				if( orddd_lite_params.orddd_is_cart == 1 ) {
					jQuery( "#orddd_lite_time_slot" ).attr( "style", "cursor: pointer !important;max-width:300px" );
				} else {
					jQuery( "#orddd_lite_time_slot" ).attr( "style", "cursor: pointer !important" );
				}
				jQuery( "#orddd_lite_time_slot" ).removeAttr( "disabled" ); 

				if( '1' !== orddd_lite_params.orddd_is_cart_block ) {
					orddd_load_time_slots( response );
				} 

				if( option_selected == "on" || ( 'on' == orddd_lite_params.orddd_lite_delivery_date_on_cart_page && localStorage.getItem( "orddd_lite_time_slot" ) != '' ) ) {
						jQuery( "body" ).trigger( "update_checkout" );
						if ( 'on' == orddd_lite_params.orddd_lite_delivery_date_on_cart_page && orddd_lite_params.orddd_is_cart == '1' ) {
							jQuery( "#hidden_timeslot" ).val( jQuery( "#orddd_lite_time_slot" ).find(":selected").val() );
							if ( '1' !== orddd_lite_params.orddd_is_cart_block ) {
								jQuery( "body" ).trigger( "wc_update_cart" );
							}
						}
				}
				jQuery( document ).trigger( 'orddd_lite_on_load_time_slots', [ response ] );

			});
		}
	}
} else {
	jQuery( "body" ).trigger( "update_checkout" );
	if ( 'on' == orddd_lite_params.orddd_lite_delivery_date_on_cart_page && orddd_lite_params.orddd_is_cart == '1' ) {
		jQuery( "#hidden_timeslot" ).val( jQuery( "#orddd_lite_time_slot" ).find(":selected").val() );
		jQuery( "body" ).trigger( "wc_update_cart" );
	}
}

localStorage.setItem( "orddd_deliverydate_lite_session", jQuery( "#e_deliverydate" ).val() );
localStorage.setItem( "h_deliverydate_lite_session", all );

if( localStorage.getItem( "orddd_lite_time_slot" ) == null ) {
	localStorage.setItem( "orddd_lite_time_slot", jQuery( "#orddd_lite_time_slot" ).find( ":selected" ).val() );
} 

var current_date = orddd_lite_params.orddd_lite_current_day
if ( typeof( current_date ) != 'undefined' && current_date != '' ) {
	var split_current_date = current_date.split( '-' );
	var ordd_next_date     = new Date( split_current_date[ 2 ], ( split_current_date[ 1 ] - 1 ), split_current_date[ 0 ], orddd_lite_params.orddd_lite_current_hour, orddd_lite_params.orddd_lite_current_minute );
} else {
	var ordd_next_date = new Date();
}

ordd_next_date.setHours( ordd_next_date.getHours() + 2 );
localStorage.setItem( "orddd_lite_storage_next_time", ordd_next_date.getTime() );
}

/**
* This function is called on the page load to assign/populate the delivery date to the Delivery Date field.

* @function load_lite_functions
* @memberof orddd_lite_initialize
* @since 2.8
*/
function load_lite_functions() {
if ( orddd_lite_params.orddd_lite_auto_populate_first_available_date == "on" ) {
	orddd_lite_autofil_date_time();
}
var e_deliverydate_session = localStorage.getItem( 'orddd_deliverydate_lite_session' );
if ( typeof( e_deliverydate_session ) != 'undefined' && e_deliverydate_session != '' ) {
	var h_deliverydate_session = localStorage.getItem( 'h_deliverydate_lite_session' );
	if ( typeof( h_deliverydate_session ) != 'undefined' && h_deliverydate_session != '' && h_deliverydate_session != null ) {
		var default_date_arr = h_deliverydate_session.split( '-' );
		var default_date     = new Date( default_date_arr[ 1 ] + '/' + default_date_arr[ 0 ] + '/' + default_date_arr[ 2 ] );
		jQuery( '#e_deliverydate' ).datepicker( "setDate", default_date );
		jQuery( "#h_deliverydate" ).val( h_deliverydate_session );
	}
}
}

/**
* Auto-populates the first available delivery date in the Delivery Date field.
*
* @function orddd_lite_autofil_date_time
* @memberof orddd_lite_initialize
* @since 2.8
*/
function orddd_lite_autofil_date_time() {
var current_date       = orddd_lite_params.orddd_lite_current_day
var split_current_date = current_date.split( "-" );
var current_day        = new Date( split_current_date[ 1 ] + "/" + split_current_date[ 0 ] + "/" + split_current_date[ 2 ] );

var delay_date = orddd_lite_params.orddd_lite_minimumOrderDays;
if ( delay_date != "" ) {
	var split_date = delay_date.split( "-" );
	var delay_days = new Date( split_date[ 1 ] + "/" + split_date[ 0 ] + "/" + split_date[ 2 ] );
} else {
	var delay_days = current_day;
}

if ( isNaN( delay_days ) ) {
	delay_days = new Date();
	delay_days.setDate( delay_days.getDate() + 1 );
}

if ( delay_date != "" ) {
	delay_days = minimum_date_to_set( delay_days );

	if ( delay_days != '' ) {
		var min_date_to_set = delay_days.getDate() + "-" + ( delay_days.getMonth() + 1 ) + "-" + delay_days.getFullYear();
	}
}

var date_to_set = delay_days;
jQuery( '#e_deliverydate' ).datepicker( "setDate", date_to_set );
jQuery( 'input[name="e_deliverydate"]' ).val(min_date_to_set);
jQuery( "#h_deliverydate" ).val( min_date_to_set );

var inst = jQuery.datepicker._getInst( jQuery( "#e_deliverydate" )[0] );
orddd_set_date_from_session();
}

/**
* Update the date field based on session.
*/
function orddd_set_date_from_session() {
var e_deliverydate_session = localStorage.getItem( 'orddd_deliverydate_lite_session' ),
	h_deliverydate_session = localStorage.getItem( 'h_deliverydate_lite_session' );

if ( ! e_deliverydate_session ) {
	localStorage.setItem( "orddd_deliverydate_lite_session", jQuery( '#e_deliverydate' ).val() );
	e_deliverydate_session = jQuery( '#e_deliverydate' ).val();
}

if ( ! h_deliverydate_session ) {
	localStorage.setItem( "h_deliverydate_lite_session", jQuery( "#h_deliverydate" ).val() );
	h_deliverydate_session = jQuery( "#h_deliverydate" ).val();
}

if( typeof( e_deliverydate_session ) != 'undefined' && e_deliverydate_session != '' ) {
	if ( h_deliverydate_session ) {
		var default_date_arr = h_deliverydate_session.split( '-' );
		var default_date = new Date( default_date_arr[ 1 ] + '/' + default_date_arr[ 0 ] + '/' + default_date_arr[ 2 ] );
		
		var delay_weekday = default_date.getDay();
		var day           = 'orddd_lite_weekday_' + delay_weekday;
		var enabled       = dwd( default_date );

		var delay_date = orddd_lite_params.orddd_lite_minimumOrderDays

		if( delay_date != "" && typeof( delay_date ) != 'undefined' ) {
			 var split_date = delay_date.split( "-" );
			 var delay_days = new Date ( split_date[ 1 ] + "/" + split_date[ 0 ] + "/" + split_date[ 2 ] );
		} else {
			 var delay_days = new Date();
		}

		var date_to_set = minimum_date_to_set( delay_days );
		var session_date = '';
		if( undefined !== date_to_set && '' !== date_to_set ) {
			var session_date = date_to_set.getDate() + "-" + ( date_to_set.getMonth()+1 ) + "-" + date_to_set.getFullYear();
		}

		if( delay_days < default_date && enabled[0] == true ) {
			date_to_set = default_date;
		}else {
			h_deliverydate_session = session_date;
			localStorage.setItem( 'h_deliverydate_lite_session', h_deliverydate_session );
		}

		jQuery( '#e_deliverydate' ).datepicker( "setDate", date_to_set );
		jQuery( "#h_deliverydate" ).val( h_deliverydate_session );

		jQuery( "body" ).trigger( "update_checkout" );
		if ( 'on' == orddd_lite_params.orddd_lite_delivery_date_on_cart_page && orddd_lite_params.orddd_is_cart == '1') {
			jQuery( "#hidden_timeslot" ).val( jQuery( "#orddd_lite_time_slot" ).find(":selected").val() );
			if ( '1' !== orddd_lite_params.orddd_is_cart_block ) {
				jQuery( "body" ).trigger( "wc_update_cart" );
			}
		}

		var inst = jQuery.datepicker._getInst( jQuery( '#e_deliverydate' )[0] );
		show_times( h_deliverydate_session, inst );

	}
}
}

/**
* Calculates the first available date to be enabled in the calendar
*
* @function minimum_date_to_set
* @memberof orddd_lite_initialize
* @param {object} delay_days
* @returns {object} first available delivery date
* @since 2.8
*/
function minimum_date_to_set( delay_days ) {
var disabledDays = JSON.parse( '[' + orddd_lite_params.orddd_lite_holidays + ']' );
var holidays     = [];
for ( i = 0; i < disabledDays.length; i++ ) {
	var holidays_array = disabledDays[ i ].split( ":" );
	holidays[i]        = holidays_array[ 1 ];
}

var bookedDays = JSON.parse( "[" + orddd_lite_params.orddd_lite_lockout_days + "]" );

var current_date       = orddd_lite_params.orddd_lite_current_day
var split_current_date = current_date.split( "-" );
var current_day        = new Date( split_current_date[ 1 ] + "/" + split_current_date[ 0 ] + "/" + split_current_date[ 2 ] );

var delay_time      = delay_days.getTime();
var delay_weekday   = delay_days.getDay();
var current_time    = current_day.getTime();
var current_weekday = current_day.getDay();

var j;
for ( j = current_weekday; current_time <= delay_time; j++ ) {
	if ( j >= 0 ) {
		if ( orddd_lite_params.orddd_lite_calculate_min_time_disabled_days != 'on' ) {
			day       = 'orddd_lite_weekday_' + delay_weekday;
			day_check = orddd_lite_day_check( day );
			if ( day_check == '' ) {
				delay_days.setDate( delay_days.getDate() + 1 );
				delay_time = delay_days.getTime();
				delay_weekday   = delay_days.getDay();

				current_day.setDate( current_day.getDate() + 1 );
				current_time    = current_day.getTime();
				current_weekday = current_day.getDay();
			} else {
				if ( current_day <= delay_days ) {
					var m = delay_days.getMonth(), d = delay_days.getDate(), y = delay_days.getFullYear();
					if ( orddd_lite_params.orddd_lite_disable_for_holidays != 'yes' ) {
						if ( jQuery.inArray( ( m + 1 ) + '-' + d + '-' + y, holidays ) != -1 ||
							jQuery.inArray( ( m + 1 ) + '-' + d, holidays ) != -1 ) {
							delay_days.setDate( delay_days.getDate() + 1 );
							delay_time = delay_days.getTime();
						}
					}

					if ( jQuery.inArray( ( m + 1 ) + "-" + d + "-" + y, bookedDays ) != -1 ) {
						delay_days.setDate( delay_days.getDate() + 1 );
						delay_time = delay_days.getTime();
					}
					current_day.setDate( current_day.getDate() + 1 );
					current_time    = current_day.getTime();
					current_weekday = current_day.getDay();
				}
			}
		} else {
			if ( current_day <= delay_days ) {
				var m     = delay_days.getMonth(), d = delay_days.getDate(), y = delay_days.getFullYear();
				day       = 'orddd_lite_weekday_' + delay_days.getDay();
				day_check = orddd_lite_day_check( day );
				if ( day_check == '' ) {
					delay_days.setDate( delay_days.getDate() + 1 );
					delay_time = delay_days.getTime();
				} else if ( orddd_lite_params.orddd_lite_disable_for_holidays != 'yes' &&
					( jQuery.inArray( ( m + 1 ) + '-' + d + '-' + y, holidays ) != -1 ||
						jQuery.inArray( ( m + 1 ) + '-' + d, holidays ) != -1 )

					) {
					delay_days.setDate( delay_days.getDate() + 1 );
					delay_time = delay_days.getTime();
				} else if ( jQuery.inArray( ( m + 1 ) + "-" + d + "-" + y, bookedDays ) != -1 ) {
					delay_days.setDate( delay_days.getDate() + 1 );
					delay_time = delay_days.getTime();
				}
				current_day.setDate( current_day.getDate() + 1 );
				current_time    = current_day.getTime();
				current_weekday = current_day.getDay();
			}
		}
	} else {
		break;
	}
}

if ( delay_days != '' ) {
	if ( orddd_lite_params.orddd_lite_disable_for_holidays != 'yes' ) {
		for ( i = 0; i < holidays.length; i++ ) {
			var dm = delay_days.getMonth(), dd = delay_days.getDate(), dy = delay_days.getFullYear();
			if ( jQuery.inArray( ( dm + 1 ) + "-" + dd + "-" + dy, holidays ) != -1 ||
				jQuery.inArray( ( m + 1 ) + '-' + d, holidays ) != -1 ) {
				delay_days.setDate( delay_days.getDate() + 1 );
				delay_time = delay_days.getTime();
			}
		}
	}

	var dm = delay_days.getMonth(), dd = delay_days.getDate(), dy = delay_days.getFullYear();
	if ( jQuery.inArray( ( dm + 1 ) + "-" + dd + "-" + dy, bookedDays ) != -1 ) {
		delay_days.setDate( delay_days.getDate() + 1 );
		delay_time = delay_days.getTime();
	}
}

return delay_days;
}

/**
* This function disables the date in the calendar for holidays.
*
* @function nd
* @memberof orddd_lite_initialize
* @param {object} date - date to be checked
* @returns {bool} Returns true or false based on date available or not
* @since 2.8
*/
function nd( date ) {

var disabledDays = JSON.parse( '[' + orddd_lite_params.orddd_lite_holidays + ']' );
var m            = date.getMonth(), d = date.getDate(), y = date.getFullYear();
var currentdt    = date.getTime();

var delay_date = orddd_lite_params.orddd_lite_minimumOrderDays
if ( '' !== delay_date ) {
	var split_date = delay_date.split( '-' );
	var delay_days = new Date( split_date[1] + '/' + split_date[0] + '/' + split_date[2] );
	var dt = new Date( delay_days );
} else {
	var dt = new Date();
}
var today = dt.getTime();
for ( i = 0; i < disabledDays.length; i++ ) {
	var holidays_array = disabledDays[ i ].split( ":" );
	if ( holidays_array[ 1 ] == ( ( m + 1 ) + '-' + d + '-' + y ) ||
		holidays_array[ 1 ] == ( ( m + 1 ) + '-' + d ) ) {
		if ( '' == holidays_array[ 0 ] ) {
			return [ false, "holidays", jsL10n.holidayText ];
		} else {
			return [ false, "holidays", holidays_array[ 0 ]  ];
		}
	}
}
if ( currentdt >= today ) {
	var PartialBooked = JSON.parse( '[' + orddd_lite_params.orddd_lite_partial_days + ']' );
	if ( PartialBooked.length && jQuery.inArray( ( m + 1 ) + '-' + d + '-' + y, PartialBooked ) != -1 ) {
		return [ true, "partially-booked available-deliveries", 'Available' ];
	} else {
		return [ true, 'available-deliveries', 'Available' ];
	}
} else {
	return [ false ];
}
}

/**
* This function disables the date in the calendar for disabled weekdays and for which lockout is reached.
*
* @function dwd
* @memberof orddd_lite_initialize
* @param {object} date - date to be checked
* @returns {bool} Returns true or false based on date available or not
* @since 1.0
*/
function dwd( date ) {
var m           = date.getMonth(), d = date.getDate(), y = date.getFullYear();
var lockoutDays = JSON.parse( '[' + orddd_lite_params.orddd_lite_lockout_days + ']' );
for ( i = 0; i < lockoutDays.length; i++ ) {
	if ( jQuery.inArray( ( m + 1 ) + '-' + d + '-' + y, lockoutDays ) != -1 ) {
		return [ false, "booked_dates", jsL10n.bookedText ];
	}
}
var day = 'orddd_lite_weekday_' + date.getDay();
if ( orddd_lite_day_check( day ) != 'checked' ) {
	return [false];
}
return [true];
}

/**
* Check if the weekday is enabled based on the global or custom settings.
* @returns String
* @since 3.16.0
*/
function orddd_lite_day_check( day ) {
var weekday_enabled = orddd_lite_params[day];
return weekday_enabled;
}

/**
* The function is called for each day in the datepicker before it is displayed.
*
* @function chd
* @memberof orddd_lite_initialize
* @param {object} date - date to be checked
* @returns {array} Returns an array
* @since 1.0
*/
function chd( date ) {
var nW = dwd( date );
return nW[ 0 ] ? nd( date ) : nW;
}

/**
* This function is called just before the datepicker is displayed.
*
* @function avd
* @memberof orddd_lite_initialize
* @param {object} date - date to be checked
* @returns {object} options object to update the datepicker
* @since 1.0
*/
function avd( date, inst ) {
// Added to not translate the calendar when the site is translated using Google Translator. 
inst.dpDiv.addClass( 'notranslate' );

var disabledDays = JSON.parse( '[' + orddd_lite_params.orddd_lite_holidays + ']' );
var holidays     = [];
for ( i = 0; i < disabledDays.length; i++ ) {
	var holidays_array = disabledDays[ i ].split( ":" );
	holidays[i]        = holidays_array[ 1 ];
}

var bookedDays = JSON.parse( "[" + orddd_lite_params.orddd_lite_lockout_days + "]" );

var delay_date = orddd_lite_params.orddd_lite_minimumOrderDays
var split_date = delay_date.split( '-' );
var delay_days = new Date( split_date[1] + '/' + split_date[0] + '/' + split_date[2] );
delay_days.setDate( delay_days.getDate() );

var current_date       = orddd_lite_params.orddd_lite_current_day
var split_current_date = current_date.split( '-' );
var current_day        = new Date( split_current_date[ 1 ] + '/' + split_current_date[ 0 ] + '/' + split_current_date[ 2 ] );

var noOfDaysToFind = parseInt( orddd_lite_params.orddd_lite_number_of_dates );

if ( isNaN( delay_days ) ) {
	delay_days = new Date();
	delay_days.setDate( delay_days.getDate() + 1 );

}
if ( isNaN( noOfDaysToFind ) || 0 === noOfDaysToFind ) {
	noOfDaysToFind = 1000;
}

if ( delay_date != "" ) {
	var delay_time      = delay_days.getTime();
	var delay_weekday   = delay_days.getDay();
	var current_time    = current_day.getTime();
	var current_weekday = current_day.getDay();
	var j;
	for ( j = current_weekday; current_time <= delay_time; j++ ) {
		if ( j >= 0 ) {
			if ( orddd_lite_params.orddd_lite_calculate_min_time_disabled_days != 'on' ) {
				day       = 'orddd_lite_weekday_' + delay_weekday;
				day_check = orddd_lite_day_check( day );
				if ( day_check == '' ) {
					delay_days.setDate( delay_days.getDate() + 1 );
					delay_time = delay_days.getTime();
					delay_weekday   = delay_days.getDay();
					current_day.setDate( current_day.getDate() + 1 );
					current_time    = current_day.getTime();
					current_weekday = current_day.getDay();
				} else {
					if ( current_day <= delay_days ) {
						var m = delay_days.getMonth(), d = delay_days.getDate(), y = delay_days.getFullYear();
						if ( orddd_lite_params.orddd_lite_disable_for_holidays != 'yes' ) {
							if ( jQuery.inArray( ( m + 1 ) + '-' + d + '-' + y, holidays ) != -1 ||
								jQuery.inArray( ( m + 1 ) + '-' + d, holidays ) != -1 ) {
								delay_days.setDate( delay_days.getDate() + 1 );
								delay_time = delay_days.getTime();
							}
						}

						if ( jQuery.inArray( ( m + 1 ) + "-" + d + "-" + y, bookedDays ) != -1 ) {
							delay_days.setDate( delay_days.getDate() + 1 );
							delay_time = delay_days.getTime();
						}

						current_day.setDate( current_day.getDate() + 1 );
						current_time    = current_day.getTime();
						current_weekday = current_day.getDay();
					}
				}
			} else {
				break;
			}
		} else {
			break;
		}
	}
}

var minDate      = delay_days;
var date         = new Date();
var t_year       = date.getFullYear();
var t_month      = date.getMonth() + 1;
var t_day        = date.getDate();
var t_month_days = new Date( t_year, t_month, 0 ).getDate();

start           = ( delay_days.getMonth() + 1 ) + "/" + delay_days.getDate() + "/" + delay_days.getFullYear();
var start_month = delay_days.getMonth() + 1;
var start_year  = delay_days.getFullYear();

var end_date = new Date( ad( delay_days , noOfDaysToFind ) );
end          = ( end_date.getMonth() + 1 ) + "/" + end_date.getDate() + "/" + end_date.getFullYear();

var specific_max_date = start;
var m                 = date.getMonth(), d = date.getDate(), y = date.getFullYear();
var currentdt         = m + '-' + d + '-' + y;

var dt    = new Date();
var today = dt.getMonth() + '-' + dt.getDate() + '-' + dt.getFullYear();

var loopCounter = gd( start , end , 'days' );
var prev        = delay_days;

is_holiday_exclude = orddd_lite_params.is_holiday_exclude;
var new_l_end, is_holiday;
for ( var i = 1; i <= loopCounter; i++ ) {
	var l_start  = new Date( start );
	var l_end    = new Date( end );
	new_l_end    = l_end;
	var new_date = new Date( ad( l_start, i ) );

	var day    = "";
	day        = 'orddd_lite_weekday_' + new_date.getDay();
	day_check  = orddd_lite_day_check( day );
	is_holiday = nd( new_date );
	if ( day_check != "checked" || ( ! is_holiday[0]  && ! is_holiday_exclude ) ) {
		new_l_end   = l_end = new Date( ad( l_end, 2 ) );
		end         = ( l_end.getMonth() + 1 ) + "/" + l_end.getDate() + "/" + l_end.getFullYear();
		loopCounter = gd( start , end , 'days' );
	}
}

var maxMonth         = new_l_end.getMonth() + 1;
var maxYear          = new_l_end.getFullYear();
var number_of_months = parseInt( orddd_lite_params.orddd_lite_number_of_months );
if ( maxMonth > start_month || maxYear > start_year ) {
	return {
		minDate: new Date( start ),
		maxDate: l_end,
		numberOfMonths: number_of_months
	};
} else {
	return {
		minDate: new Date( start ),
		maxDate: l_end
	};
}
}

/**
* This function is called to find the end date to be set in the calendar.
*
* @function ad
* @memberof orddd_lite_initialize
* @param {object} dateObj
* @param {number} numDays - number of dates to choose
* @returns {number} returns the end date to be set in the calendar
* @since 1.0
*/
function ad( dateObj, numDays ) {
return dateObj.setDate( dateObj.getDate() + ( numDays - 1 ) );
}

/**
* This function is called to find the difference between the two dates.
*
* @function gd
* @memberof orddd_lite_initialize
* @param {string} date1 - start date
* @param {string} date2 - end date
* @param {string} interval - days
* @returns {number} returns the number between two dates.
* @since 1.0
*/
function gd( date1, date2, interval ) {
var second   = 1000,
minute       = second * 60,
hour         = minute * 60,
day          = hour * 24,
week         = day * 7;
date1        = new Date( date1 ).getTime();
date2        = (date2 == 'now') ? new Date().getTime() : new Date( date2 ).getTime();
var timediff = date2 - date1;
if ( isNaN( timediff ) ) {
	return NaN;
}
switch ( interval ) {
	case "years":
		return date2.getFullYear() - date1.getFullYear();
	case "months":
		return ( (date2.getFullYear() * 12 + date2.getMonth() ) - ( date1.getFullYear() * 12 + date1.getMonth() ) );
	case "weeks":
		return Math.floor( timediff / week );
	case "days":
		return Math.floor( timediff / day ) + 1;
	case "hours":
		return Math.floor( timediff / hour );
	case "minutes":
		return Math.floor( timediff / minute );
	case "seconds":
		return Math.floor( timediff / second );
	default:
		return undefined;
}
}


/** 
* Load the time slots in the time slot dropdown on select of date
*
* @function orddd_load_time_slots
* @param {string} Response returned from the ajax call
* @since
*/
function orddd_load_time_slots( response ) {
var orddd_time_slots = response.split( "/" );
jQuery( "#orddd_lite_time_slot" ).empty();
var selected_value = '';
for( i = 0; i < orddd_time_slots.length; i++ ) {
	var time_slot_to_display = orddd_time_slots[ i ].split( "_" );
	if( 'select' == time_slot_to_display[ 0 ].replace(/\s/g, "") ) {
		jQuery( "#orddd_lite_time_slot" ).append( jQuery( "<option></option>" ).attr( { value:"select", selected:"selected" } ).text( jsL10n.selectText ) );
		selected_value = orddd_time_slots[ i ];
	} else if( 'asap' == time_slot_to_display[ 0 ] ) {
		if( typeof time_slot_to_display[ 3 ] != 'undefined' ) {
			jQuery( "#orddd_lite_time_slot option:selected" ).removeAttr( "selected" );
			jQuery( "#orddd_lite_time_slot" ).append( jQuery( "<option></option>" ).attr( {value:time_slot_to_display[ 0 ], selected:"selected"}).text( jsL10n.asapText ) );
			selected_value = time_slot_to_display[ 0 ];    
		} else {
			jQuery( "#orddd_lite_time_slot" ).append( jQuery( "<option></option>" ).attr( {value:time_slot_to_display[ 0 ]} ).text( jsL10n.asapText ) );
		}
	} else if( 'NA' == time_slot_to_display[ 0 ] ) {
		if( typeof time_slot_to_display[ 3 ] != 'undefined' ) {
			jQuery( "#orddd_lite_time_slot option:selected" ).removeAttr( "selected" );
			jQuery( "#orddd_lite_time_slot" ).append( jQuery( "<option></option>" ).attr( {value:time_slot_to_display[ 0 ], selected:"selected"}).text( jsL10n.NAText ) );
			selected_value = time_slot_to_display[ 0 ];    
		} else {
			jQuery( "#orddd_lite_time_slot" ).append( jQuery( "<option></option>" ).attr( {value:time_slot_to_display[ 0 ]} ).text( jsL10n.NAText ) );
		}
	} else if( typeof time_slot_to_display[ 3 ] != 'undefined' ) {
		jQuery( "#orddd_lite_time_slot option:selected" ).removeAttr( "selected" );
		if( typeof time_slot_to_display[ 2 ] != 'undefined' && time_slot_to_display[ 2 ] != '' ) {
			var time_slot_charges = decodeHtml( time_slot_to_display[ 2 ] );
			jQuery( "#orddd_lite_time_slot" ).append( jQuery( "<option></option>" ).attr( {value:time_slot_to_display[ 0 ], selected:"selected"}).text( time_slot_to_display[ 1 ] + " " + time_slot_charges ) );
		} else {
			jQuery( "#orddd_lite_time_slot" ).append( jQuery( "<option></option>" ).attr( {value:time_slot_to_display[ 0 ], selected:"selected"}).text( time_slot_to_display[ 1 ] ) );
		}
		selected_value = time_slot_to_display[ 0 ];
	} else {
		if( typeof time_slot_to_display[ 2 ] != 'undefined' && time_slot_to_display[ 2 ] != '' ) {
			var time_slot_charges = decodeHtml( time_slot_to_display[ 2 ] );
			jQuery( "#orddd_lite_time_slot" ).append( jQuery( "<option></option>" ).attr( "value", time_slot_to_display[ 0 ] ).text( time_slot_to_display[ 1 ] + " " + time_slot_charges ) );
		} else {
			jQuery( "#orddd_lite_time_slot" ).append( jQuery( "<option></option>" ).attr( "value", time_slot_to_display[ 0 ] ).text( time_slot_to_display[ 1 ] ) );
		}
	}
}

/**
 * Decodes the html entities for currency symbol.
 *
 * @function decodeHtml
 * @param {string} html - String to decode
 * @returns {string} Decoded string.
 * @since 8.0
 */
function decodeHtml(html) {
	var txt 	  = document.createElement("textarea");
	txt.innerHTML = html;
	return txt.value;
}
}

/**
* Saves the delivery information which are changed in the admin Orders page
*
* @function save_delivery_dates
* @memberof orddd_initialize_functions
* @param {string} notify - Yes/No
* @since 3.13.0
*/
function save_delivery_dates( notify ) {
var data = {
	order_id: orddd_lite_admin_params.orddd_lite_order_id,
	e_deliverydate: jQuery( '#e_deliverydate').val(),
	h_deliverydate: jQuery( "#h_deliverydate" ).val(),
	orddd_time_slot: jQuery( "#orddd_lite_time_slot option:selected" ).val(),
	orddd_post_type: orddd_lite_admin_params.orddd_post_type,
	orddd_notify_customer: notify,
	orddd_charges: jQuery( '#del_charges' ).val(),
	action: "save_delivery_dates"
};

if( orddd_lite_admin_params.orddd_lite_admin_url != '' && typeof( orddd_lite_admin_params.orddd_lite_admin_url ) != 'undefined' ) {
	jQuery( "#orddd_lite_update_notice" ).html( 'Updating delivery details...' );
	jQuery.post( orddd_lite_admin_params.orddd_lite_admin_url + 'admin-ajax.php', data, function( response ) {
		var validations = response.split( "," );
		if ( validations[ 0 ] == "yes" && validations[ 1 ] == "yes" && validations[2] == "yes" ) {
			jQuery( "#orddd_lite_update_notice" ).html( "Delivery details have been updated." );
			jQuery( "#orddd_lite_update_notice" ).attr( "color", "green" );
			jQuery( "#orddd_lite_update_notice" ).fadeIn();
			setTimeout( function() {
				jQuery( "#orddd_lite_update_notice" ).fadeOut();
			},3000 );
		} else if ( validations[ 0 ] == "no" && ( orddd_lite_admin_params.orddd_lite_date_field_mandatory == "checked" ) ) {
			jQuery( "#orddd_lite_update_notice" ).html( orddd_lite_admin_params.orddd_lite_field_label + " is mandatory." );
			jQuery( "#orddd_lite_update_notice" ).attr( "color", "red" );
			jQuery( "#orddd_lite_update_notice" ).fadeIn();
			setTimeout( function() {
				jQuery( "#orddd_lite_update_notice" ).fadeOut();
			},3000 );
		} else if ( validations[ 1 ] == "no" && ( ( orddd_lite_admin_params.orddd_lite_enable_time_slot == "on" && orddd_lite_admin_params.orddd_lite_time_slot_mandatory == "checked" ) ) ) {
			jQuery( "#orddd_lite_update_notice" ).html( orddd_lite_admin_params.orddd_lite_timeslot_field_label + " is mandatory." );
			jQuery( "#orddd_lite_update_notice" ).attr( "color", "red" );
			jQuery( "#orddd_lite_update_notice" ).fadeIn();
			setTimeout( function() {
				jQuery( "#orddd_lite_update_notice" ).fadeOut();
			},3000 );
		}
	});
}
}

jQuery.ajaxPrefilter( function(options, originalOptions, jqXHR) {

const query = options.url.substring(options.url.indexOf('?') + 1);	
var urlParams = new URLSearchParams(query);
const wcAjax = urlParams.get('wc-ajax');

if ( 'checkout' == wcAjax && options.data.indexOf( 'h_deliverydate' ) == -1 ) {	
	if( '' !== jQuery('#h_deliverydate').val() ){
		options.data += '&h_deliverydate=' + jQuery('#h_deliverydate').val();
	}
}

})

jQuery(document).ready( function() {
	var formats = ["MM d, yy","MM d, yy"];
    
    jQuery.extend( jQuery.datepicker, { afterShow: function( event ) {
		jQuery.datepicker._getInst( event.target ).dpDiv.css( "z-index", 9999 );
        if ( jQuery( "#orddd_lite_number_of_months" ).val() == "1" ) {
            jQuery.datepicker._getInst( event.target ).dpDiv.css( "width", "300px" );
        } else {
            jQuery.datepicker._getInst( event.target ).dpDiv.css( "width", "40em" );
        }
	} });

    jQuery( "#e_deliverydate" ).val("").datepicker( { 
    	dateFormat: jQuery( "#orddd_lite_delivery_date_format" ).val(), 
    	firstDay: parseInt( jQuery( "#orddd_first_day_of_week" ).val() ), 
    	beforeShow: avd, 
    	beforeShowDay: chd, 
    	showButtonPanel: true,
    	closeText: jsL10n.clearText,
    	onSelect: orddd_on_select_date,
        onClose:function( dateStr, inst ) {
            if ( dateStr != "" ) {
                var monthValue = inst.selectedMonth+1;
                var dayValue = inst.selectedDay;
                var yearValue = inst.selectedYear;
                var all = dayValue + "-" + monthValue + "-" + yearValue;
                jQuery( "#h_deliverydate" ).val( all );
                // If "Clear" gets clicked, then really clear it
                var event = arguments.callee.caller.caller.arguments[0];
                if( typeof( event ) !== "undefined" ) {
                    if ( jQuery( event.delegateTarget ).hasClass( "ui-datepicker-close" ) ) {
                        jQuery( this ).val( "" ); 
                        jQuery( "#h_deliverydate" ).val( "" );
                    }
                }
            }
            jQuery( "#e_deliverydate" ).blur();
        }            
    }).focus( function ( event ) {
        jQuery(this).trigger( "blur" );
        jQuery.datepicker.afterShow( event );
    });

    if ( jQuery( "#orddd_lite_field_note" ).val() != '' ) {
        jQuery( "#e_deliverydate_field" ).append( "<small class='orddd_lite_field_note'>" + jQuery( "#orddd_lite_field_note" ).val() + "</small>" );
    }

    window.onload = load_lite_functions;
});

function orddd_on_select_date( date, inst ) {
	var monthValue = inst.selectedMonth+1;
    var dayValue = inst.selectedDay;
    var yearValue = inst.selectedYear;
    var all = dayValue + "-" + monthValue + "-" + yearValue;

	var data = {
        e_deliverydate: jQuery( "#e_deliverydate" ).val(),
        h_deliverydate: all,
        action: "orddd_lite_update_delivery_session"
    };
    
    jQuery.post( jQuery( '#orddd_admin_url' ).val() + "admin-ajax.php", data, function( response ) {
    });
}

function load_lite_functions() {
	if( jQuery( "#orddd_lite_auto_populate_first_available_date" ).val() == "on" ) {
		orddd_lite_autofil_date_time();
    }

    if( typeof( jQuery( '#e_deliverydate_lite_session' ).val() ) != 'undefined' && jQuery( '#e_deliverydate_lite_session' ).val() != '' ) {
        var e_deliverydate_session = jQuery( '#e_deliverydate_lite_session' ).val();
        var h_deliverydate_session = jQuery( '#h_deliverydate_lite_session' ).val();
        var default_date_arr = h_deliverydate_session.split( '-' );
        var default_date = new Date( default_date_arr[ 1 ] + '/' + default_date_arr[ 0 ] + '/' + default_date_arr[ 2 ] );
        jQuery( '#e_deliverydate' ).datepicker( "setDate", default_date );
        jQuery( "#h_deliverydate" ).val( h_deliverydate_session );
        
    }
}

function orddd_lite_autofil_date_time() {
	var current_date = jQuery( "#orddd_lite_current_day" ).val();
	var split_current_date = current_date.split( "-" );
	var current_day = new Date ( split_current_date[ 1 ] + "/" + split_current_date[ 0 ] + "/" + split_current_date[ 2 ] );
	
	var delay_date = jQuery( "#orddd_lite_minimumOrderDays" ).val();
	if( delay_date != "" ) {
		var split_date = delay_date.split( "-" );
		var delay_days = new Date ( split_date[ 1 ] + "/" + split_date[ 0 ] + "/" + split_date[ 2 ] );
	} else {
		var delay_days = current_day;
	}
	
	if ( isNaN( delay_days ) ) {
		delay_days = new Date();
    	delay_days.setDate( delay_days.getDate()+1 );
	}
	
	if( delay_date != "" ) {
		delay_days = minimum_date_to_set( delay_days );
        if( delay_days != '' ) {
			var min_date_to_set = delay_days.getDate() + "-" + ( delay_days.getMonth()+1 ) + "-" + delay_days.getFullYear();
        }
	}

	var date_to_set = delay_days;
	jQuery( '#e_deliverydate' ).datepicker( "setDate", date_to_set );
	jQuery( "#h_deliverydate" ).val( min_date_to_set );
}

function minimum_date_to_set( delay_days ) {
	var disabledDays = eval( "[" + jQuery( "#orddd_lite_holidays" ).val() + "]" );
	var holidays = [];
	for ( i = 0; i < disabledDays.length; i++ ) {
		var holidays_array = disabledDays[ i ].split( ":" );
		holidays[i] = holidays_array[ 1 ];
	}

	var bookedDays = eval( "[" + jQuery( "#orddd_lite_lockout_days" ).val() + "]" );

	var current_date = jQuery( "#orddd_lite_current_day" ).val();
	var split_current_date = current_date.split( "-" );
	var current_day = new Date ( split_current_date[ 1 ] + "/" + split_current_date[ 0 ] + "/" + split_current_date[ 2 ] );

	var delay_time = delay_days.getTime();
    var current_time = current_day.getTime();
    var current_weekday = current_day.getDay();
    
	var j;
	for ( j = current_weekday ; current_time <= delay_time ; j++ ) {
		if( j >= 0 ) {
			if ( jQuery( "#orddd_lite_calculate_min_time_disabled_days" ).val() != 'on' ) {
				day = 'orddd_lite_weekday_' + current_weekday;
				day_check = jQuery( "#" + day ).val();
				if ( day_check == '' ) {
					delay_days.setDate( delay_days.getDate()+1 );
					delay_time = delay_days.getTime();
					current_day.setDate( current_day.getDate()+1 );
					current_time = current_day.getTime();
					current_weekday = current_day.getDay();
				} else {
					if( current_day <= delay_days ) {
						var m = current_day.getMonth(), d = current_day.getDate(), y = current_day.getFullYear();
						if( jQuery.inArray( ( m+1 ) + '-' + d + '-' + y, holidays ) != -1 ) {	
							delay_days.setDate( delay_days.getDate()+1 );
							delay_time = delay_days.getTime();
						}
						current_day.setDate( current_day.getDate()+1 );
						current_time = current_day.getTime();
						current_weekday = current_day.getDay();
					}
				}
			} else {
				day = 'orddd_lite_weekday_' + current_weekday;
				day_check = jQuery( "#" + day ).val();
				if ( day_check == '' ) {
					delay_days.setDate( delay_days.getDate()+1 );
					delay_time = delay_days.getTime();
					current_day.setDate( current_day.getDate()+1 );
					current_time = current_day.getTime();
					current_weekday = current_day.getDay();
				} else {
					if( current_day <= delay_days ) {
						var m = current_day.getMonth(), d = current_day.getDate(), y = current_day.getFullYear();
						if( jQuery.inArray( ( m+1 ) + '-' + d + '-' + y, holidays ) != -1 ) {	
							delay_days.setDate( delay_days.getDate()+1 );
							delay_time = delay_days.getTime();
						}
						current_day.setDate( current_day.getDate()+1 );
						current_time = current_day.getTime();
						current_weekday = current_day.getDay();
					}
				}
			}
		} else {
			break;
		}
	}
	
    if( delay_days != '' ) {
    	for ( i = 0; i < holidays.length; i++ ) {
	        var dm = delay_days.getMonth(), dd = delay_days.getDate(), dy = delay_days.getFullYear();
	        if( jQuery.inArray( ( dm+1 ) + "-" + dd + "-" + dy, holidays ) != -1 ) {
	            delay_days.setDate( delay_days.getDate()+1 );
	            delay_time = delay_days.getTime();
	        }
	    }

        var dm = delay_days.getMonth(), dd = delay_days.getDate(), dy = delay_days.getFullYear();
        if( jQuery.inArray( ( dm+1 ) + "-" + dd + "-" + dy, bookedDays ) != -1 ) {
            delay_days.setDate( delay_days.getDate()+1 );
            delay_time = delay_days.getTime();
        } 
    }
    
	return delay_days;
}

function nd( date ) {
	var disabledDays = eval( '[' + jQuery( '#orddd_lite_holidays' ).val() + ']' );
	var m = date.getMonth(), d = date.getDate(), y = date.getFullYear();
	var currentdt = m + '-' + d + '-' + y;
	
	var dt = new Date();
	var today = dt.getMonth() + '-' + dt.getDate() + '-' + dt.getFullYear();
	for ( i = 0; i < disabledDays.length; i++ ) {
		var holidays_array = disabledDays[ i ].split( ":" );
		if( holidays_array[ 1 ] == ( ( m+1 ) + '-' + d + '-' + y ) ) {
			if( '' == holidays_array[ 0 ] ) {
				return [ false, "", jsL10n.holidayText ];
			} else {
				return [ false, "", holidays_array[ 0 ]  ];
			}
		}
	}
	return [ true ];
}

function dwd( date ) {
	var m = date.getMonth(), d = date.getDate(), y = date.getFullYear();
	var lockoutDays = eval( '[' + jQuery( '#orddd_lite_lockout_days' ).val() + ']' );
	for ( i = 0; i < lockoutDays.length; i++ ) {
		if( jQuery.inArray( ( m+1 ) + '-' + d + '-' + y, lockoutDays ) != -1 ) {
			return [ false, "", jsL10n.bookedText ];
		}
	}
	var day = 'orddd_lite_weekday_' + date.getDay();
	if ( jQuery( "#" + day ).val() != 'checked' ) {
		return [false];
	}
	return [true];
}

function chd( date ) {
	var nW = dwd( date );
	return nW[ 0 ] ? nd( date ) : nW;
}

function avd( date ) {
	var disabledDays = eval( "[" + jQuery( "#orddd_lite_holidays" ).val() + "]" );
	var holidays = [];
	for ( i = 0; i < disabledDays.length; i++ ) {
		var holidays_array = disabledDays[ i ].split( ":" );
		holidays[i] = holidays_array[ 1 ];
	}

	var delay_date = jQuery( "#orddd_lite_minimumOrderDays" ).val();
	var split_date = delay_date.split('-');
	var delay_days = new Date ( split_date[1] + '/' + split_date[0] + '/' + split_date[2] );
	delay_days.setDate( delay_days.getDate() );
	
	var current_date = jQuery( "#orddd_lite_current_day" ).val();
	var split_current_date = current_date.split( '-' );
	var current_day = new Date ( split_current_date[ 1 ] + '/' + split_current_date[ 0 ] + '/' + split_current_date[ 2 ] );

	var noOfDaysToFind = parseInt( jQuery( "#orddd_lite_number_of_dates" ).val() );
	
	if( isNaN( delay_days ) ) {
		delay_days = new Date();
		delay_days.setDate( delay_days.getDate()+1 );
	
	}
	if( isNaN( noOfDaysToFind ) || 0 === noOfDaysToFind ) {
		noOfDaysToFind = 1000;
	}
	
	if( delay_date != "" ) {
		var delay_time = delay_days.getTime();
		var current_time = current_day.getTime();
		var current_weekday = current_day.getDay();
		var j;
		for ( j = current_weekday ; current_time <= delay_time ; j++ ) {
			if( j >= 0 ) {
				if ( jQuery( "#orddd_lite_calculate_min_time_disabled_days" ).val() != 'on' ) {
					day = 'orddd_lite_weekday_' + current_weekday;
					day_check = jQuery( "#" + day ).val();
					if ( day_check == '' ) {
						delay_days.setDate( delay_days.getDate()+1 );
						delay_time = delay_days.getTime();
						current_day.setDate( current_day.getDate()+1 );
						current_time = current_day.getTime();
						current_weekday = current_day.getDay();
					} else {
						if( current_day <= delay_days ) {
							var m = current_day.getMonth(), d = current_day.getDate(), y = current_day.getFullYear();
							if( jQuery.inArray( ( m+1 ) + '-' + d + '-' + y, holidays ) != -1 ) {	
								delay_days.setDate( delay_days.getDate()+1 );
								delay_time = delay_days.getTime();
							}
							current_day.setDate( current_day.getDate()+1 );
							current_time = current_day.getTime();
							current_weekday = current_day.getDay();
						}
					}
				} else {
					if( current_day <= delay_days ) {
						var m = current_day.getMonth(), d = current_day.getDate(), y = current_day.getFullYear();
						if( jQuery.inArray( ( m+1 ) + '-' + d + '-' + y, holidays ) != -1 ) {	
							delay_days.setDate( delay_days.getDate()+1 );
							delay_time = delay_days.getTime();
						}
						current_day.setDate( current_day.getDate()+1 );
						current_time = current_day.getTime();
						current_weekday = current_day.getDay();
					}
				}
			} else {
				break;
			}
		}
	}

	var minDate = delay_days;
	var date = new Date();
	var t_year = date.getFullYear();
	var t_month = date.getMonth()+1;
	var t_day = date.getDate();
	var t_month_days = new Date( t_year, t_month, 0 ).getDate();
	
	start = ( delay_days.getMonth()+1 ) + "/" + delay_days.getDate() + "/" + delay_days.getFullYear();
	var start_month = delay_days.getMonth()+1;
	var start_year = delay_days.getFullYear();
	
	var end_date = new Date( ad( delay_days , noOfDaysToFind ) );
	end = ( end_date.getMonth()+1 ) + "/" + end_date.getDate() + "/" + end_date.getFullYear();
	
	var specific_max_date = start;
	var m = date.getMonth(), d = date.getDate(), y = date.getFullYear();
	var currentdt = m + '-' + d + '-' + y;
	
	var dt = new Date();
	var today = dt.getMonth() + '-' + dt.getDate() + '-' + dt.getFullYear();
	
	var loopCounter = gd(start , end , 'days');
	var prev = delay_days;
	var new_l_end, is_holiday;
	for( var i = 1; i <= loopCounter; i++ ) {
		var l_start = new Date( start );
		var l_end = new Date( end );
		new_l_end = l_end;
		var new_date = new Date( ad( l_start, i ) );

		var day = "";
		day = 'orddd_lite_weekday_' + new_date.getDay();
		day_check = jQuery( "#" + day ).val();
		is_holiday = nd( new_date );
		if( day_check != "checked" || is_holiday != 'true' ) {
			new_l_end = l_end = new Date( ad( l_end, 2 ) );
			end = ( l_end.getMonth()+1 ) + "/" + l_end.getDate() + "/" + l_end.getFullYear();
			loopCounter = gd(start , end , 'days');
		}
	}

	var maxMonth = new_l_end.getMonth()+1;
	var maxYear = new_l_end.getFullYear();
	var number_of_months = parseInt( jQuery( "#orddd_lite_number_of_months" ).val() );
	if ( maxMonth > start_month || maxYear > start_year ) {
		return {
			minDate: new Date(start),
	        maxDate: l_end,
			numberOfMonths: number_of_months 
	    };
	}
	else {
		return {
			minDate: new Date(start),
			maxDate: l_end
		};
	}
}

function ad( dateObj, numDays ) {
	return dateObj.setDate( dateObj.getDate() + ( numDays - 1 ) );
}

function gd( date1, date2, interval ) {
	var second = 1000,
	minute = second * 60,
	hour = minute * 60,
	day = hour * 24,
	week = day * 7;
	date1 = new Date(date1).getTime();
	date2 = (date2 == 'now') ? new Date().getTime() : new Date( date2 ).getTime();
	var timediff = date2 - date1;
	if ( isNaN( timediff ) ) return NaN;
		switch ( interval ) {
		case "years":
			return date2.getFullYear() - date1.getFullYear();
		case "months":
			return ( (date2.getFullYear() * 12 + date2.getMonth() ) - ( date1.getFullYear() * 12 + date1.getMonth() ) );
		case "weeks":
			return Math.floor( timediff / week );
		case "days":
			return Math.floor( timediff / day );
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

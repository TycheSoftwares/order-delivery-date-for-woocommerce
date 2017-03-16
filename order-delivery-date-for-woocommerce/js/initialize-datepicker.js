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
				return [ false, "", "Holiday" ];
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
			return [ false, "", "Booked" ];
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
	var delay_date = jQuery( "#orddd_lite_minimumOrderDays" ).val();
	var split_date = delay_date.split('-');
	var delay_days = new Date ( split_date[1] + '/' + split_date[0] + '/' + split_date[2] );
	delay_days.setDate( delay_days.getDate() );
	var noOfDaysToFind = parseInt( jQuery( "#orddd_lite_number_of_dates" ).val() );
	
	if( isNaN( delay_days ) ) {
		//delay_days = 0;
		delay_days = new Date();
		delay_days.setDate( delay_days.getDate()+1 );
	}
	if( isNaN( noOfDaysToFind ) ) {
		noOfDaysToFind = 1000;
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

		if( day_check != "checked" ) {
			new_l_end = l_end = new Date( ad( l_end, 1 ) );
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
	return dateObj.setDate( dateObj.getDate() + numDays );
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


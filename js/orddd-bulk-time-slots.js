jQuery(document).ready( function( $ ) {

	var time_format = 'H:i';
	if( '1' == jsL10n.time_format ) {
		time_format = 'h:i A';
	}

    $('#orddd_lite_time_slot_ends_at').timepicker({ 
		'scrollDefault': 'now', 
		'timeFormat' : time_format,
		'step'	: 15,
		'listWidth' : 1,
	});

	$('#orddd_lite_time_slot_starts_from').timepicker({ 
		'scrollDefault': 'now', 
		'timeFormat' : time_format,
		'step'	: 15,
		'listWidth' : 1,
	});

	$('#orddd_lite_time_from_hours, #orddd_lite_time_to_hours').timepicker({ 
		'scrollDefault': 'now', 
		'timeFormat' : time_format,
		'step'	: 15,
		'listWidth' : 1,
	});

	$( '#orddd_lite_individual_time_slot_page' ).hide();
	$( '#orddd_lite_bulk_time_slot_page' ).hide();
	$( '#orddd_lite_individual' ).on( 'click', function(e) {
		e.preventDefault();
		$( '#orddd_lite_individual_time_slot_page' ).show();
		$( '#orddd_lite_bulk_time_slot_page' ).hide();
		$( '#orddd_lite_individual_or_bulk' ).val('individual');
	});

	$( '#orddd_lite_bulk' ).on( 'click', function(e) {
		e.preventDefault();
		$( '#orddd_lite_individual_time_slot_page' ).hide();
		$( '#orddd_lite_bulk_time_slot_page' ).show();
		$( '#orddd_lite_individual_or_bulk' ).val('bulk');
	});
	var count = 0;
	
	$( '#add_another_slot' ).on( 'click', function(e) {
		e.preventDefault();
		if( count < 0 ){
			count = 0;
		}
		count++;

		$( '.add-timeslot' ).parent().append( `
			<section class="add-timeslot">
				<input type="text" name="orddd_lite_time_from_hours[]" id="orddd_lite_time_from_hours_${count}" value=""/> To
				<input type="text" name="orddd_lite_time_to_hours[]" id="orddd_lite_time_to_hours_${count}" value=""/> 
				<a href="#" class="orddd_lite_remove_slot" role="button">Remove</a>
			</section> 
		` );
		$('#orddd_lite_time_from_hours_' + count ).timepicker({ 
			'scrollDefault': 'now', 
			'timeFormat' : time_format,
			'step'	: 15,
			'listWidth' : 1,
		});
	
		$('#orddd_lite_time_to_hours_' + count ).timepicker({ 
			'scrollDefault': 'now', 
			'timeFormat' : time_format,
			'step'	: 15,
			'listWidth' : 1,
		});
	});

	$( 'body' ).on( 'click', 'a.orddd_lite_remove_slot', function(e) {
		count--;
		$(this).parent().remove();
		e.preventDefault();
	});
});


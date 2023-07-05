jQuery( document ).ready( function(){
    var first_day = 0;
    if ( 'object' === typeof orddd_lite_params ) {
        if ( '' != orddd_lite_params['orddd_first_day_of_week'] ) {
            first_day = orddd_lite_params['orddd_first_day_of_week'];
        }
    }
    
    jQuery( '#orddd_lite_custom_startdate, #orddd_lite_custom_enddate' ) 
    .datepicker({
        firstDay: parseInt( first_day ),
        dateFormat: "yy-mm-dd",
        onSelect: function(selected,evnt) {
            if ( 'orddd_lite_custom_startdate' == evnt.id ) {
                jQuery('#orddd_lite_custom_enddate').datepicker('option', 'minDate', new Date( selected ) );
                if ( jQuery( '#orddd_lite_custom_enddate' ).val() == '' ) {
                    jQuery( '#orddd_lite_custom_enddate' ).val( selected );
                }
            }

            if ( 'orddd_lite_custom_enddate' == evnt.id ) {
                if ( jQuery( '#orddd_lite_custom_startdate' ).val() == '' ) {
                    jQuery( '#orddd_lite_custom_startdate' ).val( selected );
                }
            }
        }
    });

    jQuery( 'select#order_delivery_date_lite_filter' ).select2();

    // If the filter dropdown will have the pre-selected the custom option on page load, then It will show the start and end date
    if ( 'custom' === jQuery( '.orddd_filter' ).val() ){
        jQuery( "#orddd_lite_custom_startdate" ).removeAttr( "style" );
        jQuery( "#orddd_lite_custom_enddate" ).removeAttr( "style" );
    }

    jQuery( '.orddd_filter' ).on( 'change', function(){
        let value = jQuery( '.orddd_filter' ).val();
        if ( 'custom' === value ) {
            jQuery( "#orddd_lite_custom_startdate" ).removeAttr( "style" );
            jQuery( "#orddd_lite_custom_enddate" ).removeAttr( "style" );
        } else {
            jQuery( "#orddd_lite_custom_startdate" ).css( "display", "none" );
            jQuery( "#orddd_lite_custom_enddate" ).css( "display", "none" );
            jQuery( '#orddd_lite_custom_startdate' ).val('');
            jQuery( '#orddd_lite_custom_enddate' ).val('');
        }
    });
});


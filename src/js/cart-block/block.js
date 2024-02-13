/**
 * External dependencies
 */
import { useEffect, useState, useCallback } from '@wordpress/element';
import { useSelect, useDispatch } from '@wordpress/data';
import { VALIDATION_STORE_KEY, CHECKOUT_STORE_KEY } from '@woocommerce/block-data';
import DeliveryDate from './deliveryDate';
import TimeSlot from './timeSlot';
import { __ } from '@wordpress/i18n';
import LoadingMask  from '../loadingMask';

const Block = ({ extensions, cart }) => {
	const [ isLoading, setIsLoading ] = useState( false );
	const [ isRequired, setIsRequired ] = useState( true );
	const [ updateSession, setUpdateSession ] = useState('');

	useEffect( () => {
		jQuery( document ).ready( function() {
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
				window.onload = load_lite_functions();
		});
		
		
		
	}, [] );

	const setLoading = ( load ) => {
		setIsLoading( load );
	}

    return (
		<LoadingMask
			isLoading={ isLoading }
			screenReaderLabel={ __(
				'Loading delivery date�',
				'order-delivery-date'
			) }
			showSpinner={ true }
		>
			<div
				className={ 'orddd-lite-checkout-fields' }
			>

				<DeliveryDate  
					updateSession={updateSession}
					setLoading={setLoading}
				/>

				<TimeSlot 
					updateSession={updateSession} 
					setLoading={setLoading}
				/>

				<input type="hidden" id="h_deliverydate" name="h_deliverydate" value="" />
			</div>
		</LoadingMask>
	);
}

export default Block;
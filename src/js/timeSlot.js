/**
 * External dependencies
 */
import { useEffect, useState, useCallback } from '@wordpress/element';
import { ComboboxControl } from '@wordpress/components';
import { extensionCartUpdate, ValidationInputError } from '@woocommerce/blocks-checkout';
import { useSelect, useDispatch } from '@wordpress/data';
import { VALIDATION_STORE_KEY } from '@woocommerce/block-data';
import classnames from 'classnames';

import { __ } from '@wordpress/i18n';

const TimeSlot = ({ checkoutData, updateSession, setLoading }) => {
    const [ isRequired, setIsRequired ] = useState( true );
    const [ timeOptions, setTimeOptions ] = useState([]);
    const [ timeSlot, setTimeSlot ] = useState('');
	const [ timeLabel, setTimeLabel ] = useState( __( 'Time Slot', 'order-delivery-date' ) );
    const { setExtensionData } = checkoutData;
    const [ isEnabled, setIsEnabled ] = useState( false );
	const [ isDeliveryEnabled, setIsDeliveryEnabled ] = useState( true ) ;
	const { setValidationErrors, clearValidationError } = useDispatch( VALIDATION_STORE_KEY );
    const errorId = 'orddd_lite_time_slot';
	const errorMessage = __( 'Please select a time slot', 'order-delivery-date' );

    useEffect(() => {
    	if( orddd_lite_params.orddd_lite_enable_time_slot !== "on" ) {
    		setIsDeliveryEnabled( false );
    	}

		if ( !isDeliveryEnabled ) {
			return;
		}

		let timeValue = 'undefined' !== localStorage.getItem( 'orddd_lite_time_slot' ) ? localStorage.getItem( 'orddd_lite_time_slot' ) : 'select';
		setTimeSlot( timeValue );
		setIsRequired( 'checked' === orddd_lite_params.orddd_lite_time_slot_mandatory ? true : false );
		setTimeLabel(orddd_lite_params.orddd_lite_delivery_timeslot_field_label);

		setExtensionData( 'order-delivery-date', 'orddd_lite_time_slot', timeSlot );
        setExtensionData( 'order-delivery-date', 'time_slot_mandatory', isRequired );
        setExtensionData( 'order-delivery-date', 'time_field_label', timeLabel );

		jQuery( document ).on( 'orddd_lite_on_load_time_slots', function( data, values ) {
			let arr = [];
			let time_slot_session = localStorage.getItem( 'orddd_lite_time_slot' );
			let option_selected = orddd_lite_params.orddd_lite_auto_populate_first_available_time_slot;
			let orddd_time_slots = values.split( "/" );

			for( i = 0; i < orddd_time_slots.length; i++ ) {
				let time_slot_to_display = orddd_time_slots[ i ].split( "_" );
				if( 'select' == time_slot_to_display[ 0 ].replace(/\s/g, "") ) {
					arr.push( {
						label: jsL10n.selectText,
						value: "select"
					})
				} else if( 'asap' == time_slot_to_display[ 0 ] ) {
					arr.push( {
						label: jsL10n.asapText,
						value: time_slot_to_display[ 0 ]
					})
				} else {
					arr.push( {
						label: time_slot_to_display[1],
						value: time_slot_to_display[0]
					})
				}
				if ( 1 === i && 'on' === option_selected ) {
					setTimeSlot( time_slot_to_display[1] );
					onChangeTimeSlot( time_slot_to_display[0] );
				}
			};
			setTimeOptions( arr );
		});

		if ( ! isRequired || timeSlot ) {
			clearValidationError( errorId );
		} else {
			setValidationErrors( {
				[ errorId ]: {
					message: errorMessage,
					hidden: true,
				},
			} );
		}
		clearValidationError( errorId );
        
    },[ setExtensionData, isDeliveryEnabled, setTimeOptions, setTimeSlot, updateSession, setIsEnabled, setValidationErrors, clearValidationError ] );

    const onChangeTimeSlot = useCallback(
		( value ) => {
			setTimeSlot( value );
			localStorage.setItem( 'orddd_lite_time_slot', value );
			setExtensionData( 'order-delivery-date', 'orddd_lite_time_slot', value );
			setLoading( true );
			var update_cart = extensionCartUpdate( {
                namespace: 'order-delivery-date',
                data: {
                    orddd_lite_time_slot: value,
					h_deliverydate: localStorage.getItem( 'h_deliverydate_session' ),
                },
            });
			update_cart.then( () => { setLoading( false ) } )
		},
		[ setTimeSlot, setExtensionData ]
	);

	const error = useSelect( ( select ) => {
		const store = select( VALIDATION_STORE_KEY );
		return store.getValidationError( errorId );
	} );
	
	if ( !isDeliveryEnabled ) {
		return null;
	}

    return (
        <div
			id={ 'orddd_lite_time_slot' }
			className={ classnames( 'wc-block-components-combobox', {
				'is-active': timeSlot,
				'has-error': error?.message && ! error?.hidden,
			} ) }
		>
			<ComboboxControl
				className={ 'wc-block-components-combobox-control' }
				label={ timeLabel }
				onChange={ onChangeTimeSlot }
				onFilterValueChange={ () => null }
				options={ timeOptions }
				value={ timeSlot || '' }
				allowReset={ false }
				aria-invalid={ error?.message && ! error?.hidden }
			/>
			<ValidationInputError propertyName={ errorId } />
		</div>
    )    
}

export default TimeSlot;
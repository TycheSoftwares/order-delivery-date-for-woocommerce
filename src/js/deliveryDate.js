/**
 * External dependencies
 */
import { useEffect, useState, useCallback } from '@wordpress/element';
import { getSetting } from '@woocommerce/settings';
import { ValidatedTextInput } from '@woocommerce/blocks-checkout';
import { VALIDATION_STORE_KEY } from '@woocommerce/block-data';
import { useSelect, useDispatch } from '@wordpress/data';
import { extensionCartUpdate } from '@woocommerce/blocks-checkout';
import { __ } from '@wordpress/i18n';

const DeliveryDate = ({ checkoutData, shippingMethod, updateSession, setLoading }) => {
    const [ deliveryDate, setDeliveryDate ] = useState('');
    const [ isRequired, setIsRequired ] = useState( true );
    const [ dateLabel, setDateLabel ] = useState( __( 'Delivery Date', 'order-delivery-date' ) );
    const { setExtensionData } = checkoutData;
	const { setValidationErrors, clearValidationError } = useDispatch( VALIDATION_STORE_KEY );
    const [ isEnabled, setIsEnabled ] = useState( true ) ;
    const [ datePlaceholder, setdatePlaceholder ] = useState( __( 'Choose a Date', 'order-delivery-date' ) );

    useEffect(() => {
        setIsEnabled( orddd_lite_params.is_enable_delivery_date );
        setDateLabel(orddd_lite_params.orddd_lite_delivery_date_field_label);
		setDeliveryDate( localStorage.getItem( 'orddd_deliverydate_lite_session' ) );
        setExtensionData( 'order-delivery-date', 'e_deliverydate', deliveryDate );
		setExtensionData( 'order-delivery-date', 'h_deliverydate', localStorage.getItem( 'h_deliverydate_lite_session' ) );
        setIsRequired( 'checked' === orddd_lite_params.orddd_lite_date_field_mandatory ? true : false );
        setdatePlaceholder( orddd_lite_params.orddd_lite_delivery_date_field_placeholder );
        if ( ! isEnabled ) {
			return;
		}

        jQuery( document ).on( 'change', '#e_deliverydate', function( e ) {
			setDeliveryDate( e.target.value );
            setExtensionData( 'order-delivery-date', 'e_deliverydate', e.target.value );
            setExtensionData( 'order-delivery-date', 'h_deliverydate', localStorage.getItem( 'h_deliverydate_lite_session' ) );
            setLoading( true );
            var update_cart = extensionCartUpdate( {
                namespace: 'order-delivery-date',
                data: {
                    e_deliverydate: e.target.value,
                    h_deliverydate: localStorage.getItem( 'h_deliverydate_lite_session' )
                },
            });
            update_cart.then( () => { setLoading( false ) } )
		});

        /** For inline datepicker */
        jQuery( document ).on( 'change', '#orddd_lite_datepicker', function( e ) {
			setDeliveryDate( e.target.value );
            setExtensionData( 'order-delivery-date', 'e_deliverydate', e.target.value );
            setExtensionData( 'order-delivery-date', 'h_deliverydate', localStorage.getItem( 'h_deliverydate_lite_session' ) );
            setLoading( true );
            var update_cart = extensionCartUpdate( {
                namespace: 'order-delivery-date',
                data: {
                    e_deliverydate: e.target.value,
                    h_deliverydate: localStorage.getItem( 'h_deliverydate_lite_session' )
                },
            });
            update_cart.then( () => { setLoading( false ) } )
		});

        jQuery( document ).on( 'orddd_on_clear_text', function() {
            setLoading( true );
            var update_cart = extensionCartUpdate( {
                namespace: 'order-delivery-date',
                data: {
                    e_deliverydate: '',
                    h_deliverydate: '',
                },
            });
            update_cart.then( () => { 
                localStorage.setItem( 'h_deliverydate_lite_session', '' );
                localStorage.setItem( 'orddd_deliverydate_lite_session', '' );
                setDeliveryDate('');
                setLoading( false );
            } )
        });

        if ( isRequired && '' === deliveryDate ) {
			setValidationErrors({
				['e_deliverydate']: {
					message: __( 'Please select a delivery date', 'order-delivery-date' ),
					hidden: false,
				},
			});
			return;
		}
		clearValidationError('e_deliverydate');
        
    },[setExtensionData, deliveryDate, isRequired, isEnabled, setDeliveryDate, shippingMethod, updateSession, setValidationErrors, clearValidationError ]);

    const { validationError } = useSelect((select) => {
		const store = select('wc/store/validation');
		return {
			validationError: store.getValidationError('e_deliverydate'),
		};
	});

    const onDateChange = useCallback(
		( value ) => {
			setDeliveryDate( value );
			setExtensionData( 'order-delivery-date', 'e_deliverydate', value );
			setExtensionData( 'order-delivery-date', 'h_deliverydate', localStorage.getItem( 'h_deliverydate_lite_session' ) );
            
		},
		[ setDeliveryDate. setExtensionData ]
	)

    return (
        <div
            style={{ display: ! isEnabled ? 'none' : 'block' }}
        >
            <ValidatedTextInput
                id="e_deliverydate"
                errorId="e_deliverydate"
                type="text"
                required={isRequired}
                className={'orddd-datepicker'}
                label={ dateLabel }
                placeholder = {datePlaceholder}
                value={ deliveryDate }
                onBlur={ ( value ) => setDeliveryDate( value )  }
                onChange={ onDateChange }
            /> 
            <small class='orddd_lite_field_note' >{ orddd_lite_params.orddd_lite_field_note }</small>
        </div>
        
    )    
}

export default DeliveryDate;
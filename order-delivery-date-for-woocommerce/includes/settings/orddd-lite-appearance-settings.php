<?php
/**
 * Order Delivery Date Appearance Settings
 *
 * @author Tyche Softwares
 * @package Order-Delivery-Date-Pro-for-WooCommerce/Admin/Settings/General
 * @since 3.9
 * @category Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class orddd_lite_appearance_settings {
	
    /**
     * Callback for adding Appearance tab settings
     *
     * @since 1.5
     */
    
    public static function orddd_lite_appearance_admin_setting_callback() { }
        
    /**
     * Callback for adding Calendar Language setting
     *
     * @param array $args Callback arguments
     * @since 1.5
     */
    public static function orddd_lite_appearance_calendar_language_callback( $args ) {
        global $orddd_lite_languages;
        $language_selected = get_option( 'orddd_lite_language_selected' );
        if ( $language_selected == "" ) {
            $language_selected = "en-GB";
        }
    
        echo '<select id="orddd_lite_language_selected" name="orddd_lite_language_selected">';
    
        foreach ( $orddd_lite_languages as $key => $value ) {
            $sel = "";
            if ( $key == $language_selected ) {
                $sel = "selected";
            }
            echo "<option value='$key' $sel>$value</option>";
        }
    
        echo '</select>';
    
        $html = '<label for="orddd_lite_language_selected"> ' . $args[ 0 ] . '</label>';
        echo $html;
    }
        
    /**
    * Callback for adding Date formats setting
    *
    * @param array $args Callback arguments
    * @since 1.5
    */
    public static function orddd_lite_appearance_date_formats_callback( $args ) {
        global $orddd_lite_date_formats;
    
        echo '<select name="orddd_lite_delivery_date_format" id="orddd_lite_delivery_date_format" size="1">';
    
        foreach ( $orddd_lite_date_formats as $k => $format ) {
            printf( "<option %s value='%s'>%s</option>\n",
                selected( $k, get_option( 'orddd_lite_delivery_date_format' ), false ),
                esc_attr( $k ),
    		    date( $format )
            );
        }
        echo '</select>';
    
        $html = '<label for="orddd_lite_delivery_date_format">' . $args[ 0 ] . '</label>';
                echo $html;
    }
    
    /**
    * Callback for adding First day of week setting
    *
    * @param array $args Callback arguments
    * @since 1.5
    */
    
    public static function orddd_lite_appearance_first_day_of_week_callback( $args ) {
        global $orddd_lite_days;
        $day_selected = get_option( 'orddd_lite_start_of_week' );
        if( $day_selected == "" ) {
            $day_selected = 0;
        }
    
        echo '<select id="orddd_lite_start_of_week" name="orddd_lite_start_of_week">';
    
        foreach ( $orddd_lite_days as $key => $value ) {
            $sel = "";
            if ( $key == $day_selected ) {
                $sel = " selected ";
            }
            echo "<option value='$key' $sel>$value</option>";
        }
        echo '</select>';
    
    	$html = '<label for="orddd_lite_start_of_week"> ' . $args[ 0 ] . '</label>';
        echo $html;
    }
        
    /**
	* Callback for adding Delivery Date field label setting
    *
    * @param array $args Callback arguments
    * @since 1.5
    */

    public static function orddd_lite_delivery_date_field_label_callback( $args ) {
	    echo '<input type="text" name="orddd_lite_delivery_date_field_label" id="orddd_lite_delivery_date_field_label" value="' . get_option( 'orddd_lite_delivery_date_field_label' ) . '" maxlength="40"/>';

	    $html = '<label for="orddd_lite_delivery_date_field_label"> ' . $args[ 0 ] . '</label>';
	    echo $html;
    }
    
    /**
    * Callback for adding Delivery Date field placeholder setting
    *
    * @param array $args Callback arguments
    * @since 1.5
    */
    
    public static function orddd_lite_delivery_date_field_placeholder_callback( $args ) {
        echo '<input type="text" name="orddd_lite_delivery_date_field_placeholder" id="orddd_lite_delivery_date_field_placeholder" value="' . get_option( 'orddd_lite_delivery_date_field_placeholder' ) . '" maxlength="40"/>';
    
        $html = '<label for="orddd_lite_delivery_date_field_placeholder"> ' . $args[ 0 ] . '</label>';
        echo $html;
    }
        
    /**
    * Callback for adding Delivery Date field note text setting
    *
    * @param array $args Callback arguments
    * @since 1.5
    */
    
    public static function orddd_lite_delivery_date_field_note_text_callback( $args ) {
        echo '<textarea rows="2" cols="90" name="orddd_lite_delivery_date_field_note" id="orddd_lite_delivery_date_field_note">' . stripslashes( get_option( 'orddd_lite_delivery_date_field_note' ) ) . '</textarea>';
    
        $html = '<label for="orddd_lite_delivery_date_field_note"> ' . $args[ 0 ] . '</label>';
        echo $html;
    }
    
    /**
    * Callback for adding Number of months setting
    *
    * @param array $args Callback arguments
    * @since 1.5
    */
    
    public static function orddd_lite_appearance_number_of_months_callback( $args ) {
        global $orddd_lite_number_of_months;
    	echo '<select name="orddd_lite_number_of_months" id="orddd_lite_number_of_months" size="1">';
    
        foreach ( $orddd_lite_number_of_months as $k => $v ) {
            printf( "<option %s value='%s'>%s</option>\n",
                selected( $k, get_option( 'orddd_lite_number_of_months' ), false ),
                esc_attr( $k ),
                $v
            );
        }
        echo '</select>';
                     
        $html = '<label for="orddd_lite_number_of_months">' . $args[ 0 ] . '</label>';
        echo $html;
    }
        
    /**
    * Callback for adding Delivery Date fields in Shipping section setting
    *
    * @param array $args Callback arguments
    * @since 1.5
    */
    
    public static function orddd_lite_delivery_date_in_shipping_section_callback( $args ) {
        $orddd_lite_date_in_billing = 'checked';
        $orddd_lite_date_in_shipping = $orddd_lite_date_before_order_notes = $orddd_lite_date_after_order_notes = '';
        if ( get_option( 'orddd_lite_delivery_date_fields_on_checkout_page' ) == "billing_section" ) {
            $orddd_lite_date_in_billing = 'checked';
            $orddd_lite_date_in_shipping = '';
            $orddd_lite_date_before_order_notes = '';
            $orddd_lite_date_after_order_notes = '';
        } else if ( get_option( 'orddd_lite_delivery_date_fields_on_checkout_page' ) == "shipping_section" ) {
            $orddd_lite_date_in_shipping = 'checked';
            $orddd_lite_date_in_billing = '';
            $orddd_lite_date_before_order_notes = '';
            $orddd_lite_date_after_order_notes = '';
        } else if ( get_option( 'orddd_lite_delivery_date_fields_on_checkout_page' ) == "before_order_notes" ) {
            $orddd_lite_date_before_order_notes = 'checked';
            $orddd_lite_date_in_billing = '';
            $orddd_lite_date_in_shipping = '';
            $orddd_lite_date_after_order_notes = '';
        } else if ( get_option( 'orddd_lite_delivery_date_fields_on_checkout_page' ) == "after_order_notes" ) {
            $orddd_lite_date_after_order_notes = 'checked';
            $orddd_lite_date_in_billing = '';
            $orddd_lite_date_in_shipping = '';
            $orddd_lite_date_before_order_notes = '';
        }
        
        echo '<input type="radio" name="orddd_lite_delivery_date_fields_on_checkout_page" id="orddd_lite_delivery_date_fields_on_checkout_page" value="billing_section" ' . $orddd_lite_date_in_billing . '>' . __( 'In Billing Section', 'order-delivery-date' ) . '&nbsp;&nbsp;
            <input type="radio" name="orddd_lite_delivery_date_fields_on_checkout_page" id="orddd_lite_delivery_date_fields_on_checkout_page" value="shipping_section" ' . $orddd_lite_date_in_shipping . '>' . __( 'In Shipping Section', 'order-delivery-date' ) . '&nbsp;&nbsp;
            <input type="radio" name="orddd_lite_delivery_date_fields_on_checkout_page" id="orddd_lite_delivery_date_fields_on_checkout_page" value="before_order_notes" ' . $orddd_lite_date_before_order_notes . '>' . __( 'Before Order Notes', 'order-delivery-date' ) . '&nbsp;&nbsp;
	        <input type="radio" name="orddd_lite_delivery_date_fields_on_checkout_page" id="orddd_lite_delivery_date_fields_on_checkout_page" value="after_order_notes" ' . $orddd_lite_date_after_order_notes . '>' . __( 'After Order Notes', 'order-delivery-date' );
    	
        $html = '<label for="orddd_lite_delivery_date_fields_on_checkout_page"> ' . $args[ 0 ] . '</label>';
    	echo $html;
    }
    
    /**
    * Callback for adding Delivery Date field on Cart page setting
    *
    * @param array $args Callback arguments
    * @since 1.5
    */

    public static function orddd_lite_delivery_date_on_cart_page_callback( $args ) {
        $delivery_date_on_cart_page = "";
        if ( get_option( ' orddd_lite_delivery_date_on_cart_page' ) == 'on' ) {
            $delivery_date_on_cart_page = "checked";
        }
        
        echo '<input type="checkbox" name="orddd_lite_delivery_date_on_cart_page" id="orddd_lite_delivery_date_on_cart_page" class="day-checkbox" ' . $delivery_date_on_cart_page . '/>';

        $html = '<label for="orddd_lite_delivery_date_on_cart_page"> ' . $args[0] . '</label>';
        echo $html; 
    }

    /**
    * Callback for adding Calendar theme setting
    *
    * @param array $args Callback arguments
    * @since 1.5
    */
        
    public static function orddd_lite_appearance_calendar_theme_callback( $args ) {
        global $orddd_lite_calendar_themes;
    	$language_selected = get_option( 'orddd_lite_language_selected' );
        if ( $language_selected == "" ) {
            $language_selected = "en-GB";
        }
    	
        $first_day_of_week = '1';
        if( get_option( 'orddd_lite_start_of_week' ) != '' ) {
            $first_day_of_week = get_option( 'orddd_lite_start_of_week' );
        }

    	echo '<input type="hidden" name="orddd_lite_calendar_theme" id="orddd_lite_calendar_theme" value="' . get_option( 'orddd_lite_calendar_theme' ) . '">
    	   <input type="hidden" name="orddd_lite_calendar_theme_name" id="orddd_lite_calendar_theme_name" value="' . get_option( 'orddd_lite_calendar_theme_name' ) . '">';
        echo '<script>
            jQuery( document ).ready( function( ) {
                var calendar_themes = ' . json_encode( $orddd_lite_calendar_themes ) .'
                jQuery( "#switcher" ).themeswitcher( {
                    onclose: function( ) {
                        var cookie_name = this.cookiename;
                        jQuery( "input#orddd_lite_calendar_theme" ).val( jQuery.cookie( cookie_name ) );
                        jQuery.each( calendar_themes, function( key, value ) {
                            if( jQuery.cookie( cookie_name ) == key ) {
                                jQuery( "input#orddd_lite_calendar_theme_name" ).val( value );
                            }
                        });
                        jQuery( "<link/>", {
                            rel: "stylesheet",
                            type: "text/css",
                            href: "' . esc_url( plugins_url() .  "/order-delivery-date-for-woocommerce/css/datepicker.css" ). '"
                        }).appendTo("head");
                    },
                    imgpath: "'. esc_url( plugins_url() . '/order-delivery-date-for-woocommerce/images/' ) .'",
                    loadTheme: "' . get_option( 'orddd_lite_calendar_theme_name' ) . '",
                    
                });
            });

            jQuery( function() {
                jQuery.datepicker.setDefaults( jQuery.datepicker.regional[ "" ] );
                jQuery( "#datepicker" ).datepicker({firstDay:' . $first_day_of_week . '});
                jQuery( "#datepicker" ).datepicker( jQuery.datepicker.regional[ "' . $language_selected . '" ] );
                jQuery( "#localisation_select" ).change(function() {
                    jQuery( "#datepicker" ).datepicker( "option", jQuery.datepicker.regional[ jQuery( this ).val() ] );
                    });
                });
        </script>
        <div id="switcher"></div>
        <br><strong>' . __( 'Preview theme:', 'order-delivery-date' ) . '</strong><br>
        <div id="datepicker" style="width:300px"></div>';
    
    	$html = '<label for="orddd_lite_calendar_theme_name"> ' . $args[0] . '</label>';
    	echo $html;
    }
     
    /**
     * Callback for adding checkbox to hide delivery date field for virtual products
     *
     * $params array $args Callback arguments
     * @since 1.5
     */  

    public static function orddd_lite_appearance_virtual_product_callback( $args ) {
        if ( get_option( 'orddd_lite_no_fields_for_virtual_product' ) == 'on' ) {
            $orddd_lite_no_fields_for_virtual_product = "checked";
        } else {
            $orddd_lite_no_fields_for_virtual_product = "";
        }
        
        echo '<input type="checkbox" name="orddd_lite_no_fields_for_virtual_product" id="orddd_lite_no_fields_for_virtual_product" class="day-checkbox"' . $orddd_lite_no_fields_for_virtual_product . '/><label class="orddd_lite_no_fields_for_product_type">' . __( 'Virtual Products', 'order-delivery-date' ) . '</label>';
        
        if ( get_option( 'orddd_lite_no_fields_for_featured_product' ) == 'on' ) {
            $orddd_lite_no_fields_for_featured_product = "checked";
        } else {
            $orddd_lite_no_fields_for_featured_product = "";
        }
        
        echo '<input type="checkbox" name="orddd_lite_no_fields_for_featured_product" id="orddd_lite_no_fields_for_featured_product" class="day-checkbox"' . $orddd_lite_no_fields_for_featured_product . '/><label class="orddd_lite_no_fields_for_product_type">' . __( 'Featured Products', 'order-delivery-date' ) . '</label>';
        
        $html = '<label for="orddd_lite_no_fields_for_product_type"> ' . $args[ 0 ] . '</label>';
        echo $html;
    }
}
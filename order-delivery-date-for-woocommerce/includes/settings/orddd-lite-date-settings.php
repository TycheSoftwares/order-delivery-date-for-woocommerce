<?php
/**
 * General Settings for Order Delivery Date
 *
 * @author Tyche Softwares
 * @package Order-Delivery-Date-Lite-for-WooCommerce/Admin/Settings/Date
 * @since 3.9
 * @category Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class orddd_lite_date_settings {
    
     /**
     * Callback for Order Delivery Date Settings section
     *
     * @since 1.5
     */    

    public static function orddd_lite_delivery_date_setting() { }
    
    
     /**
     * Callback for adding Enable Delivery Date checkbox
     *
     * $params array $args Callback arguments
     * @since 1.5
     */    

    public static function orddd_lite_enable_delivery_date_callback( $args ) {
        $enable_delivery_date = "";
        if ( get_option( 'orddd_lite_enable_delivery_date' ) == 'on' ) {
            $enable_delivery_date = "checked";
        }
         
        echo '<input type="checkbox" name="orddd_lite_enable_delivery_date" id="orddd_lite_enable_delivery_date" class="day-checkbox" value="on" ' . $enable_delivery_date . ' />';
        
        $html = '<label for="orddd_lite_enable_delivery_date"> ' . $args[0] . '</label>';
        echo $html;
    }
    
    /**
     * Callback for delivery checkout option to select Calendar or Text Block
     *
     * @param array $args Extra arguments containing label & class for the field
     * @since 3.9
     * 
     * @todo: disable this field
     */

    public static function orddd_lite_delivery_checkout_options_callback( $args ) {
        global $orddd_weekdays;
        
        $orddd_delivery_checkout_options_delivery_calendar = "checked";

        ?>
        <p><label><input type="radio" name="orddd_lite_delivery_checkout_options" id="orddd_lite_delivery_checkout_options" value="delivery_calendar"<?php echo $orddd_delivery_checkout_options_delivery_calendar; ?>/><?php _e( 'Calendar', 'order-delivery-date' ) ;?></label>
        <label><input type="radio" name="orddd_delivery_checkout_options" id="orddd_delivery_checkout_options" value="text_block" disabled readonly/><?php _e( 'Text block <b><i>Upgrade to <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank">Order Delivery Date Pro for WooCommerce</a> to enable the setting.</i></b>', 'order-delivery-date' ) ;?></label></p>
        <?php
        $html = '<label for="orddd_lite_delivery_checkout_options"> ' . $args[0] . '</label>';
        echo $html;
    }

    /**
     * Callback for adding Delivery Weekdays setting
     *
     * @param string $input Value of the weekday setting 
     * 
     * @return string $input
     * 
     * @todo Unused Function. Need to check and remove it. 
     * @since 3.9
     */
    
    public static function orddd_lite_weekday_0_save( $input ) {
        $input = orddd_lite_date_settings::return_orddd_lite_weekday_input( 'orddd_lite_weekday_0' );
        return $input;
    }

    /**
     * Callback for adding Delivery Weekdays setting
     *
     * @param string $input Value of the weekday setting 
     * 
     * @return string $input
     * 
     * @todo Unused Function. Need to check and remove it. 
     * @since 3.9
     */
    public static function orddd_lite_weekday_1_save( $input ) {
        $input = orddd_lite_date_settings::return_orddd_lite_weekday_input( 'orddd_lite_weekday_1' );
        return $input;
    }

    /**
     * Callback for adding Delivery Weekdays setting
     *
     * @param string $input Value of the weekday setting 
     * 
     * @return string $input
     * 
     * @todo Unused Function. Need to check and remove it. 
     * @since 3.9
     */

    public static function orddd_lite_weekday_2_save( $input ) {
        $input = orddd_lite_date_settings::return_orddd_lite_weekday_input( 'orddd_lite_weekday_2' );
        return $input;
    }

    /**
     * Callback for adding Delivery Weekdays setting
     *
     * @param string $input Value of the weekday setting 
     * 
     * @return string $input
     * 
     * @todo Unused Function. Need to check and remove it. 
     * @since 3.9
     */
    public static function orddd_lite_weekday_3_save( $input ) {
        $input = orddd_lite_date_settings::return_orddd_lite_weekday_input( 'orddd_lite_weekday_3' );
        return $input;
    }

    /**
     * Callback for adding Delivery Weekdays setting
     *
     * @param string $input Value of the weekday setting 
     * 
     * @return string $input
     * 
     * @todo Unused Function. Need to check and remove it. 
     * @since 3.9
     */
    public static function orddd_lite_weekday_4_save( $input ) {
        $input = orddd_lite_date_settings::return_orddd_lite_weekday_input( 'orddd_lite_weekday_4' );
        return $input;
    }

    /**
     * Callback for adding Delivery Weekdays setting
     *
     * @param string $input Value of the weekday setting 
     * 
     * @return string $input
     * 
     * @todo Unused Function. Need to check and remove it. 
     * @since 3.9
     */
    public static function orddd_lite_weekday_5_save( $input ) {
        $input = orddd_lite_date_settings::return_orddd_lite_weekday_input( 'orddd_lite_weekday_5' );
        return $input;
    }

    /**
     * Callback for adding Delivery Weekdays setting
     *
     * @param string $input Value of the weekday setting 
     * 
     * @return string $input
     * 
     * @todo Unused Function. Need to check and remove it. 
     * @since 3.9
     */

    public static function orddd_lite_weekday_6_save( $input ) {
        $input = orddd_lite_date_settings::return_orddd_lite_weekday_input( 'orddd_lite_weekday_6' );
        return $input;
    }
    
    /**
     * Return the selected weekdays
     * 
     * @todo Unused Function. Need to check and remove it. 
     * @param string $weekday 
     * @return string $input 
     * @since 3.9
     */
    public static function return_orddd_lite_weekday_input( $weekday ) {
        global $orddd_lite_weekdays;
        $input = '';
        if( isset( $_POST[ 'orddd_lite_weekdays' ] ) ) {
            $weekdays = $_POST[ 'orddd_lite_weekdays' ];
            if( in_array( $weekday, $weekdays ) ) {
                $input = 'checked';
            }
        }
        return $input;
    }

    /**
     * Callback for adding Delivery Weekdays dropdown
     *
     * $params array $args Callback arguments
     * @since 1.5
     */    

    public static function orddd_lite_delivery_days_callback( $args ) {
        global $orddd_lite_weekdays;
        
        echo '<select class="orddd_lite_weekdays" id="orddd_lite_weekdays" name="orddd_lite_weekdays[]" placeholder="Select Weekdays" multiple="multiple">';
                foreach ( $orddd_lite_weekdays as $n => $day_name ) {
                    if( "checked" == get_option( $n ) ) {
                        print( '<option name="' . $n . '" value="' . $n . '" selected>' .  $day_name . '</option>' );
                    } else {
                        print( '<option name="' . $n . '" value="' . $n . '">' .  $day_name . '</option>' );
                    }
                    
                }
        echo '</select>';
        echo '<script>
            jQuery( ".orddd_lite_weekdays" ).select2();
        </script>';
    
        $html = '<label for="orddd_lite_weekdays"> ' . $args[ 0 ] . '</label>';
        echo $html;   
    }
    
    /**
     * Callback to add Weekday Settings field
     * 
     * @param array $args Extra arguments containing label & class for the field
     * @since 3.9
     *
     * @todo: disable this field
     */
    public static function orddd_lite_enable_day_wise_settings_callback( $args ) {
        echo '<input type="checkbox" name="orddd_enable_day_wise_settings" id="orddd_enable_day_wise_settings" class="day-checkbox" value="on" disabled readonly />';
        
        $html = '<label for="orddd_enable_day_wise_settings"> ' . $args[0] . '</label>';
        echo $html;      
    }

    /**
     * Callback for adding Minimum Delivery Time (in hours) text field
     *
     * $params array $args Callback arguments
     * @since 1.5
     */    

    public static function orddd_lite_minimum_delivery_time_callback( $args ) {
        printf( '<input type="number" name="orddd_lite_minimumOrderDays" id="orddd_lite_minimumOrderDays" value="' . get_option( 'orddd_lite_minimumOrderDays' ) . '"/>' );
        $html = '<label for="orddd_lite_minimumOrderDays"> '  . $args[0] . '</label>';
        echo $html;
    }

    /**
     * Callback for adding Number of Dates to choose text field
     *
     * $params array $args Callback arguments
     * @since 1.5
     */    

    public static function orddd_lite_number_of_dates_callback( $args ) {
        printf( '<input type="number" name="orddd_lite_number_of_dates" id="orddd_lite_number_of_dates" value="' . get_option( 'orddd_lite_number_of_dates' ) . '"/>' );
        $html = '<label for="orddd_lite_number_of_dates"> '  . $args[0] . '</label>';
        echo $html;
    }
    

     /**
     * Callback for adding Mandatory checkbox
     *
     * $params array $args Callback arguments
     * @since 1.5
     */   

    public static function orddd_lite_date_field_mandatory_callback( $args ) {
        printf( '<input type="checkbox" name="orddd_lite_date_field_mandatory" id="orddd_lite_date_field_mandatory" class="day-checkbox" value="checked" ' . get_option( 'orddd_lite_date_field_mandatory' ) . ' />' );
        $html = '<label for="orddd_lite_date_field_mandatory"> '. $args[0] . '</label>';
        echo $html;
    }
    
    /**
     * Callback for adding Maximum orders per day text field
     *
     * $params array $args Callback arguments
     * @since 1.5
     */   

    public static function orddd_lite_lockout_date_after_orders_callback( $args ) {
        printf( '<input type="number" name="orddd_lite_lockout_date_after_orders" id="orddd_lite_lockout_date_after_orders" value="' . get_option( 'orddd_lite_lockout_date_after_orders' ) . '"/>' );
        $html = '<label for="orddd_lite_lockout_date_after_orders"> ' . $args[ 0 ] . '</label>';
        echo $html;
    }
    
    /**
     * Callback to add the Maximum Deliveries based on per product quantity setting
     * 
     * @param array $args Extra arguments containing label & class for the field
     * @since 3.9
     *
     * @todo: disable this field.
     */
    
    public static function orddd_lockout_date_quantity_based_callback( $args ) {
        $orddd_lockout_date_quantity_based = "";
        if ( get_option( 'orddd_lockout_date_quantity_based' ) == 'on' ) {
            $orddd_lockout_date_quantity_based = "checked";
        }
        
        echo '<input type="checkbox" name="orddd_lockout_date_quantity_based" id="orddd_lockout_date_quantity_based" value="on" ' . $orddd_lockout_date_quantity_based . '/>';
        
        $html = '<label for="orddd_lockout_date_quantity_based"> ' . $args[ 0 ] . '</label>';
        echo $html;
    }

    /**
     * Callback for adding Default sorting checkbox of Delivery date column on edit order
     *
     * $params array $args Callback arguments
     * @since 1.5
     */   

    public static function orddd_lite_enable_default_sorting_of_column_callback( $args ) {
        printf( '<input type="checkbox" name="orddd_lite_enable_default_sorting_of_column" id="orddd_lite_enable_default_sorting_of_column" value="checked"' . get_option( 'orddd_lite_enable_default_sorting_of_column' ) . '/>' );
        $html = '<label for="orddd_lite_enable_default_sorting_of_column"> ' . $args[ 0 ] . '</label>';
        echo $html;
    }
    
    /**
     * Callback for adding Auto Populate First available delivery date checkbox
     *
     * $params array $args Callback arguments
     * @since 1.5
     */  

    public static function orddd_lite_auto_populate_first_available_date_callback( $args ) {
        $orddd_lite_auto_populate_first_available_date = '';
        if ( get_option( 'orddd_lite_auto_populate_first_available_date' ) == 'on' ) {
            $orddd_lite_auto_populate_first_available_date = "checked";
        }
        
        echo '<input type="checkbox" name="orddd_lite_auto_populate_first_available_date" id="orddd_lite_auto_populate_first_available_date" class="day-checkbox" ' . $orddd_lite_auto_populate_first_available_date . '/>';
        
        $html = '<label for="orddd_lite_auto_populate_first_available_date"> '. $args[ 0 ] . '</label>';
        echo $html;
    }

    /**
     * Callback for adding a checkbox of Calculating minimum delivery time on disable days
     *
     * $params array $args Callback arguments
     * @since 1.5
     */  

    public static function orddd_lite_calculate_min_time_disabled_days_callback( $args ) {
        $orddd_lite_calculate_min_time_disabled_days = '';
        if ( get_option( 'orddd_lite_calculate_min_time_disabled_days' ) == 'on' ) {
            $orddd_lite_calculate_min_time_disabled_days = "checked";
        }
        
        echo '<input type="checkbox" name="orddd_lite_calculate_min_time_disabled_days" id="orddd_lite_calculate_min_time_disabled_days" class="day-checkbox" ' . $orddd_lite_calculate_min_time_disabled_days . '/>';
        
        $html = '<label for="orddd_lite_calculate_min_time_disabled_days"> '. $args[ 0 ] . '</label>';
        echo $html;   
    }
}
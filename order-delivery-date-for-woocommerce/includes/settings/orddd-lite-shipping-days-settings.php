<?php
/**
 * Order Delivery Shipping Days Settings
 *
 * @author Tyche Softwares
 * @package Order-Delivery-Date-Lite-for-WooCommerce/Admin/Settings/Shipping-Days
 * @since 3.9
 * @category Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class orddd_lite_shipping_days_settings {
    
    /**
     * Callback for adding Shipping days tab settings
     */
    public static function orddd_lite_shipping_days_settings_section_callback() { 
        _e( '<b>Shipping Days</b> refers to the working days of your own company. <b>Delivery Days</b> refers to the working days of your shipping company to whom you submit your orders for deliveries. <br>Leave this unchanged if you handle delivery & shipping by yourself.<a href="https://www.tychesoftwares.com/docs/docs/order-delivery-date-pro-for-woocommerce/setup-delivery-dates/?utm_source=userwebsite&utm_medium=link&utm_campaign=OrderDeliveryDateProSetting" target="_blank" class="dashicons dashicons-external" style="line-height:unset;"></a>', 'order-delivery-date' );
    }
    
    /**
     * Callback for adding Enable time slot setting
     *
     * @param array $args Extra arguments containing label & class for the field
     */
    
    public static function orddd_lite_enable_shipping_days_callback( $args ) {
        echo '<input type="checkbox" name="orddd_enable_shipping_days" id="orddd_enable_shipping_days" class="day-checkbox" disabled readonly/>';
        
        $html = '<label for="orddd_enable_shipping_days"> ' . $args[0] . '</label>';
        echo $html;
    }

    /**
     * Callback for selecting weekdays if 'Weekdays' option is selected
     * 
     * @param array $args Extra arguments containing label & class for the field
     */
    public static function orddd_lite_shipping_days_callback( $args ) {
        global $orddd_lite_weekdays;
        echo '<select class="orddd_lite_shipping_days" id="orddd_lite_shipping_days" name="orddd_lite_shipping_days[]" placeholder="Select Weekdays" multiple="multiple" disabled readonly>';
                foreach ( $orddd_lite_weekdays as $n => $day_name ) {
                    print( '<option name="' . $n . '" value="' . $n . '" selected>' .  $day_name . '</option>' );
                }
        echo '</select>';
        echo '<script>
            jQuery( ".orddd_lite_shipping_days" ).select2();
        </script>';
    
        $html = '<label for="orddd_lite_shipping_days"> ' . $args[ 0 ] . '</label>';
        echo $html;   
    }
}
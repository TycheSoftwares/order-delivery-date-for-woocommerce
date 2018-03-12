<?php
/**
 * Order Delivery Date Lite Uninstall
 *
 * Uninstalling Order Delivery Date Lite delets all settings for the plugin
 *
 * @author      Tyche Softwares
 * @package     Order-Delivery-Date-Lite-for-WooCommerce/Admin/Uninstaller
 * @version     3.2
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'orddd_lite_db_version' );
delete_option( 'orddd_lite_enable_delivery_date' );

$orddd_lite_weekdays = array( 
    'orddd_weekday_0' => __( 'Sunday', 'order-delivery-date' ),
    'orddd_weekday_1' => __( 'Monday', 'order-delivery-date' ),
    'orddd_weekday_2' => __( 'Tuesday', 'order-delivery-date' ),
    'orddd_weekday_3' => __( 'Wednesday', 'order-delivery-date' ),
    'orddd_weekday_4' => __( 'Thursday', 'order-delivery-date' ),
    'orddd_weekday_5' => __( 'Friday', 'order-delivery-date' ),
    'orddd_weekday_6' => __( 'Saturday', 'order-delivery-date' ) );

foreach ( $orddd_lite_weekdays as $n => $day_name ) {
    delete_option( $n );
}

delete_option( 'orddd_lite_minimumOrderDays' );
delete_option( 'orddd_lite_number_of_dates' );
delete_option( 'orddd_lite_date_field_mandatory' );            
delete_option( 'orddd_lite_lockout_date_after_orders' );
delete_option( 'orddd_lite_lockout_days' );
delete_option( 'orddd_lite_update_value' );
delete_option( 'orddd_lite_abp_hrs' );
delete_option( 'orddd_lite_enable_default_sorting_of_column' );
delete_option( 'orddd_lite_auto_populate_first_available_date' );
delete_option( 'orddd_lite_calculate_min_time_disabled_days' );

// appearance options
delete_option( 'orddd_lite_language_selected' );
delete_option( 'orddd_lite_delivery_date_format' );
delete_option( 'orddd_lite_start_of_week' );
delete_option( 'orddd_lite_delivery_date_field_label' );
delete_option( 'orddd_lite_delivery_date_field_placeholder' );
delete_option( 'orddd_lite_delivery_date_field_note' );
delete_option( 'orddd_lite_number_of_months' );
delete_option( 'orddd_lite_delivery_date_fields_on_checkout_page' );
delete_option( 'orddd_lite_default_appearance_settings' );    
delete_option( 'orddd_lite_delivery_date_on_cart_page' );
delete_option( 'orddd_lite_calendar_theme' );
delete_option( 'orddd_lite_calendar_theme_name' );
delete_option( 'orddd_lite_no_fields_for_virtual_product' );
delete_option( 'orddd_lite_no_fields_for_featured_product' );

//holidays
delete_option( 'orddd_lite_holidays' );
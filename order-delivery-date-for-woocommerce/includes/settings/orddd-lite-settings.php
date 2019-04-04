<?php
/** 
 * Order Delivery Date for WooCommerce Lite
 *
 * Settings added for the plugin in the admin
 *
 * @author      Tyche Softwares
 * @package     Order-Delivery-Date-Lite-for-WooCommerce/Admin/Settings
 * @since       1.5
 */

/**
 * Class for adding the settings of the plugin in admin.
 */

class orddd_lite_settings {

    /**
     * Adds Order Delivery Date menu in admin dashboard
     * 
     * @hook admin_menu
     * @since 1.5
     */

	public static function orddd_lite_order_delivery_date_menu() {
        add_menu_page( 'Order Delivery Date', 'Order Delivery Date', 'manage_woocommerce', 'order_delivery_date_lite', array( 'orddd_lite_settings', 'orddd_lite_order_delivery_date_settings' ) );
    }
    
    /**
     * Add settings field on Date Settings tab. 
     *
     * @globals array $orddd_lite_weekdays Weekdays array 
     * @hook admin_init
     * @since 1.5
     */    
    public static function order_lite_delivery_date_admin_settings() {
        global $orddd_lite_weekdays;
        // First, we register a section. This is necessary since all future options must belong to one.
        add_settings_section(
            'orddd_lite_date_settings_section',		// ID used to identify this section and with which to register options
            __( 'Order Delivery Date Settings', 'order-delivery-date' ),		// Title to be displayed on the administration page
            array( 'orddd_lite_settings', 'orddd_lite_delivery_date_setting' ),		// Callback used to render the description of the section
            'orddd_lite_date_settings_page'				// Page on which to add this section of options
        );
        
        add_settings_field(
            'orddd_lite_enable_delivery_date',
            __( 'Enable Delivery Date:', 'order-delivery-date' ),
            array( 'orddd_lite_settings', 'orddd_lite_enable_delivery_date_callback' ),
            'orddd_lite_date_settings_page',
            'orddd_lite_date_settings_section',
            array ( __( 'Enable Delivery Date capture on the checkout page.', 'order-delivery-date' ) )
        );
    
        add_settings_field(
            'orddd_lite_delivery_days',
            __( 'Delivery Days:', 'order-delivery-date' ),
            array( 'orddd_lite_settings', 'orddd_lite_delivery_days_callback' ),
            'orddd_lite_date_settings_page',
            'orddd_lite_date_settings_section',
            array ( '&nbsp;' . __( 'Select weekdays for delivery.', 'order-delivery-date' ) )
        );
         
        add_settings_field(
            'orddd_lite_minimumOrderDays',
            __( 'Minimum Delivery time (in hours):', 'order-delivery-date' ),
            array( 'orddd_lite_settings', 'orddd_lite_minimum_delivery_time_callback' ),
            'orddd_lite_date_settings_page',
            'orddd_lite_date_settings_section',
            array ( __( 'Minimum number of hours required to prepare for delivery.', 'order-delivery-date' ) )
        );
         
        add_settings_field(
            'orddd_lite_number_of_dates',
            __( 'Number of dates to choose:', 'order-delivery-date' ),
            array( 'orddd_lite_settings', 'orddd_lite_number_of_dates_callback' ),
            'orddd_lite_date_settings_page',
            'orddd_lite_date_settings_section',
            array ( __( 'Number of dates available for delivery.', 'order-delivery-date' ) )
        );
         
        add_settings_field(
            'orddd_lite_date_field_mandatory',
            __( 'Mandatory field?:', 'order-delivery-date' ),
            array( 'orddd_lite_settings', 'orddd_lite_date_field_mandatory_callback' ),
            'orddd_lite_date_settings_page',
            'orddd_lite_date_settings_section',
            array ( __( 'Selection of delivery date on the checkout page will become mandatory.', 'order-delivery-date' ) )
        );
        
        add_settings_field(
            'orddd_lite_lockout_date_after_orders',
            __( 'Lockout date after X orders:', 'order-delivery-date' ),
            array( 'orddd_lite_settings', 'orddd_lite_lockout_date_after_orders_callback' ),
            'orddd_lite_date_settings_page',
            'orddd_lite_date_settings_section',
            array ( __( 'Maximum deliveries/orders per day.', 'order-delivery-date' ) )
        );
        
        add_settings_field(
            'orddd_lite_enable_default_sorting_of_column',
            __( 'Sort on WooCommerce Orders Page:', 'order-delivery-date' ),
            array( 'orddd_lite_settings', 'orddd_lite_enable_default_sorting_of_column_callback' ),
            'orddd_lite_date_settings_page',
            'orddd_lite_date_settings_section',
            array ( __( 'Enable default sorting of orders (in descending order) by Delivery Date on WooCommerce -> Orders page', 'order-delivery-date' ) )
            );
        
        add_settings_field(
            'orddd_lite_auto_populate_first_available_date',
            __( 'Auto-populate first available Delivery date:', 'order-delivery-date' ),
            array( 'orddd_lite_settings', 'orddd_lite_auto_populate_first_available_date_callback' ),
            'orddd_lite_date_settings_page',
            'orddd_lite_date_settings_section',
            array( __( 'Auto-populate first available Delivery date when the checkout page loads.', 'order-delivery-date' ) )
        );

        add_settings_field(
            'orddd_lite_calculate_min_time_disabled_days',
            __( 'Apply Minimum Delivery Time for non working weekdays:', 'order-delivery-date' ),
            array( 'orddd_lite_settings', 'orddd_lite_calculate_min_time_disabled_days_callback' ),
            'orddd_lite_date_settings_page',
            'orddd_lite_date_settings_section',
            array( __( 'If selected, then the Minimum Delivery Time (in hours) will be applied on the non working weekdays which are unchecked in Delivery Weekdays. If unchecked, then it will not be applied. For example, if Minimum Delivery Time (in hours) is set to 48 hours and Saturday is disabled for delivery. Now if a customer visits the website on Firday, then the first available date will be Monday and not Sunday.', 'order-delivery-date' ) )
        );

        foreach ( $orddd_lite_weekdays as $n => $day_name ) {
            register_setting(
                'orddd_lite_date_settings',
                $n
            );
        }
        
        register_setting(
            'orddd_lite_date_settings',
            'orddd_lite_enable_delivery_date'
        );
    
        register_setting(
            'orddd_lite_date_settings',
            'orddd_lite_minimumOrderDays'
        );
         
        register_setting(
            'orddd_lite_date_settings',
            'orddd_lite_number_of_dates'
        );
    
        register_setting(
            'orddd_lite_date_settings',
            'orddd_lite_date_field_mandatory'
        );
        
        register_setting(
            'orddd_lite_date_settings',
            'orddd_lite_lockout_date_after_orders'
        );
        
        register_setting(
            'orddd_lite_date_settings',
            'orddd_lite_enable_default_sorting_of_column'
        );

        register_setting(
            'orddd_lite_date_settings',
            'orddd_lite_auto_populate_first_available_date'
        );

        register_setting(
            'orddd_lite_date_settings',
            'orddd_lite_calculate_min_time_disabled_days'
        );
        do_action ( "orddd_lite_add_new_settings" );
    }

    /**
     * Add settings field on Appearance tab. 
     *
     * @hook admin_init
     * @since 1.5
     */    

    public static function order_lite_appearance_admin_settings() {
        add_settings_section(
            'orddd_lite_appearance_section',
            __( 'Calendar Appearance', 'order-delivery-date' ),
            array( 'orddd_lite_settings', 'orddd_lite_appearance_admin_setting_callback' ),
            'orddd_lite_appearance_page'
        );
    
        add_settings_field(
            'orddd_lite_language_selected',
            __( 'Calendar Language:', 'order-delivery-date' ),
            array( 'orddd_lite_settings', 'orddd_lite_appearance_calendar_language_callback' ),
            'orddd_lite_appearance_page',
            'orddd_lite_appearance_section',
            array ( __( 'Choose a Language.', 'order-delivery-date' ) )
        );
    
        add_settings_field(
            'orddd_lite_delivery_date_format',
            __( 'Date Format:', 'order-delivery-date' ),
            array( 'orddd_lite_settings', 'orddd_lite_appearance_date_formats_callback' ),
            'orddd_lite_appearance_page',
            'orddd_lite_appearance_section',
            array( '<br>' . __( 'The format in which the Delivery Date appears to the customers on the checkout page once the date is selected.', 'order-delivery-date' ) )
        );
    
        add_settings_field(
            'orddd_lite_start_of_week',
            __( 'First Day of Week:', 'order-delivery-date' ),
            array( 'orddd_lite_settings', 'orddd_lite_appearance_first_day_of_week_callback' ),
            'orddd_lite_appearance_page',
            'orddd_lite_appearance_section',
            array( __( 'Choose the first day of week displayed on the Delivery Date calendar.', 'order-delivery-date' ) )
        );
         
        add_settings_field(
            'orddd_lite_delivery_date_field_label',
            __( 'Field Label:', 'order-delivery-date' ),
            array( 'orddd_lite_settings', 'orddd_lite_delivery_date_field_label_callback' ),
            'orddd_lite_appearance_page',
            'orddd_lite_appearance_section',
            array( __( 'Choose the label that is to be displayed for the field on checkout page.', 'order-delivery-date' ) )
        );
         
        add_settings_field(
            'orddd_lite_delivery_date_field_placeholder',
            __( 'Field Placeholder Text:', 'order-delivery-date' ),
            array( 'orddd_lite_settings', 'orddd_lite_delivery_date_field_placeholder_callback' ),
            'orddd_lite_appearance_page',
            'orddd_lite_appearance_section',
            array( __( 'Choose the placeholder text that is to be displayed for the field on checkout page.', 'order-delivery-date' ) )
        );
         
        add_settings_field(
            'orddd_lite_delivery_date_field_note',
            __( 'Field Note Text:', 'order-delivery-date' ),
            array( 'orddd_lite_settings', 'orddd_lite_delivery_date_field_note_text_callback' ),
            'orddd_lite_appearance_page',
            'orddd_lite_appearance_section',
            array( '<br>' . __( 'Choose the note to be displayed below the delivery date field on checkout page.', 'order-delivery-date' ) )
        );
         
        add_settings_field(
            'orddd_lite_number_of_months',
            __( 'Number of Months:', 'order-delivery-date' ),
            array( 'orddd_lite_settings', 'orddd_lite_appearance_number_of_months_callback' ),
            'orddd_lite_appearance_page',
            'orddd_lite_appearance_section',
            array ( __( 'The number of months to be shown on the calendar.', 'order-delivery-date' ) )
        );
         
        add_settings_field(
            'orddd_lite_delivery_date_fields_on_checkout_page',
            __( 'Field placement on the Checkout page:', 'order-delivery-date' ),
            array( 'orddd_lite_settings', 'orddd_lite_delivery_date_in_shipping_section_callback' ),
            'orddd_lite_appearance_page',
            'orddd_lite_appearance_section',
            array( __( '</br>The Delivery Date field will be displayed in the selected section.</br><i>Note: WooCommerce automatically hides the Shipping section fields for Virtual products.</i>', 'order-delivery-date' ) )
        );
        
        add_settings_field(
            'orddd_lite_delivery_date_on_cart_page',
            __( 'Delivery Date field on Cart page:', 'order-delivery-date' ),
            array( 'orddd_lite_settings', 'orddd_lite_delivery_date_on_cart_page_callback' ),
            'orddd_lite_appearance_page',
            'orddd_lite_appearance_section',
            array( __( 'Add the Delivery Date field on the cart page along with the Checkout page.', 'order-delivery-date' ) )
        );

        add_settings_field(
            'orddd_lite_calendar_theme_name',
            __( 'Theme:', 'order-delivery-date' ),
            array( 'orddd_lite_settings', 'orddd_lite_appearance_calendar_theme_callback' ),
            'orddd_lite_appearance_page',
            'orddd_lite_appearance_section',
            array( __( 'Select the theme for the calendar which blends with the design of your website.', 'order-delivery-date' ) )
        );
        
        add_settings_field(
            'orddd_lite_no_fields_for_product_type',
            __( 'Disable the Delivery Date Field for:', 'order-delivery-date' ),
            array( 'orddd_lite_settings', 'orddd_lite_appearance_virtual_product_callback' ),
            'orddd_lite_appearance_page',
            'orddd_lite_appearance_section',
            array( __( '<br>Disable the Delivery Date on the Checkout page for Virtual products and Featured products.', 'order-delivery-date' ) )
        );
         
        register_setting(
            'orddd_lite_appearance_settings',
            'orddd_lite_language_selected'
        );
         
        register_setting(
            'orddd_lite_appearance_settings',
            'orddd_lite_delivery_date_format'
        );
         
        register_setting(
            'orddd_lite_appearance_settings',
            'orddd_lite_start_of_week'
        );
         
        register_setting(
            'orddd_lite_appearance_settings',
            'orddd_lite_delivery_date_field_label'
        );
         
        register_setting(
            'orddd_lite_appearance_settings',
            'orddd_lite_delivery_date_field_placeholder'
        );
         
        register_setting(
            'orddd_lite_appearance_settings',
            'orddd_lite_delivery_date_field_note'
        );
         
        register_setting(
            'orddd_lite_appearance_settings',
            'orddd_lite_number_of_months'
        );
         
        register_setting(
            'orddd_lite_appearance_settings',
            'orddd_lite_delivery_date_fields_on_checkout_page'
        );
         
        register_setting(
            'orddd_lite_appearance_settings',
            'orddd_lite_delivery_date_on_cart_page'
        );

        register_setting(
            'orddd_lite_appearance_settings',
            'orddd_lite_calendar_theme_name'
        );
         
        register_setting(
            'orddd_lite_appearance_settings',
            'orddd_lite_calendar_theme'
        );
        
        register_setting(
            'orddd_lite_appearance_settings',
            'orddd_lite_no_fields_for_virtual_product'
        );

        register_setting(
            'orddd_lite_appearance_settings',
            'orddd_lite_no_fields_for_featured_product'
        );            
    }
    
    /**
     * Add settings field on Holidays tab. 
     *
     * @hook admin_init
     * @since 1.5
     */    

    public static function order_lite_holidays_admin_settings() {
        // First, we register a section. This is necessary since all future options must belong to one.
        add_settings_section (
            'orddd_lite_holidays_section',
            __( 'Add Holiday', 'order-delivery-date' ),
            array( 'orddd_lite_settings', 'orddd_lite_holidays_admin_settings_callback' ),
            'orddd_lite_holidays_page'
        );
    
        add_settings_field (
            'orddd_lite_holiday_name',
            __( 'Name:', 'order-delivery-date' ),
            array( 'orddd_lite_settings', 'orddd_lite_holidays_name_callback' ),
            'orddd_lite_holidays_page',
            'orddd_lite_holidays_section',
            array ( __( 'Enter the name of the holiday here.', 'order-delivery-date' ) )
        );
    
        add_settings_field(
            'orddd_lite_holiday_date',
            __( 'Date:', 'order-delivery-date' ),
            array( 'orddd_lite_settings', 'orddd_lite_holidays_date_callback' ),
            'orddd_lite_holidays_page',
            'orddd_lite_holidays_section',
            array ( __( 'Select the holiday date here.', 'order-delivery-date' ) )
        );
   
        register_setting(
            'orddd_lite_holidays_settings',
            'orddd_lite_holidays',
            array( 'orddd_lite_settings', 'orddd_lite_holidays_callback' )
        );
    }

    /**
     * Callback for Order Delivery Date Settings section
     *
     * @since 1.5
     */    

	public static function orddd_lite_delivery_date_setting() { }
    
    /**
     * Callback for adding settings tab in the Order Delivery Date menu
     *
     * @globals array $orddd_lite_weekdays Weekdays array
     * @since 1.5
     */    
 
    public static function orddd_lite_order_delivery_date_settings() {
        global $orddd_lite_weekdays;
        $action = $active_date_settings = $active_appearance = $active_holidays = '';
        if ( isset( $_GET[ 'action' ] ) ) {
            $action = $_GET[ 'action' ];
        } else {
            $action = "date";
        }
        
        if ( $action == 'date' || $action == '' ) {
            $active_date_settings = "nav-tab-active";
        }
        
        if ( $action == 'appearance' ) {
            $active_appearance = "nav-tab-active";
        }
        
        if( $action == 'holidays' ) {
            $active_holidays = 'nav-tab-active'; 
        }
        ?>
        <h2><?php _e( 'Order Delivery Date Settings', 'order-delivery-date' ); ?></h2>
        <?php 
        settings_errors();
        ?>	
        <h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
            <a href="admin.php?page=order_delivery_date_lite&action=date" class="nav-tab <?php echo $active_date_settings; ?>"><?php _e( 'Date Settings', 'order-delivery-date' );?> </a>
            <a href="admin.php?page=order_delivery_date_lite&action=appearance" class="nav-tab <?php echo $active_appearance; ?>"> <?php _e( 'Appearance', 'order-delivery-date' );?> </a>
            <a href="admin.php?page=order_delivery_date_lite&action=holidays" class="nav-tab <?php echo $active_holidays; ?>"> <?php _e( 'Holidays', 'order-delivery-date' );?> </a>

            <?php do_action ( "orddd_lite_add_settings_tab" ); ?>
        </h2>
        <?php
        do_action ( "orddd_lite_add_tab_content" );
        if ( $action == 'date' || $action == '' ) {
            print( '<div id="content">
                <form method="post" action="options.php">');
                    settings_fields( "orddd_lite_date_settings" );
                    do_settings_sections( "orddd_lite_date_settings_page" );
                    submit_button ( __( 'Save Settings', 'order-delivery-date' ), 'primary', 'save_orddd_lite', true );
                print('</form>
            </div>');
        } elseif ( $action == 'appearance' ) {
            print( '<div id="content">
                <form method="post" action="options.php">');
                settings_fields( "orddd_lite_appearance_settings" );
                do_settings_sections( "orddd_lite_appearance_page" );
                submit_button ( __( 'Save Settings', 'order-delivery-date' ), 'primary', 'save', true );
                print('</form>
            </div>' );
        } elseif ( $action == 'holidays' ) {
            print( '<div id="content">
                <form method="post" action="options.php">');
                settings_fields( "orddd_lite_holidays_settings" );
                do_settings_sections( "orddd_lite_holidays_page" );
                submit_button ( __( 'Save Settings', 'order-delivery-date' ), 'primary', 'save', true );
                print('</form>
            </div>' );

            echo "<h3 id='holidays_table_head'>" . __( 'Holidays', 'order-delivery-date' ) . "</h3>";
            include_once( 'class-view-holidays.php' );
            $orddd_table = new ORDDD_LITE_View_Holidays_Table();
            $orddd_table->orddd_prepare_items();
            ?>
            <div id = "orddd_lite_holidays_list">
                <form id="holidays" method="get" >
                    <input type="hidden" name="page" value="order_delivery_date_lite" />
                    <input type="hidden" name="tab" value="holidays" />
                    <?php $orddd_table->display(); ?>
                </form>
            </div>
            <?php    
        }
    }
    

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
     * Callback for adding Delivery Weekdays dropdown
     *
     * $params array $args Callback arguments
     * @since 1.5
     */    

    public static function orddd_lite_delivery_days_callback( $args ) {
        global $orddd_lite_weekdays;
        printf( '<fieldset class="orddd-lite-days-fieldset">
            <legend><b>' . __( 'Weekdays:', 'order-delivery-date' ) . '</b></legend>'
        );
        $html = '';
        printf( '<table>' );
        foreach ( $orddd_lite_weekdays as $n => $day_name ) {
            printf('<tr>
    	       <td class="orddd_lite_fieldset_padding"><input type="checkbox" name="' . $n . '" id="' . $n .'" value="checked" ' . get_option( $n ) . '/></td>
    	       <td class="orddd_lite_fieldset_padding"><label class="ord_label" for="' . $day_name . '">' . __( $day_name, 'order-delivery-date' ) . '</label></td>'
            );
        }
        printf( '</table>
        </fieldset>');
    
        $html .= '<label for="orddd_lite_delivery_days"> '  . $args[0] . '</label>';
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
    
    /**
     * Callback for adding Holidays tab
     *
     * $params array $args Callback arguments
     * @since 1.5
     */  

    public static function orddd_lite_holidays_admin_settings_callback() {}

    /**
     * Callback for adding Holiday name text field
     *
     * $params array $args Callback arguments
     * @since 1.5
     */  

    public static function orddd_lite_holidays_name_callback( $args ) {
        echo '<input type="text" name="orddd_lite_holiday_name" id="orddd_lite_holiday_name" class="orddd_lite_holiday_name"/>';
   
        $html = '<label for="orddd_lite_holiday_name"> ' . $args[0] . '</label>';
        echo $html;
    }
    
    /**
     * Callback for adding holiday dates
     *
     * $params array $args Callback arguments
     * @since 1.5
     */  

    public static function orddd_lite_holidays_date_callback( $args ) {
        $current_language = get_option( 'orddd_lite_language_selected' );
        $first_day_of_week = '1';
        if( get_option( 'orddd_lite_start_of_week' ) != '' ) {
            $first_day_of_week = get_option( 'orddd_lite_start_of_week' );
        }
        print( '<script type="text/javascript">
             jQuery( document ).ready( function() {
                jQuery.datepicker.setDefaults( jQuery.datepicker.regional[ "en-GB" ] );
                var formats = [ "mm-dd-yy", "d.m.y", "d M, yy","MM d, yy" ];
                jQuery( "#orddd_lite_holiday_date" ).val( "" ).datepicker( {
                    constrainInput: true,
                    dateFormat: formats[0],
                    firstDay: ' . $first_day_of_week . '
                } );
            } );
        </script>' );

        echo '<input type="text" name="orddd_lite_holiday_date" id="orddd_lite_holiday_date" class="orddd_lite_holiday_date" />';

        $html = '<label for="orddd_lite_holiday_date"> ' . $args[0] . '</label>';
        echo $html;
    }  

    /**
     * Callback for saving holidays in json object
     *
     * $params array $input Content of the selected settings
     * @return array $output Json object of the holidays added 
     * @since 1.5
     */  

    public static function orddd_lite_holidays_callback( $input ) {
        $output = array();
        if( isset( $_POST[ 'orddd_lite_holiday_date' ]  ) ) {
            $date_arr = explode( "-", sanitize_text_field( $_POST[ 'orddd_lite_holiday_date' ] ) );
            $holiday_date = date( ORDDD_LITE_HOLIDAY_DATE_FORMAT, gmmktime( 0, 0, 0, $date_arr[ 0 ], $date_arr[ 1 ], $date_arr[ 2 ] ) );

            $holidays = get_option( 'orddd_lite_holidays' );
            if ( $holidays == '' || $holidays == '{}' || $holidays == '[]' ) {
                $holidays_arr = array();
            } else {
                $holidays_arr = json_decode( $holidays );
            }
            
            foreach ( $holidays_arr as $k => $v ) {
                $holidays_new_arr[] = array( 'n' => $v->n, 'd' => $v->d );
            }
            
            $holiday_name = str_replace( "\'", "", sanitize_text_field( $_POST[ 'orddd_lite_holiday_name' ] ) );
            $holiday_name = str_replace( '\"', '', $holiday_name );
            $holidays_new_arr[] = array( 'n' => $holiday_name,
                'd' => $holiday_date );
            $holidays_jarr = json_encode( $holidays_new_arr );
            $output = $holidays_jarr;    
        } else {
            $output = $input;
        }
        
        return $output;
    }

    /**
     * Callback for deleting the selected holidays
     *
     * @since 1.5
     */  

    public static function orddd_lite_delete_settings() {
        if ( ( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'order_delivery_date_lite' ) && ( isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] == 'holidays' ) && ( ( isset( $_GET[ 'action' ] ) && $_GET[ 'action' ] == 'orddd_lite_delete' ) || ( isset( $_GET[ 'action2' ] ) && $_GET[ 'action2' ] == 'orddd_lite_delete' ) ) ) {

            $holiday = array();
            if( isset( $_GET[ 'holiday' ] ) ) {
                $holiday = $_GET[ 'holiday' ];
            }
                
            $holidays = get_option( 'orddd_lite_holidays' );
            $holidays_arr = json_decode( $holidays );
            foreach( $holiday as $h_key => $h_value ) {
                foreach( $holidays_arr as $subKey => $subValue ) {
                    if( $subValue->d == $h_value ) {
                        unset( $holidays_arr[$subKey] );
                    }
                }
            }
           
            $holidays_jarr = json_encode( array_values($holidays_arr) );
           
            update_option( 'orddd_lite_holidays', $holidays_jarr );                
            wp_safe_redirect( admin_url( '/admin.php?page=order_delivery_date_lite&action=holidays' ) );
        }
    }
}

$orddd_lite_settings = new orddd_lite_settings();
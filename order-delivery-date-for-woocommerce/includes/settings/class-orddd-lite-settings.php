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

// Include required files.

require_once 'class-orddd-lite-date-settings.php';
require_once 'class-orddd-lite-shipping-days-settings.php';
require_once 'class-orddd-lite-appearance-settings.php';
require_once 'class-orddd-lite-holidays-settings.php';
require_once 'class-orddd-lite-calendar-sync-settings.php';

/**
 * Class for adding the settings of the plugin in admin.
 */
class Orddd_Lite_Settings {

	/**
	 * Adds Order Delivery Date menu in admin dashboard
	 *
	 * @hook admin_menu
	 * @since 1.5
	 */
	public static function orddd_lite_order_delivery_date_menu() {
		add_menu_page( 'Order Delivery Date', 'Order Delivery Date', 'manage_woocommerce', 'order_delivery_date_lite', array( 'Orddd_Lite_Settings', 'orddd_lite_order_delivery_date_settings' ) );
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
			'orddd_lite_date_settings_section',     // ID used to identify this section and with which to register options.
			__( 'Order Delivery Date Settings', 'order-delivery-date' ),        // Title to be displayed on the administration page.
			array( 'orddd_Lite_Date_Settings', 'orddd_lite_delivery_date_setting' ),        // Callback used to render the description of the section.
			'orddd_lite_date_settings_page'             // Page on which to add this section of options.
		);

		add_settings_field(
			'orddd_lite_enable_delivery_date',
			__( 'Enable Delivery Date:', 'order-delivery-date' ),
			array( 'orddd_Lite_Date_Settings', 'orddd_lite_enable_delivery_date_callback' ),
			'orddd_lite_date_settings_page',
			'orddd_lite_date_settings_section',
			array( __( 'Enable Delivery Date capture on the checkout page.', 'order-delivery-date' ) )
		);

		// Setting available for Pro Version Only.
		add_settings_field(
			'orddd_delivery_checkout_options',
			__( 'Delivery Checkout options:', 'order-delivery-date' ),
			array( 'orddd_Lite_Date_Settings', 'orddd_lite_delivery_checkout_options_callback' ),
			'orddd_lite_date_settings_page',
			'orddd_lite_date_settings_section',
			array( __( 'Choose the delivery date option to be displayed on the checkout page.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_delivery_days',
			__( 'Delivery Days:', 'order-delivery-date' ),
			array( 'orddd_Lite_Date_Settings', 'orddd_lite_delivery_days_callback' ),
			'orddd_lite_date_settings_page',
			'orddd_lite_date_settings_section',
			array( '&nbsp;' . __( 'Select weekdays for delivery.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_minimumOrderDays',
			__( 'Minimum Delivery time (in hours):', 'order-delivery-date' ),
			array( 'orddd_Lite_Date_Settings', 'orddd_lite_minimum_delivery_time_callback' ),
			'orddd_lite_date_settings_page',
			'orddd_lite_date_settings_section',
			array( __( 'Minimum number of hours required to prepare for delivery.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_number_of_dates',
			__( 'Number of dates to choose:', 'order-delivery-date' ),
			array( 'orddd_Lite_Date_Settings', 'orddd_lite_number_of_dates_callback' ),
			'orddd_lite_date_settings_page',
			'orddd_lite_date_settings_section',
			array( __( 'Number of dates available for delivery.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_date_field_mandatory',
			__( 'Mandatory field?:', 'order-delivery-date' ),
			array( 'orddd_Lite_Date_Settings', 'orddd_lite_date_field_mandatory_callback' ),
			'orddd_lite_date_settings_page',
			'orddd_lite_date_settings_section',
			array( __( 'Selection of delivery date on the checkout page will become mandatory.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_lockout_date_after_orders',
			__( 'Maximum Order Deliveries per day (based on per order):', 'order-delivery-date' ),
			array( 'orddd_Lite_Date_Settings', 'orddd_lite_lockout_date_after_orders_callback' ),
			'orddd_lite_date_settings_page',
			'orddd_lite_date_settings_section',
			array( __( 'Maximum deliveries/orders per day.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_enable_default_sorting_of_column',
			__( 'Sort on WooCommerce Orders Page:', 'order-delivery-date' ),
			array( 'orddd_Lite_Date_Settings', 'orddd_lite_enable_default_sorting_of_column_callback' ),
			'orddd_lite_date_settings_page',
			'orddd_lite_date_settings_section',
			array( __( 'Enable default sorting of orders (in descending order) by Delivery Date on WooCommerce -> Orders page', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_auto_populate_first_available_date',
			__( 'Auto-populate first available Delivery date:', 'order-delivery-date' ),
			array( 'orddd_Lite_Date_Settings', 'orddd_lite_auto_populate_first_available_date_callback' ),
			'orddd_lite_date_settings_page',
			'orddd_lite_date_settings_section',
			array( __( 'Auto-populate first available Delivery date when the checkout page loads.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_calculate_min_time_disabled_days',
			__( 'Apply Minimum Delivery Time for non working weekdays:', 'order-delivery-date' ),
			array( 'orddd_Lite_Date_Settings', 'orddd_lite_calculate_min_time_disabled_days_callback' ),
			'orddd_lite_date_settings_page',
			'orddd_lite_date_settings_section',
			array( __( 'If selected, then the Minimum Delivery Time (in hours) will be applied on the non working weekdays which are unchecked in Delivery Weekdays. If unchecked, then it will not be applied. For example, if Minimum Delivery Time (in hours) is set to 48 hours and Saturday is disabled for delivery. Now if a customer visits the website on Friday, then the first available date will be Monday and not Sunday.', 'order-delivery-date' ) )
		);

		register_setting(
			'orddd_lite_date_settings',
			'orddd_lite_enable_delivery_date'
		);

		foreach ( $orddd_lite_weekdays as $n => $day_name ) {
			register_setting(
				'orddd_lite_date_settings',
				$n,
				array( 'orddd_lite_date_settings', $n . '_save' )
			);
		}

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

		do_action( 'orddd_lite_add_new_settings' );

		add_settings_field(
			'orddd_enable_day_wise_settings',
			__( 'Weekday Settings:', 'order-delivery-date' ),
			array( 'orddd_Lite_Date_Settings', 'orddd_lite_enable_day_wise_settings_callback' ),
			'orddd_lite_date_settings_page',
			'orddd_lite_date_settings_section',
			array( __( 'Enable this setting to add Additional charges, Additional charges\' checkout label, Same day cut-off time, Next day cut-off time and Minimum Delivery Time (in hours) for each weekday.<br><b><i>Upgrade to <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank">Order Delivery Date Pro for WooCommerce</a> to enable the setting.</i></b>' ) )
		);

		// Shipping Days section.
		add_settings_section(
			'orddd_shipping_days_settings_section',
			__( 'Shipping Days Settings', 'order-delivery-date' ),
			array( 'Orddd_Lite_Shipping_Days_Settings', 'orddd_lite_shipping_days_settings_section_callback' ),
			'orddd_lite_date_settings_page'
		);

		add_settings_field(
			'orddd_enable_shipping_days',
			__( 'Enable Shipping days based calculation:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Shipping_Days_Settings', 'orddd_lite_enable_shipping_days_callback' ),
			'orddd_lite_date_settings_page',
			'orddd_shipping_days_settings_section',
			array( __( 'Calculate Minimum Delivery Time (in hours), Same Day cut-off and Next Day cut-off based on the shipping days selected.<br><b><i>Upgrade to <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank">Order Delivery Date Pro for WooCommerce</a> to enable the setting.</i></b>', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_shipping_days',
			__( 'Shipping Days:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Shipping_Days_Settings', 'orddd_lite_shipping_days_callback' ),
			'orddd_lite_date_settings_page',
			'orddd_shipping_days_settings_section',
			array( '&nbsp' . __( 'Select weekdays for shipping.<br><b><i>Upgrade to <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank">Order Delivery Date Pro for WooCommerce</a> to enable the setting.</i></b>', 'order-delivery-date' ) )
		);
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
			array( 'Orddd_Lite_Appearance_Settings', 'orddd_lite_appearance_admin_setting_callback' ),
			'orddd_lite_appearance_page'
		);

		add_settings_field(
			'orddd_lite_language_selected',
			__( 'Calendar Language:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Appearance_Settings', 'orddd_lite_appearance_calendar_language_callback' ),
			'orddd_lite_appearance_page',
			'orddd_lite_appearance_section',
			array( __( 'Choose a Language.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_delivery_date_format',
			__( 'Date Format:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Appearance_Settings', 'orddd_lite_appearance_date_formats_callback' ),
			'orddd_lite_appearance_page',
			'orddd_lite_appearance_section',
			array( '<br>' . __( 'The format in which the Delivery Date appears to the customers on the checkout page once the date is selected.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_start_of_week',
			__( 'First Day of Week:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Appearance_Settings', 'orddd_lite_appearance_first_day_of_week_callback' ),
			'orddd_lite_appearance_page',
			'orddd_lite_appearance_section',
			array( __( 'Choose the first day of week displayed on the Delivery Date calendar.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_delivery_date_field_label',
			__( 'Field Label:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Appearance_Settings', 'orddd_lite_delivery_date_field_label_callback' ),
			'orddd_lite_appearance_page',
			'orddd_lite_appearance_section',
			array( __( 'Choose the label that is to be displayed for the field on checkout page.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_delivery_date_field_placeholder',
			__( 'Field Placeholder Text:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Appearance_Settings', 'orddd_lite_delivery_date_field_placeholder_callback' ),
			'orddd_lite_appearance_page',
			'orddd_lite_appearance_section',
			array( __( 'Choose the placeholder text that is to be displayed for the field on checkout page.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_delivery_date_field_note',
			__( 'Field Note Text:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Appearance_Settings', 'orddd_lite_delivery_date_field_note_text_callback' ),
			'orddd_lite_appearance_page',
			'orddd_lite_appearance_section',
			array( '<br>' . __( 'Choose the note to be displayed below the delivery date field on checkout page.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_number_of_months',
			__( 'Number of Months:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Appearance_Settings', 'orddd_lite_appearance_number_of_months_callback' ),
			'orddd_lite_appearance_page',
			'orddd_lite_appearance_section',
			array( __( 'The number of months to be shown on the calendar.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_delivery_date_fields_on_checkout_page',
			__( 'Field placement on the Checkout page:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Appearance_Settings', 'orddd_lite_delivery_date_in_shipping_section_callback' ),
			'orddd_lite_appearance_page',
			'orddd_lite_appearance_section',
			array( __( '</br>The Delivery Date field will be displayed in the selected section.</br><i>Note: WooCommerce automatically hides the Shipping section fields for Virtual products.</i>', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_delivery_date_on_cart_page',
			__( 'Delivery Date field on Cart page:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Appearance_Settings', 'orddd_lite_delivery_date_on_cart_page_callback' ),
			'orddd_lite_appearance_page',
			'orddd_lite_appearance_section',
			array( __( 'Add the Delivery Date field on the cart page along with the Checkout page.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_calendar_theme_name',
			__( 'Theme:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Appearance_Settings', 'orddd_lite_appearance_calendar_theme_callback' ),
			'orddd_lite_appearance_page',
			'orddd_lite_appearance_section',
			array( __( 'Select the theme for the calendar which blends with the design of your website.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_no_fields_for_product_type',
			__( 'Disable the Delivery Date Field for:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Appearance_Settings', 'orddd_lite_appearance_virtual_product_callback' ),
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
		add_settings_section(
			'orddd_lite_holidays_section',
			__( 'Add Holiday', 'order-delivery-date' ),
			array( 'Orddd_Lite_Holidays_Settings', 'orddd_lite_holidays_admin_settings_callback' ),
			'orddd_lite_holidays_page'
		);

		add_settings_field(
			'orddd_lite_holiday_name',
			__( 'Holiday Name:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Holidays_Settings', 'orddd_lite_holidays_name_callback' ),
			'orddd_lite_holidays_page',
			'orddd_lite_holidays_section'
		);

		add_settings_field(
			'orddd_lite_holiday_date',
			__( 'From Date:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Holidays_Settings', 'orddd_lite_holidays_from_date_callback' ),
			'orddd_lite_holidays_page',
			'orddd_lite_holidays_section'
		);

		add_settings_field(
			'orddd_lite_holiday_to_date',
			__( 'To Date:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Holidays_Settings', 'orddd_lite_holidays_to_date_callback' ),
			'orddd_lite_holidays_page',
			'orddd_lite_holidays_section',
			array( __( '<br>Leave the "To Date:" field unchanged for single day holidays.</i></b>', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_allow_recurring_holiday',
			__( 'Allow Recurring:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Holidays_Settings', 'orddd_lite_allow_recurring_holiday_callback' ),
			'orddd_lite_holidays_page',
			'orddd_lite_holidays_section',
			array( __( 'Enable to block the holidays for all future years.', 'order-delivery-date' ) )
		);

		register_setting(
			'orddd_lite_holidays_settings',
			'orddd_lite_holidays',
			array( 'Orddd_Lite_Holidays_Settings', 'orddd_lite_holidays_callback' )
		);
	}

	/**
	 * Add settings fields to sync Google Calendar.
	 * This settings are only shown in the lite version. They can be used only in Pro.
	 *
	 * @since 3.9
	 */
	public static function orddd_lite_calendar_sync_settings_callback() {

		add_settings_section(
			'orddd_lite_calendar_sync_general_settings_section',
			__( 'General Settings', 'order-delivery-date' ),
			array( 'Orddd_Lite_Calendar_Sync_Settings', 'orddd_lite_calendar_sync_general_settings_callback' ),
			'orddd_lite_calendar_sync_settings_page'
		);

		add_settings_field(
			'orddd_lite_calendar_event_location',
			__( 'Event Location', 'order-delivery-date' ),
			array( 'Orddd_Lite_Calendar_Sync_Settings', 'orddd_lite_calendar_event_location_callback' ),
			'orddd_lite_calendar_sync_settings_page',
			'orddd_lite_calendar_sync_general_settings_section',
			array( __( '<br>Enter the text that will be used in the location field of the calendar event. If left empty, the website description will be used. <br><i>Note: You can use ADDRESS, FULL_ADDRESS and CITY placeholders which will be replaced by their real values.</i><br><b><i>Upgrade to <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank">Order Delivery Date Pro for WooCommerce</a> to enable the setting.</i></b>', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_calendar_event_summary',
			__( 'Event summary (name)', 'order-delivery-date' ),
			array( 'Orddd_Lite_Calendar_Sync_Settings', 'orddd_lite_calendar_event_summary_callback' ),
			'orddd_lite_calendar_sync_settings_page',
			'orddd_lite_calendar_sync_general_settings_section'
		);

		add_settings_field(
			'orddd_lite_calendar_event_description',
			__( 'Event Description', 'order-delivery-date' ),
			array( 'Orddd_Lite_Calendar_Sync_Settings', 'orddd_lite_calendar_event_description_callback' ),
			'orddd_lite_calendar_sync_settings_page',
			'orddd_lite_calendar_sync_general_settings_section',
			array( __( '<br>For the above 2 fields, you can use the following placeholders which will be replaced by their real values:&nbsp;SITE_NAME, CLIENT, PRODUCTS, PRODUCT_WITH_QTY, ORDER_DATE_TIME, ORDER_DATE, ORDER_NUMBER, PRICE, PHONE, NOTE, ADDRESS, FULL_ADDRESS , EMAIL (Client\'s email).<br><b><i>Upgrade to <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank">Order Delivery Date Pro for WooCommerce</a> to enable the setting.</i></b>', 'order-delivery-date' ) )
		);

		add_settings_section(
			'orddd_lite_calendar_sync_customer_settings_section',
			__( 'Customer Add to Calendar button Settings', 'order-delivery-date' ),
			array( 'Orddd_Lite_Calendar_Sync_Settings', 'orddd_lite_calendar_sync_customer_settings_callback' ),
			'orddd_lite_calendar_sync_settings_page'
		);

		add_settings_field(
			'orddd_lite_add_to_calendar_order_received_page',
			__( 'Show Add to Calendar button on Order received page', 'order-delivery-date' ),
			array( 'Orddd_Lite_Calendar_Sync_Settings', 'orddd_lite_add_to_calendar_order_received_page_callback' ),
			'orddd_lite_calendar_sync_settings_page',
			'orddd_lite_calendar_sync_customer_settings_section',
			array( __( 'Show Add to Calendar button on the Order Received page for the customers.<br><b><i>Upgrade to <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank">Order Delivery Date Pro for WooCommerce</a> to enable the setting.</i></b>', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_add_to_calendar_customer_email',
			__( 'Show Add to Calendar button in the Customer notification email', 'order-delivery-date' ),
			array( 'Orddd_Lite_Calendar_Sync_Settings', 'orddd_lite_add_to_calendar_customer_email_callback' ),
			'orddd_lite_calendar_sync_settings_page',
			'orddd_lite_calendar_sync_customer_settings_section',
			array( __( 'Show Add to Calendar button in the Customer notification email.<br><b><i>Upgrade to <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank">Order Delivery Date Pro for WooCommerce</a> to enable the setting.</i></b>', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_add_to_calendar_my_account_page',
			__( 'Show Add to Calendar button on My account', 'order-delivery-date' ),
			array( 'Orddd_Lite_Calendar_Sync_Settings', 'orddd_lite_add_to_calendar_my_account_page_callback' ),
			'orddd_lite_calendar_sync_settings_page',
			'orddd_lite_calendar_sync_customer_settings_section',
			array( __( 'Show Add to Calendar button on My account page for the customers.<br><b><i>Upgrade to <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank">Order Delivery Date Pro for WooCommerce</a> to enable the setting.</i></b>', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_calendar_in_same_window',
			__( 'Open Calendar in Same Window', 'order-delivery-date' ),
			array( 'Orddd_Lite_Calendar_Sync_Settings', 'orddd_lite_calendar_in_same_window_callback' ),
			'orddd_lite_calendar_sync_settings_page',
			'orddd_lite_calendar_sync_customer_settings_section',
			array( __( 'As default, the Calendar is opened in a new tab or window. If you check this option, user will be redirected to the Calendar from the same page, without opening a new tab or window.<br><b><i>Upgrade to <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank">Order Delivery Date Pro for WooCommerce</a> to enable the setting.</i></b>', 'order-delivery-date' ) )
		);

		add_settings_section(
			'orddd_lite_calendar_sync_admin_settings_section',
			__( 'Admin Calendar Sync Settings', 'order-delivery-date' ),
			array( 'Orddd_Lite_Calendar_Sync_Settings', 'orddd_lite_calendar_sync_admin_settings_section_callback' ),
			'orddd_lite_calendar_sync_settings_page'
		);

		add_settings_field(
			'orddd_lite_calendar_sync_integration_mode',
			__( 'Integration Mode', 'order-delivery-date' ),
			array( 'Orddd_Lite_Calendar_Sync_Settings', 'orddd_lite_calendar_sync_integration_mode_callback' ),
			'orddd_lite_calendar_sync_settings_page',
			'orddd_lite_calendar_sync_admin_settings_section',
			array( __( '<br>Select method of integration. "Sync Automatically" will add the delivery events to the Google calendar, which is set in the "Calendar to be used" field, automatically when a customer places an order. Also, an "Add to Calendar" button is added on the Delivery Calendar page in admin to Sync past orders. <br>"Sync Manually" will add an "Add to Google Calendar" button in emails received by admin and New customer order.<br>"Disabled" will disable the integration with Google Calendar.<br>Note: Import of the events will work manually using .ics link.<br><b><i>Upgrade to <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank">Order Delivery Date Pro for WooCommerce</a> to enable the setting.</i></b>', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_sync_calendar_instructions',
			__( 'Instructions', 'order-delivery-date' ),
			array( 'Orddd_Lite_Calendar_Sync_Settings', 'orddd_lite_sync_calendar_instructions_callback' ),
			'orddd_lite_calendar_sync_settings_page',
			'orddd_lite_calendar_sync_admin_settings_section'
		);

		add_settings_field(
			'orddd_lite_calendar_key_file_name',
			__( 'Key file name', 'order-delivery-date' ),
			array( 'Orddd_Lite_Calendar_Sync_Settings', 'orddd_lite_calendar_key_file_name_callback' ),
			'orddd_lite_calendar_sync_settings_page',
			'orddd_lite_calendar_sync_admin_settings_section',
			array( __( '<br>Enter key file name here without extention, e.g. ab12345678901234567890-privatekey.<br><b><i>Upgrade to <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank">Order Delivery Date Pro for WooCommerce</a> to enable the setting.</i></b>', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_calendar_service_acc_email_address',
			__( 'Service account email address', 'order-delivery-date' ),
			array( 'Orddd_Lite_Calendar_Sync_Settings', 'orddd_lite_calendar_service_acc_email_address_callback' ),
			'orddd_lite_calendar_sync_settings_page',
			'orddd_lite_calendar_sync_admin_settings_section',
			array( __( '<br>Enter Service account email address here, e.g. 1234567890@developer.gserviceaccount.com.<br><b><i>Upgrade to <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank">Order Delivery Date Pro for WooCommerce</a> to enable the setting.</i></b>', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_calendar_id',
			__( 'Calendar to be used', 'order-delivery-date' ),
			array( 'Orddd_Lite_Calendar_Sync_Settings', 'orddd_lite_calendar_id_callback' ),
			'orddd_lite_calendar_sync_settings_page',
			'orddd_lite_calendar_sync_admin_settings_section',
			array( __( '<br>Enter the ID of the calendar in which your deliveries will be saved, e.g. abcdefg1234567890@group.calendar.google.com.<br><b><i>Upgrade to <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank">Order Delivery Date Pro for WooCommerce</a> to enable the setting.</i></b>', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_admin_add_to_calendar_delivery_calendar',
			__( 'Show "Export to Google Calendar" button on Delivery Calendar page', 'order-delivery-date' ),
			array( 'Orddd_Lite_Calendar_Sync_Settings', 'orddd_lite_admin_add_to_calendar_delivery_calendar_callback' ),
			'orddd_lite_calendar_sync_settings_page',
			'orddd_lite_calendar_sync_admin_settings_section',
			array( __( 'Show "Export to Google Calendar" button on the Order Delivery Date -> Delivery Calendar page.<br><i>Note: This button can be used to export the already placed orders with future deliveries from the current date to the calendar used above.</i><br><b><i>Upgrade to <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank">Order Delivery Date Pro for WooCommerce</a> to enable the setting.</i></b>', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_admin_add_to_calendar_email_notification',
			__( 'Show Add to Calendar button in New Order email notification', 'order-delivery-date' ),
			array( 'Orddd_Lite_Calendar_Sync_Settings', 'orddd_lite_admin_add_to_calendar_email_notification_callback' ),
			'orddd_lite_calendar_sync_settings_page',
			'orddd_lite_calendar_sync_admin_settings_section',
			array( __( 'Show "Add to Calendar" button in the New Order email notification.<br><b><i>Upgrade to <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank">Order Delivery Date Pro for WooCommerce</a> to enable the setting.</i></b>', 'order-delivery-date' ) )
		);

		add_settings_section(
			'orddd_lite_calendar_import_ics_feeds_section',
			__( 'Import Events', 'order-delivery-date' ),
			array( 'Orddd_Lite_Calendar_Sync_Settings', 'orddd_lite_calendar_import_ics_feeds_section_callback' ),
			'orddd_lite_calendar_sync_settings_page'
		);

		add_settings_field(
			'orddd_lite_ics_feed_url_instructions',
			__( 'Instructions', 'order-delivery-date' ),
			array( 'Orddd_Lite_Calendar_Sync_Settings', 'orddd_lite_ics_feed_url_instructions_callback' ),
			'orddd_lite_calendar_sync_settings_page',
			'orddd_lite_calendar_import_ics_feeds_section'
		);

		add_settings_field(
			'orddd_lite_ics_feed_url',
			__( 'iCalendar/.ics Feed URL', 'order-delivery-date' ),
			array( 'Orddd_Lite_Calendar_Sync_Settings', 'orddd_lite_ics_feed_url_callback' ),
			'orddd_lite_calendar_sync_settings_page',
			'orddd_lite_calendar_import_ics_feeds_section'
		);

		add_settings_field(
			'orddd_lite_real_time_import',
			__( 'Import frequency', 'order-delivery-date' ),
			array( 'Orddd_Lite_Calendar_Sync_Settings', 'orddd_lite_real_time_import_callback' ),
			'orddd_lite_calendar_sync_settings_page',
			'orddd_lite_calendar_import_ics_feeds_section',
			array( __( 'Import events from Google calendar based on the time set below. By default, all events from the Google calendar will be imported once every 24 hours.<br><b><i>Upgrade to <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank">Order Delivery Date Pro for WooCommerce</a> to enable the setting.</i></b>', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_wp_cron_minutes',
			__( 'Enter Import frequency (in minutes)', 'order-delivery-date' ),
			array( 'Orddd_Lite_Calendar_Sync_Settings', 'orddd_lite_wp_cron_minutes_callback' ),
			'orddd_lite_calendar_sync_settings_page',
			'orddd_lite_calendar_import_ics_feeds_section',
			array( __( 'The duration in minutes at which events from the Google Calendar ICS feeds will be imported automatically in the store. <i>Note: Setting this to a lower value then 10 minutes may impact the performance of your store.</i><br><b><i>Upgrade to <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank">Order Delivery Date Pro for WooCommerce</a> to enable the setting.</i></b>' ) )
		);
	}

	/**
	 * Callback for adding settings tab in the Order Delivery Date menu
	 *
	 * @globals array $orddd_lite_weekdays Weekdays array
	 * @since 1.5
	 */
	public static function orddd_lite_order_delivery_date_settings() {
		global $orddd_lite_weekdays;
		$action                 = '';
		$active_date_settings   = '';
		$active_appearance      = '';
		$active_holidays        = '';
		$active_shipping_based  = '';
		$calendar_sync_settings = '';

		// phpcs:ignore WordPress.Security.NonceVerification
		if ( isset( $_GET['action'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification
			$action = sanitize_key( wp_unslash( $_GET['action'] ) );
		} else {
			$action = 'date';
		}

		if ( 'date' === $action || '' === $action ) {
			$active_date_settings = 'nav-tab-active';
		}

		if ( 'appearance' === $action ) {
			$active_appearance = 'nav-tab-active';
		}

		if ( 'holidays' === $action ) {
			$active_holidays = 'nav-tab-active';
		}

		if ( 'shipping_based' === $action ) {
			$active_shipping_based = 'nav-tab-active';
		}

		if ( 'calendar_sync_settings' === $action ) {
			$calendar_sync_settings = 'nav-tab-active';
		}

		?>
		<h2><?php esc_html_e( 'Order Delivery Date Settings', 'order-delivery-date' ); ?></h2>
		<?php
		settings_errors();
		?>
		<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
			<a href="admin.php?page=order_delivery_date_lite&action=date" class="nav-tab <?php echo esc_attr( $active_date_settings ); ?>"><?php esc_html_e( 'Date Settings', 'order-delivery-date' ); ?> </a>
			<a href="admin.php?page=order_delivery_date_lite&action=appearance" class="nav-tab <?php echo esc_attr( $active_appearance ); ?>"> <?php esc_html_e( 'Appearance', 'order-delivery-date' ); ?> </a>
			<a href="admin.php?page=order_delivery_date_lite&action=holidays" class="nav-tab <?php echo esc_attr( $active_holidays ); ?>"> <?php esc_html_e( 'Holidays', 'order-delivery-date' ); ?> </a>
			<a href="admin.php?page=order_delivery_date_lite&action=shipping_based" class="nav-tab <?php echo esc_attr( $active_shipping_based ); ?>"> <?php esc_html_e( 'Custom Delivery Settings', 'order-delivery-date' ); ?> </a>
			<a href="admin.php?page=order_delivery_date_lite&action=calendar_sync_settings" class="nav-tab <?php echo esc_attr( $calendar_sync_settings ); ?>"> <?php esc_html_e( 'Google Calendar Sync', 'order-delivery-date' ); ?> </a>
			<?php do_action( 'orddd_lite_add_settings_tab' ); ?>
		</h2>
		<?php
		do_action( 'orddd_lite_add_tab_content' );
		if ( 'date' === $action || '' === $action ) {
			print( '<div id="content">
                <form method="post" action="options.php">' );
					settings_fields( 'orddd_lite_date_settings' );
					do_settings_sections( 'orddd_lite_date_settings_page' );
					submit_button( __( 'Save Settings', 'order-delivery-date' ), 'primary', 'save_orddd_lite', true );
				print( '</form>
            </div>' );
		} elseif ( 'appearance' === $action ) {
			print( '<div id="content">
                <form method="post" action="options.php">' );
				settings_fields( 'orddd_lite_appearance_settings' );
				do_settings_sections( 'orddd_lite_appearance_page' );
				submit_button( __( 'Save Settings', 'order-delivery-date' ), 'primary', 'save', true );
				print( '</form>
            </div>' );
		} elseif ( 'holidays' === $action ) {
			print( '<div id="content">
                <form method="post" action="options.php">' );
				settings_fields( 'orddd_lite_holidays_settings' );
				do_settings_sections( 'orddd_lite_holidays_page' );
				submit_button( __( 'Save Settings', 'order-delivery-date' ), 'primary', 'save', true );
				print( '</form>
            </div>' );

			echo "<h3 id='holidays_table_head'>" . esc_html__( 'Holidays', 'order-delivery-date' ) . '</h3>';
			include_once 'class-orddd-lite-view-holidays-table.php';
			$orddd_table = new Orddd_Lite_View_Holidays_Table();
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
		} elseif ( 'shipping_based' === $action ) {
			echo '<br>
            <b>
                <i>Upgrade to <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank">Order Delivery Date Pro for WooCommerce</a> to enable creating delivery schedules by Product Categories, Shipping Methods, Shipping Classes, Table Rate Shipping Methods & Pickup Locations.
                </i>
            </b>
            <br>
            <br>
            <b>
                <i>You can refer to our documentation for creating Custom Delivery Schedules <a href="https://www.tychesoftwares.com/docs/docs/order-delivery-date-pro-for-woocommerce/custom-delivery-settings/" target="_blank">here</a>.
                </i>
            </b>';

		} elseif ( 'calendar_sync_settings' === $action ) {
			print( '<div id="content">
                <form method="post" action="options.php">' );
					settings_fields( 'orddd_lite_calendar_sync_settings' );
					do_settings_sections( 'orddd_lite_calendar_sync_settings_page' );
					submit_button( __( 'Save Settings', 'order-delivery-date' ), 'primary', 'save', true );
				print( '</form>
            </div>' );
		}
	}

	/**
	 * Callback for deleting the selected holidays
	 *
	 * @since 1.5
	 */
	public static function orddd_lite_delete_settings() {
		// phpcs:ignore WordPress.Security.NonceVerification
		if ( ( isset( $_GET['page'] ) && 'order_delivery_date_lite' === $_GET['page'] ) && ( isset( $_GET['tab'] ) && 'holidays' === $_GET['tab'] ) && ( ( isset( $_GET['action'] ) && 'orddd_lite_delete' === $_GET['action'] ) || ( isset( $_GET['action2'] ) && 'orddd_lite_delete' === $_GET['action2'] ) ) ) {

			$holiday = array();
			// phpcs:ignore WordPress.Security.NonceVerification
			if ( isset( $_GET['holiday'] ) ) {
				// phpcs:ignore WordPress.Security.NonceVerification
				$holiday = array_map( 'sanitize_text_field', wp_unslash( $_GET['holiday'] ) );
			}

			$holidays     = get_option( 'orddd_lite_holidays' );
			$holidays_arr = json_decode( $holidays );
			foreach ( $holiday as $h_key => $h_value ) {
				foreach ( $holidays_arr as $sub_key => $sub_value ) {
					if ( $sub_value->d === $h_value ) {
						unset( $holidays_arr[ $sub_key ] );
					}
				}
			}

			$holidays_jarr = wp_json_encode( array_values( $holidays_arr ) );

			update_option( 'orddd_lite_holidays', $holidays_jarr );
			wp_safe_redirect( admin_url( '/admin.php?page=order_delivery_date_lite&action=holidays' ) );
		}
	}
}

$orddd_lite_settings = new orddd_lite_settings();

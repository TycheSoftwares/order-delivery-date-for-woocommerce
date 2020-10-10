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
require_once 'class-orddd-lite-delivery-days-settings.php';
require_once 'class-orddd-lite-time-settings.php';
require_once 'class-orddd-lite-additional-settings.php';
require_once 'class-orddd-lite-time-slot-settings.php';

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
			'orddd_lite_delivery_time_format',
			__( 'Time Format:', 'order-delivery-date' ),
			array( 'orddd_lite_appearance_settings', 'orddd_lite_time_format_callback' ),
			'orddd_lite_appearance_page',
			'orddd_lite_appearance_section',
			array( __( 'The time range will come in the selected format. If 12 hour format is selected, then the time slider will appear in am/pm format.', 'order-delivery-date' ) )
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
			'orddd_lite_delivery_timeslot_field_label',
			__( 'Time slot Field Label:', 'order-delivery-date' ),
			array( 'orddd_lite_appearance_settings', 'orddd_lite_delivery_timeslot_field_label_callback' ),
			'orddd_lite_appearance_page',
			'orddd_lite_appearance_section',
			array( __( 'Choose a label that is to be displayed for the time slot field on the checkout page.', 'order-delivery-date' ) )
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
			'orddd_lite_delivery_time_format'
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
			'orddd_lite_delivery_timeslot_field_label'
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
	 * Add settings fields & Register settings for time slots in the 'Time Slot' tab
	 *
	 * @since 3.11.0
	 */
	public static function orddd_lite_time_slot_settings() {

		add_settings_section(
			'orddd_lite_time_slot_section',
			__( 'Time Slot Settings', 'order-delivery-date' ),
			array( 'Orddd_Lite_Time_Slot_Settings', 'orddd_lite_time_slot_admin_settings_callback' ),
			'orddd_lite_time_slot_page'
		);

		add_settings_field(
			'orddd_lite_enable_time_slot',
			__( 'Enable time slot capture:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Time_Slot_Settings', 'orddd_lite_time_slot_enable_callback' ),
			'orddd_lite_time_slot_page',
			'orddd_lite_time_slot_section',
			array( __( 'Allows the customer to choose a time slot for delivery on the checkout page.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_time_slot_mandatory',
			__( 'Mandatory field?:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Time_Slot_Settings', 'orddd_lite_time_slot_mandatory_callback' ),
			'orddd_lite_time_slot_page',
			'orddd_lite_time_slot_section',
			array( __( 'Selection of Time slot on the checkout page will become mandatory.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_time_slot_asap',
			__( "Show 'As Soon As Possible' option:", 'order-delivery-date' ),
			array( 'Orddd_Lite_Time_Slot_Settings', 'orddd_lite_time_slot_asap_callback' ),
			'orddd_lite_time_slot_page',
			'orddd_lite_time_slot_section',
			array( __( 'A new option will be added in the Time slot dropdown on checkout page.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_global_lockout_time_slots',
			__( 'Global Maximum Order Deliveries for Time slots:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Time_Slot_Settings', 'orddd_lite_global_lockout_time_slots_callback' ),
			'orddd_lite_time_slot_page',
			'orddd_lite_time_slot_section',
			array( __( 'Maximum deliveries/orders applied to all the Time slots if the individual Maximum Order Deliveries for Time slots is blank for Custom Delivery Settings.<br><i>Note: Leave blank for Unlimited Deliveries.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_auto_populate_first_available_time_slot',
			__( 'Auto-populate first available delivery time slot:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Time_Slot_Settings', 'orddd_lite_show_first_available_time_slot_callback' ),
			'orddd_lite_time_slot_page',
			'orddd_lite_time_slot_section',
			array( __( 'Auto-populate first available Delivery time slot when the date is selected on the checkout page.', 'order-delivery-date' ) )
		);

		add_settings_section(
			'orddd_lite_add_time_slot_section',
			__( 'Add Time Slot <a href=https://www.tychesoftwares.com/docs/docs/order-delivery-date-pro-for-woocommerce/setup-delivery-date-with-time/?utm_source=userwebsite&utm_medium=link&utm_campaign=OrderDeliveryDateProSetting" target="_blank" class="dashicons dashicons-external" style="line-height:unset;"></a>', 'order-delivery-date' ),
			array( 'Orddd_Lite_Time_Slot_Settings', 'orddd_lite_add_time_slot_admin_settings_callback' ),
			'orddd_lite_time_slot_page'
		);

		add_settings_field(
			'orddd_lite_time_slot_for_delivery_days',
			__( 'Time Slot for:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Time_Slot_Settings', 'orddd_lite_time_slot_for_delivery_days_callback' ),
			'orddd_lite_time_slot_page',
			'orddd_lite_add_time_slot_section',
			array( __( 'Select Weekday option or Specific delivery dates option to create a time slot.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_time_slot_for_weekdays',
			__( 'Select Delivery Days/Dates:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Time_Slot_Settings', 'orddd_lite_time_slot_for_weekdays_callback' ),
			'orddd_lite_time_slot_page',
			'orddd_lite_add_time_slot_section',
			array( __( 'Select Delivery Days/Dates for which you want to create an exclusive Time Slot. To create a time slot for all the weekdays, select "All".', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_time_from_hours',
			__( 'Time From:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Time_Slot_Settings', 'orddd_lite_time_from_hours_callback' ),
			'orddd_lite_time_slot_page',
			'orddd_lite_add_time_slot_section',
			array( __( 'Start time for the time slot.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_time_to_hours',
			__( 'Time To:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Time_Slot_Settings', 'orddd_lite_time_to_hours_callback' ),
			'orddd_lite_time_slot_page',
			'orddd_lite_add_time_slot_section',
			array( __( 'End time for the time slot.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_time_slot_lockout',
			__( 'Maximum Order Deliveries per time slot (based on per order):', 'order-delivery-date' ),
			array( 'Orddd_Lite_Time_Slot_Settings', 'orddd_lite_time_slot_lockout_callback' ),
			'orddd_lite_time_slot_page',
			'orddd_lite_add_time_slot_section',
			array( __( 'A time slot will become unavailable for further deliveries once these many orders are placed for delivery for that time slot. <br> <em>Note: If Max order deliveries is set, then that will get priority over time slot lockout.</em>', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_time_slot_additional_charges',
			__( 'Additional Charges for time slot and Checkout label:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Time_Slot_Settings', 'orddd_lite_time_slot_additional_charges_callback' ),
			'orddd_lite_time_slot_page',
			'orddd_lite_add_time_slot_section',
			array( __( 'Add delivery charges (if applicable) for time slot and add the label to be displayed on Checkout page.', 'order-delivery-date' ) )
		);

		register_setting(
			'orddd_lite_time_slot_settings',
			'orddd_lite_enable_time_slot'
		);

		register_setting(
			'orddd_lite_time_slot_settings',
			'orddd_lite_time_slot_mandatory'
		);

		register_setting(
			'orddd_lite_time_slot_settings',
			'orddd_lite_time_slot_asap'
		);
		register_setting(
			'orddd_lite_time_slot_settings',
			'orddd_lite_global_lockout_time_slots'
		);
		register_setting(
			'orddd_lite_time_slot_settings',
			'orddd_lite_auto_populate_first_available_time_slot'
		);

		register_setting(
			'orddd_lite_time_slot_settings',
			'orddd_lite_time_slot_for_delivery_days'
		);

		register_setting(
			'orddd_lite_time_slot_settings',
			'orddd_lite_delivery_time_slot_log',
			array( 'Orddd_Lite_Time_Slot_Settings', 'orddd_lite_delivery_time_slot_callback' )
		);
	}

	/**
	 * Add settings field & Register settings to block time slots
	 *
	 * @since 3.11.0
	 */
	public static function orddd_lite_disable_time_slot_settings() {
		add_settings_section(
			'orddd_lite_disable_time_slot_section',
			__( 'Block a Time Slot', 'order-delivery-date' ),
			array( 'Orddd_Lite_Time_Slot_Settings', 'orddd_lite_disable_time_slot_callback' ),
			'orddd_lite_timeslot_disable_page'
		);

		add_settings_field(
			'orddd_lite_disable_time_slot_for_delivery_days',
			__( 'Block Time Slot for:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Time_Slot_Settings', 'orddd_lite_disable_time_slot_for_delivery_days_callback' ),
			'orddd_lite_timeslot_disable_page',
			'orddd_lite_disable_time_slot_section',
			array( __( 'Select "Dates" option to block time slots for individual dates. Select "Weekdays" option to block the time slots for a weekday or multiple weekdays.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_disable_time_slot_for_weekdays',
			__( 'Select Weekdays:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Time_Slot_Settings', 'orddd_lite_disable_time_slot_for_weekdays_callback' ),
			'orddd_lite_timeslot_disable_page',
			'orddd_lite_disable_time_slot_section',
			array( __( 'Select Weekdays for which you want to block the time slots.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_selected_time_slots_to_be_disabled',
			__( 'Select Time Slots to block:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Time_Slot_Settings', 'orddd_lite_selected_time_slots_to_be_disabled_callback' ),
			'orddd_lite_timeslot_disable_page',
			'orddd_lite_disable_time_slot_section',
			array( __( 'This will list all the time slots which are created in General Settings or in Custom Delivery Settings.', 'order-delivery-date' ) )
		);

		register_setting(
			'orddd_lite_disable_time_slot_settings',
			'orddd_lite_disable_time_slot_log',
			array( 'Orddd_Lite_Time_Slot_Settings', 'orddd_lite_disable_time_slots_callback' )
		);
	}

	/**
	 * Specific dates settings tab.
	 *
	 * @return void
	 */
	public static function orddd_delivery_days_settings() {

		add_settings_section(
			'orddd_delivery_days_section',
			__( 'Add Specific Delivery Dates', 'order-delivery-date' ),
			array( 'ORDDD_Lite_Delivery_Days_Settings', 'orddd_delivery_days_admin_setting_callback' ),
			'orddd_delivery_days_page'
		);

		add_settings_field(
			'orddd_enable_specific_delivery_dates',
			__( 'Enable Specific Delivery Dates:', 'order-delivery-date' ),
			array( 'ORDDD_Lite_Delivery_Days_Settings', 'orddd_delivery_days_enable_callback' ),
			'orddd_delivery_days_page',
			'orddd_delivery_days_section',
			array( __( 'Enable this option to choose specific delivery dates on the checkout page.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_delivery_date_1',
			__( 'Specific Delivery Date:', 'order-delivery-date' ),
			array( 'ORDDD_Lite_Delivery_Days_Settings', 'orddd_delivery_days_datepicker_1_callback' ),
			'orddd_delivery_days_page',
			'orddd_delivery_days_section',
			array( '' )
		);

		add_settings_field(
			'orddd_delivery_date_2',
			__( 'Specific Delivery Date:', 'order-delivery-date' ),
			array( 'ORDDD_Lite_Delivery_Days_Settings', 'orddd_delivery_days_datepicker_2_callback' ),
			'orddd_delivery_days_page',
			'orddd_delivery_days_section',
			array( '' )
		);

		add_settings_field(
			'orddd_delivery_date_3',
			__( 'Specific Delivery Date:', 'order-delivery-date' ),
			array( 'ORDDD_Lite_Delivery_Days_Settings', 'orddd_delivery_days_datepicker_3_callback' ),
			'orddd_delivery_days_page',
			'orddd_delivery_days_section',
			array( '' )
		);

		register_setting(
			'orddd_delivery_days_settings',
			'orddd_enable_specific_delivery_dates'
		);

		register_setting(
			'orddd_delivery_days_settings',
			'orddd_delivery_dates',
			array( 'orddd_delivery_days_settings', 'orddd_delivery_dates_callback' )
		);
	}

	/**
	 * Add settings fields & Register settings in Time Settings tab
	 */
	public static function orddd_time_settings() {

		add_settings_section(
			'orddd_time_settings',
			__( 'Order Delivery Time Settings', 'order-delivery-date' ),
			array( 'Orddd_Lite_Time_Settings', 'orddd_delivery_time_settings_callback' ),
			'orddd_time_settings_page'
		);

		add_settings_section(
			'orddd_time_settings_section',
			__( 'Time Settings', 'order-delivery-date' ),
			array( 'Orddd_Lite_Time_Settings', 'orddd_delivery_time_settings_callback' ),
			'orddd_time_settings_page'
		);

		add_settings_field(
			'orddd_enable_delivery_time',
			__( 'Enable delivery time capture:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Time_Settings', 'orddd_enable_delivery_time_capture_callback' ),
			'orddd_time_settings_page',
			'orddd_time_settings_section',
			array( __( 'Enable to choose the time for delivery on the checkout page.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_time_range',
			__( 'Time Range:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Time_Settings', 'orddd_time_range_callback' ),
			'orddd_time_settings_page',
			'orddd_time_settings_section',
			array( '<br>' . __( 'Select time range for the time sliders.', 'order-delivery-date' ) )
		);

		add_settings_section(
			'orddd_same_day_delivery_section',
			__( 'Same Day Delivery', 'order-delivery-date' ),
			array( 'Orddd_Lite_Time_Settings', 'orddd_same_day_delivery_callback' ),
			'orddd_time_settings_page'
		);

		add_settings_field(
			'orddd_enable_same_day_delivery',
			__( 'Enable Same day delivery:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Time_Settings', 'orddd_enable_same_day_delivery_callback' ),
			'orddd_time_settings_page',
			'orddd_same_day_delivery_section',
			array( __( 'Enable same day delivery for the orders.', 'order-delivery-date' ) . '<br><i>' . __( 'This is very useful in cases when your customers are gifting items to their loved ones, especially on birthdays, anniversaries, etc.', 'order-delivery-date' ) . '</i>' )
		);

		add_settings_field(
			'cutoff_time_for_same_day_delivery_orders',
			__( 'Cut-off time for same day delivery orders:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Time_Settings', 'orddd_cutoff_time_for_same_day_delivery_orders_callback' ),
			'orddd_time_settings_page',
			'orddd_same_day_delivery_section',
			array( '<br>' . __( 'Current day will be disabled if an order is placed after the time mentioned in this field.', 'order-delivery-date' ) . '<br><i>' . __( 'The timezone is taken from the Settings -> General -> Timezone field.', 'order-delivery-date' ) . '</i>' )
		);

		add_settings_field(
			'orddd_same_day_additional_charges',
			__( 'Additional Charges for same day delivery:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Time_Settings', 'orddd_additional_charges_for_same_day_delivery_callback' ),
			'orddd_time_settings_page',
			'orddd_same_day_delivery_section',
			array( __( 'Set additional charges for same day delivery.', 'order-delivery-date' ) )
		);

		add_settings_section(
			'orddd_next_day_delivery_section',
			__( 'Next Day Delivery', 'order-delivery-date' ),
			array( 'Orddd_Lite_Time_Settings', 'orddd_next_day_delivery_callback' ),
			'orddd_time_settings_page'
		);

		add_settings_field(
			'orddd_enable_next_day_delivery',
			__( 'Enable Next day delivery:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Time_Settings', 'orddd_enable_next_day_delivery_callback' ),
			'orddd_time_settings_page',
			'orddd_next_day_delivery_section',
			array( __( 'If you deliver on the next day, enable this option.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'cutoff_time_for_next_day_delivery_orders',
			__( 'Cut-off time for next day delivery orders:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Time_Settings', 'orddd_cutoff_time_for_next_day_delivery_orders_callback' ),
			'orddd_time_settings_page',
			'orddd_next_day_delivery_section',
			array( '<br>' . __( 'Next day will be disabled if an order is placed after the time mentioned in this field.', 'order-delivery-date' ) . '<br><i>' . __( 'The timezone is taken from the Settings -> General -> Timezone field.', 'order-delivery-date' ) . '</i>' )
		);

		add_settings_field(
			'orddd_next_day_additional_charges',
			__( 'Additional Charges for next day delivery:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Time_Settings', 'orddd_additional_charges_for_next_day_delivery_callback' ),
			'orddd_time_settings_page',
			'orddd_next_day_delivery_section',
			array( __( 'Set additional charges for next day delivery.', 'order-delivery-date' ) )
		);

		register_setting(
			'orddd_time_settings',
			'orddd_enable_delivery_time'
		);

		register_setting(
			'orddd_time_settings',
			'orddd_delivery_from_hours'
		);

		register_setting(
			'orddd_time_settings',
			'orddd_delivery_from_mins'
		);

		register_setting(
			'orddd_time_settings',
			'orddd_delivery_to_hours'
		);

		register_setting(
			'orddd_time_settings',
			'orddd_delivery_to_mins'
		);

		register_setting(
			'orddd_time_settings',
			'orddd_enable_same_day_delivery'
		);

		register_setting(
			'orddd_time_settings',
			'orddd_disable_same_day_delivery_after_hours'
		);

		register_setting(
			'orddd_time_settings',
			'orddd_disable_same_day_delivery_after_minutes'
		);

		register_setting(
			'orddd_time_settings',
			'orddd_same_day_additional_charges'
		);

		register_setting(
			'orddd_time_settings',
			'orddd_enable_next_day_delivery'
		);

		register_setting(
			'orddd_time_settings',
			'orddd_disable_next_day_delivery_after_hours'
		);

		register_setting(
			'orddd_time_settings',
			'orddd_disable_next_day_delivery_after_minutes'
		);

		register_setting(
			'orddd_time_settings',
			'orddd_next_day_additional_charges'
		);
	}

	/**
	 * Add settings fields & Register settings in Date Settings tab for Integration with our plugins
	 */
	public static function orddd_integration_of_plugins() {

		add_settings_section(
			'orddd_lite_additional_settings_section',
			__( 'Additional Settings', 'order-delivery-date' ),
			array( 'Orddd_Lite_Additional_Settings', 'orddd_lite_additional_settings_section_callback' ),
			'orddd_lite_additional_settings_page'
		);

		add_settings_field(
			'orddd_lite_show_column_on_orders_page_check',
			__( 'Show on Orders Listing Page:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Additional_Settings', 'orddd_lite_show_column_on_orders_page_check_callback' ),
			'orddd_lite_additional_settings_page',
			'orddd_lite_additional_settings_section',
			array( __( 'Displays the Delivery Date on the WooCommerce->Orders page.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_show_filter_on_orders_page_check',
			__( 'Show Filter on Orders Listing Page:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Additional_Settings', 'orddd_lite_show_filter_on_orders_page_check_callback' ),
			'orddd_lite_additional_settings_page',
			'orddd_lite_additional_settings_section',
			array( __( 'Displays the Filter on the WooCommerce->Orders page that allows you to view orders to be delivered today, tomorrow or in any month.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_auto_populate_first_available_date',
			__( 'Auto-populate first available Delivery date:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Additional_Settings', 'orddd_lite_enable_autofill_of_delivery_date_callback' ),
			'orddd_lite_additional_settings_page',
			'orddd_lite_additional_settings_section',
			array( __( 'Auto-populate first available Delivery date when the checkout page loads.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_enable_tax_calculation_for_delivery_charges',
			__( 'Enable Tax calculation for Delivery charges', 'order-delivery-date' ),
			array( 'Orddd_Lite_Additional_Settings', 'orddd_lite_enable_tax_calculation_for_delivery_charges_callback' ),
			'orddd_lite_additional_settings_page',
			'orddd_lite_additional_settings_section',
			array( __( 'Enable Tax calculation for Delivery charges on the checkout page.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite__no_fields_for_product_type',
			__( 'Disable the Delivery Date and Time Slot Fields for:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Additional_Settings', 'orddd_lite_appearance_virtual_product_callback' ),
			'orddd_lite_additional_settings_page',
			'orddd_lite_additional_settings_section',
			array( __( '<br>Disable the Delivery Date and Time Slot on the Checkout page for Virtual products and Featured products.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_allow_customers_to_edit_date',
			__( 'Allow Customers to edit Delivery Date & Time:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Additional_Settings', 'orddd_lite_allow_customers_to_edit_date_callback' ),
			'orddd_lite_additional_settings_page',
			'orddd_lite_additional_settings_section',
			array( __( 'When enabled, it will add Delivery Date & Time field on the My Account -> Orders -> View page. So customers will be able to edit the date and time once the order is placed.<br>', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_enable_availability_display',
			__( 'Display availability on date', 'order-delivery-date' ),
			array( 'Orddd_Lite_Additional_Settings', 'orddd_lite_enable_availability_display_callback' ),
			'orddd_lite_additional_settings_page',
			'orddd_lite_additional_settings_section',
			array( __( 'When enabled, it will display the availability on hover of the dates in the delivery calendar on checkout page.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_show_partially_booked_dates',
			__( 'Show Partially Booked Dates on the Delivery Calendar', 'order-delivery-date' ),
			array( 'Orddd_Lite_Additional_Settings', 'orddd_lite_show_partially_booked_dates_callback' ),
			'orddd_lite_additional_settings_page',
			'orddd_lite_additional_settings_section',
			array( __( 'When enabled, it will show the dates with diagonally separated colors of Booked dates and Available Dates if 1 or more orders are placed for that date. <div class="orddd-tooltip">', 'order-delivery-date' ) )
		);

		add_settings_section(
			'orddd_lite_integration_with_other_plugins',
			__( 'Integration with Other Plugins:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Additional_Settings', 'orddd_lite_integration_with_other_plugins_callback' ),
			'orddd_lite_additional_settings_page'
		);

		add_settings_field(
			'orddd_lite_show_fields_in_csv_export_check',
			__( 'WooCommerce Customer/ Order CSV Export plugin', 'order-delivery-date' ),
			array( 'Orddd_Lite_Additional_Settings', 'orddd_lite_show_fields_in_csv_export_check_callback' ),
			'orddd_lite_additional_settings_page',
			'orddd_lite_integration_with_other_plugins',
			array( __( 'Displays the Delivery details in the CSV Export File.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_show_fields_in_pdf_invoice_and_packing_slips',
			__( 'WooCommerce PDF Invoices & Packing Slips plugin', 'order-delivery-date' ),
			array( 'Orddd_Lite_Additional_Settings', 'orddd_lite_show_fields_in_pdf_invoice_and_packing_slips_callback' ),
			'orddd_lite_additional_settings_page',
			'orddd_lite_integration_with_other_plugins',
			array( __( 'Displays the Delivery details in the PDF Invoice and Packing Slips.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_show_fields_in_invoice_and_delivery_note',
			__( 'WooCommerce Print Invoice & Delivery Note plugin', 'order-delivery-date' ),
			array( 'Orddd_Lite_Additional_Settings', 'orddd_lite_show_fields_in_invoice_and_delivery_note_callback' ),
			'orddd_lite_additional_settings_page',
			'orddd_lite_integration_with_other_plugins',
			array( __( 'Displays the Delivery details in the Invoice and Delivery Note.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_show_fields_in_cloud_print_orders',
			__( 'WooCommerce Print orders plugin', 'order-delivery-date' ),
			array( 'Orddd_Lite_Additional_Settings', 'orddd_lite_show_fields_in_cloud_print_orders_callback' ),
			'orddd_lite_additional_settings_page',
			'orddd_lite_integration_with_other_plugins',
			array( __( 'Displays the Delivery details in the print copy of the order.', 'order-delivery-date' ) )
		);

		add_settings_section(
			'orddd_lite_compatibility_with_other_plugins',
			__( 'Compatibility with Other Plugins:', 'order-delivery-date' ),
			array( 'Orddd_Lite_Additional_Settings', 'orddd_lite_compatibility_with_other_plugins_callback' ),
			'orddd_lite_additional_settings_page'
		);

		add_settings_field(
			'orddd_lite_shipping_multiple_address_compatibility',
			__( 'WooCommerce Shipping Multiple addresses', 'order-delivery-date' ),
			array( 'Orddd_Lite_Additional_Settings', 'orddd_lite_shipping_multiple_address_compatibility_callback' ),
			'orddd_lite_additional_settings_page',
			'orddd_lite_compatibility_with_other_plugins',
			array( __( 'When enabled, it will allow to choose a Delivery Date & Time (if enabled) for each shipping address chosen on checkout page with the WooCommerce Shipping Multiple addresses plugin.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lite_amazon_payments_advanced_gateway_compatibility',
			__( 'WooCommerce Amazon Payments Advanced Gateway', 'order-delivery-date' ),
			array( 'Orddd_Lite_Additional_Settings', 'orddd_lite_amazon_payments_advanced_gateway_compatibility_callback' ),
			'orddd_lite_additional_settings_page',
			'orddd_lite_compatibility_with_other_plugins',
			array( __( 'If enabled, it will add the Delivery Date and Time fields when the customer clicks on "Pay with Amazon" button.', 'order-delivery-date' ) )
		);

		register_setting(
			'orddd_lite_additional_settings',
			'orddd_lite_amazon_payments_advanced_gateway_compatibility'
		);

		register_setting(
			'orddd_lite_additional_settings',
			'orddd_lite_show_fields_in_csv_export_check'
		);

		register_setting(
			'orddd_lite_additional_settings',
			'orddd_lite_show_fields_in_pdf_invoice_and_packing_slips'
		);

		register_setting(
			'orddd_lite_additional_settings',
			'orddd_lite_show_fields_in_invoice_and_delivery_note'
		);

		register_setting(
			'orddd_lite_additional_settings',
			'orddd_lite_show_fields_in_cloud_print_orders'
		);

		register_setting(
			'orddd_lite_additional_settings',
			'orddd_lite_show_column_on_orders_page_check'
		);

		register_setting(
			'orddd_lite_additional_settings',
			'orddd_lite_enable_default_sorting_of_column'
		);

		register_setting(
			'orddd_lite_additional_settings',
			'orddd_lite_show_filter_on_orders_page_check'
		);

		register_setting(
			'orddd_lite_additional_settings',
			'orddd_lite_auto_populate_first_available_date'
		);
		register_setting(
			'orddd_lite_additional_settings',
			'orddd_lite_enable_tax_calculation_for_delivery_charges'
		);

		register_setting(
			'orddd_lite_additional_settings',
			'orddd_lite_no_fields_for_virtual_product'
		);

		register_setting(
			'orddd_lite_additional_settings',
			'orddd_lite_no_fields_for_featured_product'
		);

		register_setting(
			'orddd_lite_additional_settings',
			'orddd_lite_allow_customers_to_edit_date'
		);

		register_setting(
			'orddd_lite_additional_settings',
			'orddd_lite_enable_availability_display'
		);

		register_setting(
			'orddd_lite_additional_settings',
			'orddd_lite_show_partially_booked_dates'
		);

		register_setting(
			'orddd_lite_additional_settings',
			'orddd_lite_send_email_to_admin_when_date_updated'
		);

		register_setting(
			'orddd_lite_additional_settings',
			'orddd_lite_disable_edit_after_cutoff'
		);

		register_setting(
			'orddd_lite_additional_settings',
			'orddd_lite_shipping_multiple_address_compatibility'
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
		$action                  = '';
		$active_date_settings    = '';
		$active_appearance       = '';
		$active_holidays         = '';
		$active_shipping_based   = '';
		$calendar_sync_settings  = '';
		$active_general_settings = '';

		// phpcs:ignore WordPress.Security.NonceVerification
		if ( isset( $_GET['action'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification
			$action = sanitize_key( wp_unslash( $_GET['action'] ) );
		} else {
			$action = 'general_settings';
		}

		if ( 'general_settings' === $action || '' === $action ) {
			$active_general_settings = 'nav-tab-active';
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
		<div class="wrap woocommerce">
			<nav class="nav-tab-wrapper woo-nav-tab-wrapper" id="orddd_settings_tabs">
				<a href="admin.php?page=order_delivery_date_lite&action=general_settings" class="nav-tab <?php echo esc_attr( $active_general_settings ); ?>"><?php esc_attr_e( 'General Settings', 'order-delivery-date' ); ?> </a>
				<a href="admin.php?page=order_delivery_date_lite&action=shipping_based" class="nav-tab <?php echo esc_attr( $active_shipping_based ); ?>"> <?php esc_attr_e( 'Custom Delivery Settings', 'order-delivery-date' ); ?> </a>
				<a href="admin.php?page=order_delivery_date_lite&action=calendar_sync_settings" class="nav-tab <?php echo esc_attr( $calendar_sync_settings ); ?>"> <?php esc_attr_e( 'Google Calendar Sync', 'order-delivery-date' ); ?> 
				</a>
				<?php
					do_action( 'orddd_lite_add_settings_tab' );
				?>
			</nav>
		</div>

		<?php
		do_action( 'orddd_lite_add_tab_content' );
		if ( 'general_settings' === $action || '' === $action ) {
			$date_settings_class       = '';
			$shipping_days_class       = '';
			$delivery_date_class       = '';
			$time_settings_class       = '';
			$holidays_class            = '';
			$appearance_class          = '';
			$time_slot_class           = '';
			$additional_settings_class = '';
			$section                   = '';
			if ( isset( $_GET['section'] ) ) { //phpcs:ignore
				$section = sanitize_text_field( $_GET['section'] );//phpcs:ignore
			} else {
				$section = '';
			}

			if ( 'date_settings' === $section || '' === $section ) {
				$date_settings_class = 'current';
			}

			if ( 'delivery_dates' === $section ) {
				$delivery_date_class = 'current';
			}

			if ( 'time_settings' === $section ) {
				$time_settings_class = 'current';
			}

			if ( 'holidays' === $section ) {
				$holidays_class = 'nav-tab-active';
			}

			if ( 'appearance' === $section ) {
				$appearance_class = 'current';
			}

			if ( 'time_slot' === $section ) {
				$time_slot_class = 'current';
			}

			if ( 'additional_settings' === $section ) {
				$additional_settings_class = 'current';
			}

			?>
			<ul class="subsubsub" id="orddd_general_settings_list">
				<li>
					<a href="admin.php?page=order_delivery_date_lite&action=general_settings&section=date_settings" class="<?php echo esc_attr( $date_settings_class ); ?>"><?php esc_attr_e( 'Date Settings', 'order-delivery-date' ); ?> </a> |
				</li>
				<li>
					<a href="admin.php?page=order_delivery_date_lite&action=general_settings&section=delivery_dates" class="<?php echo esc_attr( $delivery_date_class ); ?>"><?php esc_attr_e( 'Specific Delivery Dates', 'order-delivery-date' ); ?> </a> | 
				</li>
				<li>
					<a href="admin.php?page=order_delivery_date_lite&action=general_settings&section=time_settings" class="<?php echo esc_attr( $time_settings_class ); ?>"><?php esc_attr_e( 'Time Settings', 'order-delivery-date' ); ?> </a> | 
				</li>
				<li>
					<a href="admin.php?page=order_delivery_date_lite&action=general_settings&section=holidays" class="<?php echo esc_attr( $holidays_class ); ?>"><?php esc_attr_e( 'Holidays', 'order-delivery-date' ); ?> </a> |
				</li>
				<li>
					<a href="admin.php?page=order_delivery_date_lite&action=general_settings&section=appearance" class="<?php echo esc_attr( $appearance_class ); ?>"><?php esc_attr_e( 'Appearance', 'order-delivery-date' ); ?> </a> |
				</li>
				<li>
					<a href="admin.php?page=order_delivery_date_lite&action=general_settings&section=time_slot" class="<?php echo esc_attr( $time_slot_class ); ?>"><?php esc_attr_e( 'Time Slot', 'order-delivery-date' ); ?> </a> |
				</li>
				<li>
					<a href="admin.php?page=order_delivery_date_lite&action=general_settings&section=additional_settings" class="<?php echo esc_attr( $additional_settings_class ); ?>"><?php esc_attr_e( 'Additional Settings', 'order-delivery-date' ); ?> </a>
				</li>
				<?php do_action( 'orddd_general_settings_links', $section ); ?>
			</ul>
			<br class="clear">

			<?php

			switch ( $section ) {
				case 'date_settings':
					print( '<div id="content">
						<form method="post" action="options.php">' );
							settings_fields( 'orddd_lite_date_settings' );
							do_settings_sections( 'orddd_lite_date_settings_page' );
							submit_button( __( 'Save Settings', 'order-delivery-date' ), 'primary', 'save_orddd_lite', true );
						print( '</form>
					</div>' );
					break;
				case 'holidays':
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
							<input type="hidden" name="tab" value="general_settings" />
							<input type="hidden" name="section" value="holidays" />
							<?php $orddd_table->display(); ?>
						</form>
					</div>
					<?php
					break;

				case 'delivery_dates':
					print( '<div id="content">
						<div class="orddd-col-left" >
							<form method="post" action="options.php">' );
								settings_fields( 'orddd_delivery_days_settings' );
								do_settings_sections( 'orddd_delivery_days_page' );
								submit_button( __( 'Save Settings', 'order-delivery-date' ), 'primary', 'save', true );
							print( '</form>
						</div>
					</div>' );
					echo "<div class='orddd-col-right'><h3 id='delivery_date_table_head'>" . esc_attr_e( 'Specific Delivery Dates', 'order-delivery-date' ) . '</h3>';
					include_once 'class-orddd-lite-view-specific-table.php';
					$orddd_table = new ORDDD_Lite_View_Specific_Table();
					$orddd_table->orddd_prepare_items();
					?>
						<div id = "orddd_delivery_dates_list">
							<form id="delivery-dates" method="get" >
								<input type="hidden" name="page" value="order_delivery_date_lite" />
								<input type="hidden" name="tab" value="general_settings" />
								<input type="hidden" name="section" value="delivery_dates" />
								<?php $orddd_table->display(); ?>
							</form>
						</div>
					</div>
					<?php
					break;

				case 'time_settings':
					print( '<div id="content">
						<form method="post" action="options.php">' );
							settings_fields( 'orddd_time_settings' );
							do_settings_sections( 'orddd_time_settings_page' );
							submit_button( __( 'Save Settings', 'order-delivery-date' ), 'primary', 'save', true );
						print( '</form>
					</div>' );
					break;

				case 'time_slot':
					print( '<div id="content">
						<form method="post" action="options.php">' );
							settings_fields( 'orddd_lite_time_slot_settings' );
							do_settings_sections( 'orddd_lite_time_slot_page' );
							submit_button( __( 'Save Settings', 'order-delivery-date' ), 'primary', 'save', true );
						print( '</form>
					</div>' );

					$existing_timeslots_str = get_option( 'orddd_lite_disable_time_slot_log' );
					$existing_timeslots_arr = array();
					if ( 'null' == $existing_timeslots_str || '' == $existing_timeslots_str || '{}' == $existing_timeslots_str || '[]' == $existing_timeslots_str ) { // phpcs:ignore
						$existing_timeslots_arr = array();
					} else {
						$existing_timeslots_arr = json_decode( $existing_timeslots_str );
					}
					?>
					<a href="admin.php?page=order_delivery_date_lite&action=general_settings&section=block_time_slot_settings" class="block_time_slot">
					<?php
					esc_attr_e( 'Block Time Slots', 'order-delivery-date' );
						echo ' (' . count( $existing_timeslots_arr ) . ')';
					?>

					</a>
					<h3 id='timeslots_table_head'>
					<?php
						echo esc_attr_e( 'Time Slots', 'order-delivery-date' );
					?>
					</h3>
					<?php
					include_once 'class-orddd-lite-view-time-slots.php';
					$orddd_table = new ORDDD_Lite_View_Time_Slots();
					$orddd_table->orddd_lite_prepare_items();
					?>
					<div id = "orddd_time_slot_list">
						<form id="time-slot" method="get" >
							<input type="hidden" name="page" value="order_delivery_date_lite" />
							<input type="hidden" name="tab" value="general_settings" />
							<input type="hidden" name="section" value="time_slot" />
							<?php $orddd_table->display(); ?>
						</form>
					</div>
					<?php
					break;
				case 'block_time_slot_settings':
					?>
						<a href="admin.php?page=order_delivery_date_lite&action=general_settings&section=time_slot" class="back_to_time_slot"><?php esc_attr_e( 'Back to Time Slots', 'order-delivery-date' ); ?> </a>
						<?php
						print( '<div id="content">
							<form method="post" action="options.php">' );
								settings_fields( 'orddd_lite_disable_time_slot_settings' );
								do_settings_sections( 'orddd_lite_timeslot_disable_page' );
								submit_button( __( 'Save Settings', 'order-delivery-date' ), 'primary', 'save', true );
							print( '</form>
						</div>' );
						echo "<h3 id='block_timeslot_table_head'>" . esc_attr_e( 'Blocked Time Slots', 'order-delivery-date' ) . '</h3>';
						include_once 'class-orddd-lite-view-disable-time-slots.php';
						$orddd_table_test = new ORDDD_Lite_View_Disable_Time_Slots();
						$orddd_table_test->orddd_prepare_items();
						?>
						<div id = "orddd_disable_time_slot_list">
							<form id="time-slot" method="get" >
								<input type="hidden" name="page" value="order_delivery_date_lite" />
								<input type="hidden" name="tab" value="general_settings" />
								<input type="hidden" name="section" value="block_time_slot_settings" />
								<?php $orddd_table_test->display(); ?>
							</form>
						</div>
					<?php
					break;
				case 'appearance':
					print( '<div id="content">
						<form method="post" action="options.php">' );
						settings_fields( 'orddd_lite_appearance_settings' );
						do_settings_sections( 'orddd_lite_appearance_page' );
						submit_button( __( 'Save Settings', 'order-delivery-date' ), 'primary', 'save', true );
						print( '</form>
					</div>' );
					break;

				case 'additional_settings':
					print( '<div id="content">
						<form method="post" action="options.php">' );
							settings_fields( 'orddd_lite_additional_settings' );
							do_settings_sections( 'orddd_lite_additional_settings_page' );
							submit_button( __( 'Save Settings', 'order-delivery-date' ), 'primary', 'save', true );
						print( '</form>
					</div>' );
					break;

				default:
					print( '<div id="content">
						<form method="post" action="options.php">' );
							settings_fields( 'orddd_lite_date_settings' );
							do_settings_sections( 'orddd_lite_date_settings_page' );
							submit_button( __( 'Save Settings', 'order-delivery-date' ), 'primary', 'save_orddd_lite', true );
						print( '</form>
					</div>' );
					break;
			}
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
		if ( ( isset( $_GET['page'] ) && 'order_delivery_date_lite' === $_GET['page'] ) && ( isset( $_GET['tab'] ) && 'general_settings' === $_GET['tab'] && ( isset( $_GET['section'] ) && sanitize_text_field( $_GET['section'] ) == 'holidays' ) ) && ( ( isset( $_GET['action'] ) && 'orddd_lite_delete' === $_GET['action'] ) || ( isset( $_GET['action2'] ) && 'orddd_lite_delete' === $_GET['action2'] ) ) ) { //phpcs:ignore

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
			wp_safe_redirect( admin_url( '/admin.php?page=order_delivery_date_lite&action=general_settings&section=holidays' ) );
		}

		if ( ( isset( $_GET['page'] ) && sanitize_text_field( $_GET['page'] ) == 'order_delivery_date_lite' ) && ( isset( $_GET['tab'] ) && sanitize_text_field( $_GET['tab'] ) == 'general_settings' ) && ( isset( $_GET['section'] ) && sanitize_text_field( $_GET['section'] ) == 'time_slot' ) ) { //phpcs:ignore

			if ( ( isset( $_GET['action'] ) && sanitize_text_field( $_GET['action'] ) == 'orddd_delete' ) || ( isset( $_GET['action2'] ) && sanitize_text_field( $_GET['action2'] ) == 'orddd_delete' ) ) { //phpcs:ignore

				$time_slot_to_delete = array();
				if ( isset( $_GET['time_slot'] ) ) { //phpcs:ignore
					$time_slot_to_delete = $_GET['time_slot']; //phpcs:ignore
				}
				foreach ( $time_slot_to_delete as $t_key => $t_value ) {
					$time_values   = explode( ',', $t_value );
					$date_to_check = '';
					$fh            = '';
					$fm            = '';
					$th            = '';
					$tm            = '';
					$tv            = '';
					if ( isset( $time_values[0] ) ) {
						$date_to_check = $time_values[0];
					}
					if ( isset( $time_values[1] ) ) {
						$fh = $time_values[1];
					}

					if ( isset( $time_values[2] ) ) {
						$fm = $time_values[2];
					}

					if ( isset( $time_values[3] ) ) {
						$th = $time_values[3];
					}

					if ( isset( $time_values[4] ) ) {
						$tm = $time_values[4];
					}

					if ( isset( $time_values[5] ) ) {
						$tv = $time_values[5];
					}

					$time_slot_str    = get_option( 'orddd_lite_delivery_time_slot_log' );
					$time_slots       = json_decode( $time_slot_str );
					$timeslot_new_arr = array();
					if ( 'null' == $time_slots || '' == $time_slots || '{}' == $time_slots || '[]' == $time_slots ) { // phpcs:ignore
						$time_slots = array();
					}

					foreach ( $time_slots as $key => $v ) {
						if ( 'array' === gettype( json_decode( $v->dd ) ) && count( json_decode( $v->dd ) ) > 0 ) {
							$dd         = json_decode( $v->dd );
							$new_dd_str = '[';
							$count_dd   = 0;
							if ( is_array( $dd ) ) {
								$count_dd = count( $dd );
							}
							for ( $i = 0; $i < $count_dd; $i++ ) {
								if ( ! ( $fh == $v->fh && $fm == $v->fm && $th == $v->th && $tm == $v->tm && $date_to_check == $dd[ $i ] && $tv == $v->tv ) ) { //phpcs:ignore
									$new_dd_str .= '"' . $dd[ $i ] . '",';
								}
							}
							$new_dd_str = substr( $new_dd_str, 0, strlen( $new_dd_str ) - 1 );
							if ( trim( $new_dd_str ) !== '' ) {
								$new_dd_str        .= ']';
								$timeslot_new_arr[] = array(
									'tv'                 => $v->tv,
									'dd'                 => $new_dd_str,
									'lockout'            => $v->lockout,
									'additional_charges' => $v->additional_charges,
									'additional_charges_label' => $v->additional_charges_label,
									'fh'                 => $v->fh,
									'fm'                 => $v->fm,
									'th'                 => $v->th,
									'tm'                 => $v->tm,
								);
							}
						} else {
							if ( $fh == $v->fh && $fm == $v->fm && $th == $v->th && $tm == $v->tm && $date_to_check == $v->dd && $tv == $v->tv ) { //phpcs:ignore
								unset( $v );
							} else {
								$timeslot_new_arr[] = array(
									'tv'                 => $v->tv,
									'dd'                 => $v->dd,
									'lockout'            => $v->lockout,
									'additional_charges' => $v->additional_charges,
									'additional_charges_label' => $v->additional_charges_label,
									'fh'                 => $v->fh,
									'fm'                 => $v->fm,
									'th'                 => $v->th,
									'tm'                 => $v->tm,
								);
							}
						}
					}
					$timeslot_jarr = wp_json_encode( $timeslot_new_arr );
					update_option( 'orddd_lite_delivery_time_slot_log', $timeslot_jarr );
				}
			}
			wp_safe_redirect( admin_url( '/admin.php?page=order_delivery_date_lite&action=general_settings&section=time_slot' ) );
		}

		if ( ( isset( $_GET['page'] ) && sanitize_text_field( $_GET['page'] ) == 'order_delivery_date_lite' ) && ( isset( $_GET['tab'] ) && sanitize_text_field( $_GET['tab'] ) == 'general_settings' ) && ( isset( $_GET['section'] ) && sanitize_text_field( $_GET['section'] ) == 'block_time_slot_settings' ) ) { //phpcs:ignore

			if ( ( isset( $_GET['action'] ) && sanitize_text_field( $_GET['action'] ) == 'orddd_delete' ) || ( isset( $_GET['action2'] ) && sanitize_text_field( $_GET['action2'] ) == 'orddd_delete' ) ) { //phpcs:ignore
				$block_time_slot_to_delete = array();
				if ( isset( $_GET['block_time_slot'] ) ) { //phpcs:ignore
					$block_time_slot_to_delete = $_GET['block_time_slot']; //phpcs:ignore
				}

				foreach ( $block_time_slot_to_delete as $t_key => $t_value ) {
					$time_values   = explode( ',', $t_value );
					$date_to_check = '';
					$timeslot      = '';
					if ( isset( $time_values[0] ) ) {
						$date_to_check = $time_values[0];
					}
					if ( isset( $time_values[1] ) ) {
						$timeslot = $time_values[1];
					}

					$disable_time_slot_str    = get_option( 'orddd_lite_disable_time_slot_log' );
					$disable_time_slots       = json_decode( $disable_time_slot_str );
					$disable_timeslot_new_arr = array();
					if ( 'null' == $disable_time_slots || '' == $disable_time_slots || '{}' == $disable_time_slots || '[]' == $disable_time_slots ) { //phpcs:ignore
						$disable_time_slots = array();
					}

					$timeslot_disable_new_arr = array();
					foreach ( $disable_time_slots as $disable_key => $disable_v ) {
						$time_slots = json_decode( $disable_v->ts );
						if ( ( isset( $timeslot ) && in_array( $timeslot, $time_slots, true ) ) && ( isset( $date_to_check ) && $date_to_check == $disable_v->dd ) ) { //phpcs:ignore
							// do nothing as this time slot needs to be deleted.
							$key = array_search( $timeslot, $time_slots ); //phpcs:ignore
							unset( $time_slots[ $key ] );

							if ( is_array( $time_slots ) && count( $time_slots ) === 0 ) {
								unset( $disable_time_slots[ $disable_key ] );
							}

							$new_ts_str = '[';
							foreach ( $time_slots as $time_slot_key => $time_slot_value ) {
								$new_ts_str .= '"' . $time_slot_value . '",';
							}
							$new_ts_str = substr( $new_ts_str, 0, strlen( $new_ts_str ) - 1 );

							if ( trim( $new_ts_str ) !== '' ) {
								$new_ts_str                .= ']';
								$timeslot_disable_new_arr[] = array(
									'dtv' => $disable_v->dtv,
									'dd'  => $disable_v->dd,
									'ts'  => $new_ts_str,
								);
							}
						} else {
							$timeslot_disable_new_arr[] = array(
								'dtv' => $disable_v->dtv,
								'dd'  => $disable_v->dd,
								'ts'  => $disable_v->ts,
							);
						}
					}
					$disable_timeslot_jarr = wp_json_encode( $timeslot_disable_new_arr );
					update_option( 'orddd_lite_disable_time_slot_log', $disable_timeslot_jarr );
				}
			}

			wp_safe_redirect( admin_url( 'admin.php?page=order_delivery_date_lite&action=general_settings&section=block_time_slot_settings' ) );
		}
	}

	/**
	 * Callback for showing the notice for helping small businesses during Covid 19 crisis
	 *
	 * @since 3.10.2
	 */
	public function orddd_lite_info_notice() {

		_e( '<div class="notice notice-info my-dismiss-notice is-dismissible"><p style="font-size:17px;">Across the world, businesses are going through a tough time with COVID-19. In such times, we want to do our bit to support small businesses. Since shoppers are increasingly relying on delivery right now, we are giving <strong>50% off on the Order Delivery Date Pro plugin until April 14 2020</strong>. If you don\'t love it, get a full refund in 30 days, no questions asked!<br><br>Please use the coupon code STAYSAFE to avail the discount. <strong><a target="_blank" href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=customerstore&utm_medium=link&utm_campaign=OrderDeliveryDateLiteCovidNotice"><u>BUY NOW FOR $49.50 (<span style="text-decoration:line-through;">$99.00</span>)</u></a><strong>.</p></div>', 'order-delivery-date' ); //phpcs:ignore
	}
}

$orddd_lite_settings = new orddd_lite_settings();

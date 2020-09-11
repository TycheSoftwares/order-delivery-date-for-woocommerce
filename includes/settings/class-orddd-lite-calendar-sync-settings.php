<?php
/**
 * Order Calendar Sync Settings
 *
 * @author Tyche Softwares
 * @package Order-Delivery-Date-Lite-for-WooCommerce/Admin/Settings/Google-Calendar-Sync
 * @since 3.9
 * @category Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Orddd_Lite_Calendar_Sync_Settings Class
 *
 * @class Orddd_Lite_Calendar_Sync_Settings
 */
class Orddd_Lite_Calendar_Sync_Settings {
	/**
	 * Callback for adding Date Settings tab settings
	 */
	public static function orddd_lite_calendar_sync_general_settings_callback() {}

	/**
	 * Callback for adding the Event Location field in the Google sync settings
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 3.9
	 */
	public static function orddd_lite_calendar_event_location_callback( $args ) {
		?>
			<input type="text" name="orddd_lite_calendar_event_location" id="orddd_lite_calendar_event_location" disabled readonly />
			<label for="orddd_lite_calendar_event_location"> <?php echo wp_kses_post( $args[0] ); ?> </label>
		<?php
	}

	/**
	 * Callback for adding the Event Summary name field in the Google sync settings
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 3.9
	 */
	public static function orddd_lite_calendar_event_summary_callback( $args ) {
		?>
			<input id="orddd_lite_calendar_event_summary" name="orddd_lite_calendar_event_summary" size="90" type="text" disabled readonly/>';
		<?php
	}

	/**
	 * Callback for adding the Event description field in the Google sync settings
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 3.9
	 */
	public static function orddd_lite_calendar_event_description_callback( $args ) {
		?>
			<textarea id="orddd_lite_calendar_event_description" name="orddd_lite_calendar_event_description" cols="90" rows="4" disabled readonly></textarea>
			<label for="orddd_lite_calendar_event_description"> <?php echo wp_kses_post( $args[0] ); ?> </label>
		<?php
	}

	/**
	 * Callback for adding the Add to Calendar button on for Customer Settings section
	 *
	 * @since 3.9
	 */
	public static function orddd_lite_calendar_sync_customer_settings_callback() { }


	/**
	 * Callback for adding the Add to Calendar button on Order Received Page
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 3.9
	 */
	public static function orddd_lite_add_to_calendar_order_received_page_callback( $args ) {
		?>
			<input type="checkbox" name="orddd_lite_add_to_calendar_order_received_page" id="orddd_lite_add_to_calendar_order_received_page" class="day-checkbox" disabled readonly />
			<label for="orddd_lite_add_to_calendar_order_received_page"> <?php echo wp_kses_post( $args[0] ); ?> </label>
		<?php
	}


	/**
	 * Callback for adding the Add to Calendar button in the customer notification email
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 3.9
	 */
	public static function orddd_lite_add_to_calendar_customer_email_callback( $args ) {
		?>
			<input type="checkbox" name="orddd_lite_add_to_calendar_customer_email" id="orddd_lite_add_to_calendar_customer_email" class="day-checkbox" disabled readonly/>
			<label for="orddd_lite_add_to_calendar_customer_email"> <?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding the Add to Calendar button on My Account page
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 3.9
	 */
	public static function orddd_lite_add_to_calendar_my_account_page_callback( $args ) {
		?>
			<input type="checkbox" name="orddd_lite_add_to_calendar_my_account_page" id="orddd_lite_add_to_calendar_my_account_page" class="day-checkbox" disabled readonly/>
			<label for="orddd_lite_add_to_calendar_my_account_page"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback to open the calendar in the same window and tab
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 3.9
	 */
	public static function orddd_lite_calendar_in_same_window_callback( $args ) {
		?>
			<input type="checkbox" name="orddd_lite_calendar_in_same_window" id="orddd_lite_calendar_in_same_window" class="day-checkbox" disabled readonly/>
			<label for="orddd_lite_calendar_in_same_window"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding the Add to Calendar button for admin settings section
	 *
	 * @since 3.9
	 */
	public static function orddd_lite_calendar_sync_admin_settings_section_callback() { }

	/**
	 * Callback to select the type of Calendar sync integration - automatically, manually or disabled
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 3.9
	 */
	public static function orddd_lite_calendar_sync_integration_mode_callback( $args ) {
		?>
			<input type="radio" name="orddd_lite_calendar_sync_integration_mode" id="orddd_lite_calendar_sync_integration_mode" disabled readonly/><?php esc_attr_e( 'Sync Automatically', 'order-delivery-date' ); ?> &nbsp;&nbsp;
			<input type="radio" name="orddd_lite_calendar_sync_integration_mode" id="orddd_lite_calendar_sync_integration_mode" disabled readonly/><?php esc_attr_e( 'Sync Manually', 'order-delivery-date' ); ?> &nbsp;&nbsp;
			<input type="radio" name="orddd_lite_calendar_sync_integration_mode" id="orddd_lite_calendar_sync_integration_mode" disabled readonly/><?php esc_attr_e( 'Disabled', 'order-delivery-date' ); ?>
			<label for="orddd_lite_calendar_sync_integration_mode"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Display the stepd for syncing the Google Calendar on clicking 'Show me how'
	 *
	 * @since 3.9
	 */
	public static function orddd_lite_sync_calendar_instructions_callback() {
		esc_attr_e( 'To set up Google Calendar API, please click on "Show me how" link and carefully follow these steps:', '' ) . '
            <span class="description" ><a href="#orddd-instructions" id="show_instructions" data-target="api-instructions" class="orddd-info_trigger" title="' . __( 'Click to toggle instructions', 'order-delivery-date' ) . '" disabled >' . __( 'Show me how', 'order-delivery-date' ) . '</a></span>';
	}

	/**
	 * Callback for adding Key File name field to enter the file name without extension
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 3.9
	 */
	public static function orddd_lite_calendar_key_file_name_callback( $args ) {
		?>
			<input id="orddd_lite_calendar_details_1[orddd_lite_calendar_key_file_name]" name= "orddd_lite_calendar_details_1[orddd_lite_calendar_key_file_name]" size="90" type="text" disabled readonly/>
			<label for="orddd_lite_calendar_key_file_name"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding the 'Serveice Account Email Address' field in the settings
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 3.9
	 */
	public static function orddd_lite_calendar_service_acc_email_address_callback( $args ) {
		?>
			<input id="ordddlite_calendar_details_1[orddd_lite_calendar_service_acc_email_address]" name="orddd_lite_calendar_details_1[orddd_lite_calendar_service_acc_email_address]" size="90" type="text" disabled readonly/>
			<label for="orddd_lite_calendar_service_acc_email_address"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding the 'Calendar to be used' field in the settings to enter the Calendar ID
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 3.9
	 */
	public static function orddd_lite_calendar_id_callback( $args ) {
		?>
			<input id="orddd_lite_calendar_details_1[orddd_lite_calendar_id]" name="orddd_lite_calendar_details_1[orddd_clite_alendar_id]" size="90" type="text" disabled readonly/>
			<label for="orddd_lite_calendar_id"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding the 'Add to Calendar' button in the New Order email notification
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 3.9
	 */
	public static function orddd_lite_admin_add_to_calendar_email_notification_callback( $args ) {
		?>
			<input type="checkbox" name="orddd_lite_admin_add_to_calendar_email_notification" id="orddd_lite_admin_add_to_calendar_email_notification" disabled readonly/>
			<label for="orddd_lite_admin_add_to_calendar_email_notification"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding the 'Add to Calendar' button in the admin Delivery Calendar page
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 3.9
	 */
	public static function orddd_lite_admin_add_to_calendar_delivery_calendar_callback( $args ) {
		?>
			<input type="checkbox" name="orddd_lite_admin_add_to_calendar_delivery_calendar" id="orddd_lite_admin_add_to_calendar_delivery_calendar" disabled readonly />
			<label for="orddd_lite_admin_add_to_calendar_delivery_calendar"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Display the description for the Import Events section
	 *
	 * @since 3.9
	 */
	public static function orddd_lite_calendar_import_ics_feeds_section_callback() {
		esc_attr_e( 'Events will be imported using the ICS Feed url. Each event will create a new WooCommerce Order. The event\'s date & time will be set as that order\'s Delivery Date & Time. <br>Lockout will be updated for global settings for the set Delivery Date & Time.', 'order-delivery-date' );
	}

	/**
	 * Callback for adding instructions to set up Import events using ics feed urls
	 *
	 * @since 3.9
	 */
	public static function orddd_lite_ics_feed_url_instructions_callback() {
		esc_attr_e( 'To set up Import events using ics feed urls, please click on "Show me how" link and carefully follow these steps:', 'order-delivery-date' ) . '
        <span class="ics-feed-description" >
            <a href="#orddd-ics-feed-instructions" id="show_instructions" data-target="api-instructions" class="orddd_ics_feed-info_trigger" title="' . __( 'Click to toggle instructions', 'order-delivery-date' ) . '" disabled>' . __( 'Show me how', 'order-delivery-date' ) . '</a></span>';
	}

	/**
	 * Callback for adding the 'iCalendar/.ics Feed URL' field in the Import Events section
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 3.9
	 */
	public static function orddd_lite_ics_feed_url_callback( $args ) {
		echo "<table id='orddd_lite_ics_url_list'>
            <tr id='0' >
                <td class='orddd_lite_ics_feed_url'>
                    <input type='text' id='orddd_ics_fee_url_0' size='60' disabled readonly>
                </td>
                <td class='orddd_lite_ics_feed_url'>
                    <input type='button' value='Save' id='save_ics_url' class='save_button' name='0' disabled >
                </td>
                <td class='orddd_lite_ics_feed_url'>
                    <input type='button' class='save_button' id='0' name='import_ics' value='Import Events' disabled='disabled'>
                </td>
                <td class='orddd_lite_ics_feed_url'>
                    <input type='button' class='save_button' id='0' name='delete_ics_feed' value='Delete' disabled='disabled'>
                </td>
            </tr>
        </table>";

		echo "<input type='button' class='save_button' id='add_new_ics_feed' name='add_new_ics_feed' value='" . wp_kses_post( 'Add New Ics feed url', 'order-delivery-date' ) . "' disabled>";

		echo '<br><b><i>Upgrade to <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=ordddupgradetopro&utm_medium=link&utm_campaign=OrderDeliveryDateLite" target="_blank">Order Delivery Date Pro for WooCommerce</a> to enable the setting.</i></b>';
	}

	/**
	 * Callback for adding 'Real Time Import' checkbox in the Import Events section.
	 *
	 * @param array $args Extra arguments containing label & class for the checkbox.
	 * @since 3.9
	 */
	public static function orddd_lite_real_time_import_callback( $args ) {
		?>
			<input type="checkbox" name="orddd_lite_real_time_import" id="orddd_lite_real_time_import" class="day-checkbox" disabled readonly/>
			<label for="orddd_lite_real_time_import"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for accepting minutes for automated WP cron to import events from google calendar.
	 *
	 * @param array $args Extra arguments containing label & class for the minutes google calendar text box.
	 * @since 3.9
	 */
	public static function orddd_lite_wp_cron_minutes_callback( $args ) {
		?>
			<input id="orddd_lite_wp_cron_minutes" name= "orddd_lite_wp_cron_minutes" type="text" disabled readonly/>
			<label for="orddd_lite_wp_cron_minutes"><?php echo wp_kses_post( $args[0] ); ?></label>';
		<?php
	}
}


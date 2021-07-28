<?php
/**
 * Order Delivery Date for WooCommerce Lite
 *
 * Handles email sending from the plugin.
 *
 * @author      Tyche Softwares
 * @package     Order-Delivery-Date-Lite-for-WooCommerce/Class-ORDDD-Email-Manager
 * @since       3.13.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * ORDDD_LITE_Email_Manager Class
 *
 * @class ORDDD_LITE_Email_Manager
 */
class ORDDD_LITE_Email_Manager {

	/**
	 * Constructor sets up hooks to add the
	 * email actions to WooCommerce emails.
	 *
	 * @since 3.13.0
	 */
	public function __construct() {
		add_filter( 'woocommerce_email_classes', array( &$this, 'orddd_lite_init_emails' ) );

		// Email Actions.
		$email_actions = array(
			'orddd_lite_admin_update_date',

		);

		foreach ( $email_actions as $action ) {
			add_action( $action, array( 'WC_Emails', 'send_transactional_email' ), 10, 10 );
		}

		add_filter( 'woocommerce_template_directory', array( $this, 'orddd_lite_template_directory' ), 10, 2 );

	}

	/**
	 * Adds the Email class file to ensure the emails
	 * from the plugin are fired based on the settings.
	 *
	 * @param array $emails - List of Emails already setup by WooCommerce.
	 * @return array $emails - List of Emails with the ones from the plugin included.
	 *
	 * @hook woocommerce_email_classes
	 * @since 3.13.0
	 */
	public function orddd_lite_init_emails( $emails ) {
		if ( ! isset( $emails['ORDDD_Lite_Email_Update_Date'] ) ) {
			$emails['ORDDD_Lite_Email_Update_Date'] = require_once dirname( __DIR__, 1 ) . '/emails/class-orddd-lite-email-update-date.php';
		}
		return $emails;
	}

	/**
	 * Returns the directory name in which the template file is present.
	 *
	 * @param string $directory - Directory Name in which the template is present.
	 * @param string $template - Email Template File Name.
	 * @return string $directory - Directory Name in which the template is present. Modified when the template is for our plugin.
	 *
	 * @hook woocommerce_template_directory
	 * @since 5.7
	 */
	public function orddd_lite_template_directory( $directory, $template ) {
		if ( false !== strpos( $template, 'order-' ) ) {
			return 'order-delivery-date-for-woocommerce';
		}

		return $directory;
	}

	/**
	 * Adds a hook to fire the delivery date/time edit email notice.
	 *
	 * @param integer $order_id - Order ID for which the Delivery Date/Time is edited.
	 * @param string  $updated_by - States by whom are the details being updated. Valid Values: admin|customer.
	 *
	 * @since 6.8
	 */
	public static function orddd_lite_send_email_on_update( $order_id, $updated_by ) {
		WC_Emails::instance();
		do_action( 'orddd_lite_admin_update_date_notification', $order_id, $updated_by );
	}
}//end class
new ORDDD_Lite_Email_Manager();

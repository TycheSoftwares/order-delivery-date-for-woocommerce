<?php
/**
 * Order Delivery Date for WooCommerce Lite
 *
 * Delivery Details Edited Email. An email sent to the admin or customer when the delivery details are edited.
 *
 * @author      Tyche Softwares
 * @package     Order-Delivery-Date-Lite-for-WooCommerce/Emails/Class-ORDDD-Lite-Email-Update-Date
 * @since       3.13.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * ORDDD_Lite_Email_Update_Date Class
 *
 * @class ORDDD_Lite_Email_Update_Date
 * @extends     WC_Email
 */
class ORDDD_Lite_Email_Update_Date extends WC_Email {

	/**
	 * Constructor.
	 *
	 * Defines class variables and hooks as needed.
	 *
	 * @since 3.13.0
	 */
	public function __construct() {

		$this->id          = 'orddd_lite_update_date';
		$this->title       = __( 'Delivery Date & Time Updated', 'order-delivery-date' );
		$this->description = __( 'Delivery Date & Time is is being updated for the order.', 'order-delivery-date' );

		$this->template_html  = 'emails/admin-update-date.php';
		$this->template_plain = 'emails/plain/admin-update-date.php';

		// Triggers for this email.
		add_action( 'orddd_lite_admin_update_date_notification', array( $this, 'trigger' ), 10, 2 );

		// Call parent constructor.
		parent::__construct();

		// Other settings.
		$this->template_base = untrailingslashit( plugin_dir_path( __DIR__, 1 ) ) . '/templates/';
		$this->recipient     = $this->get_option( 'recipient', get_option( 'admin_email' ) );
	}

	/**
	 * Sends an email to the admin or customer when the customer or admin
	 * edits the delivery details for an order respectively.
	 *
	 * @param integer $order_id - Order ID for which details are being edited.
	 * @param string  $updated_by - States by whom are the details being updated. Valid Values: admin|customer.
	 *
	 * @hook orddd_lite_admin_update_date_notification
	 * @since 3.13.0
	 */
	public function trigger( $order_id, $updated_by ) {
		if ( $order_id ) {
			$this->order_id  = $order_id;
			$order           = new WC_Order( $order_id );
			$order_date      = $order->get_date_created();
			$this->find[]    = '{order_date}';
			$this->replace[] = date_i18n( wc_date_format(), strtotime( $order_date ) );

			$this->find[]    = '{order_number}';
			$this->replace[] = $order_id;
			if ( ! $this->get_recipient() ) {
				return;
			}

			if ( 'admin' === $updated_by ) {
				$recipient = $order->get_billing_email();
			} else {
				$recipient = $this->get_recipient();
			}

			$this->updated_by = $updated_by;
			$this->send( $recipient, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		}
	}

	/**
	 * This function gets the HTML content for the email sent to the admin or customer
	 * when the customer or admin edits the delivery details for an order respectively.
	 *
	 * @since 3.13.0
	 */
	public function get_content_html() {
		ob_start();
		wc_get_template(
			$this->template_html,
			array(
				'order_id'      => $this->order_id,
				'sent_to_admin' => true,
				'plain_text'    => false,
				'email'         => $this,
				'email_heading' => $this->get_heading(),
				'updated_by'    => $this->updated_by,
			),
			'',
			$this->template_base
		);
		return ob_get_clean();
	}

	/**
	 * This function gets the Plain content for the email sent to the admin or customer
	 * when the customer or admin edits the delivery details for an order respectively.
	 *
	 * @since 3.13.0
	 */
	public function get_content_plain() {
		ob_start();
		wc_get_template(
			$this->template_plain,
			array(
				'order_id'      => $this->order_id,
				'sent_to_admin' => true,
				'plain_text'    => false,
				'email'         => $this,
				'email_heading' => $this->get_heading(),
				'updated_by'    => $this->updated_by,
			),
			'',
			$this->template_base
		);
		return ob_get_clean();
	}

	/**
	 * Get email subject.
	 *
	 * @since  3.13.0
	 * @return string
	 */
	public function get_default_subject() {
		return __( '[{blogname}] Delivery Date & Time is Updated for (Order {order_number}) - {order_date}', 'order-delivery-date' );
	}

	/**
	 * Get email heading.
	 *
	 * @since  3.13.0
	 * @return string
	 */
	public function get_default_heading() {
		return __( 'Delivery Date & Time Updated', 'order-delivery-date' );
	}


	/**
	 * This function adds the form fields for the Email to be visible in
	 * WooCommerce->Settings->Emails->Delivery Date & Time Updated
	 *
	 * @since 3.13.0
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled'    => array(
				'title'   => __( 'Enable/Disable', 'order-delivery-date' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable this email notification', 'order-delivery-date' ),
				'default' => 'yes',
			),
			'recipient'  => array(
				'title'       => __( 'Recipient(s)', 'woocommerce' ),
				'type'        => 'text',
				/* translators: %s: admin email */
				'description' => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to <code>%s</code>.', 'woocommerce' ), esc_attr( get_option( 'admin_email' ) ) ),
				'placeholder' => '',
				'default'     => '',
			),
			'subject'    => array(
				'title'       => __( 'Subject', 'order-delivery-date' ),
				'type'        => 'text',
				/* translators: %s: subject */
				'description' => sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'order-delivery-date' ), $this->subject ),
				'placeholder' => $this->get_default_subject(),
				'default'     => '',
			),
			'heading'    => array(
				'title'       => __( 'Email Heading', 'order-delivery-date' ),
				'type'        => 'text',
				/* translators: %s: heading */
				'description' => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'order-delivery-date' ), $this->heading ),
				'placeholder' => $this->get_default_heading(),
				'default'     => '',
			),
			'email_type' => array(
				'title'       => __( 'Email type', 'order-delivery-date' ),
				'type'        => 'select',
				'description' => __( 'Choose which format of email to send.', 'order-delivery-date' ),
				'default'     => 'html',
				'class'       => 'email_type',
				'options'     => array(
					'plain'     => __( 'Plain text', 'order-delivery-date' ),
					'html'      => __( 'HTML', 'order-delivery-date' ),
					'multipart' => __( 'Multipart', 'order-delivery-date' ),
				),
			),
		);
	}

}
return new ORDDD_Lite_Email_Update_Date();


<?php
/**
 * Specific dates table.
 *
 * @package order-delivery-date/Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Load WP_List_Table if not loaded.
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Display Specific Delivery Dates Table in General Settings in admin.
 *
 * @author Tyche Softwares
 * @package Order-Delivery-Date-Pro-for-WooCommerce/Admin/Settings/General
 */
class ORDDD_Lite_View_Specific_Table extends WP_List_Table {

	/**
	 * URL of this page
	 *
	 * @var string
	 */
	public $base_url;

	/**
	 * Get things started
	 *
	 * @see WP_List_Table::__construct()
	 */
	public function __construct() {

		global $status, $page;
		// Set parent defaults.
		parent::__construct(
			array(
				'singular' => __( 'delivery_date', 'order-delivery-date' ), // singular name of the listed records.
				'plural'   => __( 'delivery_dates', 'order-delivery-date' ), // plural name of the listed records.
				'ajax'     => false,                        // Does this table support ajax?
			)
		);
		$this->process_bulk_action();
		$this->base_url = admin_url( 'admin.php?page=order_delivery_date_lite&action=general_settings&section=delivery_dates' );
	}

	/**
	 * Bulk Delete settings.
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		return array(
			'orddd_delete' => __( 'Delete', 'order-delivery-date' ),
		);
	}

	/**
	 * Add the check box for the items to bulk select
	 *
	 * @param Object $item Table item.
	 */
	public function column_cb( $item ) {
		$row_id = '';
		if ( isset( $item->dd ) && '' !== $item->dd ) {
			$row_id = $item->dd;
			return sprintf(
				'<input type="checkbox" name="%1$s[]" value="%2$s" />',
				'delivery_date',
				$row_id
			);
		}
	}

	/**
	 * Prepare items to display in the table
	 */
	public function orddd_prepare_items() {
		$columns               = $this->get_columns();
		$hidden                = array(); // No hidden columns.
		$data                  = $this->orddd_shipping_settings_data();
		$sortable              = array();
		$status                = isset( $_GET['status'] ) ? $_GET['status'] : 'any'; //phpcs:ignore
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->items           = $data;
	}

	/**
	 * Return columns to add in the table
	 *
	 * @return array $columns Name of the columns in the table
	 */
	public function get_columns() {
		$columns = array(
			'cb'                               => '<input type="checkbox" />',
			'delivery_date'                    => __( 'Date', 'order-delivery-date' ),
			'delivery_date_additional_charges' => __( 'Additional Charges', 'order-delivery-date' ),
			'delivery_date_checkout_label'     => __( 'Label', 'order-delivery-date' ),
			'delivery_date_lockout'            => __( 'Maximum Orders', 'order-delivery-date' ),
		);
		return $columns;
	}

	/**
	 * Callback function
	 */
	public function orddd_shipping_settings_data() {}

	/**
	 * Column default
	 *
	 * @param array  $delivery_date_settings Settings.
	 * @param string $column_name Column name.
	 * @return void
	 */
	public function column_default( $delivery_date_settings, $column_name ) {}
}


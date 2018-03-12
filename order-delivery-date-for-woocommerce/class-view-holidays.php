<?php 
/**
 * Order Delivery Date for WooCommerce Lite
 *
 * Functions to display the added holidays in WP List Table.
 *
 * @author      Tyche Softwares
 * @package     Order-Delivery-Date-Lite-for-WooCommerce/Admin/Display-Holidays
 * @since       1.9
 */

// Load WP_List_Table if not loaded
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * ORDDD_LITE_View_Holidays_Table Class
 *
 * @class ORDDD_LITE_View_Holidays_Table
 */

class ORDDD_LITE_View_Holidays_Table extends WP_List_Table {

	/**
	 * URL of this page
	 *
	 * @var string
	 * @since 2.8
	 */
	public $base_url;
	
	/**
	 * Get things started
	 *
	 * @see WP_List_Table::__construct()
	 */
	public function __construct() {
		global $status, $page;
		// Set parent defaults
		parent::__construct( array(
            'singular' => __( 'holiday', 'order-delivery-date' ), //singular name of the listed records
            'plural'   => __( 'holidays', 'order-delivery-date' ), //plural name of the listed records
			'ajax'      => false             			// Does this table support ajax?
		) );
		$this->process_bulk_action();
		$this->base_url = admin_url( 'admin.php?page=order_delivery_date_lite&action=holidays' );
	}
	
	/**
	 * Add the Delete Bulk Action
	 * @since 2.8
	 */
	public function get_bulk_actions() {
	    return array(
	        'orddd_lite_delete' => __( 'Delete', 'order-delivery-date' )
	    );
	}
	
    /**
	 * It is used to add the check box for the items
	 * 
	 * @param object $item
	 * @since 2.8
	 **/
	function column_cb( $item ){
	    $row_id = '';
	    if( isset( $item->holiday_date_stored ) && "" != $item->holiday_date_stored ){
	        $row_id = $item->holiday_date_stored;
	        return sprintf(
	            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
	            'holiday',
	            $row_id
	        );
	    }
	}
	
	/**
	 * Prepare items to display in the table
	 * 
	 * @since 2.8
	 */
	public function orddd_prepare_items() {
		$columns  = $this->get_columns();
		$hidden   = array(); // No hidden columns
		$data     = $this->orddd_lite_holidays_data();
		$sortable = array();
		$status   = isset( $_GET['status'] ) ? $_GET['status'] : 'any';
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->items = $data;
	}
	
	/**
	 * Return columns to be displayed in the table
	 * 
	 * @return array $columns - An array of column Names
	 * @since 2.8
	 */
	public function get_columns() {
		$columns = array(
		    'cb'                 =>  '<input type="checkbox" />',
    		'holiday_name'   => __( 'Name', 'order-delivery-date' ),
    		'holiday_date'   => __( 'Date', 'order-delivery-date' ),
		);
		return apply_filters( 'orddd_holidays_table_columns', $columns );
	}
	
	/**
	 * Displays the data in the table
	 * 
	 * @return array $return_holidays - contains the holidays to be displayed
	 * @since 2.8
	 */
	public function orddd_lite_holidays_data() { 
		$holidays_arr = $return_holidays = array();
		$holidays = get_option( 'orddd_lite_holidays' );
		if ( $holidays != '' && $holidays != '{}' && $holidays != '[]' && $holidays != 'null' ) {
		    $holidays_arr = json_decode( $holidays );
		}
		$holiday_count = 0;
		foreach ( $holidays_arr as $key => $value ) {
		    $return_holidays[ $key ] = new stdClass();
		    $return_holidays[ $key ]->holiday_name = $value->n;
		    $date_from_arr = explode( "-", $value->d );
		    $holiday_date = date( 'm-d-Y', gmmktime( 0, 0, 0, $date_from_arr[ 0 ], $date_from_arr[ 1 ], $date_from_arr[ 2 ] ) );
		    $return_holidays[ $key ]->holiday_date = $holiday_date;
		    $return_holidays[ $key ]->holiday_date_stored = $value->d;
		}
		return apply_filters( 'orddd_lite_holidays_data', $return_holidays );
	}
	
	/**
	 * Add Edit and Delete link in each row of the table data
	 * 
	 * @param resource $holiday_settings - Holiday details
     * @param string $column_name - Column Name
     * @return $arrayName = array('' => , );
     * @since 2.8
	 */
	public function column_default( $holiday_settings, $column_name ) {
	    $value = isset( $holiday_settings->$column_name ) ? $holiday_settings->$column_name : '';
		return apply_filters( 'orddd_lite_table_column_default', $value, $holiday_settings, $column_name );
	}	
}
?>
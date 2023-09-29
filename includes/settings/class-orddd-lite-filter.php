<?php
/**
 * Order Delivery Date for WooCommerce Lite
 *
 * Functions to add columns on WooCommerced->Orders page.
 *
 * @author      Tyche Softwares
 * @package     Order-Delivery-Date-Lite-for-WooCommerce/Admin/Delivery-Date-on-Edit-Order-Page
 * @since       1.9
 */

// Include Common file.
require_once WP_PLUGIN_DIR . '/order-delivery-date-for-woocommerce/includes/class-orddd-lite-common.php';

/**
 * Order Delivery Date Filter Class
 *
 * @class Orddd_Lite_Filter
 */
class Orddd_Lite_Filter {

	/**
	 * Default Constructor
	 *
	 * @since 3.12.0
	 */
	public function __construct() {
		//Delivery Date & Time on WooCommerce Edit Order page in Admin
		if ( get_option( 'orddd_lite_delivery_date_fields_on_checkout_page' ) == 'billing_section' || get_option( 'orddd_lite_delivery_date_fields_on_checkout_page' ) == 'after_your_order_table' || get_option( 'orddd_lite_delivery_date_fields_on_checkout_page' ) == 'custom' ) {
		    add_action( 'woocommerce_admin_order_data_after_billing_address',  array( &$this, 'orddd_lite_display_delivery_info_admin_order_meta') , 10, 1 );
		} else if ( get_option( 'orddd_lite_delivery_date_fields_on_checkout_page' ) == 'shipping_section'|| get_option( 'orddd_lite_delivery_date_fields_on_checkout_page' ) == 'before_order_notes' || get_option( 'orddd_lite_delivery_date_fields_on_checkout_page' ) == 'after_order_notes' ) {
		    add_action( 'woocommerce_admin_order_data_after_shipping_address', array( &$this, 'orddd_lite_display_delivery_info_admin_order_meta') , 10, 1 );
		}
		// Delivery date & Delivery Time in Order Preview in Admin
		add_filter( 'woocommerce_admin_order_preview_get_order_details', array( &$this, 'orddd_lite_admin_order_preview_add_delivery_date' ), 20, 2 );

		if ( 'on' === get_option('orddd_lite_show_filter_on_orders_page_check') ) {
			add_action( 'restrict_manage_posts',                array( &$this, 'orddd_lite_restrict_orders' ), 15 );
		    add_filter( 'request',                              array( &$this, 'orddd_lite_add_filterable_field' ) );
		    add_filter( 'woocommerce_shop_order_search_fields', array( &$this, 'orddd_lite_add_search_fields' ) );
		}
	}

	/**
	 * This function adds the Delivery Date column to WooCommerce->Orders page
	 *
	 * @param array $columns - List of columns already present.
	 * @return array $new_columns - List of columns with the new ones added
	 * @since 1.9
	 */
	public static function orddd_lite_woocommerce_order_delivery_date_column( $columns ) {
		$new_columns = ( is_array( $columns ) ) ? $columns : array();
		unset( $new_columns['wc_actions'] );
		// edit this for you column(s).
		// all of your columns will be added before the actions column.
		$date_field_label = '' !== get_option( 'orddd_lite_delivery_date_field_label' ) ? get_option( 'orddd_lite_delivery_date_field_label' ) : 'Delivery Date';
		// phpcs:ignore
		if ( 'Delivery Date' === $date_field_label ) {
			$date_field_label = __( 'Delivery Date', 'order-delivery-date' );
		}
		$new_columns['order_delivery_date'] = __( $date_field_label, 'order-delivery-date' ); // Title for column heading.
		$new_columns['wc_actions']          = $columns['wc_actions'];
		return $new_columns;
	}

	/**
	 * This function adds the Delivery Date for each order on WooCommerce->Orders page
	 *
	 * @param str $column - Name of the column.
	 * @since 1.9
	 */
	public static function orddd_lite_woocommerce_custom_column_value( $column, $post_id ) {
		global $post, $the_order, $orddd_lite_date_formats;
		if ( 'order_delivery_date' === $column ) {
			$delivery_date_formatted = Orddd_Lite_Common::orddd_lite_get_order_delivery_date(  $post_id );
			echo esc_attr( $delivery_date_formatted );
			$time_slot = orddd_lite_common::orddd_get_order_timeslot(  $post_id );
			echo '<p>' . esc_attr( $time_slot ) . '</p>';
		}
	}

	/**
	 * Adds the Delivery Date column to the list of sortable columns
	 * in WooCommerce->Orders page.
	 *
	 * @param array $columns - list of sortable columns.
	 * @return array $columns - list of sortable columns with our column added
	 * @since 1.9
	 */
	public static function orddd_lite_woocommerce_custom_column_value_sort( $columns ) {
		$columns['order_delivery_date'] = '_orddd_lite_timestamp';
		return $columns;
	}
	

	
	/**
	 * Delivery date column orderby. 
	 * 
	 * Helps WooCommerce understand using the value based on which a column should be sorted.
	 * The delivery date is stored as a timestamp in the _orddd_timestamp variable in wp_postmeta
	 * 
	 * @param array $clauses      - Query clauses
	 * @param array $query_object - Query object
	 * @return array $clauses     - Updated Query clauses.
	 * 
	 * @hook request
	 * @since 9.30
	 */
	public static function orddd_lite_woocommerce_delivery_datetime_orderby( $clauses, $query_object ) {

		if ( get_option( "orddd_lite_enable_default_sorting_of_column" ) == 'on' ) {
			global $wpdb;

			if (  isset( $_GET['post_type'] ) && 'shop_order' === $_GET['post_type'] && ( ( isset( $_GET[ 'orderby' ] ) && '_orddd_lite_timestamp' === $_GET[ 'orderby' ] ) || ( ! isset( $_GET['orderby'] ) && 'on' === get_option( "orddd_lite_enable_default_sorting_of_column" ) ) ) ) {


				$clauses['join'] .= "LEFT JOIN " . $wpdb->prefix . "postmeta wpm ON ( " . $wpdb->prefix . "posts.Id = wpm.post_id AND wpm.meta_key = '_orddd_lite_timestamp' )
				LEFT JOIN " . $wpdb->prefix . "postmeta wpm1 ON ( " . $wpdb->prefix . "posts.Id = wpm1.post_id AND wpm1.meta_key = '_orddd_timestamp' )
				LEFT JOIN " . $wpdb->prefix . "postmeta wpm2 ON ( " . $wpdb->prefix . "posts.Id = wpm2.post_id AND wpm2.meta_key = '_orddd_lite_timeslot_timestamp' )";

				$orderby = ( ! isset( $_GET['order'] ) || 'desc' === $_GET['order'] ) ? 'desc' : 'asc';
				$orderby = " COALESCE( wpm2.meta_value, wpm.meta_value ) " . $orderby ." " ;

				$clauses['orderby'] =  ! empty( $clauses['orderby'] ) ? $orderby . ', ' . $clauses['orderby'] : $orderby;
            }

		}
		return $clauses;
	}
	/**
	 * Displays the Delivery date & Delivery time on WooCommerce->Orders->Edit Order page.
	 * 
	 * @param WC_Order $order - Order object
	 * 
	 * @hook woocommerce_admin_order_data_after_billing_address
	 *       woocommerce_admin_order_data_after_shipping_address
	 * @since 3.12.0      
	 */
	public static function orddd_lite_display_delivery_info_admin_order_meta( $order ) {		
		if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {            
            $order_id = $order->get_id();
        } else {
            $order_id = $order->id;
        }
		
		$delivery_date_formatted = Orddd_Lite_Common::orddd_lite_get_order_delivery_date( $order_id );
		$date_field_label        = get_option( 'orddd_lite_delivery_date_field_label' );

		if( '' !== $delivery_date_formatted ) {
			echo '<p><strong>' . __( $date_field_label, 'order-delivery-date' ) . ': </strong>' . $delivery_date_formatted;
		}
		
		$time_slot        = Orddd_Lite_Common::orddd_get_order_timeslot( $order_id );
		$time_field_label = get_option( 'orddd_lite_delivery_timeslot_field_label' );

		if ( '' !== $time_slot ) {
			echo '<p><strong>' . __( $time_field_label, 'order-delivery-date' ) . ': </strong>' . $time_slot . '</p>';
		}
	}

	/**
	 * Displays the Delivery Date & Delivery time on Order Preview page in Admin
	 * 
	 * @param $data 
	 * @param WC_Order $order - Order object
	 * 
	 * @hook woocommerce_admin_order_preview_get_order_details
	 * @since 3.12.0
	 */
	public static function orddd_lite_admin_order_preview_add_delivery_date( $data, $order ) {
		$order_id                = $order->get_id();
		$delivery_date_formatted = Orddd_Lite_Common::orddd_lite_get_order_delivery_date( $order_id );
		$field_date_label        = get_option( 'orddd_lite_delivery_date_field_label' );
		$orddd_timeslot          = Orddd_Lite_Common::orddd_get_order_timeslot( $order_id );

		if ( '' != $delivery_date_formatted ) {
	        $data[ 'payment_via' ] = $data[ 'payment_via' ] . '<br>' . '<strong>'.$field_date_label.'</strong>' . $delivery_date_formatted;

	        if ( '' != $orddd_timeslot ) {
	        	$data[ 'payment_via' ] = $data[ 'payment_via' ] . ',<br>' . $orddd_timeslot;
	    	}
		}
    	return $data;
	}

	/**
	 * Add a delivery date dropdown filter on WooCommerce Orders page.
	 *
	 * @return void
	 */
	public static function orddd_lite_restrict_orders() {
		global $typenow, $wpdb, $wp_locale;

		if ( 'shop_order' !== $typenow ) {
			return;
		}

		$gmt = false;
		if ( has_filter( 'orddd_gmt_calculations' ) ) {
			$gmt = apply_filters( 'orddd_gmt_calculations', '' );
		}

		$current_time      = current_time( 'timestamp', $gmt );
		$javascript        = '';
		$filter_field_name = 'order_delivery_date_lite_filter';
		$db_field_name     = '_orddd_lite_timestamp';
		$date_display      = 'display:none;';
		$startdate         = '';
		$enddate           = '';

		$months = $wpdb->get_results(
			$wpdb->prepare(
				'SELECT YEAR( FROM_UNIXTIME( meta_value ) ) as year, MONTH( FROM_UNIXTIME( meta_value ) ) as month, CAST( meta_value AS UNSIGNED ) AS meta_value_num
				FROM ' . $wpdb->postmeta . '
				WHERE meta_key = %s
				GROUP BY year, month
				ORDER BY meta_value_num DESC',
				$db_field_name
			)
		);

		$month_count = 0;
		if ( is_array( $months ) ) {
			$month_count = count( $months );
		}

		if ( ! $month_count || ( 1 == $month_count && 0 == $months[0]->month ) ) {
			return;
		}

		if ( isset( $_GET[ $filter_field_name ] ) && 'today' === $_GET[ $filter_field_name ] ) {
			$m = $_GET[ $filter_field_name ];
		} elseif ( isset( $_GET[ $filter_field_name ] ) && 'tomorrow' === $_GET[ $filter_field_name ] ) {
			$m = $_GET[ $filter_field_name ];
		} elseif ( isset( $_GET[ $filter_field_name ] ) && 'custom' === $_GET[ $filter_field_name ] ) {
			$m            = $_GET[ $filter_field_name ];
			$date_display = '';
			$startdate    = isset( $_GET[ 'orddd_lite_custom_startdate' ] ) ? wp_unslash( sanitize_key( $_GET[ 'orddd_lite_custom_startdate' ] ) ) : '';
			$enddate      = isset( $_GET[ 'orddd_lite_custom_enddate' ] ) ? wp_unslash( sanitize_key( $_GET[ 'orddd_lite_custom_enddate' ] ) ) : '';
		} else {
			$m = isset( $_GET[ $filter_field_name ] ) ? (int) $_GET[ $filter_field_name ] : 0;
		}

		$today_name          = __( 'Today', 'order-delivery-date' );
		$tomorrow_name       = __( 'Tomorrow', 'order-delivery-date' );
		$custom_filter_label = __( 'Custom', 'order-delivery-date' );

		$today_option = array(
			'year'           => date( 'Y', $current_time ),
			'month'          => 'today',
			'meta_value_num' => $current_time,
			'month_name'     => $today_name,
		);

		$tomorrow_date   = date( 'Y-m-d', strtotime( '+1 day', $current_time ) );
		$tomorrow_time   = strtotime( $tomorrow_date );
		$tomorrow_option = array(
			'year'           => date( 'Y', $tomorrow_time ),
			'month'          => 'tomorrow',
			'meta_value_num' => $tomorrow_time,
			'month_name'     => $tomorrow_name,
		);

		$custom  = array(
			'year'           => '',
			'month'          => 'custom',
			'meta_value_num' => '',
			'month_name'     => $custom_filter_label,
		);

		array_unshift( $months, (object)$today_option, (object)$tomorrow_option, (object)$custom );
		?>

		<select name="order_delivery_date_lite_filter" id="order_delivery_date_lite_filter" class="orddd_filter">
			<option value=""><?php esc_html_e( 'Show all Delivery Dates', 'order-delivery-date' ); ?></option>
			<?php
			foreach ( $months as $arc_row ) {
				if ( 'today' !== $arc_row->month && 'tomorrow' !== $arc_row->month && 'custom' !== $arc_row->month ) {
					if ( 0 == $arc_row->year || '1969' == $arc_row->year ) {
						continue;
					}
					$month = zeroise( $arc_row->month, 2 );
					$year = $arc_row->year;

					printf( '<option %s value="%s">%s</option>',
						selected( $m, $year . $month, false ),
						esc_attr( $arc_row->year . $month ),
						/* translators: 1: month name, 2: 4-digit year */
						sprintf( esc_html_x( '%1$s %2$d', 'order-delivery-date' ), $wp_locale->get_month( $month ), $year )
					);
				} else {
					$arc_row->year = $year = '';
					$month = $arc_row->month;
					printf( '<option %s value="%s">%s</option>',
						selected( $m, $arc_row->month, false ),
						esc_attr( $arc_row->month ),
						esc_html( $arc_row->month_name )
					);
				}
			}
		?>
		</select>

		<input type="text" name="orddd_lite_custom_startdate" id="orddd_lite_custom_startdate" class="orddd_datepicker" value="<?php echo esc_attr( $startdate ); ?>" style="width:100px;<?php echo $date_display; ?>" placeholder="<?php esc_html_e( 'Start Date', 'order-delivery-date' ); ?>" readonly>
		<input type="text" name="orddd_lite_custom_enddate" id="orddd_lite_custom_enddate" class="orddd_datepicker" value="<?php echo esc_attr( $enddate ); ?>" style="width:100px;<?php echo $date_display; ?>" placeholder="<?php esc_html_e( 'End Date', 'order-delivery-date' ); ?>" readonly>
		<?php
	}

	/**
	 * Filter the orders based on option selected from delivery date filter dropdown.
	 *
	 * @param array $vars array of queries.
	 * @return array
	 */
	public static function orddd_lite_add_filterable_field( $vars ) {
		global $typenow;
		if ( 'shop_order' != $typenow ) {
			return $vars;
		}

		$gmt = false;
		if( has_filter( 'orddd_gmt_calculations' ) ) {
			$gmt = apply_filters( 'orddd_gmt_calculations', '' );
		}
		$current_time = current_time( 'timestamp', $gmt );

		$meta_queries = array( 'relation' => 'AND' );

		// if the field is filterable and selected by the user.
		if ( isset( $_GET[ 'order_delivery_date_lite_filter' ] ) && $_GET[ 'order_delivery_date_lite_filter' ] ) {
			$date = $_GET[ 'order_delivery_date_lite_filter' ];

			switch( $date ) {
				case 'today':
					// from the start to the end of the month.
					$current_date = date( 'Y-m-d', $current_time );

					$from_date = date( 'Y-m-d H:i:s', strtotime( $current_date . '00:00:00' ) );
					$to_date = date( 'Y-m-d H:i:s', strtotime( $current_date . '23:59:59' ) );
	
					$meta_queries[] = array(
						'key'     => '_orddd_lite_timestamp',
						'value'   => array( strtotime( $from_date ), strtotime( $to_date ) ),
						'type'    => 'NUMERIC',
						'compare' => 'BETWEEN',
					);
					break;
				case 'tomorrow':
					$current_date = date( 'Y-m-d', strtotime('+1 day', $current_time ) );

					$from_date = date( 'Y-m-d H:i:s', strtotime( $current_date . '00:00:00' ) );
					$to_date = date( 'Y-m-d H:i:s', strtotime( $current_date . '23:59:59' ) );
	
					$meta_queries[] = array(
						'key'     => '_orddd_lite_timestamp',
						'value'   => array( strtotime( $from_date ), strtotime( $to_date ) ),
						'type'    => 'NUMERIC',
						'compare' => 'BETWEEN'
					);
					break;
				case 'custom':
					$current_date = date( 'Y-m-d', $current_time );
					$startdate    = isset( $_GET[ 'orddd_lite_custom_startdate' ] ) && '' !== $_GET[ 'orddd_lite_custom_startdate' ] ? $_GET[ 'orddd_lite_custom_startdate' ] : $current_date;
					$enddate      = isset( $_GET[ 'orddd_lite_custom_enddate' ] ) && '' !== $_GET[ 'orddd_lite_custom_enddate' ] ? $_GET[ 'orddd_lite_custom_enddate' ] : $startdate;
					$from_date    = date( 'Y-m-d H:i:s', strtotime( $startdate . '00:00:00' ) );
					$to_date      = date( 'Y-m-d H:i:s', strtotime( $enddate . '23:59:59' ) );

					$meta_queries[] = array(
						'key'     => '_orddd_lite_timestamp',
						'value'   => array( strtotime( $from_date ), strtotime( $to_date ) ),
						'type'    => 'NUMERIC',
						'compare' => 'BETWEEN',
					);
					break;
				default:
					// from the start to the end of the month.
					$from_date = substr( $date, 0, 4 ) . '-' . substr( $date, 4, 2 ) . '-01';
					$to_date   = substr( $date, 0, 4 ) . '-' . substr( $date, 4, 2 ) . '-' . date( 't', strtotime( $from_date ) );
					$meta_queries[] = array(
						'key'     => '_orddd_lite_timestamp',
						'value'   => array( strtotime( $from_date.' 00:00:00' ), strtotime( $to_date .' 23:59:59' ) ),
						'type'    => 'NUMERIC',
						'compare' => 'BETWEEN',
					);
			}
		}

		// update the query vars with our meta filter queries, if needed
		if ( is_array( $meta_queries ) && count( $meta_queries ) > 1 ) {
			$vars = array_merge(
				$vars,
				array( 'meta_query' => $meta_queries )
			);
		}
		return $vars;
	}

	/**
	 * Search orders based on delivery date entered in search field.
	 *
	 * @param array $search_fields Search fields.
	 * @return array
	 */
	public static function orddd_lite_add_search_fields( $search_fields ) {
		array_push( $search_fields, get_option( 'orddd_lite_delivery_date_field_label' ) );
		return $search_fields;
	}

}
$orddd_lite_filter = new Orddd_Lite_Filter();


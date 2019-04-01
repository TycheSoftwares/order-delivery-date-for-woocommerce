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

include_once( WP_PLUGIN_DIR . '/order-delivery-date-for-woocommerce/includes/orddd-lite-common.php' );

/**
 * Order Delivery Date Filter Class
 *
 * @class orddd_lite_filter
 */
class orddd_lite_filter {
    
    /**
     * This function adds the Delivery Date column to WooCommerce->Orders page
     * 
     * @param array $columns - List of columns already present
     * @return array $new_columns - List of columns with the new ones added
     * @since 1.9
     */
    public static function orddd_lite_woocommerce_order_delivery_date_column( $columns ) {
        $new_columns = ( is_array( $columns  )) ? $columns : array();
        unset( $new_columns[ 'wc_actions' ] );
        //edit this for you column(s)
        //all of your columns will be added before the actions column
        $new_columns[ 'order_delivery_date' ] = __( get_option( 'orddd_lite_delivery_date_field_label' ), 'order-delivery-date' ); //Title for column heading
        $new_columns[ 'wc_actions' ] = $columns[ 'wc_actions' ];
        return $new_columns;
    }
    
    /**
     * This function adds the Delivery Date for each order on WooCommerce->Orders page
     * 
     * @param str $column - Name of the column
     * @since 1.9
     */
    public static function orddd_lite_woocommerce_custom_column_value( $column ) {
        global $post, $orddd_lite_date_formats;
        if ( $column == 'order_delivery_date' ) {
            $delivery_date_formatted = orddd_lite_common::orddd_lite_get_order_delivery_date( $post->ID  );
            echo $delivery_date_formatted;
        }
    }
    
    /**
     * Adds the Delivery Date column to the list of sortable columns
     * in WooCommerce->Orders page.
     * 
     * @param array $columns - list of sortable columns
     * @return array $columns - list of sortable columns with our column added
     * @since 1.9
     */
    public static function orddd_lite_woocommerce_custom_column_value_sort( $columns ) {
        $columns[ 'order_delivery_date' ] = '_orddd_lite_timestamp';
        return $columns;
    }
    
    /**
     * Delivery date column orderby. This help WooCommerce to understand the field on which the sorting should be based on.
     * The delivery date is stored as a timestamp in the _orddd_lite_timestamp variable in wp_postmeta
     *
     * @param array $vars
     * @return array
     * @since 1.9
     **/
    public static function orddd_lite_woocommerce_delivery_date_orderby( $vars ) {
        global $typenow;
        $delivery_field_label = '_orddd_lite_timestamp';
        if ( isset( $vars[ 'orderby' ] ) && $vars[ 'orderby' ] != '' ) {
            if ( $delivery_field_label == $vars[ 'orderby' ] ) {
                $sorting_vars = array( 'orderby'  => array( 'meta_value_num' => $vars[ 'order' ], 'date' => 'ASC' ) );
                if ( !isset( $_GET[ 'order_delivery_date_filter' ] ) || $_GET['order_delivery_date_filter'] == '' ) {
                    $sorting_vars[ 'meta_query' ] = array(  'relation' => 'OR',
                        array (
                            'key'	  => $delivery_field_label,
                            'value'	  => '',
                            'compare' => 'NOT EXISTS'
                        ),
                        array (
                            'key'	  => $delivery_field_label,
                            'compare' => 'EXISTS'
                        )
                    );
                }
                $vars = array_merge( $vars, $sorting_vars );
            }
        } elseif( get_option( 'orddd_lite_enable_default_sorting_of_column' ) == 'checked' ) {
            if ( 'shop_order' != $typenow ) {
                return $vars;
            }
            $sorting_vars = array(
                'orderby'  => array( 'meta_value_num' => 'DESC', 'date' => 'ASC' ),
                'order'	   => 'DESC');
            if ( !isset( $_GET[ 'order_delivery_date_filter' ] ) || $_GET[ 'order_delivery_date_filter' ] == '' ) {
                $sorting_vars[ 'meta_query' ] = array(  'relation' => 'OR',
                    array (
                        'key'	  => $delivery_field_label,
                        'value'	  => '',
                        'compare' => 'NOT EXISTS'
                    ),
                    array (
                        'key'	  => $delivery_field_label,
                        'compare' => 'EXISTS'
                    )
                );
            }
            $vars = array_merge( $vars, $sorting_vars );
        }
        return $vars;
    }
}

?>
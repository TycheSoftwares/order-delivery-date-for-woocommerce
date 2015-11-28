<?php 

include_once( dirname( __FILE__ ) . '/orddd-lite-common.php' );

class orddd_lite_filter {
    
    /**
     * This function are used for show custom column on order page listing. woo-orders
     *
     */
    public static function orddd_lite_woocommerce_order_delivery_date_column( $columns ) {
        $new_columns = ( is_array( $columns  )) ? $columns : array();
        unset( $new_columns[ 'order_actions' ] );
        //edit this for you column(s)
        //all of your columns will be added before the actions column
        $new_columns[ 'order_delivery_date' ] = __( get_option( 'orddd_lite_delivery_date_field_label' ), 'order-delivery-date' ); //Title for column heading
        $new_columns[ 'order_actions' ] = $columns[ 'order_actions' ];
        return $new_columns;
    }
    
    /**
     * This fnction used to add value on the custom column created on woo- order
     *
     */
    public static function orddd_lite_woocommerce_custom_column_value( $column ) {
        global $post, $orddd_lite_date_formats;
        if ( $column == 'order_delivery_date' ) {
            $delivery_date_formatted = orddd_lite_common::orddd_lite_get_order_delivery_date( $post->ID  );
            echo $delivery_date_formatted;
        }
    }
    
    /**
     * Meta key for sorting the column
     * @param array $columns
     * @return array
    */
    public static function orddd_lite_woocommerce_custom_column_value_sort( $columns ) {
        $columns[ 'order_delivery_date' ] = '_orddd_lite_timestamp';
        return $columns;
    }
    
    /**
     * Delivery date column orderby. This help woocommerce to understand which column need to sort on which value.
     * The delivery date is stored as a timestamp in the _orddd_lite_timestamp variable in wp_postmeta
     *
     * @param array $vars
     * @return array
     **/
    public static function orddd_lite_woocommerce_delivery_date_orderby( $vars ) {
        global $typenow;
        $delivery_field_label = '_orddd_lite_timestamp';
        if ( isset( $vars[ 'orderby' ] ) ) {
            if ( $delivery_field_label == $vars[ 'orderby' ] ) {
                $sorting_vars = array( 'orderby'  => 'meta_value_num' );
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
                'orderby'  => 'meta_value_num',
                'order'	   => 'DESC');
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
        return $vars;
    }
}

?>
<?php 

/*
Plugin Name: Order Delivery Date for WooCommerce (Lite version)
Plugin URI: http://www.tychesoftwares.com/store/free-plugin/order-delivery-date-on-checkout/
Description: This plugin allows customers to choose their preferred Order Delivery Date during checkout.
Author: Tyche Softwares
Version: 1.6
Author URI: http://www.tychesoftwares.com/about
Contributor: Tyche Softwares, http://www.tychesoftwares.com/
*/

$wpefield_version = '1.6';

global $weekdays_orddd_lite;

$weekdays_orddd_lite = array('orddd_weekday_0' => __( 'Sunday', 'order-delivery-date' ),
				  'orddd_weekday_1' => __( 'Monday', 'order-delivery-date' ),
				  'orddd_weekday_2' => __( 'Tuesday', 'order-delivery-date' ),
				  'orddd_weekday_3' => __( 'Wednesday', 'order-delivery-date' ),
				  'orddd_weekday_4' => __( 'Thursday', 'order-delivery-date' ),
				  'orddd_weekday_5' => __( 'Friday', 'order-delivery-date' ),
				  'orddd_weekday_6' => __( 'Saturday', 'order-delivery-date' )
				  );


register_uninstall_hook( __FILE__, 'orddd_lite_deactivate' );
function orddd_lite_deactivate() {
    global $weekdays_orddd_lite;
    foreach ( $weekdays_orddd_lite as $n => $day_name ) {
        delete_option( $n );
    }
    delete_option( 'orddd_minimumOrderDays' );
    delete_option( 'orddd_number_of_dates' );
    delete_option( 'orddd_date_field_mandatory' );

    delete_option('orddd_admin_notices');
}

add_action('woocommerce_after_checkout_billing_form', 'orddd_lite_my_custom_checkout_field'); 

// For language translation
add_action( 'init', 'update_po_file' );
function  update_po_file(){
    $domain = 'order-delivery-date';
    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );
    if ( $loaded = load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '-' . $locale . '.mo' ) ) {
        return $loaded;
    } else {
        load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
    }
}

function orddd_lite_my_custom_checkout_field( $checkout ) {	

    global $weekdays_orddd_lite;
    wp_enqueue_script( 'jquery' );
    wp_deregister_script( 'jqueryui');
    wp_enqueue_script( 'jquery-ui-datepicker' );
    
    wp_enqueue_style( 'jquery-ui', "http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/smoothness/jquery-ui.css" , '', '', false);
    wp_enqueue_style( 'datepicker', plugins_url('/css/datepicker.css', __FILE__) , '', '', false);
    
    wp_enqueue_script(
		'initialize-datepicker.js',
		plugins_url('/js/initialize-datepicker.js', __FILE__),
		'',
		'',
		false
	);

	/*print(' <link rel="stylesheet" type="text/css" href="' . plugins_url() . '/order-delivery-date-for-woocommerce/datepicker.css">
		<script type="text/javascript" src="' . plugins_url() . '/order-delivery-date-for-woocommerce/datepicker.js"></script>'
	);
	print('<script type="text/javascript" src="' . plugins_url() . '/order-delivery-date-for-woocommerce/initialize-datepicker.js"></script>');*/

	echo '<script language="javascript">jQuery(document).ready(function(){
	jQuery("#e_deliverydate").width("150px");
	var formats = ["MM d, yy","MM d, yy"];
	jQuery("#e_deliverydate").val("").datepicker({dateFormat: formats[1], minDate:1, beforeShow: avd, beforeShowDay: chd});
	jQuery("#e_deliverydate").parent().append("<br><small style=font-size:10px;>'.__('We will try our best to deliver your order on the specified date','order-delivery-date').'</small>");
    });</script>';

	if ( get_option( 'orddd_date_field_mandatory' ) == 'checked' ) {
	   $validate_wpefield = true;
	} else {
		$validate_wpefield = '';
	}
	
	echo '<div id="my_custom_checkout_field" style="width: 202%; float: left;">'; 
        
//        $display = '
//		
//		<script type="text/javascript">
//        jQuery(function() {
//			var formats = ["MM d, yy","MM d, yy"];
//			jQuery("#e_deliverydate").width("150px");
//			jQuery("#e_deliverydate").val("").datepicker({dateFormat: formats[1], beforeShow: avd, beforeShowDay: chd});
//			jQuery("#e_deliverydate").parent().append("<small style=\'font-size:10px;\'>We will try our best to deliver your order on the specified date</small>");
//        });
//        </script>';
//        echo $display;

	woocommerce_form_field( 'e_deliverydate', array(        
				'type'          => 'text',        
				'label'         => __('Delivery Date','order-delivery-date'),		
				'required'  	=> $validate_wpefield,		
				'placeholder'       => __('Delivery Date','order-delivery-date'),        
				), 
	$checkout->get_value( 'e_deliverydate' ));       
	echo '</div>';
	
	$alldays_orddd_lite = array();
	foreach ($weekdays_orddd_lite as $n => $day_name) {
		$alldays_orddd_lite[$n] = get_option($n);
	}
	$alldayskeys_orddd_lite = array_keys($alldays_orddd_lite);
	$checked = "No";
	foreach($alldayskeys_orddd_lite as $key ) {
		if($alldays_orddd_lite[$key] == 'checked') {
			$checked = "Yes";
		}
	}
	if($checked == 'Yes') {
		foreach($alldayskeys_orddd_lite as $key) {
			print('<input type="hidden" id="'.$key.'" value="'.$alldays_orddd_lite[$key].'">');
		}
	}
	else if($checked == 'No') {
		foreach($alldayskeys_orddd_lite as $key) {
			print('<input type="hidden" id="'.$key.'" value="checked">');
		}
	}
    print('<input type="hidden" name="minimumOrderDays" id="minimumOrderDays" value="'.get_option('orddd_minimumOrderDays').'">');
	print('<input type="hidden" name="number_of_dates" id="number_of_dates" value="'.get_option('orddd_number_of_dates').'">');
	print('<input type="hidden" name="date_field_mandatory" id="date_field_mandatory" value="'.get_option('orddd_date_field_mandatory').'">');
}

add_action('woocommerce_checkout_update_order_meta', 'orddd_lite_my_custom_checkout_field_update_order_meta'); 

function orddd_lite_my_custom_checkout_field_update_order_meta( $order_id ) {    
	if ($_POST['e_deliverydate']) {
		update_post_meta( $order_id, 'Delivery Date', esc_attr($_POST['e_deliverydate']));
	}
}

/**
* This function is used for show delivery date in the email notification 
**/
add_filter('woocommerce_email_order_meta_keys', 'orddd_lite_add_delivery_date_to_order_woo',10,1);

function orddd_lite_add_delivery_date_to_order_woo( $keys ) {
    $label_name = __("Delivery Date","order-delivery-date");
    $keys[$label_name] = "Delivery Date";
    return $keys;
}
/**
 * This function are used for show custom column on order page listing. woo-orders
 * 
 */
add_filter( 'manage_edit-shop_order_columns', 'orddd_lite_woocommerce_order_delivery_date_column', 20, 1 );

function orddd_lite_woocommerce_order_delivery_date_column( $columns ) {
    $new_columns = (is_array($columns)) ? $columns : array();
    unset( $new_columns['order_actions'] );

    //edit this for you column(s)
    //all of your columns will be added before the actions column
    $new_columns['order_delivery_date'] = __('Delivery Date','order-delivery-date'); //Title for column heading
    $new_columns['order_actions'] = $columns['order_actions'];
    return $new_columns;
}

/**
 * This fnction used to add value on the custom column created on woo- order
 * 
 */
add_action( 'manage_shop_order_posts_custom_column', 'orddd_lite_woocommerce_custom_column_value', 20, 1 );

function orddd_lite_woocommerce_custom_column_value( $column ) {
    global $post;
    $data = get_post_meta( $post->ID );
    //start editing, I was saving my fields for the orders as custom post meta
    //if you did the same, follow this code
    if ( $column == 'order_delivery_date' ) {    
        echo (isset($data['Delivery Date'][0]) ? $data['Delivery Date'][0] : '');
    }
}

/**
 * Validate delivery date field
 **/

if ( get_option( 'orddd_date_field_mandatory' ) == 'checked' ) {
	add_action( 'woocommerce_checkout_process', 'validate_date_wpefield' );
}
function validate_date_wpefield() {
    global $woocommerce;
	
	// Check if set, if its not set add an error.
	if ( !$_POST['e_deliverydate']  ) {
    	$message = __( '<strong>'.__(('Delivery Date'),'order-delivery-date').'</strong> is a required field.', 'order-delivery-date' );
    	wc_add_notice( $message, $notice_type = 'error' );
	}
}

add_action( 'admin_init', 'order_lite_delivery_date_admin_settings');

function order_lite_delivery_date_admin_settings() {
    global $weekdays_orddd_lite;
    // First, we register a section. This is necessary since all future options must belong to one.
    add_settings_section(
        'orddd_lite_date_settings_section',		// ID used to identify this section and with which to register options
        __( 'Order Delivery Date Settings', 'order-delivery-date' ),		// Title to be displayed on the administration page
        'orddd_lite_delivery_date_setting',		// Callback used to render the description of the section
        'orddd_lite_date_settings_page'				// Page on which to add this section of options
    );
    
     add_settings_field(
        'orddd_delivery_days',
        __('Delivery Days:','order-delivery-date'),
        'orddd_lite_delivery_days_callback',
        'orddd_lite_date_settings_page',
        'orddd_lite_date_settings_section',
        array ( '&nbsp;'.__( 'Select weekdays for delivery.', 'order-delivery-date' ) )
     );
     
     add_settings_field(
        'orddd_minimumOrderDays',
        __('Minimum Delivery time (in days):','order-delivery-date'),
        'orddd_lite_minimum_delivery_time_callback',
        'orddd_lite_date_settings_page',
        'orddd_lite_date_settings_section',
        array ( __('Minimum number of days required to prepare for delivery.', 'order-delivery-date' ))
     );
     
     add_settings_field(
        'orddd_number_of_dates',
        __('Number of dates to choose:','order-delivery-date'),
        'orddd_lite_number_of_dates_callback',
        'orddd_lite_date_settings_page',
        'orddd_lite_date_settings_section',
        array ( __('Number of dates available for delivery.', 'order-delivery-date' ))
     );
     
     add_settings_field(
        'orddd_date_field_mandatory',
        __('Mandatory field?:', 'order-delivery-date'),
        'orddd_lite_date_field_mandatory_callback',
        'orddd_lite_date_settings_page',
        'orddd_lite_date_settings_section',
        array ( __('Selection of delivery date on the checkout page will become mandatory.', 'order-delivery-date' ))
     );
     
     foreach ( $weekdays_orddd_lite as $n => $day_name ) {
         register_setting(
            'orddd_lite_date_settings',
            $n
         );
     }
     register_setting(
        'orddd_lite_date_settings',
        'orddd_minimumOrderDays'
     );
     
     register_setting(
        'orddd_lite_date_settings',
        'orddd_number_of_dates'
     );
          
     register_setting(
        'orddd_lite_date_settings',
        'orddd_date_field_mandatory'
     );
}

function orddd_lite_delivery_date_setting() { }
// ************************ 8 ******************************

//Code to create the settings page for the plugin
add_action('admin_menu', 'orddd_lite_order_delivery_date_menu');
function orddd_lite_order_delivery_date_menu()
{
	add_menu_page( 'Order Delivery Date','Order Delivery Date','administrator', 'order_delivery_date','orddd_lite_order_delivery_date_settings');
}

function orddd_lite_order_delivery_date_settings(){
	global $weekdays_orddd_lite;
	settings_errors();
    if(isset($_POST['save_orddd_lite'])&& $_POST['save_orddd_lite']!= "")
    {
            print('<div id="message" class="updated"><p>All changes have been saved.</p></div>');
    }
    
    print( '<div id="content">
        <form method="post" action="options.php">');
            settings_fields( "orddd_lite_date_settings" );
            do_settings_sections( "orddd_lite_date_settings_page" );
            submit_button ( __( 'Save Settings', 'order-delivery-date' ), 'primary', 'save_orddd_lite', true );
        print('</form>
    </div>');
}

function orddd_lite_delivery_days_callback( $args ) {   
    global $weekdays_orddd_lite;
    printf(
        '<fieldset class="days-fieldset" style="width:150px;">
        <legend><b>'.__( 'Weekdays:', 'order-delivery-date' ).'</b></legend>'
    );
        $html = '';
        printf('<table>');
            foreach ( $weekdays_orddd_lite as $n => $day_name ) {
                printf('
        	       <tr>
        	       <td style="padding: 0.5px 0.5px;"><input type="checkbox" name="'.$n.'" id="'.$n.'" value="checked" '.get_option( $n ).'/></td>
        	       <td style="padding: 0.5px 0.5px;"><label class="ord_label" for="'.$day_name.'">'.__($day_name,'order-delivery-date').'</label></td>'
                );
            }
        printf( '</table>
        </fieldset>');
    
        $html .= '<label for="orddd_delivery_days"> '  . $args[0] . '</label>';
        echo $html;
        
}

function orddd_lite_minimum_delivery_time_callback( $args ) {
    printf('
        <input type="text" name="orddd_minimumOrderDays" id="orddd_minimumOrderDays" style="width: 75px;" value="'.get_option( 'orddd_minimumOrderDays' ).'"/>'
    );
    $html = '<label for="orddd_minimumOrderDays"> '  . $args[0] . '</label>';
    echo $html;
}

function orddd_lite_number_of_dates_callback( $args ) {
    printf(
        '<input type="text" name="orddd_number_of_dates" id="orddd_number_of_dates" style="width: 75px;" value="'.get_option( 'orddd_number_of_dates' ).'"/>'
    );
    $html = '<label for="orddd_number_of_dates"> '  . $args[0] . '</label>';
    echo $html;
}

function orddd_lite_date_field_mandatory_callback( $args ) {
    printf(
        '<input type="checkbox" name="orddd_date_field_mandatory" id="orddd_date_field_mandatory" class="day-checkbox" value="checked" '.get_option( 'orddd_date_field_mandatory' ).' />'
    );
    
    $html = '<label for="orddd_date_field_mandatory"> '. $args[0] . '</label>';
    echo $html;
}

add_action( 'admin_enqueue_scripts', 'orddd_lite_my_enqueue' );

function orddd_lite_my_enqueue( $hook ) {
    
    if( 'toplevel_page_order_delivery_date' != $hook )
        return;
	wp_enqueue_script(
		'jquery-ui',
		'http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js',
		'',
		'',
		false
	);
	wp_enqueue_style( 'order-delivery-date', plugins_url('/css/order-delivery-date.css', __FILE__ ) , '', '', false);
    wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_style( 'jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css' , '', '', false);
	wp_enqueue_script(
			'jquery-min',
			'http://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js',
			'',
			'',
			false
	);
    wp_enqueue_script(
	   'jquery-ui-min',
	   'http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js',
	   '',
	   '',
	   false
	);
	wp_enqueue_script(
        'jquery-tip',
        plugins_url('/js/jquery.tipTip.minified.js', __FILE__),
        '',
        '',
        false
    );
	wp_enqueue_style( 'woocommerce_admin_styles', plugins_url() . '/woocommerce/assets/css/admin.css' );
	wp_register_script( 'woocommerce_admin', plugins_url() . '/woocommerce/assets/js/admin/woocommerce_admin.js', array('jquery', 'jquery-ui-widget', 'jquery-ui-core'));
	wp_enqueue_script( 'woocommerce_admin' );
	
	
	//<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js"></script>
    //<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.10/jquery-ui.min.js"></script>
    //<script type="text/javascript" src="jquery.themeswitcher.js"></script>

}

add_action( 'admin_footer', 'admin_notices_scripts' );

function admin_notices_scripts() {    
    wp_enqueue_script(
    'dismiss-notice.js',
    plugins_url('/js/dismiss-notice.js', __FILE__),
    '',
    '',
    false
    );
    
    wp_enqueue_style( 'dismiss-notice', plugins_url('/css/dismiss-notice.css', __FILE__ ) , '', '', false);
    
    wp_enqueue_script(
    'jquery-ui-min',
    'http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js',
    '',
    '',
    false
    );
}

add_filter('woocommerce_order_details_after_order_table','orddd_lite_add_delivery_date_to_order_page_woo');

function orddd_lite_add_delivery_date_to_order_page_woo( $order ) {
	$my_order_meta = get_post_custom( $order->id );
	if( array_key_exists( 'Delivery Date', $my_order_meta)) {
		$order_page_delivery_date = $my_order_meta['Delivery Date'];
		if ( $order_page_delivery_date != "" ) {
			echo '<p><strong>'.__(('Delivery Date'),'order-delivery-date').':</strong> ' . $order_page_delivery_date[0] . '</p>';
		}
	} 
 }

add_action( 'admin_notices', 'order_lite_coupon_notice');

function order_lite_coupon_notice() { 
    $admin_url = get_admin_url();
    echo '<input type="hidden" id="admin_url" value="'.$admin_url.'"/>';
    $admin_notice = get_option('orddd_admin_notices');
    if($admin_notice != 'yes') {
        ?>
        <div class="updated notice is-dismissible" >
            <p><?php _e( 'You can upgrade to the <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/">PRO version of Order Delivery Date for WooCommerce plugin</a> at a <b>20% discount</b>. Use the coupon code: <b>ORDPRO20</b>.<a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/"> Purchase now </a> & save $20!', 'order-delivery-date' ); ?></p>
        </div>   
   	    <?php
    }
}

add_action('wp_ajax_admin_notices','admin_notices');

function admin_notices() {
   update_option('orddd_admin_notices','yes');   
   die();
}
?>
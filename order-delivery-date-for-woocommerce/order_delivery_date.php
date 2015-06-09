<?php 

/*
Plugin Name: Order Delivery Date for Woocommerce (Lite version)
Plugin URI: http://www.tychesoftwares.com/store/free-plugin/order-delivery-date-on-checkout/
Description: This plugin allows customers to choose their preferred Order Delivery Date during checkout.
Author: Ashok Rane
Version: 1.4
Author URI: http://www.tychesoftwares.com/about
Contributor: Tyche Softwares, http://www.tychesoftwares.com/
*/

$wpefield_version = '1.4';

global $weekdays_orddd_lite;

$weekdays_orddd_lite = array('orddd_weekday_0' => 'Sunday',
				  'orddd_weekday_1' => 'Monday',
				  'orddd_weekday_2' => 'Tuesday',
				  'orddd_weekday_3' => 'Wednesday',
				  'orddd_weekday_4' => 'Thursday',
				  'orddd_weekday_5' => 'Friday',
				  'orddd_weekday_6' => 'Saturday'
				  );



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
	jQuery("#e_deliverydate").parent().append("<br><small style=font-size:10px;>We will try our best to deliver your order on the specified date</small>");
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

				'label'         => __('Delivery Date'),		

				'required'  	=> $validate_wpefield,		

				'placeholder'       => __('Delivery Date'),        

				), 

				$checkout->get_value( 'e_deliverydate' ));     

				echo '</div>';
                                
                                global $weekdays_orddd_lite;
	
	$alldays_orddd_lite = array();
	
	foreach ($weekdays_orddd_lite as $n => $day_name) 
	{
		$alldays_orddd_lite[$n] = get_option($n);
	}
	
	$alldayskeys_orddd_lite = array_keys($alldays_orddd_lite);
	
        $checked = "No";
	foreach($alldayskeys_orddd_lite as $key)
	{
		if($alldays_orddd_lite[$key] == 'checked')
		{
			$checked = "Yes";
		}
	}
	if($checked == 'Yes')
	{
		foreach($alldayskeys_orddd_lite as $key)
		{
			print('<input type="hidden" id="'.$key.'" value="'.$alldays_orddd_lite[$key].'">');
		}
	}
	else if($checked == 'No')
	{
		foreach($alldayskeys_orddd_lite as $key)
		{
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

function orddd_lite_add_delivery_date_to_order_woo( $keys )
{
       $keys[] = "Delivery Date";
       return $keys;
}
/**
 * This function are used for show custom column on order page listing. woo-orders
 * 
 */
add_filter( 'manage_edit-shop_order_columns', 'orddd_lite_woocommerce_order_delivery_date_column', 20, 1 );
function orddd_lite_woocommerce_order_delivery_date_column($columns){
$new_columns = (is_array($columns)) ? $columns : array();
unset( $new_columns['order_actions'] );

//edit this for you column(s)
//all of your columns will be added before the actions column
$new_columns['order_delivery_date'] = 'Delivery Date'; //Title for column heading
$new_columns['order_actions'] = $columns['order_actions'];
return $new_columns;
}

/**
 * This fnction used to add value on the custom column created on woo- order
 * 
 */
add_action( 'manage_shop_order_posts_custom_column', 'orddd_lite_woocommerce_custom_column_value', 20, 1 );
function orddd_lite_woocommerce_custom_column_value($column){

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

// ************************ 8 ******************************

//Code to create the settings page for the plugin
add_action('admin_menu', 'orddd_lite_order_delivery_date_menu');
function orddd_lite_order_delivery_date_menu()
{
	add_menu_page( 'Order Delivery Date','Order Delivery Date','administrator', 'order_delivery_date','orddd_lite_order_delivery_date_settings');
}
function orddd_lite_order_delivery_date_settings(){
	
    global $weekdays_orddd_lite;
    if(isset($_POST['save_orddd_lite'])&& $_POST['save_orddd_lite']!= "")
    {
            print('<div id="message" class="updated"><p>All changes have been saved.</p></div>');
    }
        print('<br />
		<div id="order-delivery-date-settings">
			<div class="ino_titlee"><h3 class="ord_h3">'.__( 'Order Delivery Date Settings', 'order-delivery-date').'</h3></div>
                        
				<form id="order-delivery-date-settings-form" name="order-delivery-date-settings" method="post">
					<input type="hidden" name="action" value="">
                                    '); ?>
						
					<?php 
					
					
				print('<div id="ord_common">
						<label class="ord_label" class="ord_label" for="delivery-days-tf">'.__('Delivery Days:', 'order-delivery-date').'</label>
						<fieldset class="days-fieldset" style="width:190px;">
							');
		
		foreach ($weekdays_orddd_lite as $n => $day_name)
		{
			print('<input type="checkbox" name="'.$n.'" id="'.$n.'" class="day-checkbox" value="checked" '.get_option($n).' " />
					<label class="ord_label" for="'.$day_name.'">'__($day_name, 'order-delivery-date')'</label>');
			print('<br>');
		}
		
		print('</fieldset>
				<div id="help">');
						?>
						<img class="help_tip" width="16" height="16" data-tip="<?php _e( 'Select the weekdays when the delivery of items takes place. <br>For example, if you deliver only on Tuesday, Wednesday, <br>Thursday & Friday, then select only those days here. The <br>remaining days will not be available for selection to the <br>customer.', 'order-delivery-date' );?>" src="<?php echo plugins_url() ;?>/woocommerce/assets/images/help.png" />
						<?php print('</div>
						<!--<div id="help">Select the weekdays when the delivery of items takes place. For example, if you deliver only on Tuesday, Wednesday, Thursday & Friday, then select only those days here. The remaining days will not be available for selection to the customer.</div>-->
					</div>

					<div id="ord_common">
						<label class="ord_label" for="order-delay-days-tf">'.__('Minimum Delivery time (in days):', 'order-delivery-date').'</label>
						<input type="text" name="minimumOrderDays" id="minimumOrderDays" value="'.get_option('orddd_minimumOrderDays').'"/>
						<div id="help">');
						?>
						<img class="help_tip" width="16" height="16" data-tip="<?php _e( 'Enter the minimum number of days it takes for you to deliver <br>an order. For example, if it takes 2 days atleast to ship an <br>order, enter 2 here. The customer can select a date that is <br>available only after the minimum days that are entered here.', 'order-delivery-date' );?>" src="<?php echo plugins_url() ;?>/woocommerce/assets/images/help.png" />
						<?php print('
						</div>
					</div>
					<div id="ord_common">
						<label class="ord_label" for="number_of_dates">'.__('Number of dates to choose:', 'order-delivery-date').'</label>
						<input type="text" name="number_of_dates" id="number_of_dates" value="'.get_option('orddd_number_of_dates').'"/>
						<div id="help">');
						?>
						<img class="help_tip" width="16" height="16" data-tip="<?php _e( 'Based on the above 2 settings, you can decide how many dates should be made available to the customer to choose from. For example, if you enter 10, then 10 different dates will be made available to the customer to choose.', 'order-delivery-date' );?>" src="<?php echo plugins_url() ;?>/woocommerce/assets/images/help.png" />
						<?php print('</div>
						<!--<div id="help">Based on the above 2 settings, you can decide how many dates should be made available to the customer to choose from. For example, if you enter 10, then 10 different dates will be made available to the customer to choose.</div>-->
					</div>
					<div id="ord_common">
						<label class="ord_label" for="date_field_mandatory">'.__('Mandatory field?:', 'order-delivery-date').'</label>
							<input type="checkbox" name="date_field_mandatory" id="date_field_mandatory" 
							class="day-checkbox" value="checked" '.get_option( 'orddd_date_field_mandatory' ).' />
								<div id="help">' );
						?>
									<img class="help_tip" width="16" height="16" data-tip="<?php  _e( "Check this option if you want to make the Delivery Date field <br>mandatory on the checkout page. Users will not be able to <br>place their orders unless the date is selected.", "order-delivery-date" );?>" src="<?php echo plugins_url() ;?>/woocommerce/assets/images/help.png" />
						<?php print( '</div></div>
					');


		print ('<div class="submit_button"><span class="submit"><input type="submit" value="'.__("Save changes","order-delivery-date").'" name="save_orddd_lite"/></span></div>
				</form>
			</div>');
    }

if(isset($_POST['save_orddd_lite'])){
            foreach ($weekdays_orddd_lite as $n => $day_name)
            {
                    if(isset($_POST[$n]))
                {
                        update_option($n,$_POST[$n]);

                }
                else
                {
                        update_option($n,'');
                }
            }
            update_option('orddd_minimumOrderDays',$_POST['minimumOrderDays']);
            update_option('orddd_number_of_dates',$_POST['number_of_dates']);
			if ( isset( $_POST['date_field_mandatory'] ) ) {
				update_option('orddd_date_field_mandatory', $_POST['date_field_mandatory']);
			} else {
				update_option('orddd_date_field_mandatory', '');
			}
        }
        
function orddd_lite_my_enqueue($hook)
{
	//echo $hook;
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
			'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.0/jquery-ui.min.js',
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
add_action( 'admin_enqueue_scripts', 'orddd_lite_my_enqueue' );

add_filter('woocommerce_order_details_after_order_table','orddd_lite_add_delivery_date_to_order_page_woo');

function orddd_lite_add_delivery_date_to_order_page_woo($order) 
{
	$my_order_meta = get_post_custom( $order->id );
	if(array_key_exists('Delivery Date',$my_order_meta))
	{
		$order_page_delivery_date = $my_order_meta['Delivery Date'];
		if ( $order_page_delivery_date != "" )
		{
			echo '<p><strong>'.__(('Delivery Date'),'order-delivery-date').':</strong> ' . $order_page_delivery_date[0] . '</p>';
		}
	} 
 }

?>
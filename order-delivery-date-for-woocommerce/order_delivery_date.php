<?php 

/*
Plugin Name: Order Delivery Date for Woocommerce (Lite version)
Plugin URI: http://www.tychesoftwares.com/store/free-plugin/order-delivery-date-on-checkout/
Description: This plugin allows customers to choose their preferred Order Delivery Date during checkout.
Author: Ashok Rane
Version: 1.2
Author URI: http://www.tychesoftwares.com/about
Contributor: Tyche Softwares, http://www.tychesoftwares.com/
*/

$wpefield_version = '1.2';

global $weekdays;

$weekdays = array('orddd_weekday_0' => 'Sunday',
				  'orddd_weekday_1' => 'Monday',
				  'orddd_weekday_2' => 'Tuesday',
				  'orddd_weekday_3' => 'Wednesday',
				  'orddd_weekday_4' => 'Thursday',
				  'orddd_weekday_5' => 'Friday',
				  'orddd_weekday_6' => 'Saturday'
				  );


add_action('woocommerce_after_checkout_billing_form', 'my_custom_checkout_field'); 

function my_custom_checkout_field( $checkout ) {	

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

				'required'  	=> false,		

				'placeholder'       => __('Delivery Date'),        

				), 

				$checkout->get_value( 'e_deliverydate' ));     

				echo '</div>';
                                
                                global $weekdays;
	
	$alldays = array();
	
	foreach ($weekdays as $n => $day_name) 
	{
		$alldays[$n] = get_option($n);
	}
	
	$alldayskeys = array_keys($alldays);
	
        $checked = "No";
	foreach($alldayskeys as $key)
	{
		if($alldays[$key] == 'checked')
		{
			$checked = "Yes";
		}
	}
	if($checked == 'Yes')
	{
		foreach($alldayskeys as $key)
		{
			print('<input type="hidden" id="'.$key.'" value="'.$alldays[$key].'">');
		}
	}
	else if($checked == 'No')
	{
		foreach($alldayskeys as $key)
		{
			print('<input type="hidden" id="'.$key.'" value="checked">');
		}
	}
        print('<input type="hidden" name="minimumOrderDays" id="minimumOrderDays" value="'.get_option('orddd_minimumOrderDays').'">');
	print('<input type="hidden" name="number_of_dates" id="number_of_dates" value="'.get_option('orddd_number_of_dates').'">');

}

add_action('woocommerce_checkout_update_order_meta', 'my_custom_checkout_field_update_order_meta'); 

function my_custom_checkout_field_update_order_meta( $order_id ) {    

	if ($_POST['e_deliverydate']) {

		update_post_meta( $order_id, 'Delivery Date', esc_attr($_POST['e_deliverydate']));

	}
	
}

/**
 * This function are used for show custom column on order page listing. woo-orders
 * 
 */
add_filter( 'manage_edit-shop_order_columns', 'woocommerce_order_delivery_date_column', 20, 1 );
function woocommerce_order_delivery_date_column($columns){
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
add_action( 'manage_shop_order_posts_custom_column', 'woocommerce_custom_column_value', 20, 1 );
function woocommerce_custom_column_value($column){

    global $post;
    
    $data = get_post_meta( $post->ID );
    //start editing, I was saving my fields for the orders as custom post meta
    //if you did the same, follow this code
    if ( $column == 'order_delivery_date' ) {    
        echo (isset($data['Delivery Date'][0]) ? $data['Delivery Date'][0] : '');
    }
}

// ************************ 8 ******************************

//Code to create the settings page for the plugin
add_action('admin_menu', 'order_delivery_date_menu');
function order_delivery_date_menu()
{
	add_menu_page( 'Order Delivery Date','Order Delivery Date','administrator', 'order_delivery_date','order_delivery_date_settings');
}
function order_delivery_date_settings(){
	
    global $weekdays;
    if(isset($_POST['save'])&& $_POST['save']!= "")
    {
            print('<div id="message" class="updated"><p>All changes have been saved.</p></div>');
    }
        print('<br />
		<div id="order-delivery-date-settings">
			<div class="ino_titlee"><h3 class="ord_h3">Order Delivery Date Settings</h3></div>
                        
				<form id="order-delivery-date-settings-form" name="order-delivery-date-settings" method="post">
					<input type="hidden" name="action" value="">
                                    '); ?>
						
					<?php 
					
					
				print('<div id="ord_common">
						<label class="ord_label" class="ord_label" for="delivery-days-tf">Delivery Days: </label>
						<fieldset class="days-fieldset" style="width:190px;">
							');
		
		foreach ($weekdays as $n => $day_name)
		{
			print('<input type="checkbox" name="'.$n.'" id="'.$n.'" class="day-checkbox" value="checked" '.get_option($n).' " />
					<label class="ord_label" for="'.$day_name.'">'.$day_name.'</label>');
			print('<br>');
		}
		
		print('</fieldset>
				<div id="help">');
						?>
						<img class="help_tip" width="16" height="16" data-tip="<?php echo 'Select the weekdays when the delivery of items takes place. <br>For example, if you deliver only on Tuesday, Wednesday, <br>Thursday & Friday, then select only those days here. The <br>remaining days will not be available for selection to the <br>customer.';?>" src="<?php echo plugins_url() ;?>/woocommerce/assets/images/help.png" />
						<?php print('</div>
						<!--<div id="help">Select the weekdays when the delivery of items takes place. For example, if you deliver only on Tuesday, Wednesday, Thursday & Friday, then select only those days here. The remaining days will not be available for selection to the customer.</div>-->
					</div>

					<div id="ord_common">
						<label class="ord_label" for="order-delay-days-tf">Minimum Delivery time (in days): </label>
						<input type="text" name="minimumOrderDays" id="minimumOrderDays" value="'.get_option('orddd_minimumOrderDays').'"/>
						<div id="help">');
						?>
						<img class="help_tip" width="16" height="16" data-tip="<?php echo 'Enter the minimum number of days it takes for you to deliver <br>an order. For example, if it takes 2 days atleast to ship an <br>order, enter 2 here. The customer can select a date that is <br>available only after the minimum days that are entered here.';?>" src="<?php echo plugins_url() ;?>/woocommerce/assets/images/help.png" />
						<?php print('
						</div>
					</div>
					<div id="ord_common">
						<label class="ord_label" for="number_of_dates">Number of dates to choose: </label>
						<input type="text" name="number_of_dates" id="number_of_dates" value="'.get_option('orddd_number_of_dates').'"/>
						<div id="help">');
						?>
						<img class="help_tip" width="16" height="16" data-tip="<?php echo 'Based on the above 2 settings, you can decide how many dates should be made available to the customer to choose from. For example, if you enter 10, then 10 different dates will be made available to the customer to choose.';?>" src="<?php echo plugins_url() ;?>/woocommerce/assets/images/help.png" />
						<?php print('</div>
						<!--<div id="help">Based on the above 2 settings, you can decide how many dates should be made available to the customer to choose from. For example, if you enter 10, then 10 different dates will be made available to the customer to choose.</div>-->
					</div>
					');


		print ('<div class="submit_button"><span class="submit"><input type="submit" value="Save changes" name="save"/></span></div>
				</form>
			</div>');
    }

if(isset($_POST['save'])){
            foreach ($weekdays as $n => $day_name)
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
        }
        
function my_enqueue($hook)
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
add_action( 'admin_enqueue_scripts', 'my_enqueue' );

?>
<?php
/**
* Order Delivery Date for WooCommerce Lite
*
* Add notices of Pro plugin in some interval of days 
*
* @author      Tyche Softwares
* @package     Order-Delivery-Date-Lite-for-WooCommerce/Admin/Pro-Notices
* @since       3.0
*/

/**
 * Class for adding notices of Pro plugin in some interval of days 
 */

class orddd_lite_pro_notices {

	/**
	 * Add notices of Pro features in the admin in the interval of 15, 30 and 45 days
	 * 
	 * @hook admin_notices
	 * @since 3.0
	 */	
	public static function orddd_lite_notices_of_pro() {
		$orddd_lite_activate_time = get_option ( 'orddd_lite_activate_time' );
        $orddd_lite_sixty_days    = strtotime( '+60 Days', $orddd_lite_activate_time );
        $current_time = current_time( 'timestamp' );

        if( !is_plugin_active( 'order-delivery-date/order_delivery_date.php' ) && 
            ( false === $orddd_lite_activate_time || ( $orddd_lite_activate_time > 0 && $current_time >= $orddd_lite_sixty_days ) ) ) {
        	global $current_user ;
			$user_id = $current_user->ID;
			
			// 15 (post delivery addon), 22 (ac lite), 30, 37 (post delivery addon) Days
			if ( ! get_user_meta( get_current_user_id(), 'orddd_pro_first_notice_ignore' ) ) {
			
				$class = 'updated notice-info point-notice';
				$style = 'position:relative';
				
				$orddd_pro_link = 'https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/checkout?edd_action=add_to_cart&download_id=16&utm_source=wpnotice&utm_medium=first&utm_campaign=OrderDeliveryDateLitePlugin';

			    $message = wp_kses_post ( __( 'Thank you for using Order Delivery Date for WooCommerce! Never login to your admin to check your deliveries by syncing the delivery dates to the Google Calendar from Order Delivery Date Pro for WooCommerce. <strong><a target="_blank" href= "'.$orddd_pro_link.'">Get it now!</a></strong>', 'order-delivery-date' ) );

			    $add_query_arguments = add_query_arg( 'orddd_pro_first_notice_ignore', '0' );
			    $cancel_button = '<a href="'.$add_query_arguments.'" class="dashicons dashicons-dismiss dashicons-dismiss-icon" style="position: absolute; top: 8px; right: 8px; color: #222; opacity: 0.4; text-decoration: none !important;"></a>';
				printf( '<div class="%1$s" style="%2$s"><p>%3$s %4$s</p></div>', $class, $style, $message, $cancel_button );
			}

			if ( get_user_meta( get_current_user_id(), 'orddd_pro_first_notice_ignore' ) &&  
				! get_user_meta( get_current_user_id(), 'orddd_pro_second_notice_ignore' ) &&
				! is_plugin_active( 'post-purchase-experience/post-purchase-experience.php' ) ) {

				$orddd_first_ignore_time = get_user_meta( get_current_user_id(), 'orddd_pro_first_notice_ignore_time' );
				$orddd_fifteen_days = strtotime( '+15 Days', $orddd_first_ignore_time[0] );

				if ( $current_time > $orddd_fifteen_days ){
					$class = 'updated notice-info point-notice';
					$style = 'position:relative';

                    $post_purchase_link = 'https://www.tychesoftwares.com/store/premium-plugins/post-delivery-product-reviews-addon-order-delivery-date-woocommerce/checkout?edd_action=add_to_cart&download_id=278278&utm_source=wpnotice&utm_medium=second&utm_campaign=OrderDeliveryDateLitePlugin';

                    $message = wp_kses_post ( __( 'Send Product review emails to the customers on the next day of delivery using Post Delivery Product Reviews Addon for Order Delivery Date plugin. <strong><a target="_blank" href= "'.$post_purchase_link.'">Have it now!</a></strong>', 'order-delivery-date' ) );

				    $add_query_arguments = add_query_arg( 'orddd_pro_second_notice_ignore', '0' );
				    $cancel_button = '<a href="'.$add_query_arguments.'" class="dashicons dashicons-dismiss dashicons-dismiss-icon" style="position: absolute; top: 8px; right: 8px; color: #222; opacity: 0.4; text-decoration: none !important;"></a>';
					printf( '<div class="%1$s" style="%2$s"><p>%3$s %4$s</p></div>', $class, $style, $message, $cancel_button );
				}
			} 

			if ( get_user_meta( get_current_user_id(), 'orddd_pro_first_notice_ignore' ) &&  
				! get_user_meta( get_current_user_id(), 'orddd_pro_third_notice_ignore' ) &&
				is_plugin_active( 'post-purchase-experience/post-purchase-experience.php' ) &&
				! is_plugin_active( 'woocommerce-abandon-cart-pro/woocommerce-ac.php' ) && 
				! is_plugin_active( 'woocommerce-abandoned-cart/woocommerce-ac.php' ) ) {
				
				$orddd_first_ignore_time = get_user_meta( get_current_user_id(), 'orddd_pro_first_notice_ignore_time' );
				$orddd_fifteen_days = strtotime( '+15 Days', $orddd_first_ignore_time[0] );

				if ( $current_time > $orddd_fifteen_days ){
					$class = 'updated notice-info point-notice';
					$style = 'position:relative';

                    $orddd_wcal_lite_link = admin_url( '/plugin-install.php?s=abandoned+cart+tyche+softwares&tab=search&type=term' );

                    $message = wp_kses_post ( __( 'Boost your sales by recovering the abandoned carts with our FREE Abandoned Cart for WooCommerce plugin. <strong><a target="_blank" href= "'.$orddd_wcal_lite_link.'">Install it now.</a></strong>', 'order-delivery-date' ) );

				    $add_query_arguments = add_query_arg( 'orddd_pro_third_notice_ignore', '0' );
				    $cancel_button = '<a href="'.$add_query_arguments.'" class="dashicons dashicons-dismiss dashicons-dismiss-icon" style="position: absolute; top: 8px; right: 8px; color: #222; opacity: 0.4; text-decoration: none !important;"></a>';
					printf( '<div class="%1$s" style="%2$s"><p>%3$s %4$s</p></div>', $class, $style, $message, $cancel_button );
				}
			} 

			if ( get_user_meta( get_current_user_id(), 'orddd_pro_first_notice_ignore' ) &&  
				! get_user_meta( get_current_user_id(), 'orddd_pro_fourth_notice_ignore' ) &&
				is_plugin_active( 'post-purchase-experience/post-purchase-experience.php' ) &&
				( is_plugin_active( 'woocommerce-abandon-cart-pro/woocommerce-ac.php' ) || 
				is_plugin_active( 'woocommerce-abandoned-cart/woocommerce-ac.php' ) ) ) {
				
				$orddd_first_ignore_time = get_user_meta( get_current_user_id(), 'orddd_pro_first_notice_ignore_time' );
				$orddd_fifteen_days = strtotime( '+15 Days', $orddd_first_ignore_time[0] );

				if ( $current_time > $orddd_fifteen_days ){
					$class = 'updated notice-info point-notice';
					$style = 'position:relative';

                    $orddd_pro_link = 'https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/checkout?edd_action=add_to_cart&download_id=16&utm_source=wpnotice&utm_medium=fourth&utm_campaign=OrderDeliveryDateLitePlugin';

                    $message = wp_kses_post ( __( 'Create Delivery Settings by Shipping Zones & Shipping Classes using Order Delivery Date Pro for WooCommerce plugin. <br>Use discount code "ORDPRO20" and grab 20% discount on the purchase of the plugin. The discount code is valid only for the first 20 customers. <strong><a target="_blank" href= "'.$orddd_pro_link.'">Purchase now</a></strong>.', 'order-delivery-date' ) );

				    $add_query_arguments = add_query_arg( 'orddd_pro_fourth_notice_ignore', '0' );
				    $cancel_button = '<a href="'.$add_query_arguments.'" class="dashicons dashicons-dismiss dashicons-dismiss-icon" style="position: absolute; top: 8px; right: 8px; color: #222; opacity: 0.4; text-decoration: none !important;"></a>';
					printf( '<div class="%1$s" style="%2$s"><p>%3$s %4$s</p></div>', $class, $style, $message, $cancel_button );
				}
			}

			// Ac Lite //
			if ( get_user_meta( get_current_user_id(), 'orddd_pro_first_notice_ignore' ) &&
				 get_user_meta( get_current_user_id(), 'orddd_pro_second_notice_ignore' ) &&
				! get_user_meta( get_current_user_id(), 'orddd_pro_third_notice_ignore' ) &&
				! is_plugin_active( 'woocommerce-abandon-cart-pro/woocommerce-ac.php' ) && 
				! is_plugin_active( 'woocommerce-abandoned-cart/woocommerce-ac.php' ) ) {

				$orddd_second_ignore_time = get_user_meta( get_current_user_id(), 'orddd_pro_second_notice_ignore_time' );
				$orddd_seven_days = strtotime( '+7 Days', $orddd_second_ignore_time[0] );				

				if ( $current_time > $orddd_seven_days ){
					$class = 'updated notice-info point-notice';
					$style = 'position:relative';

                    $orddd_wcal_lite_link = admin_url( '/plugin-install.php?s=abandoned+cart+tyche+softwares&tab=search&type=term' );

                    $message = wp_kses_post ( __( 'Boost your sales by recovering the abandoned carts with our FREE Abandoned Cart for WooCommerce plugin. <strong><a target="_blank" href= "'.$orddd_wcal_lite_link.'">Install it now.</a></strong>.', 'order-delivery-date' ) );
				    $add_query_arguments = add_query_arg( 'orddd_pro_third_notice_ignore', '0' );
				    $cancel_button = '<a href="'.$add_query_arguments.'" class="dashicons dashicons-dismiss dashicons-dismiss-icon" style="position: absolute; top: 8px; right: 8px; color: #222; opacity: 0.4; text-decoration: none !important;"></a>';
					printf( '<div class="%1$s" style="%2$s"><p>%3$s %4$s</p></div>', $class, $style, $message, $cancel_button );
				}
			}

            if ( get_user_meta( get_current_user_id(), 'orddd_pro_first_notice_ignore' ) &&
                 get_user_meta( get_current_user_id(), 'orddd_pro_second_notice_ignore' ) &&
                 ! get_user_meta( get_current_user_id(), 'orddd_pro_fourth_notice_ignore' ) &&
                 ( is_plugin_active( 'woocommerce-abandon-cart-pro/woocommerce-ac.php' ) ||
                 is_plugin_active( 'woocommerce-abandoned-cart/woocommerce-ac.php' ) ) ) {

            	$orddd_second_ignore_time = get_user_meta( get_current_user_id(), 'orddd_pro_second_notice_ignore_time' );
                $orddd_fifteen_days = strtotime( '+15 Days', $orddd_second_ignore_time[0] );

                if ( $current_time > $orddd_fifteen_days ) {
                    $class = 'updated notice-info point-notice';
                    $style = 'position:relative';

                    $orddd_pro_link = 'https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/checkout?edd_action=add_to_cart&download_id=16&utm_source=wpnotice&utm_medium=fourth&utm_campaign=OrderDeliveryDateLitePlugin';

                    $message = wp_kses_post ( __( 'Create Delivery Settings by Shipping Zones & Shipping Classes using Order Delivery Date Pro for WooCommerce plugin. <br>Use discount code "ORDPRO20" and grab 20% discount on the purchase of the plugin. The discount code is valid only for the first 20 customers. <strong><a target="_blank" href= "'.$orddd_pro_link.'">Purchase now</a></strong>.', 'order-delivery-date' ) );

                    $add_query_arguments = add_query_arg( 'orddd_pro_fourth_notice_ignore', '0' );
                    
                    $cancel_button = '<a href="'.$add_query_arguments.'" class="dashicons dashicons-dismiss dashicons-dismiss-icon" style="position: absolute; top: 8px; right: 8px; color: #222; opacity: 0.4; text-decoration: none !important    ;"></a>';
                    printf( '<div class="%1$s" style="%2$s"><p>%3$s %4$s</p></div>', $class, $style, $message, $cancel_button );
                }
            }

            if ( get_user_meta( get_current_user_id(), 'orddd_pro_first_notice_ignore' ) &&
                get_user_meta( get_current_user_id(), 'orddd_pro_second_notice_ignore' ) &&
                get_user_meta( get_current_user_id(), 'orddd_pro_third_notice_ignore' ) &&
                ! get_user_meta( get_current_user_id(), 'orddd_pro_fourth_notice_ignore' ) ) {
            	
            	$orddd_third_ignore_time = get_user_meta( get_current_user_id(), 'orddd_pro_third_notice_ignore_time' );
            	$orddd_seven_days = strtotime( '+7 Days', $orddd_third_ignore_time[0] );
                
                if ( $current_time > $orddd_seven_days ) {
                    $class = 'updated notice-info point-notice';
                    $style = 'position:relative';

                    $orddd_pro_link = 'https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/checkout?edd_action=add_to_cart&download_id=16&utm_source=wpnotice&utm_medium=fourth&utm_campaign=OrderDeliveryDateLitePlugin';

                    $message = wp_kses_post ( __( 'Create Delivery Settings by Shipping Zones & Shipping Classes using Order Delivery Date Pro for WooCommerce plugin. <br>Use discount code "ORDPRO20" and grab 20% discount on the purchase of the plugin. The discount code is valid only for the first 20 customers. <strong><a target="_blank" href= "'.$orddd_pro_link.'">Purchase now</a></strong>.', 'order-delivery-date' ) );

                    $add_query_arguments = add_query_arg( 'orddd_pro_fourth_notice_ignore', '0' );
                    
                    $cancel_button = '<a href="'.$add_query_arguments.'" class="dashicons dashicons-dismiss dashicons-dismiss-icon" style="position: absolute; top: 8px; right: 8px; color: #222; opacity: 0.4; text-decoration: none !important    ;"></a>';
                    printf( '<div class="%1$s" style="%2$s"><p>%3$s %4$s</p></div>', $class, $style, $message, $cancel_button );
                }
            }

            if ( get_user_meta( get_current_user_id(), 'orddd_pro_first_notice_ignore' ) &&
				 get_user_meta( get_current_user_id(), 'orddd_pro_second_notice_ignore' ) &&
				 get_user_meta( get_current_user_id(), 'orddd_pro_third_notice_ignore' ) &&
				 get_user_meta( get_current_user_id(), 'orddd_pro_fourth_notice_ignore' ) &&
				 ! get_user_meta( get_current_user_id(), 'orddd_pro_fifth_notice_ignore' ) &&
				 ! is_plugin_active( 'post-purchase-experience/post-purchase-experience.php' ) ) {

				$orddd_fourth_ignore_time = get_user_meta( get_current_user_id(), 'orddd_pro_fourth_notice_ignore_time' );
				$orddd_seven_days = strtotime( '+7 Days', $orddd_fourth_ignore_time[0] );				

				if ( $current_time > $orddd_seven_days ){
					$class = 'updated notice-info point-notice';
					$style = 'position:relative';

                   	$orddd_pro_link = 'https://www.tychesoftwares.com/store/premium-plugins/post-delivery-product-reviews-addon-order-delivery-date-woocommerce/checkout?edd_action=add_to_cart&download_id=278278&utm_source=wpnotice&utm_medium=fifth&utm_campaign=OrderDeliveryDateLitePlugin';

                    $message = wp_kses_post ( __( 'Receive feedbacks for your products from verified owners by sending them post delivery emails using Post Delivery Product Reviews addon of Order Delivery Date plugin. <strong><a target="_blank" href= "'.$orddd_pro_link.'">Have it now!</a></strong>', 'order-delivery-date' ) );

				    $add_query_arguments = add_query_arg( 'orddd_pro_fifth_notice_ignore', '0' );
				    $cancel_button = '<a href="'.$add_query_arguments.'" class="dashicons dashicons-dismiss dashicons-dismiss-icon" style="position: absolute; top: 8px; right: 8px; color: #222; opacity: 0.4; text-decoration: none !important;"></a>';
					printf( '<div class="%1$s" style="%2$s"><p>%3$s %4$s</p></div>', $class, $style, $message, $cancel_button );
				}
			}   	
        }
	}

	/**
	 * If a user clicks to ignore the notice, add that to their user meta
	 * 
	 * @hook admin_init
	 * @since 3.0
	 */

	public static function orddd_lite_ignore_pro_notices() {
		if ( isset( $_GET['orddd_pro_first_notice_ignore'] ) && '0' === $_GET['orddd_pro_first_notice_ignore'] ) {
			add_user_meta( get_current_user_id(), 'orddd_pro_first_notice_ignore', 'true', true );
			add_user_meta( get_current_user_id(), 'orddd_pro_first_notice_ignore_time', current_time( 'timestamp' ), true );
			wp_safe_redirect( remove_query_arg( 'orddd_pro_first_notice_ignore' ) );

		}

		if ( isset( $_GET['orddd_pro_second_notice_ignore'] ) && '0' === $_GET['orddd_pro_second_notice_ignore'] ) {
			add_user_meta( get_current_user_id(), 'orddd_pro_second_notice_ignore', 'true', true );
			add_user_meta( get_current_user_id(), 'orddd_pro_second_notice_ignore_time', current_time( 'timestamp' ), true );
			wp_safe_redirect( remove_query_arg( 'orddd_pro_second_notice_ignore' )  );
		}

		if ( isset( $_GET['orddd_pro_third_notice_ignore'] ) && '0' === $_GET['orddd_pro_third_notice_ignore'] ) {
			add_user_meta( get_current_user_id(), 'orddd_pro_third_notice_ignore', 'true', true );
			add_user_meta( get_current_user_id(), 'orddd_pro_third_notice_ignore_time', current_time( 'timestamp' ), true );
			wp_safe_redirect( remove_query_arg( 'orddd_pro_third_notice_ignore' ) );
		}

		if ( isset( $_GET['orddd_pro_fourth_notice_ignore'] ) && '0' === $_GET['orddd_pro_fourth_notice_ignore'] ) {
			add_user_meta( get_current_user_id(), 'orddd_pro_fourth_notice_ignore', 'true', true );
			add_user_meta( get_current_user_id(), 'orddd_pro_fourth_notice_ignore_time', current_time( 'timestamp' ), true );
			wp_safe_redirect( remove_query_arg( 'orddd_pro_fourth_notice_ignore' ) );
		}

		if ( isset( $_GET['orddd_pro_fifth_notice_ignore'] ) && '0' === $_GET['orddd_pro_fifth_notice_ignore'] ) {
			add_user_meta( get_current_user_id(), 'orddd_pro_fifth_notice_ignore', 'true', true );
			add_user_meta( get_current_user_id(), 'orddd_pro_fifth_notice_ignore_time', current_time( 'timestamp' ), true );
			wp_safe_redirect( remove_query_arg( 'orddd_pro_fifth_notice_ignore' ) );
		}
	}
}

$orddd_lite_pro_notices = new orddd_lite_pro_notices();
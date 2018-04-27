<?php
/**
 * Welcome page on activate or updation of the plugin
 */
?>
<style>
    .feature-section .feature-section-item {
        float:left;
        width:48%;
    }
</style>

<div class="wrap about-wrap">
    <?php echo $get_welcome_header; ?>
    <div style="float:left;width: 80%;">
        <p class="about-text" style="margin-right:20px;"><?php
            printf(
                __( "Thank you for activating or updating to the latest version of " . $plugin_name . "! If you're a first time user, welcome! You're well to accept deliveries with customer preferred delivery date." )
            );
        ?>
        </p>
    </div>
    
    <div class="wcal-badge"><img src="<?php echo $badge_url; ?>" style="width:150px;"/></div>

    <p>&nbsp;</p>

    <div class="feature-section clearfix introduction">

    <h3><?php esc_html_e( "Get Started with Order Delivery Date Lite", 'order-delivery-date-lite' ); ?></h3>

    <div class="video feature-section-item" style="float:left;padding-right:10px;">
        <img src="<?php echo $ts_dir_image_path . 'order-delivery-date-lite.png' ?>"
            alt="<?php esc_attr_e( 'Order Delivery Date Lite', 'order-delivery-date-lite' ); ?>" style="width:600px;">
    </div>

    <div class="content feature-section-item last-feature">
        <h3><?php esc_html_e( 'Enable Delivery Date Capture', 'order-delivery-date-lite' ); ?></h3>

        <p><?php esc_html_e( 'To start allowing customers to select their preferred delivery date, simply activate the Enable Delivery Date checkbox from under Order Delivery Date menu.', 'order-delivery-date-lite' ); ?></p>
        <a href="admin.php?page=order_delivery_date_lite" target="_blank" class="button-secondary">
            <?php esc_html_e( 'Click Here to go to Order Delivery Date Settings page', 'order-delivery-date-lite' ); ?>
            <span class="dashicons dashicons-external"></span>
        </a>
    </div>
    </div>

    <!-- /.intro-section -->

    <div class="content">

    <h3><?php esc_html_e( "Know more about Order Delivery Date Pro", 'order-delivery-date-lite' ); ?></h3>

    <p><?php _e( 'The Order Delivery Date Pro plugin gives you features where you can allow customers to choose a Delivery Time along with Date as compared to Lite Plugin. Here are some other notable features the Pro version provides.' ); ?></p>

    <div class="feature-section clearfix introduction">
        <div class="video feature-section-item" style="float:left;padding-right:10px;">
            <img src="<?php echo $ts_dir_image_path . 'custom-delivery-settings.png'?>"
                alt="<?php esc_attr_e( 'Order Delivery Date Lite', 'order-delivery-date-lite' ); ?>" style="width:500px;">
        </div>

        <div class="content feature-section-item last-feature">
            <h3><?php esc_html_e( 'Create Custom Delivery Schedules', 'order-delivery-date-lite' ); ?></h3>

            <p><?php esc_html_e( 'The ability to set different delivery schedule for different WooCommerce shipping zones, shipping classes and product categories is very useful for the businesses like food packet deliveries, cake shops etc which deals with delivery in different shipping zones.', 'order-delivery-date-lite' ); ?></p>

            <a href="https://www.tychesoftwares.com/custom-delivery-settings/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=OrderDeliveryDateLitePlugin" target="_blank" class="button-secondary">
                <?php esc_html_e( 'Learn More', 'order-delivery-date-lite' ); ?>
                <span class="dashicons dashicons-external"></span>
            </a>
        </div>
    </div>

    <div class="feature-section clearfix">
        <div class="content feature-section-item">

            <h3><?php esc_html_e( 'Delivery Time along with Delivery Date', 'order-delivery-date-lite' ); ?></h3>

                <p><?php esc_html_e( "The provision for allowing Delivery Time along with the Delivery Date on the checkout page makes the delivery more accurate. Delivering products on customer's preferred date and time improves your customers service.", 'order-delivery-date-lite' ); ?></p>
                <a href="https://www.tychesoftwares.com/setup-delivery-date-time/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=OrderDeliveryDateLitePlugin" target="_blank" class="button-secondary">
                    <?php esc_html_e( 'Learn More', 'order-delivery-date-lite' ); ?>
                    <span class="dashicons dashicons-external"></span>
                </a>
        </div>

        <div class="content feature-section-item last-feature">
            <img src="<?php echo $ts_dir_image_path . 'time-slots.png'; ?>" alt="<?php esc_attr_e( 'Order Delivery Date for WooCommerce Lite', 'order-delivery-date-lite' ); ?>" style="width:450px;">
        </div>
    </div>


    <div class="feature-section clearfix introduction">
        <div class="video feature-section-item" style="float:left;padding-right:10px;">
            <img src="<?php echo $ts_dir_image_path. 'google-calendar-sync.png'; ?>" alt="<?php esc_attr_e( 'Order Delivery Date for WooCommerce Lite', 'order-delivery-date-lite' ); ?>" style="width:450px;">
        </div>

        <div class="content feature-section-item last-feature">
            <h3><?php esc_html_e( 'Synchronise Deliveries with Google Calendar', 'order-delivery-date-lite' ); ?></h3>

            <p><?php esc_html_e( 'The ability to synchronise deliveries to the google calendar helps administrator or store manager to manage all the things in a single calendar.', 'order-delivery-date-lite' ); ?></p>

            <a href="https://www.tychesoftwares.com/how-to-synchornize-delivery-dates-with-your-google-calendar/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=OrderDeliveryDateLitePlugin" target="_blank" class="button-secondary">
                <?php esc_html_e( 'Learn More', 'order-delivery-date-lite' ); ?>
                <span class="dashicons dashicons-external"></span>
            </a>
        </div>
    </div>

    <div class="feature-section clearfix">
        <div class="content feature-section-item">

            <h3><?php esc_html_e( 'Different delivery settings for each weekday', 'order-delivery-date-lite' ); ?></h3>

                <p><?php esc_html_e( 'The Pro version of the plugin allows you to add different delivery settings like Same day cut-off time, Next Day cut-off time or Minimum Delivery Time for each weekday. It also allows you to add different delivery charges for different weekdays.', 'order-delivery-date-lite' ); ?></p>

                <a href="https://www.tychesoftwares.com/weekday-settings/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=OrderDeliveryDateLitePlugin" target="_blank" class="button-secondary">
                    <?php esc_html_e( 'Learn More', 'order-delivery-date-lite' ); ?>
                    <span class="dashicons dashicons-external"></span>
                </a>
        </div>

        <div class="content feature-section-item last-feature">
            <img src="<?php echo $ts_dir_image_path . 'weekday-settings.png'; ?>" alt="<?php esc_attr_e( 'Order Delivery Date for WooCommerce Lite', 'order-delivery-date-lite' ); ?>" style="width:450px;">
        </div>
    </div>

    <a href="https://www.tychesoftwares.com/differences-pro-lite-versions-order-delivery-date-woocommerce-plugin/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=OrderDeliveryDateLitePlugin" target="_blank" class="button-secondary">
        <?php esc_html_e( 'View full list of differences between Lite & Pro plugin', 'order-delivery-date' ); ?>
        <span class="dashicons dashicons-external"></span>
    </a>
    </div>

    <div class="feature-section clearfix">
        <div class="content feature-section-item">
            <h3><?php esc_html_e( 'Getting to Know Tyche Softwares', 'woocommerce-ac' ); ?></h3>
            <ul class="ul-disc">
                <li><a href="https://tychesoftwares.com/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=OrderDeliveryDateProPlugin" target="_blank"><?php esc_html_e( 'Visit the Tyche Softwares Website', 'woocommerce-ac' ); ?></a></li>
                <li><a href="https://tychesoftwares.com/premium-plugins/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=OrderDeliveryDateProPlugin" target="_blank"><?php esc_html_e( 'View all Premium Plugins', 'woocommerce-ac' ); ?></a>
                <ul class="ul-disc">
                    <li><a href="https://www.tychesoftwares.com/store/premium-plugins/woocommerce-abandoned-cart-pro/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=OrderDeliveryDateProPlugin" target="_blank">Abandoned Cart Pro Plugin for WooCommerce</a></li>
                    <li><a href="https://www.tychesoftwares.com/store/premium-plugins/woocommerce-booking-plugin/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=OrderDeliveryDateProPlugin" target="_blank">Booking & Appointment Plugin for WooCommerce</a></li>
                    <li><a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=OrderDeliveryDateProPlugin" target="_blank">Order Delivery Date for WooCommerce</a></li>
                    <li><a href="https://www.tychesoftwares.com/store/premium-plugins/product-delivery-date-pro-for-woocommerce/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=OrderDeliveryDateProPlugin" target="_blank">Product Delivery Date for WooCommerce</a></li>
                    <li><a href="https://www.tychesoftwares.com/store/premium-plugins/deposits-for-woocommerce/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=OrderDeliveryDateProPlugin" target="_blank">Deposits for WooCommerce</a></li>
                </ul>
                </li>
                <li><a href="https://tychesoftwares.com/about/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=OrderDeliveryDateProPlugin" target="_blank"><?php esc_html_e( 'Meet the team', $plugin_context ); ?></a></li>
            </ul>

        </div>
        
        <div class="content feature-section-item">
            <h3><?php esc_html_e( 'Current Offers', $plugin_context ); ?></h3>
            <p>We do not have any offers going on right now</p>
        </div>

    </div>            
    <!-- /.feature-section -->
</div>
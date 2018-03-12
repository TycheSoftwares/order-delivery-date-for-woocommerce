<?php 
/**
* Order Delivery Date for WooCommerce Lite
*
* Show welcome page when the plugin is installed
*
* @author      Tyche Softwares
* @package     Order-Delivery-Date-Lite-for-WooCommerce/Admin/Welcome-Page
* @since       3.3
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds Welcome page when the plugin is installed and activated
 *
 */

class ORDDD_Welcome {

	/**
	 * @var string The capability users should have to view the page
	 */
	public $minimum_capability = 'manage_options';

	/**
	 * Default constructor
	 *
	 * @since 3.3
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menus' ) );
		add_action( 'admin_head', array( $this, 'admin_head' ) );

		if ( !isset( $_GET[ 'page' ] ) || 
		( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] != 'orddd-about' ) ) {
			add_action( 'admin_init', array( $this, 'orddd_welcome' ) );
		}
	}

	/**
	 * Register the Dashboard Page which is later hidden but this pages
	 * is used to render the Welcome page.
	 *
	 * @hook admin_menu
	 * @access public
	 * @since  3.3
	 * @return void
	 */
	public function admin_menus() {
		$display_version = ORDDD_VERSION;

		// About Page
		add_dashboard_page(
			sprintf( esc_html__( 'Welcome to Order Delivery Date Lite %s', 'order-delivery-date-lite' ), $display_version ),
			esc_html__( 'Welcome to Order Delivery Date Lite', 'order-delivery-date-lite' ),
			$this->minimum_capability,
			'orddd-about',
			array( $this, 'about_screen' )
		);
	}

	/**
	 * Hide Individual Dashboard Pages
	 *
	 * @hook admin_head
	 * @access public
	 * @since  3.3
	 * @return void
	 */
	public function admin_head() {
		remove_submenu_page( 'index.php', 'orddd-about' );
	}

	/**
	 * Render About Screen
	 *
	 * @access public
	 * @since  3.3
	 * @return void
	 */
	public function about_screen() {
		$display_version = ORDDD_VERSION;
		// Badge for welcome page
		$badge_url = ORDDD_PLUGIN_URL . 'images/icon-256x256.png';		
		?>
		<style>
			.feature-section .feature-section-item {
				float:left;
				width:48%;
			}
		</style>
        <div class="wrap about-wrap">

			<?php $this->get_welcome_header() ?>

            <div style="float:left;width: 80%;">
            <p class="about-text" style="margin-right:20px;"><?php
				printf(
					__( "Thank you for activating or updating to the latest version of Order Delivery Date Lite! If you're a first time user, welcome! You're well to accept deliveries with customer preferred delivery date. " )
				);
				?></p>
			</div>
            <div class="orddd-badge"><img src="<?php echo $badge_url; ?>" style="width:150px;"/></div>

            <p>&nbsp;</p>

            <div class="feature-section clearfix introduction">

                <h3><?php esc_html_e( "Get Started with Order Delivery Date Lite", 'order-delivery-date-lite' ); ?></h3>

                <div class="video feature-section-item" style="float:left;padding-right:10px;">
                    <img src="<?php echo ORDDD_PLUGIN_URL . '/images/order-delivery-date-lite.png' ?>"
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
	                    <img src="<?php echo ORDDD_PLUGIN_URL . '/images/custom-delivery-settings.png'?>"
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
	                    <img src="<?php echo ORDDD_PLUGIN_URL . 'images/time-slots.png'; ?>" alt="<?php esc_attr_e( 'Order Delivery Date for WooCommerce Lite', 'order-delivery-date-lite' ); ?>" style="width:450px;">
	                </div>
	            </div>

       
	            <div class="feature-section clearfix introduction">
	                <div class="video feature-section-item" style="float:left;padding-right:10px;">
	                    <img src="<?php echo ORDDD_PLUGIN_URL . 'images/google-calendar-sync.png'; ?>" alt="<?php esc_attr_e( 'Order Delivery Date for WooCommerce Lite', 'order-delivery-date-lite' ); ?>" style="width:450px;">
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
	                    <img src="<?php echo ORDDD_PLUGIN_URL . 'images/weekday-settings.png'; ?>" alt="<?php esc_attr_e( 'Order Delivery Date for WooCommerce Lite', 'order-delivery-date-lite' ); ?>" style="width:450px;">
	                </div>
	            </div>

                <a href="https://www.tychesoftwares.com/differences-pro-lite-versions-order-delivery-date-woocommerce-plugin/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=OrderDeliveryDateLitePlugin" target="_blank" class="button-secondary">
					<?php esc_html_e( 'View full list of differences between Lite & Pro plugin', 'order-delivery-date' ); ?>
                    <span class="dashicons dashicons-external"></span>
                </a>
            </div>

            <div class="feature-section clearfix">

                <div class="content feature-section-item">

                    <h3><?php esc_html_e( 'Getting to Know Tyche Softwares', 'order-delivery-date' ); ?></h3>

                    <ul class="ul-disc">
                        <li><a href="https://www.tychesoftwares.com/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=OrderDeliveryDateLitePlugin" target="_blank"><?php esc_html_e( 'Visit the Tyche Softwares Website', 'order-delivery-date' ); ?></a></li>
                        <li><a href="https://www.tychesoftwares.com/premium-plugins/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=OrderDeliveryDateLitePlugin" target="_blank"><?php esc_html_e( 'View all Premium Plugins', 'order-delivery-date' ); ?></a>
                        <ul class="ul-disc">
                        	<li><a href="https://www.tychesoftwares.com/store/premium-plugins/woocommerce-abandoned-cart-pro/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=OrderDeliveryDateLitePlugin" target="_blank">Abandoned Cart Pro Plugin for WooCommerce</a></li>
                        	<li><a href="https://www.tychesoftwares.com/store/premium-plugins/woocommerce-booking-plugin/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=OrderDeliveryDateLitePlugin" target="_blank">Booking & Appointment Plugin for WooCommerce</a></li>
                        	<li><a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=OrderDeliveryDateLitePlugin" target="_blank">Order Delivery Date for WooCommerce</a></li>
                        	<li><a href="https://www.tychesoftwares.com/store/premium-plugins/product-delivery-date-pro-for-woocommerce/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=OrderDeliveryDateLitePlugin" target="_blank">Product Delivery Date for WooCommerce</a></li>
                        	<li><a href="https://www.tychesoftwares.com/store/premium-plugins/deposits-for-woocommerce/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=OrderDeliveryDateLitePlugin" target="_blank">Deposits for WooCommerce</a></li>
                        </ul>
                        </li>
                        <li><a href="https://tychesoftwares.com/about/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=OrderDeliveryDateLitePlugin" target="_blank"><?php esc_html_e( 'Meet the team', 'order-delivery-date' ); ?></a></li>
                    </ul>

                </div>


                <div class="content feature-section-item">

                    <h3><?php esc_html_e( 'Current Offers', 'order-delivery-date' ); ?></h3>

                    <p>Buy all our <a href="https://tychesoftwares.com/premium-plugins/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=OrderDeliveryDateLitePlugin" target="_blank">premium plugins</a> at 30% off till 31st December 2017</p>

                </div>

            </div>            
            <!-- /.feature-section -->

        </div>
		<?php

		update_option( 'orddd_welcome_page_shown', 'yes' );
		update_option( 'orddd_welcome_page_shown_time', current_time( 'timestamp' ) );
	}


	/**
	 * The header section for the welcome screen.
	 *
	 * @since 3.3
	 */
	public function get_welcome_header() {
		// Badge for welcome page
		$badge_url = ORDDD_PLUGIN_URL . 'images/icon-256x256.png';
		?>
        <h1 class="welcome-h1"><?php echo get_admin_page_title(); ?></h1>
		<?php $this->social_media_elements(); 
	}


	/**
	 * Social Media Like Buttons
	 *
	 * Various social media elements to Tyche Softwares
	 *
	 * @since 3.3
	 */
	public function social_media_elements() { 
		?>
        <div class="social-items-wrap">

            <iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2Ftychesoftwares&amp;send=false&amp;layout=button_count&amp;width=100&amp;show_faces=false&amp;font&amp;colorscheme=light&amp;action=like&amp;height=21&amp;appId=220596284639969"
                    scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100px; height:21px;"
                    allowTransparency="true"></iframe>

            <a href="https://twitter.com/tychesoftwares" class="twitter-follow-button" data-show-count="false"><?php
				printf(
					esc_html_e( 'Follow %s', 'tychesoftwares' ),
					'@tychesoftwares'
				);
				?></a>
            <script>!function (d, s, id) {
                    var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
                    if (!d.getElementById(id)) {
                        js = d.createElement(s);
                        js.id = id;
                        js.src = p + '://platform.twitter.com/widgets.js';
                        fjs.parentNode.insertBefore(js, fjs);
                    }
                }(document, 'script', 'twitter-wjs');
            </script>

        </div>
        <!--/.social-items-wrap -->
		<?php
	}


	/**
	 * Sends user to the Welcome page on first activation of Order Delivery Date Lite as well as each
	 * time Order Delivery Date Lite is upgraded to a new version
	 *
	 * @access public
	 * @since  3.3
	 *
	 * @return void
	 */
	public function orddd_welcome() {
		// Bail if activating from network, or bulk
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
			return;
		}

		if( !get_option( 'orddd_welcome_page_shown' ) ) {
			wp_safe_redirect( admin_url( 'index.php?page=orddd-about' ) );
			exit;
		}
	}
}

new ORDDD_Welcome();

<?php
/**
 * Tyche Softwares.
 *
 * Plugin Deactivation Class.
 *
 * @author      Tyche Softwares
 * @package     TycheSoftwares/PluginDeactivation
 * @category    Classes
 * @since       1.2
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Tyche_Plugin_Deactivation' ) ) {

	/**
	 * Plugin Deactivation.
	 *
	 * @since 1.1
	 */
	class Tyche_Plugin_Deactivation {

		/**
		 * Version.
		 *
		 * @var string $version
		 */
		private $version = '1.3';

		/**
		 * Plugin Version.
		 *
		 * @var string $plugin_version
		 */
		private $plugin_version = '';

		/**
		 * API Url.
		 *
		 * @var string $api_url
		 */
		protected $api_url = 'https://tracking.tychesoftwares.com/v2/';

		/**
		 * Plugin Name.
		 *
		 * @var string $plugin_name
		 */
		private $plugin_name = '';

		/**
		 * Plugin Locale.
		 *
		 * @var string $plugin_locale
		 */
		private $plugin_locale = '';

		/**
		 * Plugin Short Name.
		 *
		 * @var string $plugin_short_name
		 */
		private $plugin_short_name = '';

		/**
		 * Plugin Base.
		 *
		 * @var string $plugin_base
		 */
		private $plugin_base = '';

		/**
		 * JS script file for handling the JS events.
		 *
		 * @var string $script_file
		 */
		private $script_file = '';

		/**
		 * Construct
		 *
		 * @since 1.1
		 * @param array $options Options.
		 */
		public function __construct( $options ) {

			if ( ! $this->init_vars( $options ) ) {
				return;
			}

			add_action( 'admin_print_scripts-plugins.php', array( $this, 'enqueue_scripts' ), 30 );
			add_action( 'wp_ajax_tyche_plugin_deactivation_submit_action', array( &$this, 'tyche_plugin_deactivation_submit_action' ) );
			add_filter( 'plugin_action_links_' . $this->plugin_base, array( &$this, 'plugin_action_links' ) );
		}

		/**
		 * Initialize variables from options array.
		 *
		 * @param array $options Options.
		 *
		 * @since 1.1
		 */
		public function init_vars( $options ) {

			if ( ! is_array( $options ) ) {
				return false;
			}

			if ( ! isset( $options['plugin_name'] ) || ! isset( $options['plugin_base'] ) || ! isset( $options['script_file'] ) || ! isset( $options['plugin_short_name'] ) || ! isset( $options['version'] ) || ! isset( $options['plugin_locale'] ) ) {
				return false;
			}

			$this->plugin_name       = $options['plugin_name'];
			$this->plugin_base       = $options['plugin_base'];
			$this->script_file       = $options['script_file'];
			$this->plugin_short_name = $options['plugin_short_name'];
			$this->plugin_version    = $options['version'];
			$this->plugin_locale     = $options['plugin_locale'];

			return true;
		}

		/**
		 * Show action links on the plugin screen.
		 *
		 * @param mixed $links Plugin Action links.
		 *
		 * @return    array
		 * @since 1.1
		 */
		public function plugin_action_links( $links ) {

			if ( isset( $links['deactivate'] ) ) {
				$links['deactivate'] .= '<i class="' . $this->plugin_short_name . ' ts-slug" data-slug="' . $this->plugin_base . '" data-plugin="' . $this->plugin_name . '"></i>';
			}

			return $links;
		}

		/**
		 * Enqueue styles and scripts from the tracking server.
		 *
		 * @since 1.1
		 */
		public function enqueue_scripts() {

			$current_screen = get_current_screen();
			if ( isset( $current_screen->id ) && 'plugins' !== $current_screen->id ) {
				return;
			}

			wp_enqueue_style(
				'tyche_plugin_deactivation',
				plugins_url( '/css/style.css', __FILE__ ),
				array(),
				$this->plugin_version
			);

			wp_register_script(
				'tyche_plugin_deactivation_' . $this->plugin_short_name,
				$this->script_file,
				array( 'jquery', 'tyche' ),
				$this->plugin_version,
				true
			);
			// Hardcoded deactivation data.
			$data = array(
				'reasons'  => array(
					array(
						'id'                => 1,
						'text'              => __( 'I only needed the plugin for a short period.', 'order-delivery-date' ),
						'input_type'        => '',
						'input_placeholder' => '',
					),
					array(
						'id'                => 2,
						'text'              => __( 'I found a better plugin.', 'order-delivery-date' ),
						'input_type'        => 'textfield',
						'input_placeholder' => __( 'Please let us have the plugin\'s name so that we can make improvements', 'order-delivery-date' ),
					),
					array(
						'id'                => 3,
						'text'              => __( 'The plugin is not working.', 'order-delivery-date' ),
						'input_type'        => 'textfield',
						'input_placeholder' => __( 'Please share what was faulty with the plugin so that we may get the issue fixed.', 'order-delivery-date' ),
					),
					array(
						'id'                => 7,
						'text'              => __( 'The plugin is not compatible with another plugin.', 'order-delivery-date' ),
						'input_type'        => 'textfield',
						'input_placeholder' => __( 'We’re sorry! Please tell us about the issues so that we can get them fixed.', 'order-delivery-date' ),
					),
					array(
						'id'                => 8,
						'text'              => __( 'Some features I need are not working as per my expectation', 'order-delivery-date' ),
						'input_type'        => 'textfield',
						'input_placeholder' => __( 'Please tell us about these features.', 'order-delivery-date' ),
					),
					array(
						'id'                => 13,
						'text'              => __( 'I have purchased the Pro version of the plugin', 'order-delivery-date' ),
						'input_type'        => 'textfield',
						'input_placeholder' => __( 'We’re sorry! We would like you to tell us the plugin/theme so that we can work on the compatibility.', 'order-delivery-date' ),
					),
					array(
						'id'                => 14,
						'text'              => __( 'The plugin is great, but I need specific feature(s) that is not supported.', 'order-delivery-date' ),
						'input_type'        => '',
						'input_placeholder' => '',
					),
					array(
						'id'                => 5,
						'text'              => __( 'Minimum Delivery Time (in hours) is not working as expected', 'order-delivery-date' ),
						'input_type'        => '',
						'input_placeholder' => '',
					),
					array(
						'id'                => 12,
						'text'              => __( 'We do not have a need for the plugin as we aren’t doing deliveries anymore', 'order-delivery-date' ),
						'input_type'        => '',
						'input_placeholder' => '',
					),
					array(
						'id'                => 9,
						'text'              => __( "I don't like to share my information with you.", 'order-delivery-date' ),
						'input_type'        => '',
						'input_placeholder' => '',
					),
					array(
						'id'                => 10,
						'text'              => __( 'Other', 'order-delivery-date' ),
						'input_type'        => 'textfield',
						'input_placeholder' => '',
					),
				),
				'template' => '<div class="{PLUGIN} ts-modal no-confirmation-message">
								<div class="ts-modal-dialog">
									<div class="ts-modal-body">
										<div class="ts-modal-panel" data-panel-id="confirm">
											<p></p>
										</div>
										<div class="ts-modal-panel active" data-panel-id="reasons">
											<h3>
												<strong>
													' . __( 'If you have a moment, please let us know why you are deactivating:', 'order-delivery-date' ) . '
												</strong>
											</h3>
											
											<ul id="reasons-list">
												{HTML}
											</ul>
										</div>
									</div>
		
									<div class="ts-modal-footer">
										<a href="javascript:void(0);" class="button button-secondary button-skip-deactivate"> ' . __( 'Skip & Deactivate', 'order-delivery-date' ) . '</a>
										<a href="javascript:void(0);" class="button button-secondary button-deactivate"> ' . __( 'Submit & Deactivate', 'order-delivery-date' ) . '</a>
										<a href="javascript:void(0);" class="button button-primary button-close">' . __( 'Cancel', 'order-delivery-date' ) . '</a>
									</div>
								</div>
							</div>',
			);

			wp_localize_script(
				'tyche_plugin_deactivation_' . $this->plugin_short_name,
				'tyche_plugin_deactivation_' . $this->plugin_short_name . '_js',
				array(
					'deactivation_data'    => $data,
					'ajax_url'             => admin_url( 'admin-ajax.php' ),
					'nonce'                => wp_create_nonce( 'tyche_plugin_deactivation_submit_action' ),
					'deactivation_req_msg' => __( 'Please select a reason for deactivation!', 'order-delivery-date' ),
				)
			);

			wp_enqueue_script( 'tyche_plugin_deactivation_' . $this->plugin_short_name );
		}

		/**
		 * Called after the user has submitted his reason for deactivating the plugin.
		 *
		 * @since  1.1
		 */
		public function tyche_plugin_deactivation_submit_action() {

			if ( ! wp_verify_nonce( $_POST['nonce'], 'tyche_plugin_deactivation_submit_action' ) || ! isset( $_POST['reason_id'] ) || ! isset( $_POST['reason_text'] ) || ! isset( $_POST['plugin_short_name'] ) || ! isset( $_POST['plugin_name'] ) ) { // phpcs:ignore
				wp_send_json_error( 0 );
			}

			$reason_id = isset( $_POST['reason_id'] ) ? sanitize_text_field( wp_unslash( $_POST['reason_id'] ) ) : '';

			if ( 0 === (int) $reason_id ) {
				wp_send_json_error( 0 );
				exit;
			}

			wp_safe_remote_post(
				$this->api_url,
				array(
					'method'      => 'POST',
					'timeout'     => 60,
					'redirection' => 5,
					'httpversion' => '1.0',
					'blocking'    => false,
					'headers'     => array( 'user-agent' => 'TSTracker/' . md5( esc_url( home_url( '/' ) ) ) . ';' ),
					'body'        => wp_json_encode(
						array(
							'action'      => 'plugin-deactivation',
							'version'     => $this->version,
							'plugin_slug' => isset( $_POST['plugin_short_name'] ) ? sanitize_text_field( wp_unslash( $_POST['plugin_short_name'] ) ) : '',
							'url'         => home_url(),
							'email'       => apply_filters( 'ts_tracker_admin_email', get_option( 'admin_email' ) ),
							'plugin_name' => isset( $_POST['plugin_name'] ) ? sanitize_text_field( wp_unslash( $_POST['plugin_name'] ) ) : '',
							'reason_id'   => isset( $_POST['reason_id'] ) ? sanitize_text_field( wp_unslash( $_POST['reason_id'] ) ) : '',
							'reason_text' => isset( $_POST['reason_text'] ) ? sanitize_text_field( wp_unslash( $_POST['reason_text'] ) ) : '',
							'reason_info' => isset( $_POST['reason_info'] ) ? sanitize_text_field( wp_unslash( $_POST['reason_info'] ) ) : '',
						)
					),
				)
			);

			wp_send_json_success();
		}
	}
}

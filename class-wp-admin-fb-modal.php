<?php
/**
 * WP Admin Feedback Modal
 *
 * @author   Sébastien Dumont
 * @category Admin
 * @package  WP Admin Feedback Modal/Admin
 * @license  GPL-2.0+
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Admin_FB_Modal' ) ) {

	class WP_Admin_FB_Modal {

		public static $plugin_slug = '';

		public static $plugin_name = '';

		public static $plugin_file = '';

		public static $other       = '';

		public static $responses   = array();

		/**
		 * Initialize the Feedback Modal.
		 *
		 * @access public
		 * @static
		 * @param string $plugin_slug
		 * @param string $plugin_name
		 * @param string $plugin_file
		 * @param array  $responses
		 * @param string $other
		 */
		public static function init( $plugin_slug, $plugin_name, $plugin_file, $responses, $other ) {
			self::$plugin_slug = str_replace( '-', '_', $plugin_slug );
			self::$plugin_name = $plugin_name;
			self::$plugin_file = $plugin_file;
			self::$other       = $other;
			self::$responses   = $responses;

			include_once( dirname( __FILE__ ) . '/class-wp-admin-fb-modal-assets.php' ); // Modal Assets

			add_action( 'wp_ajax_wp_admin_fb_modal_' . self::$plugin_slug, array( __CLASS__, 'ajax_feedback_modal' ) );
			add_action( 'admin_footer', array( __CLASS__, 'deactivation_modal' ), 9999 );

			// Default method of sending feedback is via email. You can unhook this to add your own method of sending.
			add_action( 'wp_admin_fb_modal_sending', array( __CLASS__, 'send_feedback_via_email' ), 10 );
		} // END init()

		/**
		 * Sends feedback details from deactivation modal.
		 * 
		 * @access public
		 */
		public function ajax_feedback_modal() {
			$reason        = isset( $_POST['fb_reason'] ) ? trim( $_POST['fb_reason'] ) : '';
			$details       = isset( $_POST['fb_details'] ) ? sanitize_text_field( $_POST['fb_details'] ) : '';
			$from          = isset( $_POST['fb_email'] ) ? sanitize_email( $_POST['fb_email'] ) : '';

			$feedback_data = array(
				'reason'  => $reason,
				'details' => $details
			);

			/**
			 * If the user granted permision to send personal and site data 
			 * along with the feedback then append it.
			 */
			if ( ! empty( $_POST['fb_permission_granted'] ) ) {
				$user_id  = get_current_user_id();
				$userdata = get_userdata( $user_id );

				$active_plugins = (array) get_option( 'active_plugins', array() );

				if ( is_multisite() ) {
					$network_activated_plugins = array_keys( get_site_option( 'active_sitewide_plugins', array() ) );
					$active_plugins            = array_merge( $active_plugins, $network_activated_plugins );
				}

				$active_theme = wp_get_theme();

				$site_data = array(
					'first_name'      => $userdata->first_name,
					'last_name'       => $userdata->last_name,
					'from_email'      => $from,
					'site_url'        => site_url(),
					'wp_version'      => get_bloginfo( 'version' ),
					'wp_multisite'    => is_multisite(),
					'wp_memory_limit' => WP_MEMORY_LIMIT,
					'wp_debug_mode'   => ( defined( 'WP_DEBUG' ) && WP_DEBUG ),
					'wp_cron'         => ! ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ),
					'language'        => get_locale(),
					'php_version'     => phpversion(),
					'php_max_execution_time' => ini_get( 'max_execution_time' ),
					'php_max_input_vars'     => ini_get( 'max_input_vars' ),
					'default_timezone'       => date_default_timezone_get(),
					'active_plugins'  => $active_plugins,
					'theme'           => array(
						'name'           => $active_theme->get( 'Name' ),
						'version'        => $active_theme->get( 'Version' ),
						'author_url'     => esc_url_raw( $active_theme->get( 'AuthorURI' ) ),
						'is_child_theme' => is_child_theme(),
					)
				);

				$feedback_data = array_merge( $feedback_data, $site_data );
			}

			// You can filter the feedback data to add pass on more data or remove what you don't need.
			$feedback_data = apply_filters( 'wp_admin_fb_modal_' . str_replace( '-', '_', self::$plugin_name ), $feedback_data );

			do_action( 'wp_admin_fb_modal_sending', $feedback_data );
		} // END ajax_feedback_modal()

		/**
		 * Sends the feedback via email.
		 *
		 * @access public
		 * @param  string $reason
		 * @param  string $details
		 * @return bool
		 */
		public function send_feedback_via_email( $feedback_data ) {
			$send_to = sanitize_email( self::$other );
			$subject = sprintf( __( 'User Deactivated %s' ), self::$plugin_name );

			$headers = array(
				'Content-Type: text/html; charset=UTF-8',
			);

			$message = '<strong>' . __( 'Reason:' ) . '</strong> ' . $feedback_details->reason;

			if ( ! empty( $feedback_details->details ) ) {
				$message .= '<br><strong>' . __( 'Details:' ) . '</strong> ' . $feedback_details->details;
			}

			// If user granted permission to send personal data and site data then set appropiate headers and message.
			if ( ! empty( $feedback_details->first_name ) && ! empty( $feedback_details->from_email ) ) {
				$headers[] = 'From: ' . $feedback_details->first_name . ' <' . $feedback_details->from_email . '>';

				foreach( $feedback_data as $key => $value ) {
					if ( $key !== 'reason' || $key !== 'details' || $key !== 'active_plugins' || $key !== 'theme' ) {
						$message .= '<br>' . str_replace( '_', ' ', ucwords( $key ) ) . ': ' . $value;
					}
				}
			} else {
				$headers[] = 'From: ' . self::$plugin_name . ' <' . self::$other . '>';
			}

			$email = wp_mail( $send_to, $subject, $message, $headers );

			if ( $email ) {
				return true;
			}

			return false;
		} // END send_feedback_via_email()

		/**
		 * Includes the deactivation modal.
		 * 
		 * @access public
		 * @static
		 */
		public static function deactivation_modal() {
			global $pagenow;

			if ( 'plugins.php' !== $pagenow ) {
				return;
			}

			$plugin_slug = str_replace( '_', '-', self::$plugin_slug );
			$plugin_file = plugin_basename( self::$plugin_file );
			$plugin_name = self::$plugin_name;
			$responses   = self::$responses;

			include_once( dirname( __FILE__ ) . '/views/html-modal-deactivation.php' );
		} // END deactivation_modal()

	} // END class

} // END if class exists

$wp_admin_fb_modal = new WP_Admin_FB_Modal();
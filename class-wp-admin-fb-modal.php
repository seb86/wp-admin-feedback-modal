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

		public static $email       = '';

		/**
		 * Initialize the Feedback Modal.
		 *
		 * @access public
		 * @static
		 * @param string $plugin_slug
		 * @param string $plugin_name
		 * @param string $plugin_file
		 * @param string $email
		 */
		public static function init( $plugin_slug, $plugin_name, $plugin_file, $email ) {
			self::$plugin_slug = str_replace( '-', '_', $plugin_slug );
			self::$plugin_name = $plugin_name;
			self::$plugin_file = $plugin_file;
			self::$email       = $email;

			include_once( dirname( __FILE__ ) . '/class-wp-admin-fb-modal-assets.php' ); // Modal Assets

			add_action( 'wp_ajax_wp_admin_fb_modal_' . self::$plugin_slug, array( __CLASS__, 'ajax_feedback_modal' ) );
			add_action( 'admin_footer', array( __CLASS__, 'deactivation_modal' ), 9999 );
		} // END init()

		/**
		 * Sends feedback details from deactivation modal via email.
		 * 
		 * @access public
		 */
		public function ajax_feedback_modal() {
			$reason   = isset( $_POST['fb_reason'] ) ? trim( $_POST['fb_reason'] ) : '';
			$details  = isset( $_POST['fb_details'] ) ? sanitize_text_field( $_POST['fb_details'] ) : '';

			$user_id  = get_current_user_id();
			$userdata = get_userdata( $user_id );

			$from     = get_option('admin_email');
			$send_to  = sanitize_email( self::$email );
			$subject  = sprintf( __( 'Customer Deactivated %s' ), self::$plugin_name );

			$message  = '<strong>' . __( 'Reason:' ) . '</strong> ' . $reason;

			if ( ! empty( $details ) ) {
				$message .= '<br><strong>' . __( 'Details:' ) . '</strong> ' . $details;
			}

			$headers  = array(
				'Content-Type: text/html; charset=UTF-8',
				'From: ' . $userdata->display_name . ' <' . $from . '>'
			);

			$email = wp_mail( $send_to, $subject, $message, $headers );

			if ( $email ) {
				return true;
			}

			return false;
		} // END ajax_feedback_modal()

		/**
		 * Includes the deactivation modal.
		 * 
		 * @access public
		 * @static
		 */
		public static function deactivation_modal() {
			$plugin_slug = str_replace( '_', '-', self::$plugin_slug );
			$plugin_file = plugin_basename( self::$plugin_file );
			$plugin_name = self::$plugin_name;

			include_once( dirname( __FILE__ ) . '/views/html-modal-deactivation.php' );
		} // END deactivation_modal()

	} // END class

} // END if class exists

$wp_admin_fb_modal = new WP_Admin_FB_Modal();
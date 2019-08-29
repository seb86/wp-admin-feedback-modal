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

		public $plugin_slug = ''; // TODO: The text domain of the plugin would be best.

		public $plugin_name = ''; // TODO: Set the name of the plugin.

		public $email = "feedback@yourdomain.xyz"; // TODO: Set the email address you wish to send the feedback to.

		/**
		 * Constructor
		 *
		 * @access public
		 */
		public function __construct( $plugin_slug, $plugin_name, $email ) {
			$this->plugin_slug = str_replace( '-', '_', $plugin_slug );
			$this->plugin_name = $plugin_name;
			$this->email       = $email;

			include( dirname( __FILE__ ) . '/class-admin-assets.php' ); // Modal Assets

			add_action( 'wp_ajax_wp_admin_fb_modal_' . $this->plugin_slug, array( $this, 'ajax_feedback_modal' ) );
			add_action( 'admin_footer', array( $this, 'deactivation_modal' ), 9999 );
		} // END __construct()

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
			$send_to  = sanitize_email( $this->email );
			$subject  = sprintf( __( 'Customer Deactivated %s' ), $this->plugin_name );

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
		 */
		public function deactivation_modal() {
			$plugin_slug = $this->plugin_slug;
			$plugin_file = plugin_basename( __FILE__ ); // TODO: Change __FILE__ with the file of the main plugin.
			$plugin_name = $this->plugin_name;

			include_once( dirname( __FILE__ ) . '/views/html-modal-deactivation.php' );
		} // END deactivation_modal()

	} // END class

} // END if class exists

return new WP_Admin_FB_Modal();

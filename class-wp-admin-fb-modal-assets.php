<?php
/**
 * WP Admin Feedback Modal - Admin Assets.
 *
 * @author   Sébastien Dumont
 * @category Admin
 * @package  WP Admin Feedback Modal/Assets
 * @license  GPL-2.0+
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Admin_FB_Modal_Assets' ) ) {

	class WP_Admin_FB_Modal_Assets {

		public static $plugin_slug = '';

		/**
		 * Constructor
		 *
		 * @access  public
		 */
		public function __construct( $plugin_slug ) {
			self::$plugin_slug = str_replace( '_', '-', $plugin_slug );

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ), 10 );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 10 );
		} // END __construct()

		/**
		 * Registers and enqueues Stylesheets.
		 *
		 * @access public
		 */
		public function admin_styles() {
			$screen    = get_current_screen();
			$screen_id = $screen ? $screen->id : '';
			$suffix    = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

			// Modal
			if ( in_array( $screen_id, array( 'plugins' ) ) ) {
				wp_register_style( 'wp_admin_fb_modal', untrailingslashit( plugins_url( '/', __FILE__ ) ) . '/assets/css/modal' . $suffix . '.css' );
				wp_enqueue_style( 'wp_admin_fb_modal' );
			}
		} // END admin_styles()

		/**
		 * Registers and enqueues Scripts.
		 *
		 * @access public
		 */
		public function admin_scripts() {
			$screen    = get_current_screen();
			$screen_id = $screen ? $screen->id : '';
			$suffix    = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

			// Modal
			if ( in_array( $screen_id, array( 'plugins' ) ) ) {
				wp_register_script( 'wp_admin_fb_modal', untrailingslashit( plugins_url( '/', __FILE__ ) ) . '/assets/js/modal' . $suffix . '.js', array( 'jquery' ), null, true );
				wp_enqueue_script( 'wp_admin_fb_modal' );

				// Variables for the JavaScript
				wp_localize_script( 'wp_admin_fb_modal', 'wp_admin_fb_modal_params', array(
					'nonce'       => wp_create_nonce( 'wp-admin-feedback-modal-ajax' ),
					'plugin_slug' => self::$plugin_slug,
				) );
			}
		} // END admin_scripts()

	} // END class

} // END if class exists

return new WP_Admin_FB_Modal_Assets( self::$plugin_slug );

<?php
/*
 * Plugin Name: WP Admin Feedback Modal - Example
 * Plugin URI:  https://github.com/seb86/wp-admin-feedback-modal
 * Description: Example of including the WP Admin Feedback Modal in a plugin.
 * Author:      Sébastien Dumont
 * Author URI:  https://sebastiendumont.com
 * Version:     1.0.0
 * Text Domain: wp-admin-feedback-modal-example
 *
 * Copyright: © 2019 Sébastien Dumont, (mailme@sebastiendumont.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! class_exists( 'WP_Admin_FB_Modal_Example' ) ) {
	class WP_Admin_FB_Modal_Example {

		/**
		 * @var WP_Admin_FB_Modal_Example - the single instance of the class.
		 *
		 * @access protected
		 * @static
		 */
		protected static $_instance = null;

		/**
		 * Main Instance.
		 *
		 * Ensures only one instance is loaded or can be loaded.
		 *
		 * @access  public
		 * @static
		 * @see     WP_Admin_FB_Modal_Example()
		 * @return  WP_Admin_FB_Modal_Example - Main instance
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Cloning is forbidden.
		 *
		 * @access public
		 * @return void
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Cloning this object is forbidden.', 'wp-admin-feedback-modal-example' ) );
		} // END __clone()

		/**
		 * Unserializing instances of this class is forbidden.
		 *
		 * @access public
		 * @return void
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'Unserializing instances of this class is forbidden.', 'wp-admin-feedback-modal-example' ) );
		} // END __wakeup()

		/**
		 * Load the plugin.
		 *
		 * @access public
		 */
		public function __construct() {
			// Setup Constants.
			$this->setup_constants();

			// Include admin classes to handle all back-end functions.
			$this->admin_includes();
		} // END __construct()

		/**
		 * Setup Constants
		 *
		 * @access public
		 */
		public function setup_constants() {
			$this->define('WP_ADMIN_FB_MODAL_FILE', __FILE__);
			$this->define('WP_ADMIN_FB_MODAL_SLUG', 'wp-admin-feedback-modal-example');
		} // END setup_constants()

		/**
		 * Define constant if not already set.
		 *
		 * @access private
		 * @param  string $name
		 * @param  string|bool $value
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		} // END define()

		/**
		 * Include admin class to handle all back-end functions.
		 *
		 * @access public
		 * @return void
		 */
		public function admin_includes() {
			if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
				include_once( dirname( __FILE__ ) . '/class-wp-admin-fb-modal.php' );
			}

			$responses = array(
				array(
					'id'           => 'too-complicated',
					'value'        => __( 'Too Complicated' ),
					'label'        => sprintf( __( 'The plugin is %1$stoo complicated to configure.%2$s' ), '<strong>', '</strong>' ),
					'hidden_field' => 'no',
					'reason'       => array(
						'title'   => __( 'The plugin is too complicated to configure.' ),
						'content' => '<p>' . __( 'I\'m sorry to hear you are finding it difficult to use.' ) . '</p>'
					),
				),
				array(
					'id'           => 'with-external-button',
					'value'        => __( 'This option has a button' ),
					'label'        => __( 'I\'m struggling to get the plugin working.' ),
					'hidden_field' => 'no',
					'reason'       => array(
						'title'   => __( 'This option has a button.' ),
						'content' => '<p>' . __( 'Your content goes here.' ) . '</p>' .
									'<div class="text-center"><a class="external-button" href="' . esc_url( 'https://github.com/seb86/wp-admin-feedback-modal' ) . '" target="_blank">' . __( 'Button Text' ) . '</a></div>',
					),
				),
				array(
					'id'                 => 'another-plugin',
					'value'              => __( 'Another Plugin' ),
					'label'              => __( 'I\'m using another plugin I find better.' ),
					'hidden_field'       => 'yes',
					'hidden_placeholder' => __( 'What is the name of this plugin?' ),
				)
			);

			if ( class_exists( 'WP_Admin_FB_Modal' ) ) {
				$wp_admin_fb_modal->init( WP_ADMIN_FB_MODAL_SLUG, 'Plugin Example', __FILE__, 'feedback@yourdomain.xyz', $responses );
			}

		} // END admin_includes()

	} // END class

} // END if class exists

// Returns the main instance.
function WP_Admin_FB_Modal_Example() {
	return WP_Admin_FB_Modal_Example::instance();
}

// Run Example
WP_Admin_FB_Modal_Example();

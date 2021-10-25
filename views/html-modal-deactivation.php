<?php
/**
 * Modal: Deactivation intent form template.
 *
 * @author  Sébastien Dumont
 * @package WP Admin Feedback Modal
 * @license GPL-2.0+
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$deactivation_url = wp_nonce_url( add_query_arg( array(
	'action' => 'deactivate',
	'plugin' => urlencode( $plugin_file )
), admin_url( 'plugins.php' ) ), 'deactivate-plugin_' . $plugin_file );

/**
 * Write up your feedback responses along with any reason you can give for that response.
 * 
 * Example of a response.
 * 
 * array(
 * 		'id' => 'give-us-a-call',
 * 		'value' => 'Give us a Call', // The subject of the feedback.
 * 		'label' => 'Click here to find our number.' // Your short but on point response.
 * 		'hidden_field' => 'no',
 * 		'reason'       => array(
 * 			'title'   => __( 'Call Us.' ), // The title that overrides the modal heading.
 * 			'content' => '<p>' . __( 'Call us at 0000-123-456 to discuss your issue.' ) . '</p>' // Your ready response with a possible solution or answer.
 * 		),
 * )
 */
?>
<!-- Start of WP Admin Feedback Modal -->
<div class="wp-admin-fb-modal <?php echo $plugin_slug; ?>">

	<form action="#" method="post" id="wp-admin-fb-modal-deactivate-form">

	<div class="header">
		<div>
			<button class="return icon-arrow-left"><?php _e( 'Return' ); ?></button>
			<h2><?php echo sprintf( __( 'Quick feedback about %s' ), $plugin_name ); ?></h2>
		</div>
		<button class="close icon-close"><?php _e( 'Close' ); ?></button>
	</div>

	<div class="content">
		<div class="question isOpen">
			<h3><?php _e( 'If you have a moment, please let us know why you are deactivating:' ); ?></h3>
			<ul>
				<?php
				// List each additional possible response.
				foreach ( $responses as $response ) {
					echo '<li>';

					echo '<input type="radio" name="wp-admin-fb-modal-selected-reason" id="reason-' . $response['id'] . '" value="' . $response['value'] . '">';
					echo '<label for="reason-' . $response['id'] . '">' . $response['label'] . '</label>';

					if ( $response['hidden_field'] == 'yes' ) {
						echo '<div class="fieldHidden"><input type="text" name="reason-' . $response['id'] . '" id="reason-' . $response['id'] . '" value="" placeholder="' . $response['hidden_placeholder'] . '"></div>';
					}

					echo '</li>';
				}
				?>
				<li>
					<input type="radio" name="wp-admin-fb-modal-selected-reason" id="reason-temporary" value="<?php _e( 'Temporary Deactivation' ); ?>">
					<label for="reason-temporary"><?php echo sprintf( __( '%1$sIt is a temporary deactivation.%2$s I am just debugging an issue.' ), '<strong>', '</strong>' ); ?></label>
				</li>
				<li>
					<input type="radio" name="wp-admin-fb-modal-selected-reason" id="reason-other" value="<?php _e( 'Other' ); ?>">
					<label for="reason-other"><?php _e( 'Other' ); ?></label>
					<div class="fieldHidden">
						<textarea name="reason-other-details" id="reason-other-details" placeholder="<?php _e( 'Let me know why you are deactivating the plugin so I can improve it.' ); ?>"></textarea>
					</div>
				</li>
			</ul>
			<input id="reason" type="hidden" value="">
			<input id="details" type="hidden" value="">
		</div>

		<h4><?php _e( 'Have more feedback about the plugin? Don&rsquo;t hold back.' ); ?></h4>
		<textarea name="additional-feedback" id="feedback"></textarea>

		<?php
		// Prepare response with a reason if any.
		foreach( $responses as $response ) {
			if ( isset( $response['reason'] ) ) {
				echo '<div id="reason-' . $response['id'] . '-panel" class="hidden">';
				echo '<h3>' . $response['reason']['title'] . '</h3>';
				echo $response['reason']['content'];
				echo '</div>';
			}
		}
		?>
	</div>

	<div class="footer">
		<div class="deactivation-options">
			<input type="checkbox" name="permission" id="permission" disabled="disabled" /> <label for="grant-permission"><?php _e( 'I grant permission to send my personal data and site details to the developer/s to provide feedback.' ); ?></label>

			<input type="email" name="email" id="email" value="" placeholder="<?php _e( 'Please enter your email address' ); ?>" disabled="disabled">

			<a href="<?php echo esc_attr( $deactivation_url ); ?>" class="button button-secondary skip-feedback"><?php _e( 'Skip & Deactivate' ); ?></a>

			<div class="action-btns">
				<span class="action-spinner"><img src="<?php echo admin_url( '/images/spinner.gif' ); ?>" alt=""></span>
				<input type="submit" class="button button-secondary send-feedback isDisabled" id="send-deactivation" value="<?php _e( 'Send Feedback & Deactivate' ); ?>" disabled="disabled" />
				<button class="button button-primary cancel"><?php _e( 'Cancel' ); ?></button>
			</div>
		</div>

		<div class="what-will-be-sent"><a href="#"><?php _e( 'What will I be sending?' ); ?></a></div>

		<div class="whats-sent">
			<p><?php _e( 'If you granted permission and provided an email address the following is sent.' ); ?></p>
			<ul>
				<li><?php _e( 'Your profile name and the email address you provided.' ); ?></li>
				<li><?php _e( 'Your site url, WP version, PHP info, active plugins and theme.' ); ?></li>
			</ul>
		</div>

		<input type="hidden" id="wp_admin_fb_modal_ajax_url" value="<?php echo admin_url( 'admin-ajax.php' ); ?>" />
	</div>

	</form>

</div>

<div class="wp-admin-fb-modal-overlay <?php echo $plugin_slug; ?>"></div>
<!-- End of WP Admin Feedback Modal -->

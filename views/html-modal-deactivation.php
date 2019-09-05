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

	<div class="header">
		<div>
			<button class="return icon-arrow-left"><?php _e( 'Return' ); ?></button>
			<h2><?php echo sprintf( '%s ' . __( 'Feedback' ), $plugin_name ); ?></h2>
		</div>
		<button class="close icon-close"><?php _e( 'Close' ); ?></button>
	</div>

	<div class="content">
		<div class="question isOpen">
			<h3><?php _e( 'May I have a little info about why you are deactivating?' ); ?></h3>
			<ul>
				<li>
					<input type="radio" name="reason" id="reason-temporary" value="<?php __( 'Temporary Deactivation' ); ?>">
					<label for="reason-temporary"><?php echo sprintf( __( '%1$sIt is a temporary deactivation.%2$s I am just debugging an issue.' ), '<strong>', '</strong>' ); ?></label>
				</li>
				<?php
				// List each additional possible response.
				foreach ( $responses as $response ) {
					echo '<li>';

					echo '<input type="radio" name="reason" id="reason-' . $response['id'] . '" value="' . $response['value'] . '">';
					echo '<label for="reason-' . $response['id'] . '">' . $response['label'] . '</label>';

					if ( $response['hidden_field'] == 'yes' ) {
						echo '<div class="fieldHidden"><input type="text" name="reason-' . $response['id'] . '" id="reason-' . $response['id'] . '" value="" placeholder="' . $response['hidden_placeholder'] . '"></div>';
					}

					echo '</li>';
				}
				?>
				<li>
					<input type="radio" name="reason" id="reason-other" value="<?php __( 'Other' ); ?>">
					<label for="reason-other"><?php _e( 'Other' ); ?></label>
					<div class="fieldHidden">
						<textarea name="reason-other-details" id="reason-other-details" placeholder="<?php _e( 'Let me know why you are deactivating the plugin so I can improve it.' ); ?>"></textarea>
					</div>
				</li>
			</ul>
			<input id="reason" type="hidden" value="">
			<input id="details" type="hidden" value="">
		</div>

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
		<div>
			<a href="<?php echo esc_attr( $deactivation_url ); ?>" class="button button-primary isDisabled" disabled id="send-deactivation"><?php _e( 'Send Feedback & Deactivate' ); ?></a>
			<button class="cancel"><?php _e( 'Cancel' ); ?></button>
		</div>
		<a href="<?php echo esc_attr( $deactivation_url ); ?>" class="button button-secondary"><?php _e( 'Skip & Deactivate' ); ?></a>

		<p><a href="#"><?php _e( 'What will I be sending?' ); ?></a></p>	
		<div class="whats-sent">
			<ul>
				<li><?php _e( 'Your profile name and the email address you provide.' ); ?></li>
				<li><?php _e( 'Your site url, WP version, PHP info, active plugins and theme.' ); ?></li>
			</ul>
		</div>

		<input type="hidden" id="wp_admin_fb_modal_ajax_url" value="<?php echo admin_url( 'admin-ajax.php' ); ?>" />
	</div>

</div>

<div class="wp-admin-fb-modal-overlay"></div>
<!-- End of WP Admin Feedback Modal -->

(function( $ ) {

	$(function() {

		var modal        = $('.wp-admin-fb-modal.' + wp_admin_fb_modal_params.plugin_slug);
		var header_title = '';

		// Open feedback modal instead of deactivating plugin.
		$(document).on('click', 'tr[data-plugin="' + wp_admin_fb_modal_params.plugin_file + '"] .deactivate', function(e){
			console.log( 'Deactivate plugin link clicked!' );
			e.stopPropagation();
			e.preventDefault();
			$('input[type="radio"][name="wp-admin-fb-modal-selected-reason"]:checked').removeAttr('checked');
			$('.wp-admin-fb-modal-overlay.' + wp_admin_fb_modal_params.plugin_slug).addClass('wp-admin-fb-modal-active');
			$(modal).addClass('active');
			header_title = $('.header h2').text();
		});

		// Close or cancel feedback.
		$(document).on('click', '#wp-admin-fb-modal-deactivate-form .close, .deactivation-options .cancel', function (e) {
			console.log( 'Feedback modal closed!' );
			e.preventDefault();
			close_popup();
		});

		// Close feedback via ESC key.
		$(document).bind('keyup', function(event) {
			if ( event.keyCode == 27 ) {
				console.log('ESC key pressed.');
				close_popup();
			}
		});
		// Click radio.
		$('input[type="radio"][name="wp-admin-fb-modal-selected-reason"]').on('change', function() {
			if( $(this).is(':checked') ) {
				console.log('Radio is checked.');
			}
			console.log( 'Reason: ' + $(this).val() );
			selected_reason( $(this) );
			//$(".send-feedback").removeClass('isDisabled');
		});

		// Close feedback modal.
		function close_popup() {
			$('.wp-admin-fb-modal-overlay').removeClass('wp-admin-fb-modal-active');
			$(modal).removeClass('active');
			$('#wp-admin-fb-modal-deactivate-form').trigger("reset");
			$(".send-feedback").addClass('isDisabled');
		}

		// Set selected reason.
		function selected_reason( $this ) {
			var refThis = $this;
			var reason  = refThis.attr('id');

			// Set reason.
			$('#reason', modal).val(refThis.val());

			// Reset other values.
			$('#details', modal).val('');
			$('input[type=text], textarea', modal).val('');

			$('.fieldHidden', modal).removeClass('isOpen');
			$('.hidden', modal).removeClass('isOpen');
			$(".send-feedback", modal).removeClass('isDisabled');

			$('#permission', modal).removeAttr("disabled");
			$('#email', modal).removeAttr("disabled");

			// Check which reason and display panel if required.
			switch(reason) {
				case 'reason-temporary':
					// Nothing to do
				break;

				default:
					var $panel = $('#' + reason + '-panel');
					var $field = aElem.siblings('.fieldHidden');

					// If reason has a pre-defined answer, then show it.
					if ( $panel.length > 0 ) {
						refThis.question.removeClass('isOpen');
						refThis.return.addClass('isOpen');

						$panel.addClass('isOpen');

						var titleText = $panel.find('h3').text();
						this.title.text(titleText);
					} else {

						// Else, if reason requires user input, show hidden field.
						if ( $field.length > 0 ) {
							$field.addClass('isOpen');
							$field.find('input, textarea').focus();

							refThis.button.addClass('isDisabled').attr("disabled", true);
							refThis.permission.attr("disabled", true);
							refThis.email.attr("disabled", true);
						}
					}
				break;
			}
		}


		// Send response then deactivate plugin.
		$(document).on('submit', '.wp-admin-fb-modal.' + wp_admin_fb_modal_params.plugin_slug + ' #wp-admin-fb-modal-deactivate-form', function(e) {
			console.log('Feedback is sending!');
			var reason           = $('input[type="radio"][name="wp-admin-fb-modal-selected-reason"]:checked').val();
			var details          = $('#details').val();
			var email            = $('#email').val();
			var permission       = $('#permission').val();
			var deactivate_nonce = wp_admin_fb_modal_params.nonce;

			e.preventDefault();

			var data = {
				action                : 'wp_admin_fb_modal_' + wp_admin_fb_modal_params.plugin_slug,
				fb_reason             : reason,
				fb_details            : details,
				fb_email              : email,
				fb_permission_granted : permission,
				security              : deactivate_nonce
			};

			$.ajax({
				url: $("#wp_admin_fb_modal_ajax_url").val(),
				type: 'POST',
				data: data,
				beforeSend: function(){
					$(".action-spinner").show();
					$(".send-feedback").attr("disabled", "disabled");
				}
			}).done(function() {
				$(".action-spinner").hide();
				$(".send-feedback").removeAttr("disabled");

				// Continue deactivation.
				window.location.href = $('tr[data-plugin="' + wp_admin_fb_modal_params.plugin_file + '"] .deactivate a').attr('href');
			});
		});

		// What will be sent with feedback.
		$('.what-will-be-sent a').on('click', function(e) {
			e.preventDefault();

			var whatsSent = $('.whats-sent');

			whatsSent.toggle();
		});

	});

})( jQuery );

/*function Modal(aElem) {
	var refThis = this;

	this.elem          = aElem;
	this.radio         = $('input[name=reason]', aElem);
	this.closer        = $('.close, .cancel', aElem);
	this.return        = $('.return', aElem);
	this.question      = $('.question', aElem);
	this.button        = $('.button-primary', aElem);
	this.title         = $('.header h2', aElem);
	this.textFields    = $('input[type=text], textarea',aElem);
	this.hiddenReason  = $('#reason', aElem);
	this.hiddenDetails = $('#details', aElem);
	this.titleText     = this.title.text();
	this.permission    = $('#permission', aElem);
	this.email         = $('#email', aElem);

	// Write text
	this.textFields.keyup( function() {
		refThis.hiddenDetails.val( $(this).val() );

		if ( refThis.hiddenDetails.val() != '' ) {
			refThis.button.removeClass('isDisabled');
			refThis.button.removeAttr("disabled");

			refThis.permission.removeAttr("disabled");
			refThis.email.removeAttr("disabled");
		}
		else {
			refThis.button.addClass('isDisabled');
			refThis.button.attr("disabled", true);

			refThis.permission.attr("disabled", true);
			refThis.email.attr("disabled", true);
		}
	});
}*/

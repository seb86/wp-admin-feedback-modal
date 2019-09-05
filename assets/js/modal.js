var $ = jQuery;

$(document).ready( function( ) {
	var $modal = $('.wp-admin-fb-modal.' + wp_admin_fb_modal_params.plugin_slug);

	if ( $modal ) {
		new Modal($modal);
	}

	/**
	 * Send response then deactivate plugin.
	 */
	$('#send-deactivation').on('click', function(e) {
		var reason           = $('#reason').val();
		var details          = $('#details').val();
		var email            = $('#email').val();
		var permission       = $('#permission').val();
		var deactivation_url = $(this).attr("href");

		e.preventDefault();

		$.post( $("#wp_admin_fb_modal_ajax_url").val(), {
			action: 'wp_admin_fb_modal_' + wp_admin_fb_modal_params.plugin_slug,
			ajax_nonce: wp_admin_fb_modal_params.nonce,
			fb_reason: reason,
			fb_details: details,
			fb_email: email,
			fb_permission_granted: permission,
		} ).done( function( response ) {
			if ( ! response ) {
				console.log( 'No response!' );
				return;
			}

			// Continue deactivation.
			window.location.href=deactivation_url;
		} );
	});
});

function Modal(aElem) {
	var refThis = this;

	this.elem          = aElem;
	this.overlay       = $('.wp-admin-fb-modal-overlay');
	this.radio         = $('input[name=reason]', aElem);
	this.closer        = $('.close, .cancel', aElem);
	this.return        = $('.return', aElem);
	this.opener        = $('.plugins [data-slug="' + wp_admin_fb_modal_params.plugin_slug + '"] .deactivate');
	this.question      = $('.question', aElem);
	this.button        = $('.button-primary', aElem);
	this.title         = $('.header h2', aElem);
	this.textFields    = $('input[type=text], textarea',aElem);
	this.hiddenReason  = $('#reason', aElem);
	this.hiddenDetails = $('#details', aElem);
	this.titleText     = this.title.text();

	// Open
	this.opener.click( function() {
		refThis.open();

		return false;
	});

	// Close
	this.closer.click( function() {
		refThis.close();

		return false;
	});

	aElem.bind('keyup', function() {
		if ( event.keyCode == 27 ) { // ESC Key
			refThis.close();

			return false;
		}
	});

	// Back
	this.return.click( function() {
		refThis.returnToQuestion();

		return false;
	});

	// Click on radio
	this.radio.change( function() {
		refThis.change( $(this) );
	});

	// Write text
	this.textFields.keyup( function() {
		refThis.hiddenDetails.val( $(this).val() );

		if ( refThis.hiddenDetails.val() != '' ) {
			refThis.button.removeClass('isDisabled');
			refThis.button.removeAttr("disabled");
		}
		else {
			refThis.button.addClass('isDisabled');
			refThis.button.attr("disabled", true);
		}
	});
}

/**
 * Change modal state
 */
Modal.prototype.change = function(aElem) {
	var id      = aElem.attr('id');
	var refThis = this;

	// Reset values
	this.hiddenReason.val(aElem.val());
	this.hiddenDetails.val('');
	this.textFields.val('');

	$('.fieldHidden').removeClass('isOpen');
	$('.hidden').removeClass('isOpen');

	this.button.removeClass('isDisabled');
	this.button.removeAttr("disabled");

	switch(id) {
		case 'reason-temporary':
			// Nothing to do
		break;

		default:
			var $panel = $('#' + id + '-panel');
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

					refThis.button.addClass('isDisabled');
					refThis.button.attr("disabled", true);
				}
			}
		break;
	}
};

/**
 * Return to the question.
 */
Modal.prototype.returnToQuestion = function() {
	$('.fieldHidden').removeClass('isOpen');
	$('.hidden').removeClass('isOpen');

	this.question.addClass('isOpen');
	this.return.removeClass('isOpen');
	this.title.text(this.titleText);

	// Reset values
	this.hiddenReason.val('');
	this.hiddenDetails.val('');

	this.radio.attr('checked', false);
	this.button.addClass('isDisabled');
	this.button.attr("disabled", true);
};

/**
 * Open modal.
 */
Modal.prototype.open = function() {
	this.elem.css('display','block');
	this.overlay.css('display','block');
};

/**
 * Close modal.
 */
Modal.prototype.close = function() {
	this.returnToQuestion();
	this.elem.css('display','none');
	this.overlay.css('display','none');
};

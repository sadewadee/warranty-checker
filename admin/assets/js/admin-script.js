/**
 * Admin JavaScript for Warranty Checker
 *
 * @package WarrantyChecker
 */

(function($) {
	'use strict';

	$(document).ready(function() {

		/**
		 * Manual warranty status update
		 */
		$('#warranty-update-status').on('click', function(e) {
			e.preventDefault();

			var $button = $(this);
			var originalText = $button.html();

			// Confirm action
			if (!confirm(warrantyCheckerAdmin.strings.confirmUpdate || 'Update warranty statuses now?')) {
				return;
			}

			// Update button state
			$button.prop('disabled', true)
				.addClass('warranty-updating')
				.html('<span class="dashicons dashicons-update"></span> ' + warrantyCheckerAdmin.strings.updating);

			// Send AJAX request
			$.ajax({
				url: warrantyCheckerAdmin.ajaxUrl,
				type: 'POST',
				data: {
					action: 'warranty_update_status',
					nonce: warrantyCheckerAdmin.nonce
				},
				success: function(response) {
					if (response.success) {
						alert(response.data.message);
						location.reload();
					} else {
						alert(response.data.message || 'Update failed');
					}
				},
				error: function() {
					alert('An error occurred. Please try again.');
				},
				complete: function() {
					$button.prop('disabled', false)
						.removeClass('warranty-updating')
						.html(originalText);
				}
			});
		});

		/**
		 * Delete warranty
		 */
		$(document).on('click', '.warranty-delete', function(e) {
			e.preventDefault();

			var $button = $(this);
			var warrantyId = $button.data('id');
			var warrantyNumber = $button.data('number');

			// Confirm deletion
			if (!confirm(warrantyCheckerAdmin.strings.confirmDelete + '\n\nWarranty: ' + warrantyNumber)) {
				return;
			}

			// Update button state
			$button.prop('disabled', true).addClass('warranty-deleting');

			// Send AJAX request
			$.ajax({
				url: warrantyCheckerAdmin.ajaxUrl,
				type: 'POST',
				data: {
					action: 'warranty_delete',
					nonce: warrantyCheckerAdmin.nonce,
					warranty_id: warrantyId
				},
				success: function(response) {
					if (response.success) {
						$button.closest('tr').fadeOut(300, function() {
							$(this).remove();
						});
					} else {
						alert(response.data.message || 'Delete failed');
						$button.prop('disabled', false).removeClass('warranty-deleting');
					}
				},
				error: function() {
					alert('An error occurred. Please try again.');
					$button.prop('disabled', false).removeClass('warranty-deleting');
				}
			});
		});

	});

})(jQuery);

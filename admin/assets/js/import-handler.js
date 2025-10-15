/**
 * Import handler JavaScript for Warranty Checker
 *
 * @package WarrantyChecker
 */

(function($) {
	'use strict';

	$(document).ready(function() {

		/**
		 * Import now button
		 */
		$('#warranty-import-now').on('click', function(e) {
			e.preventDefault();

			var $button = $(this);
			var $resultDiv = $('#warranty-import-result');
			var sheetsUrl = $('#google_sheets_url').val();
			var originalText = $button.html();

			// Validate URL
			if (!sheetsUrl) {
				$resultDiv.removeClass('success')
					.addClass('error')
					.html('<p>Please enter a Google Sheets URL first.</p>')
					.show();
				return;
			}

			// Update button state
			$button.prop('disabled', true)
				.addClass('warranty-importing')
				.html('<span class="dashicons dashicons-update"></span> ' + warrantyCheckerAdmin.strings.importing);

			// Hide previous result
			$resultDiv.hide();

			// Send AJAX request
			$.ajax({
				url: warrantyCheckerAdmin.ajaxUrl,
				type: 'POST',
				data: {
					action: 'warranty_import',
					nonce: warrantyCheckerAdmin.nonce,
					sheets_url: sheetsUrl
				},
				success: function(response) {
					if (response.success) {
						var html = '<h3>Import Successful</h3>';
						html += '<p>' + response.data.message + '</p>';
						
						if (response.data.errors && response.data.errors.length > 0) {
							html += '<h4>Warnings:</h4>';
							html += '<ul>';
							response.data.errors.forEach(function(error) {
								html += '<li>' + error + '</li>';
							});
							html += '</ul>';
						}

						$resultDiv.removeClass('error')
							.addClass('success')
							.html(html)
							.show();

						// Reload after 2 seconds to update import history
						setTimeout(function() {
							location.reload();
						}, 2000);
					} else {
						$resultDiv.removeClass('success')
							.addClass('error')
							.html('<h3>Import Failed</h3><p>' + response.data.message + '</p>')
							.show();
					}
				},
				error: function(xhr, status, error) {
					$resultDiv.removeClass('success')
						.addClass('error')
						.html('<h3>Import Failed</h3><p>An error occurred: ' + error + '</p>')
						.show();
				},
				complete: function() {
					$button.prop('disabled', false)
						.removeClass('warranty-importing')
						.html(originalText);
				}
			});
		});

	});

})(jQuery);

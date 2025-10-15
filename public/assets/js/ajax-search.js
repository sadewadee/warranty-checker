/**
 * AJAX search JavaScript for Warranty Checker
 *
 * @package WarrantyChecker
 */

(function($) {
	'use strict';

	$(document).ready(function() {

		var $form = $('#warranty-checker-form');
		var $input = $('#warranty_number');
		var $result = $('#warranty-result');
		var $loading = $('#warranty-loading');

		/**
		 * Form submission
		 */
		$form.on('submit', function(e) {
			e.preventDefault();

			var warrantyNumber = $input.val().trim();
			var searchType = $form.find('input[name="search_type"]').val();

			// Validate input
			if (!warrantyNumber) {
				showError(warrantyCheckerAjax.strings.required);
				return;
			}

			// Show loading
			$form.hide();
			$result.hide();
			$loading.show();

			// Send AJAX request
			$.ajax({
				url: warrantyCheckerAjax.ajaxUrl,
				type: 'POST',
				data: {
					action: 'warranty_check',
					nonce: warrantyCheckerAjax.nonce,
					warranty_number: warrantyNumber,
					search_type: searchType
				},
				success: function(response) {
					if (response.success) {
						showResult(response.data);
					} else {
						showError(response.data.message);
					}
				},
				error: function() {
					showError(warrantyCheckerAjax.strings.error);
				},
				complete: function() {
					$loading.hide();
				}
			});
		});

		/**
		 * Show success result
		 */
		function showResult(data) {
			var daysInfo = '';
			if (data.days_remaining >= 0) {
				daysInfo = data.days_remaining + ' ' + warrantyCheckerAjax.strings.daysRemaining;
			} else {
				daysInfo = Math.abs(data.days_remaining) + ' ' + warrantyCheckerAjax.strings.daysAgo;
			}

			var statusClass = 'warranty-status-' + data.warranty_status;

			var html = '<div class="warranty-result-success">';
			html += '<div class="warranty-result-header">';
			html += '<h3>Warranty Found</h3>';
			html += '<span class="warranty-status ' + statusClass + '">' + ucFirst(data.warranty_status) + '</span>';
			html += '</div>';
			html += '<div class="warranty-result-body">';

			if (data.warranty_number) {
				html += '<div class="warranty-field">';
				html += '<label>Warranty Number:</label>';
				html += '<span>' + escapeHtml(data.warranty_number) + '</span>';
				html += '</div>';
			}

			if (data.invoice_number) {
				html += '<div class="warranty-field">';
				html += '<label>Invoice Number:</label>';
				html += '<span>' + escapeHtml(data.invoice_number) + '</span>';
				html += '</div>';
			}

			if (data.customer_name) {
				html += '<div class="warranty-field">';
				html += '<label>Customer Name:</label>';
				html += '<span>' + escapeHtml(data.customer_name) + '</span>';
				html += '</div>';
			}

			if (data.product_name) {
				html += '<div class="warranty-field">';
				html += '<label>Product:</label>';
				html += '<span>' + escapeHtml(data.product_name);
				if (data.product_category) {
					html += ' <small>(' + escapeHtml(data.product_category) + ')</small>';
				}
				html += '</span>';
				html += '</div>';
			}

			if (data.warranty_start_date && data.warranty_end_date) {
				html += '<div class="warranty-field">';
				html += '<label>Warranty Period:</label>';
				html += '<span>' + formatDate(data.warranty_start_date) + ' to ' + formatDate(data.warranty_end_date) + '</span>';
				html += '</div>';
			}

			if (data.warranty_end_date) {
				html += '<div class="warranty-field">';
				html += '<label>Days Remaining:</label>';
				html += '<span>' + daysInfo + '</span>';
				html += '</div>';
			}

			if (data.installer_name) {
				html += '<div class="warranty-field">';
				html += '<label>Installer:</label>';
				html += '<span>' + escapeHtml(data.installer_name) + '</span>';
				html += '</div>';
			}

			html += '</div>';
			html += '<button type="button" class="warranty-button-secondary" id="warranty-search-again">Check Another Warranty</button>';
			html += '</div>';

			$result.html(html).show();
		}

		/**
		 * Show error result
		 */
		function showError(message) {
			var html = '<div class="warranty-result-error">';
			html += '<h3>Warranty Not Found</h3>';
			html += '<p>' + escapeHtml(message) + '</p>';
			html += '<button type="button" class="warranty-button-secondary" id="warranty-search-again">Try Again</button>';
			html += '</div>';

			$result.html(html).show();
		}

		/**
		 * Search again button
		 */
		$(document).on('click', '#warranty-search-again', function() {
			$result.hide();
			$form.show();
			$input.val('').focus();
		});

		/**
		 * Helper: Escape HTML
		 */
		function escapeHtml(text) {
			if (!text) return '';
			var map = {
				'&': '&amp;',
				'<': '&lt;',
				'>': '&gt;',
				'"': '&quot;',
				"'": '&#039;'
			};
			return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
		}

		/**
		 * Helper: Uppercase first letter
		 */
		function ucFirst(str) {
			if (!str) return '';
			return str.charAt(0).toUpperCase() + str.slice(1);
		}

		/**
		 * Helper: Format date
		 */
		function formatDate(dateStr) {
			if (!dateStr) return '';
			var date = new Date(dateStr);
			if (isNaN(date.getTime())) return dateStr;
			
			var options = { year: 'numeric', month: 'short', day: 'numeric' };
			return date.toLocaleDateString('en-US', options);
		}

	});

})(jQuery);

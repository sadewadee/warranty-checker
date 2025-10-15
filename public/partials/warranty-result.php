<?php
/**
 * Warranty result template
 *
 * This template is not directly included but used as a reference for JavaScript rendering
 * The actual HTML is generated in ajax-search.js based on this structure
 *
 * @package WarrantyChecker
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// This file serves as a reference for the result structure
// Actual rendering is done via JavaScript in ajax-search.js
?>

<!--
Result structure for reference:

<div class="warranty-result-success">
	<div class="warranty-result-header">
		<h3>Warranty Found</h3>
		<span class="warranty-status warranty-status-{status}">{Status}</span>
	</div>

	<div class="warranty-result-body">
		<div class="warranty-field">
			<label>Warranty Number:</label>
			<span>{warranty_number}</span>
		</div>
		
		<div class="warranty-field">
			<label>Customer Name:</label>
			<span>{customer_name}</span>
		</div>

		<div class="warranty-field">
			<label>Product:</label>
			<span>{product_name}</span>
		</div>

		<div class="warranty-field">
			<label>Warranty Period:</label>
			<span>{warranty_start_date} to {warranty_end_date}</span>
		</div>

		<div class="warranty-field">
			<label>Days Remaining:</label>
			<span>{days_remaining}</span>
		</div>
	</div>

	<button type="button" class="warranty-button-secondary" id="warranty-search-again">
		Check Another Warranty
	</button>
</div>

Error structure:

<div class="warranty-result-error">
	<h3>Warranty Not Found</h3>
	<p>{error_message}</p>
	<button type="button" class="warranty-button-secondary" id="warranty-search-again">
		Try Again
	</button>
</div>
-->

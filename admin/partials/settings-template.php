<?php
/**
 * Template and help tab
 *
 * @package WarrantyChecker
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$template_url = WARRANTY_CHECKER_URL . 'templates/warranty-template-standard.csv';
?>

<div class="warranty-template-section">
	<h2><?php esc_html_e( 'Standard Template', 'warranty-checker' ); ?></h2>
	
	<div class="warranty-template-box">
		<div class="template-icon">
			<span class="dashicons dashicons-media-spreadsheet"></span>
		</div>
		<div class="template-info">
			<h3><?php esc_html_e( 'Standard Template (11 Columns)', 'warranty-checker' ); ?></h3>
			<p><?php esc_html_e( 'Use this template to format your warranty data correctly for import.', 'warranty-checker' ); ?></p>
			<a href="<?php echo esc_url( $template_url ); ?>" class="button button-primary" download>
				<span class="dashicons dashicons-download"></span>
				<?php esc_html_e( 'Download Template', 'warranty-checker' ); ?>
			</a>
		</div>
	</div>

	<hr>

	<h3><?php esc_html_e( 'Template Columns', 'warranty-checker' ); ?></h3>
	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Column Name', 'warranty-checker' ); ?></th>
				<th><?php esc_html_e( 'Required', 'warranty-checker' ); ?></th>
				<th><?php esc_html_e( 'Description', 'warranty-checker' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><code>warranty_number</code></td>
				<td><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Yes', 'warranty-checker' ); ?></td>
				<td><?php esc_html_e( 'Unique warranty identifier', 'warranty-checker' ); ?></td>
			</tr>
			<tr>
				<td><code>invoice_number</code></td>
				<td><span class="dashicons dashicons-minus"></span> <?php esc_html_e( 'No', 'warranty-checker' ); ?></td>
				<td><?php esc_html_e( 'Related invoice number', 'warranty-checker' ); ?></td>
			</tr>
			<tr>
				<td><code>customer_name</code></td>
				<td><span class="dashicons dashicons-minus"></span> <?php esc_html_e( 'No', 'warranty-checker' ); ?></td>
				<td><?php esc_html_e( 'Customer full name', 'warranty-checker' ); ?></td>
			</tr>
			<tr>
				<td><code>customer_email</code></td>
				<td><span class="dashicons dashicons-minus"></span> <?php esc_html_e( 'No', 'warranty-checker' ); ?></td>
				<td><?php esc_html_e( 'Customer email address', 'warranty-checker' ); ?></td>
			</tr>
			<tr>
				<td><code>customer_phone</code></td>
				<td><span class="dashicons dashicons-minus"></span> <?php esc_html_e( 'No', 'warranty-checker' ); ?></td>
				<td><?php esc_html_e( 'Customer phone number', 'warranty-checker' ); ?></td>
			</tr>
			<tr>
				<td><code>product_name</code></td>
				<td><span class="dashicons dashicons-minus"></span> <?php esc_html_e( 'No', 'warranty-checker' ); ?></td>
				<td><?php esc_html_e( 'Product/service name', 'warranty-checker' ); ?></td>
			</tr>
			<tr>
				<td><code>product_category</code></td>
				<td><span class="dashicons dashicons-minus"></span> <?php esc_html_e( 'No', 'warranty-checker' ); ?></td>
				<td><?php esc_html_e( 'Product category', 'warranty-checker' ); ?></td>
			</tr>
			<tr>
				<td><code>purchase_date</code></td>
				<td><span class="dashicons dashicons-minus"></span> <?php esc_html_e( 'No', 'warranty-checker' ); ?></td>
				<td><?php esc_html_e( 'Purchase date (YYYY-MM-DD)', 'warranty-checker' ); ?></td>
			</tr>
			<tr>
				<td><code>warranty_start_date</code></td>
				<td><span class="dashicons dashicons-minus"></span> <?php esc_html_e( 'No', 'warranty-checker' ); ?></td>
				<td><?php esc_html_e( 'Warranty start date (YYYY-MM-DD)', 'warranty-checker' ); ?></td>
			</tr>
			<tr>
				<td><code>warranty_end_date</code></td>
				<td><span class="dashicons dashicons-minus"></span> <?php esc_html_e( 'No', 'warranty-checker' ); ?></td>
				<td><?php esc_html_e( 'Warranty end date (YYYY-MM-DD)', 'warranty-checker' ); ?></td>
			</tr>
			<tr>
				<td><code>installer_name</code></td>
				<td><span class="dashicons dashicons-minus"></span> <?php esc_html_e( 'No', 'warranty-checker' ); ?></td>
				<td><?php esc_html_e( 'Installer/technician name', 'warranty-checker' ); ?></td>
			</tr>
		</tbody>
	</table>

	<hr>

	<h3><?php esc_html_e( 'Quick Start Guide', 'warranty-checker' ); ?></h3>
	<ol style="list-style: decimal; margin-left: 20px; line-height: 1.8;">
		<li><?php esc_html_e( 'Download the Standard Template above', 'warranty-checker' ); ?></li>
		<li><?php esc_html_e( 'Create a new Google Sheets or open existing one', 'warranty-checker' ); ?></li>
		<li><?php esc_html_e( 'Copy the template format to your Google Sheets (include header row)', 'warranty-checker' ); ?></li>
		<li><?php esc_html_e( 'Add your warranty data below the header row', 'warranty-checker' ); ?></li>
		<li><?php esc_html_e( 'Click the "Share" button in Google Sheets', 'warranty-checker' ); ?></li>
		<li><?php esc_html_e( 'Set access to "Anyone with the link can view"', 'warranty-checker' ); ?></li>
		<li><?php esc_html_e( 'Copy the share link', 'warranty-checker' ); ?></li>
		<li><?php esc_html_e( 'Go to Import Data tab and paste the link', 'warranty-checker' ); ?></li>
		<li><?php esc_html_e( 'Click "Import Now" to import your data', 'warranty-checker' ); ?></li>
	</ol>

	<hr>

	<h3><?php esc_html_e( 'Troubleshooting', 'warranty-checker' ); ?></h3>
	
	<h4><?php esc_html_e( 'Import fails with "Invalid URL"', 'warranty-checker' ); ?></h4>
	<ul style="list-style: disc; margin-left: 20px;">
		<li><?php esc_html_e( 'Make sure the URL is a Google Sheets link', 'warranty-checker' ); ?></li>
		<li><?php esc_html_e( 'Verify the sharing settings are set to "Anyone with the link can view"', 'warranty-checker' ); ?></li>
	</ul>

	<h4><?php esc_html_e( 'Some rows are skipped', 'warranty-checker' ); ?></h4>
	<ul style="list-style: disc; margin-left: 20px;">
		<li><?php esc_html_e( 'Check that warranty_number column is not empty', 'warranty-checker' ); ?></li>
		<li><?php esc_html_e( 'Make sure all rows have the same number of columns as the header', 'warranty-checker' ); ?></li>
		<li><?php esc_html_e( 'Check date format is YYYY-MM-DD', 'warranty-checker' ); ?></li>
	</ul>

	<h4><?php esc_html_e( 'Search not finding warranties', 'warranty-checker' ); ?></h4>
	<ul style="list-style: disc; margin-left: 20px;">
		<li><?php esc_html_e( 'Clear your browser cache', 'warranty-checker' ); ?></li>
		<li><?php esc_html_e( 'Check spelling of warranty number', 'warranty-checker' ); ?></li>
		<li><?php esc_html_e( 'Verify data was imported successfully in Data Management tab', 'warranty-checker' ); ?></li>
	</ul>
</div>

</div>

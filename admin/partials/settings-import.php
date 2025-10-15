<?php
/**
 * Import settings tab
 *
 * @package WarrantyChecker
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$settings = get_option( 'warranty_checker_settings', array() );
$google_sheets_url = $settings['google_sheets_url'] ?? '';
$auto_import_enabled = $settings['auto_import_enabled'] ?? 'no';
$auto_import_schedule = $settings['auto_import_schedule'] ?? 'daily';

// Get recent import logs
$database = new Warranty_Checker_Database();
$import_logs = $database->get_import_logs( 10 );
?>

<div class="warranty-import-section">
	<h2><?php esc_html_e( 'Import Warranty Data from Google Sheets', 'warranty-checker' ); ?></h2>

	<form method="post" action="">
		<?php wp_nonce_field( 'warranty_checker_settings_action', 'warranty_checker_settings_nonce' ); ?>

		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="google_sheets_url">
						<?php esc_html_e( 'Google Sheets URL', 'warranty-checker' ); ?>
					</label>
				</th>
				<td>
					<input type="url" 
						   id="google_sheets_url" 
						   name="google_sheets_url" 
						   value="<?php echo esc_attr( $google_sheets_url ); ?>" 
						   class="regular-text" 
						   placeholder="https://docs.google.com/spreadsheets/d/...">
					<p class="description">
						<?php esc_html_e( 'Paste your Google Sheets share link here. Make sure it\'s set to "Anyone with the link can view".', 'warranty-checker' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php esc_html_e( 'Auto Import', 'warranty-checker' ); ?>
				</th>
				<td>
					<fieldset>
						<label>
							<input type="radio" name="auto_import_enabled" value="no" <?php checked( $auto_import_enabled, 'no' ); ?>>
							<?php esc_html_e( 'Manual Only', 'warranty-checker' ); ?>
						</label><br>
						<label>
							<input type="radio" name="auto_import_enabled" value="yes" <?php checked( $auto_import_enabled, 'yes' ); ?>>
							<?php esc_html_e( 'Enable Auto Import', 'warranty-checker' ); ?>
						</label>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="auto_import_schedule">
						<?php esc_html_e( 'Import Schedule', 'warranty-checker' ); ?>
					</label>
				</th>
				<td>
					<select id="auto_import_schedule" name="auto_import_schedule">
						<option value="daily" <?php selected( $auto_import_schedule, 'daily' ); ?>>
							<?php esc_html_e( 'Daily', 'warranty-checker' ); ?>
						</option>
						<option value="weekly" <?php selected( $auto_import_schedule, 'weekly' ); ?>>
							<?php esc_html_e( 'Weekly', 'warranty-checker' ); ?>
						</option>
					</select>
					<p class="description">
						<?php esc_html_e( 'How often should the plugin automatically import data from Google Sheets?', 'warranty-checker' ); ?>
					</p>
				</td>
			</tr>
		</table>

		<p class="submit">
			<button type="submit" name="warranty_checker_save_settings" class="button button-primary">
				<span class="dashicons dashicons-yes"></span>
				<?php esc_html_e( 'Save Settings', 'warranty-checker' ); ?>
			</button>
			<button type="button" id="warranty-import-now" class="button button-secondary">
				<span class="dashicons dashicons-update"></span>
				<?php esc_html_e( 'Import Now', 'warranty-checker' ); ?>
			</button>
		</p>
	</form>

	<div id="warranty-import-result" style="display:none;"></div>

	<hr>

	<h3><?php esc_html_e( 'Import History', 'warranty-checker' ); ?></h3>

	<?php if ( ! empty( $import_logs ) ) : ?>
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Date', 'warranty-checker' ); ?></th>
					<th><?php esc_html_e( 'Total Rows', 'warranty-checker' ); ?></th>
					<th><?php esc_html_e( 'Imported', 'warranty-checker' ); ?></th>
					<th><?php esc_html_e( 'Updated', 'warranty-checker' ); ?></th>
					<th><?php esc_html_e( 'Skipped', 'warranty-checker' ); ?></th>
					<th><?php esc_html_e( 'Status', 'warranty-checker' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $import_logs as $log ) : ?>
					<tr>
						<td><?php echo esc_html( Warranty_Checker_Helper::format_date( $log->started_at, 'M d, Y H:i' ) ); ?></td>
						<td><?php echo esc_html( $log->total_rows ); ?></td>
						<td><?php echo esc_html( $log->imported_rows ); ?></td>
						<td><?php echo esc_html( $log->updated_rows ); ?></td>
						<td><?php echo esc_html( $log->skipped_rows ); ?></td>
						<td>
							<?php if ( 'completed' === $log->import_status ) : ?>
								<span class="warranty-status warranty-status-active">
									<?php esc_html_e( 'Completed', 'warranty-checker' ); ?>
								</span>
							<?php elseif ( 'failed' === $log->import_status ) : ?>
								<span class="warranty-status warranty-status-expired">
									<?php esc_html_e( 'Failed', 'warranty-checker' ); ?>
								</span>
							<?php else : ?>
								<span class="warranty-status warranty-status-void">
									<?php esc_html_e( 'Processing', 'warranty-checker' ); ?>
								</span>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php else : ?>
		<p><?php esc_html_e( 'No import history yet.', 'warranty-checker' ); ?></p>
	<?php endif; ?>
</div>

</div>

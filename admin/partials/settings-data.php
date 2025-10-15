<?php
/**
 * Data management tab
 *
 * @package WarrantyChecker
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$database = new Warranty_Checker_Database();

// Pagination
$per_page = 20;
$current_page = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
$search = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';
$status_filter = isset( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] ) : '';

// Get warranties
$warranties = $database->get_warranties( $per_page, $current_page, $search, $status_filter );
$total_items = $database->count_warranties( $search, $status_filter );
$total_pages = ceil( $total_items / $per_page );
?>

<div class="warranty-data-section">
	<div class="warranty-data-header">
		<h2><?php esc_html_e( 'Warranty Records', 'warranty-checker' ); ?></h2>
		<div class="warranty-data-actions">
			<button type="button" id="warranty-update-status" class="button button-secondary">
				<span class="dashicons dashicons-update"></span>
				<?php esc_html_e( 'Update Status Now', 'warranty-checker' ); ?>
			</button>
		</div>
	</div>

	<form method="get" class="warranty-search-form">
		<input type="hidden" name="page" value="warranty-checker">
		<input type="hidden" name="tab" value="data">
		
		<div class="warranty-filters">
			<input type="search" 
				   name="s" 
				   value="<?php echo esc_attr( $search ); ?>" 
				   placeholder="<?php esc_attr_e( 'Search warranties...', 'warranty-checker' ); ?>">
			
			<select name="status">
				<option value=""><?php esc_html_e( 'All Statuses', 'warranty-checker' ); ?></option>
				<option value="active" <?php selected( $status_filter, 'active' ); ?>>
					<?php esc_html_e( 'Active', 'warranty-checker' ); ?>
				</option>
				<option value="expired" <?php selected( $status_filter, 'expired' ); ?>>
					<?php esc_html_e( 'Expired', 'warranty-checker' ); ?>
				</option>
				<option value="claimed" <?php selected( $status_filter, 'claimed' ); ?>>
					<?php esc_html_e( 'Claimed', 'warranty-checker' ); ?>
				</option>
				<option value="void" <?php selected( $status_filter, 'void' ); ?>>
					<?php esc_html_e( 'Void', 'warranty-checker' ); ?>
				</option>
			</select>
			
			<button type="submit" class="button">
				<?php esc_html_e( 'Filter', 'warranty-checker' ); ?>
			</button>
		</div>
	</form>

	<?php if ( ! empty( $warranties ) ) : ?>
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Warranty Number', 'warranty-checker' ); ?></th>
					<th><?php esc_html_e( 'Customer', 'warranty-checker' ); ?></th>
					<th><?php esc_html_e( 'Product', 'warranty-checker' ); ?></th>
					<th><?php esc_html_e( 'End Date', 'warranty-checker' ); ?></th>
					<th><?php esc_html_e( 'Status', 'warranty-checker' ); ?></th>
					<th><?php esc_html_e( 'Actions', 'warranty-checker' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $warranties as $warranty ) : ?>
					<tr>
						<td>
							<strong><?php echo esc_html( $warranty->warranty_number ); ?></strong>
						</td>
						<td>
							<?php echo esc_html( $warranty->customer_name ); ?><br>
							<small><?php echo esc_html( $warranty->customer_email ); ?></small>
						</td>
						<td>
							<?php echo esc_html( $warranty->product_name ); ?>
							<?php if ( $warranty->product_category ) : ?>
								<br><small><?php echo esc_html( $warranty->product_category ); ?></small>
							<?php endif; ?>
						</td>
						<td>
							<?php echo esc_html( Warranty_Checker_Helper::format_date( $warranty->warranty_end_date ) ); ?>
							<?php
							$days_remaining = Warranty_Checker_Helper::get_days_remaining( $warranty->warranty_end_date );
							if ( $days_remaining >= 0 ) :
								?>
								<br><small><?php echo esc_html( sprintf( __( '%d days remaining', 'warranty-checker' ), $days_remaining ) ); ?></small>
							<?php else : ?>
								<br><small><?php echo esc_html( sprintf( __( 'Expired %d days ago', 'warranty-checker' ), abs( $days_remaining ) ) ); ?></small>
							<?php endif; ?>
						</td>
						<td>
							<?php echo Warranty_Checker_Helper::format_status( $warranty->warranty_status ); ?>
						</td>
						<td>
							<button type="button" 
									class="button button-small warranty-delete" 
									data-id="<?php echo esc_attr( $warranty->id ); ?>"
									data-number="<?php echo esc_attr( $warranty->warranty_number ); ?>">
								<span class="dashicons dashicons-trash"></span>
								<?php esc_html_e( 'Delete', 'warranty-checker' ); ?>
							</button>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<?php if ( $total_pages > 1 ) : ?>
			<div class="tablenav bottom">
				<div class="tablenav-pages">
					<span class="displaying-num">
						<?php echo esc_html( sprintf( __( '%d items', 'warranty-checker' ), $total_items ) ); ?>
					</span>
					<?php
					$page_links = paginate_links(
						array(
							'base'      => add_query_arg( 'paged', '%#%' ),
							'format'    => '',
							'prev_text' => __( '&laquo;', 'warranty-checker' ),
							'next_text' => __( '&raquo;', 'warranty-checker' ),
							'total'     => $total_pages,
							'current'   => $current_page,
						)
					);
					echo wp_kses_post( $page_links );
					?>
				</div>
			</div>
		<?php endif; ?>
	<?php else : ?>
		<div class="warranty-no-data">
			<p><?php esc_html_e( 'No warranty records found.', 'warranty-checker' ); ?></p>
			<?php if ( empty( $search ) && empty( $status_filter ) ) : ?>
				<p>
					<a href="?page=warranty-checker&tab=import" class="button button-primary">
						<?php esc_html_e( 'Import Data', 'warranty-checker' ); ?>
					</a>
				</p>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>

</div>

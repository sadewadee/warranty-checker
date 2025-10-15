<?php
/**
 * Admin header with tabs
 *
 * @package WarrantyChecker
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$current_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'import';
?>

<div class="wrap warranty-checker-admin">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<?php settings_errors( 'warranty_checker_messages' ); ?>

	<nav class="nav-tab-wrapper">
		<a href="?page=warranty-checker&tab=import" class="nav-tab <?php echo 'import' === $current_tab ? 'nav-tab-active' : ''; ?>">
			<span class="dashicons dashicons-upload"></span>
			<?php esc_html_e( 'Import Data', 'warranty-checker' ); ?>
		</a>
		<a href="?page=warranty-checker&tab=data" class="nav-tab <?php echo 'data' === $current_tab ? 'nav-tab-active' : ''; ?>">
			<span class="dashicons dashicons-database"></span>
			<?php esc_html_e( 'Data Management', 'warranty-checker' ); ?>
		</a>
		<a href="?page=warranty-checker&tab=form" class="nav-tab <?php echo 'form' === $current_tab ? 'nav-tab-active' : ''; ?>">
			<span class="dashicons dashicons-forms"></span>
			<?php esc_html_e( 'Form Settings', 'warranty-checker' ); ?>
		</a>
		<a href="?page=warranty-checker&tab=template" class="nav-tab <?php echo 'template' === $current_tab ? 'nav-tab-active' : ''; ?>">
			<span class="dashicons dashicons-media-spreadsheet"></span>
			<?php esc_html_e( 'Template & Help', 'warranty-checker' ); ?>
		</a>
	</nav>

	<div class="warranty-checker-content">

<?php
/**
 * Plugin activation handler
 *
 * @package WarrantyChecker
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Warranty_Checker_Activator class
 */
class Warranty_Checker_Activator {

	/**
	 * Activate the plugin
	 */
	public static function activate() {
		self::create_tables();
		self::set_default_options();
		self::schedule_cron();
		self::save_activation_version();

		// Flush rewrite rules
		flush_rewrite_rules();
	}

	/**
	 * Create database tables
	 */
	private static function create_tables() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		// Warranty records table
		$table_name = $wpdb->prefix . 'warranty_records';

		$sql = "CREATE TABLE {$table_name} (
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			warranty_number varchar(100) NOT NULL,
			invoice_number varchar(100) DEFAULT NULL,
			customer_name varchar(255) DEFAULT NULL,
			customer_email varchar(255) DEFAULT NULL,
			customer_phone varchar(50) DEFAULT NULL,
			product_name varchar(255) DEFAULT NULL,
			product_category varchar(100) DEFAULT NULL,
			purchase_date date DEFAULT NULL,
			warranty_start_date date DEFAULT NULL,
			warranty_end_date date DEFAULT NULL,
			warranty_status varchar(20) DEFAULT 'active',
			installer_name varchar(255) DEFAULT NULL,
			file_source varchar(255) DEFAULT NULL,
			imported_at datetime DEFAULT NULL,
			updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			UNIQUE KEY warranty_number (warranty_number),
			KEY invoice_number (invoice_number),
			KEY warranty_status (warranty_status),
			KEY warranty_end_date (warranty_end_date),
			KEY customer_email (customer_email)
		) {$charset_collate};";

		// Import logs table
		$table_logs = $wpdb->prefix . 'warranty_import_logs';

		$sql_logs = "CREATE TABLE {$table_logs} (
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			file_source varchar(255) NOT NULL,
			total_rows int(11) DEFAULT 0,
			imported_rows int(11) DEFAULT 0,
			updated_rows int(11) DEFAULT 0,
			skipped_rows int(11) DEFAULT 0,
			error_rows int(11) DEFAULT 0,
			error_messages text DEFAULT NULL,
			import_status varchar(20) DEFAULT 'pending',
			started_at datetime DEFAULT NULL,
			completed_at datetime DEFAULT NULL,
			PRIMARY KEY (id),
			KEY import_status (import_status),
			KEY completed_at (completed_at)
		) {$charset_collate};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
		dbDelta( $sql_logs );
	}

	/**
	 * Set default plugin options
	 */
	private static function set_default_options() {
		// General settings
		$default_settings = array(
			'google_sheets_url'    => '',
			'auto_import_enabled'  => 'no',
			'auto_import_schedule' => 'daily',
		);

		add_option( 'warranty_checker_settings', $default_settings );

		// Form customization
		$default_form = array(
			'form_title'       => __( 'Check Your Warranty', 'warranty-checker' ),
			'placeholder_text' => __( 'Enter Warranty/Invoice Number', 'warranty-checker' ),
			'button_text'      => __( 'Check Warranty', 'warranty-checker' ),
			'show_fields'      => 'both',
		);

		add_option( 'warranty_checker_form', $default_form );

		// Messages
		$default_messages = array(
			'success_message' => __( 'Warranty found', 'warranty-checker' ),
			'error_message'   => __( 'Warranty not found', 'warranty-checker' ),
			'invalid_input'   => __( 'Please enter a valid warranty number', 'warranty-checker' ),
		);

		add_option( 'warranty_checker_messages', $default_messages );
	}

	/**
	 * Schedule WP-Cron events
	 */
	private static function schedule_cron() {
		if ( ! wp_next_scheduled( 'warranty_checker_daily_update' ) ) {
			wp_schedule_event( time(), 'daily', 'warranty_checker_daily_update' );
		}

		if ( ! wp_next_scheduled( 'warranty_checker_cleanup_logs' ) ) {
			wp_schedule_event( time(), 'weekly', 'warranty_checker_cleanup_logs' );
		}
	}

	/**
	 * Save activation version
	 */
	private static function save_activation_version() {
		add_option( 'warranty_checker_version', WARRANTY_CHECKER_VERSION );
	}
}

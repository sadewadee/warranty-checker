<?php
/**
 * WP-Cron tasks handler
 *
 * @package WarrantyChecker
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Warranty_Checker_Cron class
 */
class Warranty_Checker_Cron {

	/**
	 * Database instance
	 *
	 * @var Warranty_Checker_Database
	 */
	private $database;

	/**
	 * Importer instance
	 *
	 * @var Warranty_Checker_Importer
	 */
	private $importer;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->database = new Warranty_Checker_Database();
		$this->importer = new Warranty_Checker_Importer();
	}

	/**
	 * Initialize cron hooks
	 */
	public function init() {
		add_action( 'warranty_checker_daily_update', array( $this, 'daily_status_update' ) );
		add_action( 'warranty_checker_cleanup_logs', array( $this, 'cleanup_old_logs' ) );
		add_action( 'warranty_checker_auto_import', array( $this, 'auto_import' ) );
	}

	/**
	 * Daily warranty status update
	 * Updates all expired warranties
	 */
	public function daily_status_update() {
		$count = $this->database->update_expired_warranties();

		// Clear cache after update
		if ( $count > 0 ) {
			Warranty_Checker_Helper::clear_cache();
		}

		// Log the update
		do_action( 'warranty_checker_after_status_update', $count );
	}

	/**
	 * Cleanup old import logs
	 * Removes logs older than 30 days
	 */
	public function cleanup_old_logs() {
		$count = $this->database->cleanup_old_logs();

		// Log the cleanup
		do_action( 'warranty_checker_after_logs_cleanup', $count );
	}

	/**
	 * Auto import from Google Sheets
	 * Runs based on schedule if enabled
	 */
	public function auto_import() {
		$settings = get_option( 'warranty_checker_settings', array() );

		// Check if auto import is enabled
		if ( empty( $settings['auto_import_enabled'] ) || 'yes' !== $settings['auto_import_enabled'] ) {
			return;
		}

		// Check if URL is set
		if ( empty( $settings['google_sheets_url'] ) ) {
			return;
		}

		// Perform import
		$result = $this->importer->import_from_sheets( $settings['google_sheets_url'] );

		// Log the result
		do_action( 'warranty_checker_after_auto_import', $result );
	}

	/**
	 * Schedule auto import based on settings
	 */
	public static function schedule_auto_import() {
		$settings = get_option( 'warranty_checker_settings', array() );

		// Unschedule existing events
		$timestamp = wp_next_scheduled( 'warranty_checker_auto_import' );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, 'warranty_checker_auto_import' );
		}

		// Schedule new event if enabled
		if ( ! empty( $settings['auto_import_enabled'] ) && 'yes' === $settings['auto_import_enabled'] ) {
			$schedule = $settings['auto_import_schedule'] ?? 'daily';
			wp_schedule_event( time(), $schedule, 'warranty_checker_auto_import' );
		}
	}
}

<?php
/**
 * AJAX request handler
 *
 * @package WarrantyChecker
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Warranty_Checker_Ajax class
 */
class Warranty_Checker_Ajax {

	/**
	 * Checker instance
	 *
	 * @var Warranty_Checker_Checker
	 */
	private $checker;

	/**
	 * Importer instance
	 *
	 * @var Warranty_Checker_Importer
	 */
	private $importer;

	/**
	 * Database instance
	 *
	 * @var Warranty_Checker_Database
	 */
	private $database;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->checker = new Warranty_Checker_Checker();
		$this->importer = new Warranty_Checker_Importer();
		$this->database = new Warranty_Checker_Database();
	}

	/**
	 * Initialize AJAX handlers
	 */
	public function init() {
		// Public AJAX endpoints
		add_action( 'wp_ajax_warranty_check', array( $this, 'check_warranty' ) );
		add_action( 'wp_ajax_nopriv_warranty_check', array( $this, 'check_warranty' ) );

		// Admin AJAX endpoints
		add_action( 'wp_ajax_warranty_import', array( $this, 'import_from_sheets' ) );
		add_action( 'wp_ajax_warranty_update_status', array( $this, 'update_status_manual' ) );
		add_action( 'wp_ajax_warranty_delete', array( $this, 'delete_warranty' ) );
	}

	/**
	 * Check warranty AJAX handler
	 */
	public function check_warranty() {
		// Verify nonce
		check_ajax_referer( 'warranty_checker_nonce', 'nonce' );

		// Sanitize input
		$warranty_number = sanitize_text_field( $_POST['warranty_number'] ?? '' );
		$search_type = sanitize_text_field( $_POST['search_type'] ?? 'both' );

		if ( empty( $warranty_number ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Warranty number is required', 'warranty-checker' ),
				)
			);
		}

		// Rate limiting check
		if ( ! $this->check_rate_limit() ) {
			wp_send_json_error(
				array(
					'message' => __( 'Too many requests. Please try again in 5 minutes.', 'warranty-checker' ),
				)
			);
		}

		// Check warranty
		$result = $this->checker->check_warranty( $warranty_number, $search_type );

		if ( ! $result['found'] ) {
			wp_send_json_error(
				array(
					'message' => $result['message'],
				)
			);
		}

		wp_send_json_success( $result );
	}

	/**
	 * Import from Google Sheets AJAX handler
	 */
	public function import_from_sheets() {
		// Verify nonce
		check_ajax_referer( 'warranty_checker_admin_nonce', 'nonce' );

		// Check capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Unauthorized access', 'warranty-checker' ),
				)
			);
		}

		// Get Google Sheets URL
		$sheets_url = sanitize_text_field( $_POST['sheets_url'] ?? '' );

		if ( empty( $sheets_url ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Google Sheets URL is required', 'warranty-checker' ),
				)
			);
		}

		// Import
		$result = $this->importer->import_from_sheets( $sheets_url );

		if ( ! $result['success'] ) {
			wp_send_json_error(
				array(
					'message' => $result['message'],
				)
			);
		}

		wp_send_json_success(
			array(
				'message'  => $result['message'],
				'imported' => $result['imported'] ?? 0,
				'updated'  => $result['updated'] ?? 0,
				'skipped'  => $result['skipped'] ?? 0,
				'errors'   => $result['errors'] ?? array(),
			)
		);
	}

	/**
	 * Update warranty status manually AJAX handler
	 */
	public function update_status_manual() {
		// Verify nonce
		check_ajax_referer( 'warranty_checker_admin_nonce', 'nonce' );

		// Check capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Unauthorized access', 'warranty-checker' ),
				)
			);
		}

		// Update expired warranties
		$count = $this->database->update_expired_warranties();

		// Clear cache
		Warranty_Checker_Helper::clear_cache();

		wp_send_json_success(
			array(
				'message' => sprintf(
					__( 'Updated %d warranties to expired status', 'warranty-checker' ),
					$count
				),
				'count'   => $count,
			)
		);
	}

	/**
	 * Delete warranty AJAX handler
	 */
	public function delete_warranty() {
		// Verify nonce
		check_ajax_referer( 'warranty_checker_admin_nonce', 'nonce' );

		// Check capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Unauthorized access', 'warranty-checker' ),
				)
			);
		}

		// Get warranty ID
		$warranty_id = absint( $_POST['warranty_id'] ?? 0 );

		if ( empty( $warranty_id ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid warranty ID', 'warranty-checker' ),
				)
			);
		}

		// Delete
		$result = $this->database->delete_warranty( $warranty_id );

		if ( ! $result ) {
			wp_send_json_error(
				array(
					'message' => __( 'Failed to delete warranty', 'warranty-checker' ),
				)
			);
		}

		wp_send_json_success(
			array(
				'message' => __( 'Warranty deleted successfully', 'warranty-checker' ),
			)
		);
	}

	/**
	 * Check rate limit for public requests
	 *
	 * @return bool True if allowed, false if exceeded.
	 */
	private function check_rate_limit() {
		$ip = Warranty_Checker_Helper::get_client_ip();
		$key = 'warranty_rate_limit_' . md5( $ip );
		$count = get_transient( $key );

		if ( false === $count ) {
			set_transient( $key, 1, 5 * MINUTE_IN_SECONDS );
			return true;
		}

		if ( $count >= 10 ) {
			return false;
		}

		set_transient( $key, $count + 1, 5 * MINUTE_IN_SECONDS );
		return true;
	}
}

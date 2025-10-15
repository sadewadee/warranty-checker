<?php
/**
 * Google Sheets importer
 *
 * @package WarrantyChecker
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Warranty_Checker_Importer class
 */
class Warranty_Checker_Importer {

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
		$this->database = new Warranty_Checker_Database();
	}

	/**
	 * Import from Google Sheets
	 *
	 * @param string $sheets_url Google Sheets URL.
	 * @return array Import results.
	 */
	public function import_from_sheets( $sheets_url ) {
		// Create import log
		$log_id = $this->database->create_import_log( $sheets_url );

		// Convert to CSV export URL
		$csv_url = $this->convert_to_csv_url( $sheets_url );

		if ( ! $csv_url ) {
			$this->database->update_import_log(
				$log_id,
				array(
					'import_status'   => 'failed',
					'error_messages'  => __( 'Invalid Google Sheets URL', 'warranty-checker' ),
					'total_rows'      => 0,
					'imported_rows'   => 0,
					'updated_rows'    => 0,
					'skipped_rows'    => 0,
					'error_rows'      => 0,
				)
			);

			return array(
				'success' => false,
				'message' => __( 'Invalid Google Sheets URL', 'warranty-checker' ),
			);
		}

		// Fetch CSV data
		$response = wp_remote_get(
			$csv_url,
			array(
				'timeout' => 30,
			)
		);

		if ( is_wp_error( $response ) ) {
			$this->database->update_import_log(
				$log_id,
				array(
					'import_status'   => 'failed',
					'error_messages'  => $response->get_error_message(),
					'total_rows'      => 0,
					'imported_rows'   => 0,
					'updated_rows'    => 0,
					'skipped_rows'    => 0,
					'error_rows'      => 0,
				)
			);

			return array(
				'success' => false,
				'message' => $response->get_error_message(),
			);
		}

		$csv_data = wp_remote_retrieve_body( $response );

		// Parse and import
		$result = $this->parse_and_import( $csv_data, $sheets_url );

		// Update log
		$this->database->update_import_log(
			$log_id,
			array(
				'import_status'  => $result['success'] ? 'completed' : 'failed',
				'total_rows'     => $result['total_rows'] ?? 0,
				'imported_rows'  => $result['imported'] ?? 0,
				'updated_rows'   => $result['updated'] ?? 0,
				'skipped_rows'   => $result['skipped'] ?? 0,
				'error_rows'     => count( $result['errors'] ?? array() ),
				'error_messages' => implode( "\n", $result['errors'] ?? array() ),
			)
		);

		return $result;
	}

	/**
	 * Convert Google Sheets URL to CSV export URL
	 *
	 * @param string $url Google Sheets URL.
	 * @return string|false CSV URL or false.
	 */
	private function convert_to_csv_url( $url ) {
		// Extract spreadsheet ID from various URL formats
		$patterns = array(
			'/\/d\/([a-zA-Z0-9-_]+)/',
			'/spreadsheets\/d\/([a-zA-Z0-9-_]+)/',
		);

		foreach ( $patterns as $pattern ) {
			if ( preg_match( $pattern, $url, $matches ) ) {
				$spreadsheet_id = $matches[1];
				return "https://docs.google.com/spreadsheets/d/{$spreadsheet_id}/export?format=csv";
			}
		}

		return false;
	}

	/**
	 * Parse CSV and import to database
	 *
	 * @param string $csv_data CSV content.
	 * @param string $file_source File source URL.
	 * @return array Import results.
	 */
	private function parse_and_import( $csv_data, $file_source ) {
		if ( empty( $csv_data ) ) {
			return array(
				'success'    => false,
				'message'    => __( 'Empty data received from Google Sheets', 'warranty-checker' ),
				'total_rows' => 0,
				'imported'   => 0,
				'updated'    => 0,
				'skipped'    => 0,
				'errors'     => array(),
			);
		}

		$lines = str_getcsv( $csv_data, "\n" );

		if ( empty( $lines ) ) {
			return array(
				'success'    => false,
				'message'    => __( 'No data found in Google Sheets', 'warranty-checker' ),
				'total_rows' => 0,
				'imported'   => 0,
				'updated'    => 0,
				'skipped'    => 0,
				'errors'     => array(),
			);
		}

		// Get headers
		$headers = str_getcsv( array_shift( $lines ) );
		$headers = array_map( 'trim', $headers );

		// Validate required column
		if ( ! in_array( 'warranty_number', $headers, true ) ) {
			return array(
				'success'    => false,
				'message'    => __( 'Missing required column: warranty_number', 'warranty-checker' ),
				'total_rows' => 0,
				'imported'   => 0,
				'updated'    => 0,
				'skipped'    => 0,
				'errors'     => array( 'Missing required column: warranty_number' ),
			);
		}

		$total_rows = count( $lines );
		$imported = 0;
		$updated = 0;
		$skipped = 0;
		$errors = array();

		foreach ( $lines as $index => $line ) {
			$row = str_getcsv( $line );

			if ( count( $row ) !== count( $headers ) ) {
				$skipped++;
				$errors[] = sprintf( 'Row %d: Column count mismatch', $index + 2 );
				continue;
			}

			// Combine headers with row data
			$data = array_combine( $headers, $row );

			// Sanitize
			$data = Warranty_Checker_Helper::sanitize_warranty_data( $data );

			// Validate required field
			if ( empty( $data['warranty_number'] ) ) {
				$skipped++;
				$errors[] = sprintf( 'Row %d: Missing warranty_number', $index + 2 );
				continue;
			}

			// Add file source
			$data['file_source'] = $file_source;

			// Calculate status
			if ( ! empty( $data['warranty_end_date'] ) ) {
				$data['warranty_status'] = Warranty_Checker_Helper::calculate_status(
					$data['warranty_end_date'],
					'active'
				);
			} else {
				$data['warranty_status'] = 'active';
			}

			// Check if exists
			$existing = $this->database->get_warranty( $data['warranty_number'] );

			// Import
			$result = $this->database->upsert_warranty( $data );

			if ( false !== $result ) {
				if ( $existing ) {
					$updated++;
				} else {
					$imported++;
				}
			} else {
				$errors[] = sprintf( 'Row %d: Failed to import warranty %s', $index + 2, $data['warranty_number'] );
			}
		}

		// Clear cache after import
		Warranty_Checker_Helper::clear_cache();

		return array(
			'success'    => true,
			'message'    => sprintf(
				__( 'Import completed: %d new, %d updated, %d skipped', 'warranty-checker' ),
				$imported,
				$updated,
				$skipped
			),
			'total_rows' => $total_rows,
			'imported'   => $imported,
			'updated'    => $updated,
			'skipped'    => $skipped,
			'errors'     => $errors,
		);
	}
}

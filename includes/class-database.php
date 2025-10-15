<?php
/**
 * Database operations handler
 *
 * @package WarrantyChecker
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Warranty_Checker_Database class
 */
class Warranty_Checker_Database {

	/**
	 * Warranty records table name
	 *
	 * @var string
	 */
	private $table_name;

	/**
	 * Import logs table name
	 *
	 * @var string
	 */
	private $logs_table;

	/**
	 * Constructor
	 */
	public function __construct() {
		global $wpdb;
		$this->table_name = $wpdb->prefix . 'warranty_records';
		$this->logs_table = $wpdb->prefix . 'warranty_import_logs';
	}

	/**
	 * Get warranty by number
	 *
	 * @param string $warranty_number Warranty number.
	 * @return object|null Warranty record or null.
	 */
	public function get_warranty( $warranty_number ) {
		global $wpdb;

		return $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$this->table_name} WHERE warranty_number = %s",
				sanitize_text_field( $warranty_number )
			)
		);
	}

	/**
	 * Search warranty by number or invoice
	 *
	 * @param string $search_value Search value.
	 * @param string $search_type Search type (warranty_number or invoice_number or both).
	 * @return object|null Warranty record or null.
	 */
	public function search_warranty( $search_value, $search_type = 'both' ) {
		global $wpdb;

		$search_value = sanitize_text_field( $search_value );

		if ( 'warranty_number' === $search_type ) {
			return $this->get_warranty( $search_value );
		} elseif ( 'invoice_number' === $search_type ) {
			return $wpdb->get_row(
				$wpdb->prepare(
					"SELECT * FROM {$this->table_name} WHERE invoice_number = %s",
					$search_value
				)
			);
		} else {
			// Search both
			return $wpdb->get_row(
				$wpdb->prepare(
					"SELECT * FROM {$this->table_name} 
					WHERE warranty_number = %s OR invoice_number = %s 
					LIMIT 1",
					$search_value,
					$search_value
				)
			);
		}
	}

	/**
	 * Insert or update warranty record
	 *
	 * @param array $data Warranty data.
	 * @return int|false Row ID on success, false on failure.
	 */
	public function upsert_warranty( $data ) {
		global $wpdb;

		// Check if exists
		$existing = $this->get_warranty( $data['warranty_number'] );

		$data['updated_at'] = current_time( 'mysql' );

		if ( $existing ) {
			// Update
			$result = $wpdb->update(
				$this->table_name,
				$data,
				array( 'warranty_number' => $data['warranty_number'] ),
				$this->get_format_array( $data ),
				array( '%s' )
			);

			return false !== $result ? $existing->id : false;
		} else {
			// Insert
			$data['imported_at'] = current_time( 'mysql' );

			$result = $wpdb->insert(
				$this->table_name,
				$data,
				$this->get_format_array( $data )
			);

			return false !== $result ? $wpdb->insert_id : false;
		}
	}

	/**
	 * Get all warranties with pagination
	 *
	 * @param int    $per_page Items per page.
	 * @param int    $page_number Current page.
	 * @param string $search Search term.
	 * @param string $status Filter by status.
	 * @return array Array of warranty records.
	 */
	public function get_warranties( $per_page = 20, $page_number = 1, $search = '', $status = '' ) {
		global $wpdb;

		$offset = ( $page_number - 1 ) * $per_page;

		$where = '1=1';
		$params = array();

		if ( ! empty( $search ) ) {
			$where .= ' AND (warranty_number LIKE %s OR customer_name LIKE %s OR product_name LIKE %s)';
			$search_term = '%' . $wpdb->esc_like( sanitize_text_field( $search ) ) . '%';
			$params[] = $search_term;
			$params[] = $search_term;
			$params[] = $search_term;
		}

		if ( ! empty( $status ) ) {
			$where .= ' AND warranty_status = %s';
			$params[] = sanitize_text_field( $status );
		}

		$params[] = $per_page;
		$params[] = $offset;

		$query = "SELECT * FROM {$this->table_name} WHERE {$where} ORDER BY updated_at DESC LIMIT %d OFFSET %d";

		if ( ! empty( $params ) ) {
			$query = $wpdb->prepare( $query, $params );
		}

		return $wpdb->get_results( $query );
	}

	/**
	 * Count total warranties
	 *
	 * @param string $search Search term.
	 * @param string $status Filter by status.
	 * @return int Total count.
	 */
	public function count_warranties( $search = '', $status = '' ) {
		global $wpdb;

		$where = '1=1';
		$params = array();

		if ( ! empty( $search ) ) {
			$where .= ' AND (warranty_number LIKE %s OR customer_name LIKE %s OR product_name LIKE %s)';
			$search_term = '%' . $wpdb->esc_like( sanitize_text_field( $search ) ) . '%';
			$params[] = $search_term;
			$params[] = $search_term;
			$params[] = $search_term;
		}

		if ( ! empty( $status ) ) {
			$where .= ' AND warranty_status = %s';
			$params[] = sanitize_text_field( $status );
		}

		$query = "SELECT COUNT(*) FROM {$this->table_name} WHERE {$where}";

		if ( ! empty( $params ) ) {
			$query = $wpdb->prepare( $query, $params );
		}

		return (int) $wpdb->get_var( $query );
	}

	/**
	 * Update warranty status to expired
	 *
	 * @return int Number of rows updated.
	 */
	public function update_expired_warranties() {
		global $wpdb;

		$result = $wpdb->query(
			"UPDATE {$this->table_name} 
			SET warranty_status = 'expired' 
			WHERE warranty_status = 'active' 
			AND warranty_end_date < CURDATE()"
		);

		return $result ? $result : 0;
	}

	/**
	 * Delete warranty by ID
	 *
	 * @param int $id Warranty ID.
	 * @return bool True on success, false on failure.
	 */
	public function delete_warranty( $id ) {
		global $wpdb;

		$result = $wpdb->delete(
			$this->table_name,
			array( 'id' => absint( $id ) ),
			array( '%d' )
		);

		return false !== $result;
	}

	/**
	 * Create import log
	 *
	 * @param string $file_source File source URL.
	 * @return int Log ID.
	 */
	public function create_import_log( $file_source ) {
		global $wpdb;

		$wpdb->insert(
			$this->logs_table,
			array(
				'file_source'   => sanitize_text_field( $file_source ),
				'import_status' => 'processing',
				'started_at'    => current_time( 'mysql' ),
			),
			array( '%s', '%s', '%s' )
		);

		return $wpdb->insert_id;
	}

	/**
	 * Update import log
	 *
	 * @param int   $log_id Log ID.
	 * @param array $data Log data.
	 * @return bool True on success, false on failure.
	 */
	public function update_import_log( $log_id, $data ) {
		global $wpdb;

		$data['completed_at'] = current_time( 'mysql' );

		$result = $wpdb->update(
			$this->logs_table,
			$data,
			array( 'id' => absint( $log_id ) ),
			array( '%d', '%d', '%d', '%d', '%d', '%s', '%s', '%s' ),
			array( '%d' )
		);

		return false !== $result;
	}

	/**
	 * Get recent import logs
	 *
	 * @param int $limit Number of logs to retrieve.
	 * @return array Array of log records.
	 */
	public function get_import_logs( $limit = 10 ) {
		global $wpdb;

		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$this->logs_table} ORDER BY started_at DESC LIMIT %d",
				absint( $limit )
			)
		);
	}

	/**
	 * Cleanup old logs (older than 30 days)
	 *
	 * @return int Number of logs deleted.
	 */
	public function cleanup_old_logs() {
		global $wpdb;

		$result = $wpdb->query(
			"DELETE FROM {$this->logs_table} 
			WHERE completed_at < DATE_SUB(NOW(), INTERVAL 30 DAY)"
		);

		return $result ? $result : 0;
	}

	/**
	 * Get format array for wpdb operations
	 *
	 * @param array $data Data array.
	 * @return array Format array.
	 */
	private function get_format_array( $data ) {
		$formats = array();

		foreach ( $data as $key => $value ) {
			if ( 'id' === $key ) {
				$formats[] = '%d';
			} else {
				$formats[] = '%s';
			}
		}

		return $formats;
	}
}

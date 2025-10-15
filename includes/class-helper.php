<?php
/**
 * Helper utility functions
 *
 * @package WarrantyChecker
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Warranty_Checker_Helper class
 */
class Warranty_Checker_Helper {

	/**
	 * Calculate days remaining until warranty end
	 *
	 * @param string $end_date End date (YYYY-MM-DD).
	 * @return int Days remaining (negative if expired).
	 */
	public static function get_days_remaining( $end_date ) {
		if ( empty( $end_date ) ) {
			return 0;
		}

		$end = strtotime( $end_date );
		$now = strtotime( 'today' );

		if ( false === $end ) {
			return 0;
		}

		return (int) ( ( $end - $now ) / DAY_IN_SECONDS );
	}

	/**
	 * Format warranty status with badge
	 *
	 * @param string $status Status value.
	 * @return string Formatted status with HTML badge.
	 */
	public static function format_status( $status ) {
		$statuses = array(
			'active'  => '<span class="warranty-status warranty-status-active">' . esc_html__( 'Active', 'warranty-checker' ) . '</span>',
			'expired' => '<span class="warranty-status warranty-status-expired">' . esc_html__( 'Expired', 'warranty-checker' ) . '</span>',
			'claimed' => '<span class="warranty-status warranty-status-claimed">' . esc_html__( 'Claimed', 'warranty-checker' ) . '</span>',
			'void'    => '<span class="warranty-status warranty-status-void">' . esc_html__( 'Void', 'warranty-checker' ) . '</span>',
		);

		return isset( $statuses[ $status ] ) ? $statuses[ $status ] : esc_html( $status );
	}

	/**
	 * Sanitize warranty data
	 *
	 * @param array $data Raw data.
	 * @return array Sanitized data.
	 */
	public static function sanitize_warranty_data( $data ) {
		return array(
			'warranty_number'     => sanitize_text_field( $data['warranty_number'] ?? '' ),
			'invoice_number'      => sanitize_text_field( $data['invoice_number'] ?? '' ),
			'customer_name'       => sanitize_text_field( $data['customer_name'] ?? '' ),
			'customer_email'      => sanitize_email( $data['customer_email'] ?? '' ),
			'customer_phone'      => sanitize_text_field( $data['customer_phone'] ?? '' ),
			'product_name'        => sanitize_text_field( $data['product_name'] ?? '' ),
			'product_category'    => sanitize_text_field( $data['product_category'] ?? '' ),
			'purchase_date'       => self::sanitize_date( $data['purchase_date'] ?? '' ),
			'warranty_start_date' => self::sanitize_date( $data['warranty_start_date'] ?? '' ),
			'warranty_end_date'   => self::sanitize_date( $data['warranty_end_date'] ?? '' ),
			'installer_name'      => sanitize_text_field( $data['installer_name'] ?? '' ),
		);
	}

	/**
	 * Sanitize date (ensure YYYY-MM-DD format)
	 *
	 * @param string $date Date string.
	 * @return string|null Sanitized date or null.
	 */
	public static function sanitize_date( $date ) {
		if ( empty( $date ) ) {
			return null;
		}

		$timestamp = strtotime( $date );
		if ( false === $timestamp ) {
			return null;
		}

		return gmdate( 'Y-m-d', $timestamp );
	}

	/**
	 * Format date for display
	 *
	 * @param string $date Date string.
	 * @param string $format Date format.
	 * @return string Formatted date.
	 */
	public static function format_date( $date, $format = 'M d, Y' ) {
		if ( empty( $date ) ) {
			return '';
		}

		$timestamp = strtotime( $date );
		if ( false === $timestamp ) {
			return '';
		}

		return gmdate( $format, $timestamp );
	}

	/**
	 * Calculate warranty status based on dates
	 *
	 * @param string $end_date Warranty end date.
	 * @param string $current_status Current status.
	 * @return string Calculated status.
	 */
	public static function calculate_status( $end_date, $current_status = 'active' ) {
		if ( empty( $end_date ) ) {
			return $current_status;
		}

		$days_remaining = self::get_days_remaining( $end_date );

		if ( $days_remaining < 0 && 'active' === $current_status ) {
			return 'expired';
		}

		return $current_status;
	}

	/**
	 * Get client IP address
	 *
	 * @return string IP address.
	 */
	public static function get_client_ip() {
		$ip = '0.0.0.0';

		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return sanitize_text_field( $ip );
	}

	/**
	 * Clear warranty cache
	 *
	 * @param string $warranty_number Optional specific warranty number to clear.
	 */
	public static function clear_cache( $warranty_number = '' ) {
		if ( ! empty( $warranty_number ) ) {
			$cache_key = 'warranty_' . md5( $warranty_number );
			delete_transient( $cache_key );
		} else {
			// Clear all warranty cache
			global $wpdb;
			$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_warranty_%' OR option_name LIKE '_transient_timeout_warranty_%'" );
		}
	}

	/**
	 * Format file size
	 *
	 * @param int $bytes File size in bytes.
	 * @return string Formatted file size.
	 */
	public static function format_file_size( $bytes ) {
		$units = array( 'B', 'KB', 'MB', 'GB' );
		$bytes = max( $bytes, 0 );
		$pow = floor( ( $bytes ? log( $bytes ) : 0 ) / log( 1024 ) );
		$pow = min( $pow, count( $units ) - 1 );
		$bytes /= pow( 1024, $pow );

		return round( $bytes, 2 ) . ' ' . $units[ $pow ];
	}
}

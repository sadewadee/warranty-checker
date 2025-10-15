<?php
/**
 * Warranty checker logic
 *
 * @package WarrantyChecker
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Warranty_Checker_Checker class
 */
class Warranty_Checker_Checker {

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
	 * Check warranty by number
	 *
	 * @param string $warranty_number Warranty number.
	 * @param string $search_type Search type (warranty_number, invoice_number, both).
	 * @return array Result array.
	 */
	public function check_warranty( $warranty_number, $search_type = 'both' ) {
		// Check cache first
		$cache_key = 'warranty_' . md5( $warranty_number . $search_type );
		$cached = get_transient( $cache_key );

		if ( false !== $cached ) {
			return $cached;
		}

		// Search database
		$warranty = $this->database->search_warranty( $warranty_number, $search_type );

		if ( ! $warranty ) {
			$result = array(
				'found'   => false,
				'message' => __( 'Warranty not found', 'warranty-checker' ),
			);

			// Cache not found result for shorter time (5 minutes)
			set_transient( $cache_key, $result, 5 * MINUTE_IN_SECONDS );

			return $result;
		}

		// Update status if needed
		$current_status = $warranty->warranty_status;
		$calculated_status = Warranty_Checker_Helper::calculate_status(
			$warranty->warranty_end_date,
			$current_status
		);

		if ( $calculated_status !== $current_status ) {
			// Update status in database
			$this->database->upsert_warranty(
				array(
					'warranty_number'  => $warranty->warranty_number,
					'warranty_status'  => $calculated_status,
				)
			);
			$warranty->warranty_status = $calculated_status;
		}

		// Prepare response
		$result = array(
			'found'               => true,
			'warranty_number'     => $warranty->warranty_number,
			'invoice_number'      => $warranty->invoice_number,
			'customer_name'       => $warranty->customer_name,
			'customer_email'      => $warranty->customer_email,
			'customer_phone'      => $warranty->customer_phone,
			'product_name'        => $warranty->product_name,
			'product_category'    => $warranty->product_category,
			'purchase_date'       => $warranty->purchase_date,
			'warranty_start_date' => $warranty->warranty_start_date,
			'warranty_end_date'   => $warranty->warranty_end_date,
			'warranty_status'     => $warranty->warranty_status,
			'installer_name'      => $warranty->installer_name,
			'days_remaining'      => Warranty_Checker_Helper::get_days_remaining( $warranty->warranty_end_date ),
		);

		// Cache for 1 hour
		set_transient( $cache_key, $result, HOUR_IN_SECONDS );

		return $result;
	}
}

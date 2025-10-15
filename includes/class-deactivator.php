<?php
/**
 * Plugin deactivation handler
 *
 * @package WarrantyChecker
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Warranty_Checker_Deactivator class
 */
class Warranty_Checker_Deactivator {

	/**
	 * Deactivate the plugin
	 */
	public static function deactivate() {
		self::unschedule_cron();
		flush_rewrite_rules();
	}

	/**
	 * Unschedule WP-Cron events
	 */
	private static function unschedule_cron() {
		$timestamp = wp_next_scheduled( 'warranty_checker_daily_update' );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, 'warranty_checker_daily_update' );
		}

		$timestamp = wp_next_scheduled( 'warranty_checker_cleanup_logs' );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, 'warranty_checker_cleanup_logs' );
		}
	}
}

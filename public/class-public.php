<?php
/**
 * Public-facing functionality
 *
 * @package WarrantyChecker
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Warranty_Checker_Public class
 */
class Warranty_Checker_Public {

	/**
	 * Initialize public hooks
	 */
	public function init() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_assets' ) );

		// Initialize shortcode
		$shortcode = new Warranty_Checker_Shortcode();
		$shortcode->init();
	}

	/**
	 * Enqueue public assets
	 */
	public function enqueue_public_assets() {
		global $post;

		// Only load if shortcode is present
		if ( ! is_a( $post, 'WP_Post' ) || ! has_shortcode( $post->post_content, 'warranty_checker' ) ) {
			return;
		}

		// CSS
		wp_enqueue_style(
			'warranty-checker-public',
			WARRANTY_CHECKER_URL . 'public/assets/css/public-style.css',
			array(),
			WARRANTY_CHECKER_VERSION
		);

		// JavaScript
		wp_enqueue_script(
			'warranty-checker-ajax',
			WARRANTY_CHECKER_URL . 'public/assets/js/ajax-search.js',
			array( 'jquery' ),
			WARRANTY_CHECKER_VERSION,
			true
		);

		// Localize script
		wp_localize_script(
			'warranty-checker-ajax',
			'warrantyCheckerAjax',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'warranty_checker_nonce' ),
				'strings' => array(
					'searching'     => __( 'Searching...', 'warranty-checker' ),
					'notFound'      => __( 'Warranty not found', 'warranty-checker' ),
					'error'         => __( 'An error occurred. Please try again.', 'warranty-checker' ),
					'required'      => __( 'Please enter a warranty number', 'warranty-checker' ),
					'rateLimit'     => __( 'Too many requests. Please try again in 5 minutes.', 'warranty-checker' ),
					'daysRemaining' => __( 'days remaining', 'warranty-checker' ),
					'daysAgo'       => __( 'days ago', 'warranty-checker' ),
				),
			)
		);
	}
}

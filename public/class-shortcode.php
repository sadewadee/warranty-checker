<?php
/**
 * Shortcode handler
 *
 * @package WarrantyChecker
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Warranty_Checker_Shortcode class
 */
class Warranty_Checker_Shortcode {

	/**
	 * Initialize shortcode
	 */
	public function init() {
		add_shortcode( 'warranty_checker', array( $this, 'render_shortcode' ) );
	}

	/**
	 * Render shortcode
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string Shortcode output.
	 */
	public function render_shortcode( $atts ) {
		// Get form settings
		$form_settings = get_option( 'warranty_checker_form', array() );

		// Parse attributes
		$atts = shortcode_atts(
			array(
				'title'       => $form_settings['form_title'] ?? __( 'Check Your Warranty', 'warranty-checker' ),
				'placeholder' => $form_settings['placeholder_text'] ?? __( 'Enter Warranty/Invoice Number', 'warranty-checker' ),
				'button_text' => $form_settings['button_text'] ?? __( 'Check Warranty', 'warranty-checker' ),
				'show_fields' => $form_settings['show_fields'] ?? 'both',
			),
			$atts,
			'warranty_checker'
		);

		// Start output buffering
		ob_start();

		// Include template
		include WARRANTY_CHECKER_DIR . 'public/partials/warranty-form.php';

		// Return buffered content
		return ob_get_clean();
	}
}

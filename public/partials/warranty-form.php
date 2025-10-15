<?php
/**
 * Warranty checker form template
 *
 * @package WarrantyChecker
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>

<div class="warranty-checker-wrapper">
	<div class="warranty-checker-form">
		<h2 class="warranty-form-title"><?php echo esc_html( $atts['title'] ); ?></h2>

		<form id="warranty-checker-form" class="warranty-search-form">
			<div class="warranty-form-field">
				<input type="text" 
					   id="warranty_number" 
					   name="warranty_number" 
					   class="warranty-input" 
					   placeholder="<?php echo esc_attr( $atts['placeholder'] ); ?>" 
					   required>
			</div>

			<div class="warranty-form-submit">
				<button type="submit" class="warranty-button">
					<?php echo esc_html( $atts['button_text'] ); ?>
				</button>
			</div>

			<input type="hidden" name="search_type" value="<?php echo esc_attr( $atts['show_fields'] ); ?>">
		</form>

		<div id="warranty-result" class="warranty-result" style="display:none;"></div>

		<div id="warranty-loading" class="warranty-loading" style="display:none;">
			<span class="warranty-spinner"></span>
			<span class="warranty-loading-text"><?php esc_html_e( 'Searching...', 'warranty-checker' ); ?></span>
		</div>
	</div>
</div>

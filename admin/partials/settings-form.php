<?php
/**
 * Form settings tab
 *
 * @package WarrantyChecker
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$form_settings = get_option( 'warranty_checker_form', array() );
$form_title = $form_settings['form_title'] ?? __( 'Check Your Warranty', 'warranty-checker' );
$placeholder_text = $form_settings['placeholder_text'] ?? __( 'Enter Warranty/Invoice Number', 'warranty-checker' );
$button_text = $form_settings['button_text'] ?? __( 'Check Warranty', 'warranty-checker' );
$show_fields = $form_settings['show_fields'] ?? 'both';
?>

<div class="warranty-form-section">
	<h2><?php esc_html_e( 'Form Customization', 'warranty-checker' ); ?></h2>

	<form method="post" action="">
		<?php wp_nonce_field( 'warranty_checker_form_action', 'warranty_checker_form_nonce' ); ?>

		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="form_title">
						<?php esc_html_e( 'Form Title', 'warranty-checker' ); ?>
					</label>
				</th>
				<td>
					<input type="text" 
						   id="form_title" 
						   name="form_title" 
						   value="<?php echo esc_attr( $form_title ); ?>" 
						   class="regular-text">
					<p class="description">
						<?php esc_html_e( 'The heading text that appears above the form.', 'warranty-checker' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="placeholder_text">
						<?php esc_html_e( 'Input Placeholder', 'warranty-checker' ); ?>
					</label>
				</th>
				<td>
					<input type="text" 
						   id="placeholder_text" 
						   name="placeholder_text" 
						   value="<?php echo esc_attr( $placeholder_text ); ?>" 
						   class="regular-text">
					<p class="description">
						<?php esc_html_e( 'Placeholder text for the search input field.', 'warranty-checker' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="button_text">
						<?php esc_html_e( 'Button Text', 'warranty-checker' ); ?>
					</label>
				</th>
				<td>
					<input type="text" 
						   id="button_text" 
						   name="button_text" 
						   value="<?php echo esc_attr( $button_text ); ?>" 
						   class="regular-text">
					<p class="description">
						<?php esc_html_e( 'Text for the submit button.', 'warranty-checker' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php esc_html_e( 'Search Fields', 'warranty-checker' ); ?>
				</th>
				<td>
					<fieldset>
						<label>
							<input type="radio" name="show_fields" value="warranty_number" <?php checked( $show_fields, 'warranty_number' ); ?>>
							<?php esc_html_e( 'Warranty Number Only', 'warranty-checker' ); ?>
						</label><br>
						<label>
							<input type="radio" name="show_fields" value="invoice_number" <?php checked( $show_fields, 'invoice_number' ); ?>>
							<?php esc_html_e( 'Invoice Number Only', 'warranty-checker' ); ?>
						</label><br>
						<label>
							<input type="radio" name="show_fields" value="both" <?php checked( $show_fields, 'both' ); ?>>
							<?php esc_html_e( 'Both (Search Either)', 'warranty-checker' ); ?>
						</label>
					</fieldset>
					<p class="description">
						<?php esc_html_e( 'Choose which search field(s) to display on the form.', 'warranty-checker' ); ?>
					</p>
				</td>
			</tr>
		</table>

		<p class="submit">
			<button type="submit" name="warranty_checker_save_form" class="button button-primary">
				<span class="dashicons dashicons-yes"></span>
				<?php esc_html_e( 'Save Form Settings', 'warranty-checker' ); ?>
			</button>
		</p>
	</form>

	<hr>

	<h3><?php esc_html_e( 'Shortcode', 'warranty-checker' ); ?></h3>
	<p><?php esc_html_e( 'Use this shortcode to display the warranty checker form on any page or post:', 'warranty-checker' ); ?></p>
	<code>[warranty_checker]</code>

	<h3><?php esc_html_e( 'Shortcode Parameters', 'warranty-checker' ); ?></h3>
	<p><?php esc_html_e( 'You can customize the form using these optional parameters:', 'warranty-checker' ); ?></p>
	<ul style="list-style: disc; margin-left: 20px;">
		<li><code>title</code> - <?php esc_html_e( 'Custom form title', 'warranty-checker' ); ?></li>
		<li><code>placeholder</code> - <?php esc_html_e( 'Custom placeholder text', 'warranty-checker' ); ?></li>
		<li><code>button_text</code> - <?php esc_html_e( 'Custom button text', 'warranty-checker' ); ?></li>
	</ul>
	<p><?php esc_html_e( 'Example:', 'warranty-checker' ); ?></p>
	<code>[warranty_checker title="Cek Garansi Anda" placeholder="Masukkan Nomor" button_text="Cek Sekarang"]</code>
</div>

</div>

<?php
/**
 * Admin interface handler
 *
 * @package WarrantyChecker
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Warranty_Checker_Admin class
 */
class Warranty_Checker_Admin {

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
	 * Initialize admin hooks
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_filter( 'plugin_action_links_' . WARRANTY_CHECKER_BASENAME, array( $this, 'add_action_links' ) );
	}

	/**
	 * Add admin menu
	 */
	public function add_admin_menu() {
		add_menu_page(
			__( 'Warranty Checker', 'warranty-checker' ),
			__( 'Warranty Checker', 'warranty-checker' ),
			'manage_options',
			'warranty-checker',
			array( $this, 'render_settings_page' ),
			'dashicons-yes-alt',
			30
		);
	}

	/**
	 * Enqueue admin assets
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_admin_assets( $hook ) {
		// Only load on our admin page
		if ( 'toplevel_page_warranty-checker' !== $hook ) {
			return;
		}

		// CSS
		wp_enqueue_style(
			'warranty-checker-admin',
			WARRANTY_CHECKER_URL . 'admin/assets/css/admin-style.css',
			array(),
			WARRANTY_CHECKER_VERSION
		);

		// JavaScript
		wp_enqueue_script(
			'warranty-checker-admin',
			WARRANTY_CHECKER_URL . 'admin/assets/js/admin-script.js',
			array( 'jquery' ),
			WARRANTY_CHECKER_VERSION,
			true
		);

		// Import handler
		wp_enqueue_script(
			'warranty-checker-import',
			WARRANTY_CHECKER_URL . 'admin/assets/js/import-handler.js',
			array( 'jquery' ),
			WARRANTY_CHECKER_VERSION,
			true
		);

		// Localize script
		wp_localize_script(
			'warranty-checker-admin',
			'warrantyCheckerAdmin',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'warranty_checker_admin_nonce' ),
				'strings' => array(
					'confirmDelete' => __( 'Are you sure you want to delete this warranty?', 'warranty-checker' ),
					'confirmPurge'  => __( 'Are you sure you want to delete all warranty data? This cannot be undone!', 'warranty-checker' ),
					'importing'     => __( 'Importing...', 'warranty-checker' ),
					'updating'      => __( 'Updating...', 'warranty-checker' ),
				),
			)
		);
	}

	/**
	 * Register plugin settings
	 */
	public function register_settings() {
		register_setting( 'warranty_checker_settings_group', 'warranty_checker_settings' );
		register_setting( 'warranty_checker_form_group', 'warranty_checker_form' );
		register_setting( 'warranty_checker_messages_group', 'warranty_checker_messages' );
	}

	/**
	 * Render settings page
	 */
	public function render_settings_page() {
		// Handle form submissions
		$this->handle_form_submissions();

		// Get current tab
		$current_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'import';

		// Include header
		include WARRANTY_CHECKER_DIR . 'admin/partials/admin-header.php';

		// Include tab content
		switch ( $current_tab ) {
			case 'import':
				include WARRANTY_CHECKER_DIR . 'admin/partials/settings-import.php';
				break;
			case 'data':
				include WARRANTY_CHECKER_DIR . 'admin/partials/settings-data.php';
				break;
			case 'form':
				include WARRANTY_CHECKER_DIR . 'admin/partials/settings-form.php';
				break;
			case 'template':
				include WARRANTY_CHECKER_DIR . 'admin/partials/settings-template.php';
				break;
			default:
				include WARRANTY_CHECKER_DIR . 'admin/partials/settings-import.php';
		}
	}

	/**
	 * Handle form submissions
	 */
	private function handle_form_submissions() {
		// Save general settings
		if ( isset( $_POST['warranty_checker_save_settings'] ) ) {
			check_admin_referer( 'warranty_checker_settings_action', 'warranty_checker_settings_nonce' );

			$settings = array(
				'google_sheets_url'    => sanitize_text_field( $_POST['google_sheets_url'] ?? '' ),
				'auto_import_enabled'  => sanitize_text_field( $_POST['auto_import_enabled'] ?? 'no' ),
				'auto_import_schedule' => sanitize_text_field( $_POST['auto_import_schedule'] ?? 'daily' ),
			);

			update_option( 'warranty_checker_settings', $settings );

			add_settings_error(
				'warranty_checker_messages',
				'warranty_checker_message',
				__( 'Settings saved successfully', 'warranty-checker' ),
				'success'
			);
		}

		// Save form customization
		if ( isset( $_POST['warranty_checker_save_form'] ) ) {
			check_admin_referer( 'warranty_checker_form_action', 'warranty_checker_form_nonce' );

			$form = array(
				'form_title'       => sanitize_text_field( $_POST['form_title'] ?? '' ),
				'placeholder_text' => sanitize_text_field( $_POST['placeholder_text'] ?? '' ),
				'button_text'      => sanitize_text_field( $_POST['button_text'] ?? '' ),
				'show_fields'      => sanitize_text_field( $_POST['show_fields'] ?? 'both' ),
			);

			update_option( 'warranty_checker_form', $form );

			add_settings_error(
				'warranty_checker_messages',
				'warranty_checker_message',
				__( 'Form settings saved successfully', 'warranty-checker' ),
				'success'
			);
		}
	}

	/**
	 * Add action links to plugins page
	 *
	 * @param array $links Existing links.
	 * @return array Modified links.
	 */
	public function add_action_links( $links ) {
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			admin_url( 'admin.php?page=warranty-checker' ),
			__( 'Settings', 'warranty-checker' )
		);

		array_unshift( $links, $settings_link );

		return $links;
	}
}

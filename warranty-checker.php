<?php
/**
 * Plugin Name: Warranty Checker
 * Plugin URI: https://example.com/warranty-checker
 * Description: Mengelola dan menyediakan sistem pengecekan garansi secara online dengan import dari Google Sheets
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: warranty-checker
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Plugin version
 */
define( 'WARRANTY_CHECKER_VERSION', '1.0.0' );

/**
 * Plugin directory path
 */
define( 'WARRANTY_CHECKER_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Plugin directory URL
 */
define( 'WARRANTY_CHECKER_URL', plugin_dir_url( __FILE__ ) );

/**
 * Plugin basename
 */
define( 'WARRANTY_CHECKER_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Autoloader for plugin classes
 */
require_once WARRANTY_CHECKER_DIR . 'includes/class-loader.php';

/**
 * Plugin activation hook
 */
function warranty_checker_activate() {
	require_once WARRANTY_CHECKER_DIR . 'includes/class-activator.php';
	Warranty_Checker_Activator::activate();
}
register_activation_hook( __FILE__, 'warranty_checker_activate' );

/**
 * Plugin deactivation hook
 */
function warranty_checker_deactivate() {
	require_once WARRANTY_CHECKER_DIR . 'includes/class-deactivator.php';
	Warranty_Checker_Deactivator::deactivate();
}
register_deactivation_hook( __FILE__, 'warranty_checker_deactivate' );

/**
 * Initialize the plugin
 */
function warranty_checker_init() {
	// Load text domain
	load_plugin_textdomain(
		'warranty-checker',
		false,
		dirname( WARRANTY_CHECKER_BASENAME ) . '/languages/'
	);

	// Initialize core classes
	if ( is_admin() ) {
		$admin = new Warranty_Checker_Admin();
		$admin->init();
	} else {
		$public = new Warranty_Checker_Public();
		$public->init();
	}

	// Initialize AJAX handlers (both admin and public)
	$ajax = new Warranty_Checker_Ajax();
	$ajax->init();

	// Initialize cron tasks
	$cron = new Warranty_Checker_Cron();
	$cron->init();
}
add_action( 'plugins_loaded', 'warranty_checker_init' );

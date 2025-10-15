<?php
/**
 * Autoloader for plugin classes
 *
 * @package WarrantyChecker
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Autoload plugin classes
 *
 * @param string $class_name Class name to load.
 */
function warranty_checker_autoload( $class_name ) {
	// Only load classes that start with Warranty_Checker
	if ( strpos( $class_name, 'Warranty_Checker_' ) !== 0 ) {
		return;
	}

	// Convert class name to file path
	$class_name = str_replace( 'Warranty_Checker_', '', $class_name );
	$class_name = strtolower( $class_name );
	$class_name = str_replace( '_', '-', $class_name );

	// Check in multiple directories
	$directories = array(
		WARRANTY_CHECKER_DIR . 'includes/',
		WARRANTY_CHECKER_DIR . 'admin/',
		WARRANTY_CHECKER_DIR . 'public/',
	);

	$file_name = 'class-' . $class_name . '.php';

	foreach ( $directories as $directory ) {
		$file_path = $directory . $file_name;
		if ( file_exists( $file_path ) ) {
			require_once $file_path;
			return;
		}
	}
}

spl_autoload_register( 'warranty_checker_autoload' );

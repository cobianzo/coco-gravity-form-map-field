<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Plugin Name: Asim Gravity Forms Map Field
 * Description: A new field for Gravity Forms withe the coordinates of the Gravity Form
 * Version: 3.0.0
 * Author: @cobianzo
 * Plugin URI: https://github.com/cobianzo/asim-gravity-form-map-field
 * Author URI: https://cobianzo.com
 * License: GPLv2 or later
 * Text Domain: asim-gravity-form-map-field
 *
 * @package asim-gravity-form-map-field
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// for playwright and PHPUnit test we have our own factory data generator
$is_local = str_contains( get_option( 'siteurl' ), 'localhost' );
$is_prod  = 'production' === wp_get_environment_type();
define( 'DUMMY_DATA_GENERATOR', $is_local || ! $is_prod );




// Show a notice if Gravity Forms is not loaded.
add_action( 'plugins_loaded', function () {
	require_once plugin_dir_path( __FILE__ ) . 'inc/plugin-verifications.php';
} );

add_action( 'gform_loaded', array( 'Addon_Asim_Bootstrap', 'load' ), 5 );

class Addon_Asim_Bootstrap {
	/**
	 * Includes the necessary files for the add-on and starts the add-on.
	 *
	 * Checks if the `include_addon_framework` method exists in the GFForms class.
	 * If it does, it includes the necessary files and starts the add-on.
	 *
	 * @since 1.1.0
	 */
	public static function load() {
		if ( ! method_exists( 'GFForms', 'include_addon_framework' ) ) {
			return;
		}

		GFForms::include_addon_framework();

		require_once plugin_dir_path( __FILE__ ) . 'inc/class-addon-asim.php';
		require_once plugin_dir_path( __FILE__ ) . 'inc/render.php';
		require_once plugin_dir_path( __FILE__ ) . 'inc/class-gf-field-asimmap.php';
		require_once plugin_dir_path( __FILE__ ) . 'inc/class-hooks.php';
		Addon_Asim::get_instance();
	}
}

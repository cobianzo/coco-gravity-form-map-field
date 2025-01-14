<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Plugin Name: Asim Gravity Forms Map Field
 * Description: A new field for Gravity Forms with the coordinates of the Gravity Form
 * Version: 3.0.0
 * Author: @cobianzo
 * Plugin URI: https://github.com/cobianzo/asim-gravity-form-map-field
 * Author URI: https://cobianzo.com
 * License: GPLv2 or later
 * Requires at least: 6.3
 * Requires PHP: 8.0
 * Text Domain: asim-gravity-form-map-field
 *
 * @package asim-gravity-form-map-field
 */

namespace Asim_Gravity_Form_Map_Field;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Show a notice if Gravity Forms is not loaded.
add_action( 'plugins_loaded', function () {
	require_once plugin_dir_path( __FILE__ ) . 'inc/plugin-verifications.php';
} );

add_action( 'gform_loaded', array( 'Asim_Gravity_Form_Map_Field\Addon_Asim_Bootstrap', 'load' ), 5 );

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

		\GFForms::include_addon_framework();

		require_once plugin_dir_path( __FILE__ ) . 'inc/class-addon-asim.php';
		require_once plugin_dir_path( __FILE__ ) . 'inc/render.php';
		require_once plugin_dir_path( __FILE__ ) . 'inc/class-gf-field-asimmap.php';
		require_once plugin_dir_path( __FILE__ ) . 'inc/class-hooks.php';
		Addon_Asim::get_instance();
	}
}

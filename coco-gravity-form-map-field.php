<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Plugin Name: Coco Gravity Forms Map Field
 * Description: A new field for Gravity Forms with an interactive map where you can save a marker position or a polygon drawn by the user.
 * Version: 3.1.3
 * Author: @cobianzo
 * Author URI: https://cobianzo.com
 * License: GPLv2 or later
 * Requires at least: 6.3
 * Requires PHP: 8.0
 * Text Domain: coco-gravity-form-map-field
 * Requires at least: 6.2
 * Requires PHP: 8.2
 *
 * @package coco-gravity-form-map-field
 */

namespace Coco_Gravity_Form_Map_Field;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Show a notice if Gravity Forms is not loaded.
add_action(
	'plugins_loaded',
	function (): void {
		require_once plugin_dir_path( __FILE__ ) . 'inc/plugin-verifications.php';
	}
);

add_action( 'gform_loaded', array( 'Coco_Gravity_Form_Map_Field\Addon_Coco_Bootstrap', 'load' ), 5 );

class Addon_Coco_Bootstrap {

	/**
	 * Includes the necessary files for the add-on and starts the add-on.
	 *
	 * Checks if the `include_addon_framework` method exists in the GFForms class.
	 * If it does, it includes the necessary files and starts the add-on.
	 *
	 * @since 1.1.0
	 */
	public static function load(): void {
		if ( ! method_exists( '\GFForms', 'include_addon_framework' ) ) {
			return;
		}

		\GFForms::include_addon_framework();

		require_once plugin_dir_path( __FILE__ ) . 'inc/class-addon-coco.php';
		require_once plugin_dir_path( __FILE__ ) . 'inc/render.php';
		require_once plugin_dir_path( __FILE__ ) . 'inc/class-gf-field-cocomap.php';
		require_once plugin_dir_path( __FILE__ ) . 'inc/class-hooks.php';
		Addon_Coco::get_instance();
	}
}

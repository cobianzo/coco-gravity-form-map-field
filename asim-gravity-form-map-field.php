<?php
/**
 * Plugin Name: Asim Gravity Form Map Field
 * Description: A new field for Gravity Forms withe the coordinates of the Gravity Form
 * Version: 1.16.1
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


// Autocargar archivos necesarios.
require_once plugin_dir_path( __FILE__ ) . 'inc/class-addon.php';
require_once plugin_dir_path( __FILE__ ) . 'inc/render.php';
require_once plugin_dir_path( __FILE__ ) . 'inc/class-gf-field.php';

// Inicializa el Addon.
add_action( 'gform_loaded', function () {
	if ( class_exists( 'GFForms' ) ) {
		GFAddOn::register( 'Asim_Gravity_Forms_Map_Addon' );
	}
}, 5 );

// Función para acceder a la instancia del Addon.
function asim_gravity_forms_map_addon() {
    return Asim_Gravity_Forms_Map_Addon::get_instance();
}

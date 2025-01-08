<?php
/**
 * Plugin Name: Asim Gravity Form Map Field
 * Description: A new field for Gravity Forms withe the coordinates of the Gravity Form
 * Version: 1.16.2
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

require_once plugin_dir_path( __FILE__ ) . 'inc/class-plugin-setup.php';

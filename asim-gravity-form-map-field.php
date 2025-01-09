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


// Defines the current version of the Gravity Forms Recaptcha Add-On.
define( 'GF_RECAPTCHA_VERSION', '1.6.0' );

// Defines the minimum version of Gravity Forms required to run Gravity Forms Recaptcha Add-On.
define( 'GF_RECAPTCHA_MIN_GF_VERSION', '2.5-rc-1' );

// Make sure GForms is active and call the includes
add_action( 'plugins_loaded', function () {

	/**
	 * Check that gravity forms and Gravity_Forms_Geolocation are present and
	 * setup the value for GF_GEOLOCATION_GOOGLE_API_KEY
	 */
	$show_error = '';
	if ( ! class_exists( 'GFForms' ) ) {
		$show_error = __( 'Gravity Forms must be active to use this plugin.', 'asim-gravity-form-map-field' );
	}

	if ( ! defined( 'GF_GEOLOCATION_GOOGLE_API_KEY' ) ) {
		$show_error = __( 'You need to add a Google Maps API key to use this plugin.', 'asim-gravity-form-map-field' );
	}
	// If there are errors we show the notice
	if ( $show_error ) {
		add_action('admin_notices', function () use ( $show_error ) {
			echo '<div class="notice notice-error"><p> ' . esc_html( $show_error ) . ' </p></div>';
		});
		return;
	}
}, 99 );

add_action( 'gform_loaded', function () {
	// Requires the class file.
	require_once plugin_dir_path( __FILE__ ) . 'inc/class-addon-setup.php';

	GFAddOn::register( 'Gravity_Forms\Gravity_Forms_RECAPTCHA\GF_RECAPTCHA' );
	if ( class_exists( 'GF_RECAPTCHA' ) ) {
		wp_die( ' no existe' );
		return;
	}
	// Registers the class name with GFAddOn.
	GFAddOn::register( 'GF_RECAPTCHA' );
}, 5 );

<?php

// phpcs:disable PSR2.Classes.PropertyDeclaration.Underscore

namespace Asim_Gravity_Form_Map_Field;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'GFAddOn' ) ) {
	return;
}

class Addon_Asim extends \GFAddOn {

	protected $_version                  = '3.0.0';
	protected $_min_gravityforms_version = '2.5';
	protected $_slug                     = 'asim-gravity-forms-map-addon';
	protected $_path                     = 'asim-gravity-form-map-field/asim-gravity-form-map-field.php';
	protected $_full_path                = __FILE__;
	protected $_title                    = 'Asim Gravity Forms Map Addon';
	protected $_short_title              = 'Asim Map Field';

	private static $_instance = null;

	const SETTING_GOOGLE_MAPS_API_KEY = 'google_maps_api_key';

	/**
	 * Returns an instance of the Addon_Asim class.
	 *
	 * @return Addon_Asim
	 */
	public static function get_instance() {
		if ( null === self::$_instance ) {
				self::$_instance = new self();
		}
			return self::$_instance;
	}



	/**
	 * Initialize the add-on.
	 */
	public function init() {
			parent::init();

			// Registrar el nuevo tipo de campo.
			add_filter( 'gform_field_types', [ $this, 'register_custom_field' ] );
	}


	public function register_custom_field( $field_types ) {
			$field_types['asim-map'] = __( 'Asim Google Maps', 'asim-gravity-forms-map-addon' );
			return $field_types;
	}

	// # PLUGIN SETTINGS -------------------------------------------------------------------------------

	/**
	 * Return the plugin's icon for the plugin/form settings menu.
	 * under Forms > Settings
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	public function get_menu_icon() {
		return 'dashicons-location';
	}

	public function form_settings_fields( $form ) {
		return array(
			array(
				'title'  => esc_html__( 'Form Submission Geolocation Settings', 'asim-gravity-form-map-field' ),
				'fields' => array(
					array(
						'label' => esc_html__( 'Disable Location Collection for This Form', 'asim-gravity-form-map-field' ),
						'type'  => 'toggle',
						'name'  => self::SETTING_GOOGLE_MAPS_API_KEY,
					),
				),
			),
		);
	}

	/**
	 * Define plugin settings fields.
	 *
	 * @since  1.0
	 *
	 * @return array
	 */
	public function plugin_settings_fields() {
		return array(
			array(
				'title'       => esc_html__( 'Asim Map Field Settings', 'asim-gravity-form-map-field' ),
				'description' => sprintf(
					// translators: %1$s is the opening link tag, %2$s is the closing link tag.
					esc_html__(
						'Provide an improved user experience for your address fields by using geolocation to suggest address options as you type.  If you don\'t have a Google Places API key, you can %1$screate one here.%2$s',
						'gravityformsgeolocation'
					),
					'<a href="https://developers.google.com/maps/documentation/places/web-service" target="_blank">',
					'</a>',
				),
				'fields'      => array(
					array(
						'label'   => esc_html__( 'Google Places API Key', 'asim-gravity-form-map-field' ),
						'type'    => 'text',
						'name'    => self::SETTING_GOOGLE_MAPS_API_KEY,
						'tooltip' => esc_html__( 'Enter your Google Places API key.', 'gravityformsgeolocation' ),
						'class'   => 'small',
					),
				),
			),
		);
	}

	/**
	 * @TODO: I don't where this is shown.
	 *
	 * @param {object} $settings
	 * @return void
	 */
	public function validate_plugin_settings( $settings ) {
		if ( empty( $settings[ self::SETTING_GOOGLE_MAPS_API_KEY ] ) ) {
			$this->add_error( self::SETTING_GOOGLE_MAPS_API_KEY, esc_html__( 'The Google Maps API key is required.', 'asim-gravity-form-map-field' ) );
		}
		return $settings;
	}
}

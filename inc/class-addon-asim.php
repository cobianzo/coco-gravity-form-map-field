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

	protected $_version                  = '3.1.1';
	protected $_min_gravityforms_version = '2.5';

	/**
	 * The slug of the plugin.
	 *
	 * @var string
	 */
	protected $_slug = 'asim-gravity-forms-map-addon';

	/**
	 * The path to the plugin file.
	 *
	 * @var string
	 */
	protected $_path = 'asim-gravity-form-map-field/asim-gravity-form-map-field.php';

	/**
	 * The full path to the plugin file.
	 *
	 * @var string
	 */
	protected $_full_path = __FILE__;

	/**
	 * The title of the plugin.
	 *
	 * @var string
	 */
	protected $_title = 'Asim Gravity Forms Map Addon';

	/**
	 * The short title of the plugin.
	 *
	 * @var string
	 */
	protected $_short_title = 'Asim Map Field';

	/**
	 * The instance of the plugin.
	 *
	 * @var Addon_Asim|null
	 */
	private static $_instance = null;

	/**
	 * The setting key for the Google Maps API key.
	 *
	 * @var string
	 */
	const SETTING_GOOGLE_MAPS_API_KEY = 'google_maps_api_key';

	/**
	 * Returns an instance of the Addon_Asim class.
	 *
	 * @return Addon_Asim
	 */
	public static function get_instance(): Addon_Asim {
		if ( null === self::$_instance ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Initialize the add-on.
	 */
	public function init(): void {
		parent::init();

		// Register the new field type.
		add_filter( 'gform_field_types', [ $this, 'register_custom_field' ] );
	}

	/**
	 * Register the custom field type.
	 *
	 * @param array $field_types The field types.
	 *
	 * @return array
	 */
	public function register_custom_field( array $field_types ): array {
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
	public function get_menu_icon(): string {
		return 'dashicons-location';
	}

	/**
	 * Form settings fields.
	 *
	 * @param array $form The form.
	 *
	 * @return array
	 */
	public function form_settings_fields( $form ): array {
		return [
			[
				'title'  => __( 'Form Submission Geolocation Settings', 'asim-gravity-forms-map-addon' ),
				'fields' => [
					[
						'label' => __( 'Disable Location Collection for This Form', 'asim-gravity-forms-map-addon' ),
						'type'  => 'toggle',
						'name'  => self::SETTING_GOOGLE_MAPS_API_KEY,
					],
				],
			],
		];
	}

	/**
	 * Define plugin settings fields.
	 *
	 * @since  1.0
	 *
	 * @return array
	 */
	public function plugin_settings_fields(): array {
		return [
			[
				'title'       => __( 'Asim Map Field Settings', 'asim-gravity-forms-map-addon' ),
				'description' => sprintf(
					// translators: %1$s is the opening link tag, %2$s is the closing link tag.
					__( 'Provide an improved user experience for your address fields by using geolocation to suggest address options as you type.  If you don\'t have a Google Places API key, you can %1$screate one here.%2$s', 'gravityformsgeolocation' ),
					'<a href="https://developers.google.com/maps/documentation/places/web-service" target="_blank">',
					'</a>',
				),
				'fields'      => [
					[
						'label'   => __( 'Google Places API Key', 'asim-gravity-forms-map-addon' ),
						'type'    => 'text',
						'name'    => self::SETTING_GOOGLE_MAPS_API_KEY,
						'tooltip' => __( 'Enter your Google Places API key.', 'gravityformsgeolocation' ),
						'class'   => 'small',
					],
				],
			],
		];
	}

	/**
	 * Validate plugin settings.
	 *
	 * @param array $settings The settings.
	 *
	 * @return array
	 */
	public function validate_plugin_settings( array $settings ): array {
		if ( empty( $settings[ self::SETTING_GOOGLE_MAPS_API_KEY ] ) ) {
			$this->add_error( self::SETTING_GOOGLE_MAPS_API_KEY, __( 'The Google Maps API key is required.', 'asim-gravity-forms-map-addon' ) );
		}
		return $settings;
	}
}

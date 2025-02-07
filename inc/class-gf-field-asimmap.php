<?php

namespace Asim_Gravity_Form_Map_Field;

if ( ! class_exists( 'GF_Field' ) ) {
	return;
}

class GF_Field_AsimMap extends \GF_Field {

	public $type = 'asim-map';

	/**
	 * Google Maps API key
	 *
	 * @var string
	 */
	public $google_maps_api_key = '';

	/**
	 * Returns the field's form editor title.
	 *
	 * @since 2.5
	 *
	 * @return string
	 */
	public function get_form_editor_field_title(): string {
		return esc_attr__( 'Asim Map', 'asim-gravity-forms-map-addon' );
	}

	/**
	 * Returns the field's form editor description.
	 *
	 * @since 2.5
	 *
	 * @return string
	 */
	public function get_form_editor_field_description(): string {
		return esc_attr__( 'Stores information that should not be visible to the user but can be processed and saved with the user submission.', 'asim-gravity-forms-map-addon' );
	}

	/**
	 * Returns the field's form editor icon.
	 *
	 * This could be an icon url or a gform-icon class.
	 *
	 * @since 2.5
	 *
	 * @return string
	 */
	public function get_form_editor_field_icon(): string {
		return 'dashicons-location';
	}

	/**
	 * Returns true if conditional logic is supported by the field.
	 *
	 * @since 2.5
	 *
	 * @return bool
	 */
	public function is_conditional_logic_supported(): bool {
		return true;
	}

	/**
	 * Returns the field's form editor settings.
	 *
	 * @since 2.5
	 *
	 * @return array
	 */
	public function get_form_editor_field_settings(): array {
		return array(
			'prepopulate_field_setting',
			'label_setting',
			'default_value_setting',
			'rules_setting',
			'map_type_setting',
			'autocomplete_types_setting',
		);
	}

	/**
	 * Returns the field's input html.
	 *
	 * @param array $form The form.
	 * @param string $value The value of the field.
	 * @param array $entry The entry.
	 *
	 * @return string
	 */
	public function get_field_input( $form, $value = '', $entry = null ): string {

		if ( ! wp_script_is( 'asim-map-js', 'enqueued' ) ) {
			$asset_file = include dirname( plugin_dir_path( __FILE__ ) ) . '/build/asim-gravity-form-map-field.asset.php';
			wp_enqueue_script( 'asim-map-js', dirname( plugin_dir_url( __FILE__ ) ) . '/build/asim-gravity-form-map-field.js', $asset_file['dependencies'], $asset_file['version'], false );
		}

		$addon = Addon_Asim::get_instance();
		if ( ! strlen( $this->google_maps_api_key ) ) {
			$this->google_maps_api_key = $addon->get_plugin_setting( Addon_Asim::SETTING_GOOGLE_MAPS_API_KEY );
		}

		$input = asim_render_map_field( $this, $form, $value );

		return sprintf( "<div class='ginput_container ginput_container_text'>%s</div>", $input );
	}

	/**
	 * In the editor, wrap the field
	 */
	public function get_field_content( $value, $force_frontend_label, $form ): string {
		$form_id         = $form['id'];
		$admin_buttons   = $this->get_admin_buttons();
		$is_entry_detail = $this->is_entry_detail();
		$is_form_editor  = $this->is_form_editor();
		$is_admin        = $is_entry_detail || $is_form_editor;
		$field_label     = $this->get_field_label( $force_frontend_label, $value );
		$field_id        = $is_admin || 0 === $form_id ? "input_{$this->id}" : 'input_' . $form_id . "_{$this->id}";
		$field_content   = sprintf( "%s<label class='gfield_label gform-field-label' for='%s'>%s</label>{FIELD}", $admin_buttons, $field_id, esc_html( $field_label ) );

		return $field_content;
	}

	// # FIELD FILTER UI HELPERS ---------------------------------------------------------------------------------------

	/**
	 * Returns the filter operators for the current field.
	 *
	 * @since 2.4
	 *
	 * @return array
	 */
	public function get_filter_operators(): array {
		$operators   = parent::get_filter_operators();
		$operators[] = 'contains';

		return $operators;
	}

	/**
	 * Validates that a string represents valid geographic coordinates.
	 *
	 * The expected format is "latitude,longitude" where:
	 * - latitude must be between -90 and 90
	 * - longitude must be between -180 and 180
	 *
	 * @param string $value The value to validate
	 * @return bool True if the value is valid coordinates, false otherwise
	 */
	public function validate_coordinates( string $value ): bool {
		// If the value is empty, it is valid (the field might be optional)
		if ( empty( $value ) ) {
			return true;
		}

		// Verify the basic format (two numbers separated by a comma)
		if ( ! preg_match( '/^-?\d+\.?\d*,-?\d+\.?\d*$/', $value ) ) {
			return false;
		}

		// Split the coordinates
		$coordinates = explode( ',', $value );

		if ( count( $coordinates ) !== 2 ) {
			return false;
		}

		$lat = (float) $coordinates[0];
		$lng = (float) $coordinates[1];

		// Validate the range
		if ( $lat < -90 || $lat > 90 ) {
			return false;
		}

		if ( $lng < -180 || $lng > 180 ) {
			return false;
		}

		return true;
	}
}

\GF_Fields::register( new GF_Field_AsimMap() );

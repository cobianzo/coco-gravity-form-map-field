<?php

namespace Asim_Gravity_Form_Map_Field;

if ( ! class_exists( 'GF_Field' ) ) {
	return;
}

class GF_Field_AsimMap extends \GF_Field {

	public $type = 'asim-map';

	public $google_maps_api_key = '';

	public function get_form_editor_field_title() {
		return esc_attr__( 'Asim Map', 'asim-gravity-forms-map-addon' );
	}

	/**
	 * Returns the field's form editor description.
	 *
	 * @since 2.5
	 *
	 * @return string
	 */
	public function get_form_editor_field_description() {
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
	public function get_form_editor_field_icon() {
		return 'dashicons-location';
	}

	public function is_conditional_logic_supported() {
		return true;
	}

	public function get_form_editor_field_settings() {
		return array(
			'prepopulate_field_setting',
			'label_setting',
			'default_value_setting',
			'rules_setting',
			'map_type_setting',
		);
	}

	public function get_field_input( $form, $value = '', $entry = null ) {

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
	public function get_field_content( $value, $force_frontend_label, $form ) {
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
	public function get_filter_operators() {
		$operators   = parent::get_filter_operators();
		$operators[] = 'contains';

		return $operators;
	}

	/**
	 * Valida que una cadena represente coordenadas geográficas válidas.
	 * El formato esperado es "latitud,longitud" donde:
	 * - latitud debe estar entre -90 y 90
	 * - longitud debe estar entre -180 y 180
	 *
	 * @param string $value El valor a validar
	 * @return bool True si son coordenadas válidas, false en caso contrario
	 */
	public function validate_coordinates( $value ) {
		// Si está vacío, es válido (el campo podría ser opcional)
		if ( empty( $value ) ) {
			return true;
		}

		// Verificar el formato básico (dos números separados por coma)
		if ( ! preg_match( '/^-?\d+\.?\d*,-?\d+\.?\d*$/', $value ) ) {
			return false;
		}

		// Separar en latitud y longitud
		$coordinates = explode( ',', $value );

		if ( count( $coordinates ) !== 2 ) {
			return false;
		}

		$lat = (float) $coordinates[0];
		$lng = (float) $coordinates[1];

		// Validar rangos
		if ( $lat < -90 || $lat > 90 ) {
			return false;
		}

		if ( $lng < -180 || $lng > 180 ) {
			return false;
		}

		return true;
	}

	/**
	 * Validates the provided value as geographic coordinates.
	 *
	 * If the value does not represent valid coordinates, sets the validation
	 * state to failed and provides an appropriate validation message.
	 *
	 * @param string $value The value to validate as coordinates.
	 * @param array  $form  The form data associated with the validation.
	 *
	 * @return void
	 */
	public function validate( $value, $form ) {
		if ( ! $this->validate_coordinates( $value ) ) {
			$this->failed_validation  = true;
			$this->validation_message = __( 'For some reason the coordinates don\'t seem to be valid.', 'asim-gravity-form-map-field' );
		}
	}

	/**
	 * Sanitiza las coordenadas para asegurar un formato consistente.
	 *
	 * @param string $value El valor a sanitizar
	 * @return string Las coordenadas sanitizadas
	 */
	public function sanitize_coordinates( $value ) {

		// Si no contiene una coma, no son coordenadas válidas
		if ( false === strpos( $value, ',' ) ) {
			return '';
		}

		// Eliminar espacios
		$value = preg_replace( '/\s+/', '', $value );

		// Si no son coordenadas válidas, devolver vacío
		if ( ! $this->validate_coordinates( $value ) ) {
				return '';
		}

		// Separar y formatear con 6 decimales
		$coordinates = explode( ',', $value );
		$lat         = (float) $coordinates[0];
		$lng         = (float) $coordinates[1];

		return sprintf( '%.6f,%.6f', $lat, $lng );
	}


	public function get_value_save_entry( $value, $form, $input_name, $lead_id, $lead ) {
		return $this->sanitize_coordinates( $value );
	}

	public function get_value_entry_detail( $value, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {
		return $this->sanitize_coordinates( $value );
	}
}

\GF_Fields::register( new GF_Field_AsimMap() );

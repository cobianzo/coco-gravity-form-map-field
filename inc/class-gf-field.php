<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class GF_Field_Asim_Map extends GF_Field {

	public $type = 'asim-map';

	protected $google_maps_api_key;

	public function __construct() {
		parent::__construct();
		// TODO: grab this from settings.
		$this->google_maps_api_key = 'AIzaSyBCxO-yk6_PshNJCZ-D8OIuZElDWto_jyY';
	}

	public function get_form_editor_field_title() {
		return __( 'Asim Google Maps', 'asim-gravity-forms-map-addon' );
	}

	// Agrega configuraciones en el editor de formulario.
	public function get_form_editor_field_settings() {
		return array(
			'default_value_setting',
			'label_setting',
			'description_setting',
		);
	}

	// Renderiza el campo en el frontend.
	public function get_field_input( $form, $value = '', $entry = null ) {
		$field_id = $this->id;
		$input_id = $form['id'] . '_' . $field_id;

		$form_id         = $form['id'];
		$is_entry_detail = $this->is_entry_detail();
		return 'CVADFFDA '. print_r( $this->is_form_editor(), 1 );
		$is_form_editor  = $this->is_form_editor();

		$id       = (int) $this->id;
		$field_id = $is_entry_detail || $is_form_editor || $form_id == 0 ? "input_$id" : 'input_' . $form_id . "_$id";

		$disabled_text = $is_form_editor ? 'disabled="disabled"' : '';

		$field_type         = $is_entry_detail || $is_form_editor ? 'text' : 'hidden';
		$class_attribute    = $is_entry_detail || $is_form_editor ? '' : "class='gform_hidden'";
		$required_attribute = $this->isRequired ? 'aria-required="true"' : '';
		$invalid_attribute  = $this->failed_validation ? 'aria-invalid="true"' : 'aria-invalid="false"';

		$input = sprintf( "<input name='input_%d' id='%s' type='$field_type' {$class_attribute} {$required_attribute} {$invalid_attribute} value='%s' %s/>", $id, $field_id, esc_attr( $value ), $disabled_text );

		return sprintf( "<div class='ginput_container ginput_container_text'>%s</div>", $input );

		// Renderiza un input para un mapa interactivo.
		return asim_render_map_field( $this, $input_id, $value );
	}

	public function get_field_content( $value, $force_frontend_label, $form ) {
		$form_id         = $form['id'];
		$admin_buttons   = $this->get_admin_buttons();
		$is_entry_detail = $this->is_entry_detail();
		$is_form_editor  = $this->is_form_editor();
		$is_admin        = $is_entry_detail || $is_form_editor;
		$field_label     = $this->get_field_label( $force_frontend_label, $value );
		$field_id        = $is_admin || $form_id == 0 ? "input_{$this->id}" : 'input_' . $form_id . "_{$this->id}";
		$field_content   = ! $is_admin ? '{FIELD}' : $field_content = sprintf( "%s<label class='gfield_label gform-field-label' for='%s'>%s</label> NO THE FIELD <br/>{FIELD} <br/> the field endede", $admin_buttons, $field_id, esc_html( $field_label ) );

		return 'TODELTETE FIELD CONTENTTT' . $field_content;
	}
}

// Registrar el campo.
GF_Fields::register( new GF_Field_Asim_Map() );

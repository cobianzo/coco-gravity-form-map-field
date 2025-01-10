<?php

if ( ! class_exists( 'GF_Field' ) ) {
	return;
}



class GF_Field_AsimMap extends GF_Field {

	public $type = 'asim-map';

	public $google_maps_api_key = '';

	public function get_form_editor_field_title() {
		return esc_attr__( 'Asim Map', 'gravityforms' );
	}

	/**
	 * Returns the field's form editor description.
	 *
	 * @since 2.5
	 *
	 * @return string
	 */
	public function get_form_editor_field_description() {
		return esc_attr__( 'Stores information that should not be visible to the user but can be processed and saved with the user submission.', 'gravityforms' );
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
		return 'gform-icon--hidden2';
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
		);
	}

	public function get_field_input( $form, $value = '', $entry = null ) {

		$this->google_maps_api_key = 'AIzaSyBCxO-yk6_PshNJCZ-D8OIuZElDWto_jyY';

		if ( ! wp_script_is( 'asim-map-js', 'enqueued' ) ) {
			wp_enqueue_script( 'asim-map-js', dirname( plugin_dir_url( __FILE__ ) ) . '/build/asim-gravity-form-map-field.js', [], '1.0.0', false );
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
}

GF_Fields::register( new GF_Field_AsimMap() );

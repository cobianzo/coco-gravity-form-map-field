<?php

// we use $form and $field_id, $value, $entry

$is_entry_detail = $this->is_entry_detail();
$is_form_editor  = $this->is_form_editor();

$is_admin = $is_entry_detail || $is_form_editor;

$lat = GFFormsModel::get_input( $this, $this->id . '_lat' );
$lon = GFFormsModel::get_input( $this, $this->id . '_lon' );

?>
<div id="map_<?php echo esc_attr( $form['id'] ); ?>_<?php echo esc_attr( $field_id ); ?>"
	style="width: 100%; height: 300px;"></div>

<fieldset>

	<legend class="gfield_label gform-field-label gfield_label_before_complex">Name<span class="gfield_required"><span class="gfield_required gfield_required_text">(Required)</span></span></legend>

	<legend><?php _e( 'Geolocation', 'gf-google-maps' ); ?></legend>

	<label for="lat_<?php echo esc_attr( $form['id'] ); ?>_<?php echo esc_attr( $field_id ); ?>">
		<?php _e( 'Latitud', 'gf-google-maps' ); ?>
	</label>

	<input type="text"
		id="lat_<?php echo esc_attr( $form['id'] ); ?>_<?php echo esc_attr( $field_id ); ?>"
		name="lat_<?php echo esc_attr( $field_id ); ?>"
		value="<?php echo esc_attr( $lat ); ?>"
		readonly />

	<label for="lon_<?php echo esc_attr( $form['id'] ); ?>_<?php echo esc_attr( $field_id ); ?>">
		<?php _e( 'Longitud', 'gf-google-maps' ); ?>
	</label>

	<input type="text"
		id="lon_<?php echo esc_attr( $form['id'] ); ?>_<?php echo esc_attr( $field_id ); ?>"
		name="lon_<?php echo esc_attr( $field_id ); ?>"
		value="<?php echo esc_attr( $lon ); ?>"
		readonly />

</fieldset>

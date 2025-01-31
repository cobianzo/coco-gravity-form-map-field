<?php

namespace Asim_Gravity_Form_Map_Field;

/**
 * Hooks class.
 *
 * @package Asim_Gravity_Form_Map_Field
 */

class Hooks {


	/**
	 * Initialize hooks.
	 */
	public static function init() {
		add_action( 'gform_field_standard_settings', array( __CLASS__, 'field_sidebar_options' ), 10 );
		add_filter( 'gform_tooltips', array( __CLASS__, 'tooltips' ), 10, 1 );
	}

	/**
	 * New options for the map field in the sidebar.
	 */
	public static function field_sidebar_options( $position ) {
		// Add to the General panel in the sidebar
		if ( 50 === $position ) :

			// Option map type (satellite/terrain)
			?>
			<li class="map_type_setting field_setting">
				<label for="field_map_type" class="section_label">
					<?php esc_html_e( 'Map Type', 'asim-gravity-forms-map-addon' ); ?>
					<?php
					gform_tooltip( 'form_field_map_type' ); // we can add a tooltip if we want to
					?>
				</label>
				<select id="field_map_type" onchange="SetFieldProperty('mapType', this.value);">
					<option value=""><?php esc_html_e( 'Default', 'asim-gravity-forms-map-addon' ); ?></option>
					<option value="satellite"><?php esc_html_e( 'Satellite', 'asim-gravity-forms-map-addon' ); ?></option>
					<option value="terrain"><?php esc_html_e( 'Terrain', 'asim-gravity-forms-map-addon' ); ?></option>
				</select>
			</li>
			<!-- Option input lookup autocomplete type: cities/address etc -->
			<li class="autocomplete_types_setting field_setting">
				<label for="field_autocomplete_types" class="section_label">
					<?php esc_html_e( 'Autocomplete Type', 'asim-gravity-forms-map-addon' ); ?>
					<?php gform_tooltip( 'form_field_autocomplete_types' ); ?>
				</label>
				<select id="field_autocomplete_types" onchange="SetFieldProperty('autocompleteTypes', this.value);">
					<option value=""><?php esc_html_e( 'None', 'asim-gravity-forms-map-addon' ); ?></option>
					<option value="(cities)"><?php esc_html_e( 'Cities', 'asim-gravity-forms-map-addon' ); ?></option>
					<option value="geocode"><?php esc_html_e( 'Geocode', 'asim-gravity-forms-map-addon' ); ?></option>
					<option value="address"><?php esc_html_e( 'Address', 'asim-gravity-forms-map-addon' ); ?></option>
					<option value="(regions)"><?php esc_html_e( 'Regions', 'asim-gravity-forms-map-addon' ); ?></option>
				</select>
			</li>
			<!-- Option Map Interaction: Marker/Polygon -->
			<li class="interaction_type_setting">
				<label for="field_interaction_type" class="section_label">
					<?php esc_html_e( 'Interaction Type', 'asim-gravity-forms-map-addon' ); ?>
					<?php gform_tooltip( 'form_field_interaction_type' ); ?>
				</label>
				<select id="field_interaction_type" onchange="SetFieldProperty('interactionType', this.value);">
					<option value="marker"><?php esc_html_e( 'Marker', 'asim-gravity-forms-map-addon' ); ?></option>
					<option value="polygon"><?php esc_html_e( 'Polygon', 'asim-gravity-forms-map-addon' ); ?></option>
				</select>
			</li>

			<script>
				// Set the default values to the inputs when page loads (page of edit form /admin.php?page=gf_edit_forms&id=x)
				jQuery(document).on('gform_load_field_settings', function(event, field, form) {

					const defaultValueMapType = field['mapType'] || '';
					jQuery('#field_map_type').val(defaultValueMapType);

					const defaultValueAutocompleteTypes = field['autocompleteTypes'] || '';
					jQuery('#field_autocomplete_types').val(defaultValueAutocompleteTypes);

					const defaultValueIntereactionType = field['interactionType'] || '';
					jQuery('#field_interaction_type').val(defaultValueIntereactionType);
				});
			</script>
			<?php
		endif;
	}

	public static function tooltips( $tooltips ) {
		$tooltips['form_field_map_type']           = esc_html__( 'More info at https://developers.google.com/maps/documentation/javascript/maptypes', 'asim-gravity-forms-map-addon' );
		$tooltips['form_field_autocomplete_types'] = esc_html__( 'More info at https://developers.google.com/maps/documentation/javascript/supported_types', 'asim-gravity-forms-map-addon' );
		return $tooltips;
	}
}

Hooks::init();

<?php // phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase, WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase, WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase, WordPress.NamingConventions.ValidVariableName.InterpolatedVariableNotSnakeCase
namespace Coco_Gravity_Form_Map_Field;

/**
 * Hooks class. Notmally Gravity Form hooks that tweak the behaviour of
 * our field
 *
 * @package Coco_Gravity_Form_Map_Field
 */

class Hooks {


	/**
	 * Initialize hooks.
	 */
	public static function init() {
		add_action( 'gform_field_standard_settings', array( __CLASS__, 'field_sidebar_options' ), 10, 2 );
		add_filter( 'gform_tooltips', array( __CLASS__, 'tooltips' ), 10, 1 );
		add_filter( 'gform_entry_field_value', array( __CLASS__, 'show_link_map_field_admin' ), 10, 2 );
	}

	/**
	 * New options for the map field in the sidebar.
	 * TODO: Apparentyl the Interaction type field appears also in the other fields. FIX it
	 */
	public static function field_sidebar_options( $position, $form ) {
		if ( 50 === $position ) :
			// Option map type (satellite/terrain)
			// Note that we use display:none, so it's not shown initially. And we only show it
			// for our coco-map field, not any other gform field.
			?>
			<li class="map_type_setting field_setting coco-field-option" style="display:none">
				<label for="field_map_type" class="section_label">
					<?php esc_html_e( 'Map Type', 'coco-gravity-forms-map-addon' ); ?>
					<?php
					gform_tooltip( 'form_field_map_type' ); // we can add a tooltip if we want to
					?>
				</label>
				<select id="field_map_type" onchange="SetFieldProperty('mapType', this.value);">
					<option value=""><?php esc_html_e( 'Default', 'coco-gravity-forms-map-addon' ); ?></option>
					<option value="satellite"><?php esc_html_e( 'Satellite', 'coco-gravity-forms-map-addon' ); ?></option>
					<option value="terrain"><?php esc_html_e( 'Terrain', 'coco-gravity-forms-map-addon' ); ?></option>
				</select>
			</li>
			<!-- Option input lookup autocomplete type: cities/address etc -->
			<li class="autocomplete_types_setting field_setting coco-field-option" style="display:none">
				<label for="field_autocomplete_types" class="section_label">
					<?php esc_html_e( 'Autocomplete Type', 'coco-gravity-forms-map-addon' ); ?>
					<?php gform_tooltip( 'form_field_autocomplete_types' ); ?>
				</label>
				<select id="field_autocomplete_types" onchange="SetFieldProperty('autocompleteTypes', this.value);">
					<option value=""><?php esc_html_e( 'None', 'coco-gravity-forms-map-addon' ); ?></option>
					<option value="(cities)"><?php esc_html_e( 'Cities', 'coco-gravity-forms-map-addon' ); ?></option>
					<option value="geocode"><?php esc_html_e( 'Geocode', 'coco-gravity-forms-map-addon' ); ?></option>
					<option value="address"><?php esc_html_e( 'Address', 'coco-gravity-forms-map-addon' ); ?></option>
					<option value="(regions)"><?php esc_html_e( 'Regions', 'coco-gravity-forms-map-addon' ); ?></option>
				</select>
			</li>
			<!-- Option Map Interaction: Marker/Polygon -->
			<li class="interaction_type_setting coco-field-option" style="display:none">
				<label for="field_interaction_type" class="section_label">
					<?php esc_html_e( 'Interaction Type', 'coco-gravity-forms-map-addon' ); ?>
					<?php gform_tooltip( 'form_field_interaction_type' ); ?>
				</label>
				<select id="field_interaction_type" onchange="SetFieldProperty('interactionType', this.value);">
					<option value="marker"><?php esc_html_e( 'Marker', 'coco-gravity-forms-map-addon' ); ?></option>
					<option value="polygon"><?php esc_html_e( 'Polygon', 'coco-gravity-forms-map-addon' ); ?></option>
					<?php do_action( 'coco_gf_map_field_interaction_type_extra_options', $form ); ?>
				</select>
			</li>

			<script>
				// Set the default values to the inputs when page loads (page of edit form /admin.php?page=gf_edit_forms&id=x)
				jQuery(document).on('gform_load_field_settings', function(event, field, form) {

					if (field.type === 'coco-map') {

						jQuery('.coco-field-option').show();

						// loads the saved value
						const defaultValueMapType = field['mapType'] || '';
						jQuery('#field_map_type').val(defaultValueMapType);

						const defaultValueAutocompleteTypes = field['autocompleteTypes'] || '';
						jQuery('#field_autocomplete_types').val(defaultValueAutocompleteTypes);

						const defaultValueIntereactionType = field['interactionType'] || '';
						jQuery('#field_interaction_type').val(defaultValueIntereactionType);

					} else {
						jQuery('.coco-field-option').hide();
					}

				});
			</script>
			<?php
		endif;
	}

	public static function tooltips( $tooltips ) {
		$tooltips['form_field_map_type']           = esc_html__( 'More info at https://developers.google.com/maps/documentation/javascript/maptypes', 'coco-gravity-forms-map-addon' );
		$tooltips['form_field_autocomplete_types'] = esc_html__( 'More info at https://developers.google.com/maps/documentation/javascript/supported_types', 'coco-gravity-forms-map-addon' );
		return $tooltips;
	}

	/**
	 * In the admin, show a link to the map image.
	 * This function doesnt affect the list of values in the table of entries, but it modifies the
	 * output when clicking on a single entry (wp-admin/admin.php?page=gf_entries&view=entry&id=3&lid=8)
	 *
	 * @since 3.1.2
	 *
	 * @param string $value The value of the field.
	 * @param object $field The field.
	 *
	 * @return string The HTML to show the link to the map image.
	 */
	public static function show_link_map_field_admin( $value, $field ): string {
		if ( 'coco-map' === $field->inputType ) {

			$addon  = Addon_Coco::get_instance();
			$apiKey = $addon->get_plugin_setting( Addon_Coco::SETTING_GOOGLE_MAPS_API_KEY );
			$value  = trim( $value );
			if ( 'polygon' === GF_Field_CocoMap::entry_is_marker_or_polygon( $value ) ) {
				// Coordenadas del polígono (debe ser una serie de puntos)

				$polygonCoords   = explode( ' ', $value );
				$polygonCoords[] = $polygonCoords[0]; // close the polygon

				// Convertir las coordenadas en una cadena para la URL
				$polygonPath = implode( '|', $polygonCoords );

				// Definir el color del polígono (en formato hex sin # y opacidad)
				$polygonColor = 'ff0000ff'; // Rojo con opacidad FF

				// Construir la URL de la imagen
				$map_img_url = 'https://maps.googleapis.com/maps/api/staticmap?' .
									'size=600x400&' . // Tamaño de la imagen
									'maptype=roadmap&' . // Tipo de mapa (roadmap, satellite, hybrid, terrain)
									"path=color:0x$polygonColor|weight:3|$polygonPath&" . // Polígono con color y grosor
									"key=$apiKey";

				// Mostrar la imagen en HTML
				return "<img src='$map_img_url' />";
			} elseif ( 'marker' === GF_Field_CocoMap::entry_is_marker_or_polygon( $value ) ) {

				$map_img_url = 'https://maps.googleapis.com/maps/api/staticmap?' .
				"center={$value}&" . // Centrar el mapa en el marcador
				'zoom=14&' . // Nivel de zoom (ajústalo según necesites)
				'size=600x400&' . // Tamaño del mapa en píxeles
				'maptype=roadmap&' . // Tipo de mapa (roadmap, satellite, hybrid, terrain)
				"markers=color:red|label:A|{$value}&" . // Marcador rojo con la etiqueta 'A'
				"key={$apiKey}";

				$gmaps_link = "https://www.google.com/maps?q={$value}";

				return sprintf(
					'<img src="%s" /><br><a href="%s" target="_blank">%s</a>',
					$map_img_url,
					$gmaps_link,
					esc_html__( 'View on Google Maps', 'coco-gravity-forms-map-addon' )
				);
			}
		}
		return $value;
	}
}

Hooks::init();

// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase, WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase, WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase, WordPress.NamingConventions.ValidVariableName.InterpolatedVariableNotSnakeCase

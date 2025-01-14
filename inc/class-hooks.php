<?php
/**
 * Hooks class.
 *
 * @package Asim_Gravity_Form_Map_Field
 */

namespace Asim_Gravity_Form_Map_Field;

/**
 * Class Hooks
 */
class Hooks {

	/**
	 * Initialize hooks.
	 */
	public static function init() {
		add_action( 'gform_field_standard_settings', array( __CLASS__, 'field_sidebar_options' ), 10 );
	}

	/**
	 * Plugins loaded hook.
	 */
	public static function field_sidebar_options( $position ) {
		// Add to the General panel in the sidebar
		if ( 50 === $position ) :
			?>
			<li class="map_type_setting field_setting">
					<label for="field_map_type" class="section_label">
							<?php esc_html_e( 'Map Type', 'asim-gravity-forms-map-addon' ); ?>
							<?php gform_tooltip( 'form_field_map_type' ); // we can add a tooltip if we want to ?>
					</label>
					<select id="field_map_type" onchange="SetFieldProperty('mapType', this.value);">
							<option value=""><?php esc_html_e( 'Default', 'asim-gravity-forms-map-addon' ); ?></option>
							<option value="satellite"><?php esc_html_e( 'Satellite', 'asim-gravity-forms-map-addon' ); ?></option>
							<option value="terrain"><?php esc_html_e( 'Terrain', 'asim-gravity-forms-map-addon' ); ?></option>
					</select>
			</li>
			<?php
		endif;
	}
}


Hooks::init();

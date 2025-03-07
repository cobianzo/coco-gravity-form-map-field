<?php
namespace Coco_Gravity_Form_Map_Field;

// Verifying that Gravity Forms is loaded
add_action( 'init', function () {

	if ( ! class_exists( 'GFAddOn' ) ) {
		if ( is_admin() ) {
			add_action('admin_notices', function () {
				?>
				<div class="notice notice-warning">
					<p>
						<strong>Coco Gravity Forms Map Field:</strong>
						<?php
						esc_html_e(
							'This plugin requires Gravity Forms to be active. Please activate it to see the plugin in action.',
							'coco-gravity-form-map-field'
						);
						?>
					</p>
				</div>
				<?php
			});
		}
		return;
	}
} );

class Validations {


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
	public static function validate_coordinates( string $value ): bool {
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

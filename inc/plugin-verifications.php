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

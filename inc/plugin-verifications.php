<?php

add_action('init', function () {
	if ( ! class_exists( 'GFAddOn' ) ) {
		if ( is_admin() ) {
			add_action('admin_notices', function () {
				?>
				<div class="notice notice-warning">
					<p>
						<strong>Asim Gravity Forms Map Field:</strong>
						<?php
						esc_html_e(
							'This plugin requires Gravity Forms to be active. Please activate it to see the plugin in action.',
							'asim-gravity-form-map-field'
						);
						?>
					</p>
				</div>
				<?php
			});
		}
		return;
	}
});

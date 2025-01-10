<?php

add_action('init', function () {
	if ( ! class_exists( 'GFAddOn' ) ) {
		if ( is_admin() ) {
			add_action('admin_notices', function () {
				?>
				<div class="notice notice-warning">
					<p>
						<?php
						echo wp_kses_post( sprintf(
							__( 'The %s plugin requires Gravity Forms to be active. Please activate it to see the plugin in action.', 'asim-gravity-form-map-field' ),
							'<strong>Gravity Forms</strong>'
						));
						?>
					</p>
				</div>
				<?php
			});
		}
		return;
	}
});

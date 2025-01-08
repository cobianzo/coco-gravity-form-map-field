<?php

namespace Coco;

/**
 * Class Plugin_Setup. The plugin setup.
 *
 * Setup of the plugin, enqueuing, reference to other files
 *
 * @package asim-gravity-form-map-field
 */
class Plugin_Setup {

	/**
	 * Initialize the plugin by setting up hooks and including necessary files.
	 *
	 * @return void
	 */
	public static function init(): void {

		// When running Playwright tests, we want to create dummy data quickly with this new page
		if ( 'production' !== wp_get_environment_type() && defined( 'DUMMY_DATA_GENERATOR' ) && DUMMY_DATA_GENERATOR ) {
			require_once dirname( __DIR__ ) . '/tests/class-create-dummy-data.php'; // Create Dummy Data Page
		}
	}
}

Plugin_Setup::init();

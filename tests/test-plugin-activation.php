<?php
/**
 * Class PluginActivation
 *
 * @package Wp_Env_Portfolio_Backtrack_Theme
 */

/**
 * Sample test case.
 */
class Test_Plugin_Activation extends WP_UnitTestCase {

	protected function setUp(): void {
		parent::setUp();

		echo PHP_EOL . PHP_EOL . 'TEST 1' . PHP_EOL . '=========' . PHP_EOL ;

		// Ensure all initial actions have been triggered in WordPress.

		// if not running phpUnit in wp env, the plugin might not be activated by default
		$plugin_file = 'asim-gravity-form-map-field/asim-gravity-form-map-field.php';
		if ( ! is_plugin_active( $plugin_file ) ) {
			echo PHP_EOL . '>>> ⚠️ 2) Needed activation of plugin' . PHP_EOL . '=========' . PHP_EOL ;
			activate_plugin( $plugin_file );
		}
	}

	/**
	 * Test to verify that the plugin activates correctly.
	 */
	public function test_plugin_activation() {
		echo PHP_EOL . PHP_EOL . '1.1) ---- Test for plugin activation' . PHP_EOL;

		// Path to the main plugin file.
		$plugin_name = 'asim-gravity-form-map-field/asim-gravity-form-map-field.php';
		$plugin_file = WP_PLUGIN_DIR . '/' . $plugin_name;

		// Ensure the plugin file exists.
		$this->assertFileExists( $plugin_file, '❌ FAIL 1.1. The main plugin file does not exist.' );

		// Verify the plugin is active.
		$this->assertTrue(
				is_plugin_active( $plugin_name ),
				'❌ FAIL 1.1: The plugin did not activate correctly.' . PHP_EOL . '---------' . PHP_EOL
			);

		echo PHP_EOL . '✅ OK 1.1: Plugin activated correctly' . PHP_EOL . '---------' . PHP_EOL;

	}

}

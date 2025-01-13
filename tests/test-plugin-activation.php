<?php
require_once 'helpers.php';

/**
 * Simple initial test cases.
 */
class Test_Plugin_Activation extends WP_UnitTestCase {

	public $plugin_file;

	public function setUp(): void {
		parent::setUp();

		echo PHP_EOL . PHP_EOL . '🎬🏁 SETUP BEFORE TEST CASE' . PHP_EOL . '=================' . PHP_EOL ;
		echo PHP_EOL . ' setUp ' . PHP_EOL . '____________' . PHP_EOL ;

		$this->plugin_file = 'asim-gravity-form-map-field/asim-gravity-form-map-field.php';

				// Configurar una API key de prueba @TODO:
				// update_option('gravityformsaddon_asim-gravity-forms-map-addon_settings',
				//     ['google_maps_api_key' => $this->api_key]
				// );

		// if not running phpUnit in wp env, the plugin might not be activated by default
			// if ( ! is_plugin_active( $this->plugin_file ) ) {
			// 	echo PHP_EOL . '>>> ⚠️ 2) Needed activation of plugin' . PHP_EOL . '=========' . PHP_EOL ;
			// 	activate_plugin( $this->plugin_file );
			// }
		}

		public function tearDown(): void {
				// Limpiar después de cada test
				parent::tearDown();
				delete_option('gravityformsaddon_asim-gravity-forms-map-addon_settings');
		}

	public function test_gravity_forms_dependency() {

		echo PHP_EOL . PHP_EOL . '1.1) ---- Test for test_gravity_forms_dependency' . PHP_EOL;

		// Test when Gravity Forms is not active
		deactivate_plugins( 'gravityforms/gravityforms.php' );
		activate_plugin( $this->plugin_file );
		$this->assertFalse( is_plugin_active( 'gravityforms/gravityforms.php' ), '❌ 1.1.1 We need to deactivate gravity forms, but we could not' );

		$this->assertTrue( is_plugin_active( $this->plugin_file ), '❌ 1.1.2 We could not activate ' . $this->plugin_file );

    $this->assertTrue( has_action( 'admin_notices' ) !== false, '❌ 1.1.3 filter admin_notices not found' );

		global $current_screen;
		$current_screen = convert_to_screen('dashboard'); // Simular la pantalla del admin

		// Test error notice is shown
		ob_start(); // Captura la salida del buffer
		do_action( 'init' );
		do_action( 'admin_notices' );
		$output = ob_get_clean(); // Obtiene la salida y limpia el buffer

		// Verifica que la salida contiene el HTML esperado
		$this->assertStringContainsString('<div class="notice notice-warning">', $output, '❌ 1.1.4 not notice container ');
		$this->assertStringContainsString('<strong>Asim Gravity Forms Map Field:</strong>', $output, '❌ 1.1.4 ');

		echo PHP_EOL . PHP_EOL . '✅ OK 1.1.4 Good; the notice saying "Asim Gravity Forms Map Field: This plugin requires Gravity Forms to ... " is happening.' . PHP_EOL;
		echo PHP_EOL . $output;
	}

	/**
	 * Test to verify that the plugin does not show a notice when Gravity Forms is active.
	 */
	public function test_gravity_forms_dependency_when_active() {
		echo PHP_EOL . PHP_EOL . '1.2) ---- Test for test_gravity_forms_dependency when GF is active' . PHP_EOL;

		// Test the scenario with GF active:
		activate_plugin( 'gravityforms/gravityforms.php' );
		activate_plugin( $this->plugin_file );
		$this->assertTrue( is_plugin_active( 'gravityforms/gravityforms.php' ), '❌ 1.2.1 We need to activate gravity forms, but we could not' );

		$this->assertTrue( class_exists( 'GFAddOn' ), '❌ 1.2.2 CLASS GFAddOn not exists after activation plugin' );

		global $current_screen;
		$current_screen = convert_to_screen('dashboard'); // Simular la pantalla del admin

		ob_start(); // Captura la salida del buffer
		do_action( 'init' );
		do_action( 'admin_notices' );
		$output2 = ob_get_clean(); // Obtiene la salida y limpia el buffer


		$this->assertStringNotContainsString('<div class="notice notice-warning">', $output2, '❌ 1.2.3 The notice warning is still happening.');

		echo PHP_EOL . PHP_EOL . '✅ OK 1.2.3 Good; Once we active the GF plugin, the notice warning dissappears.' . PHP_EOL;
		echo PHP_EOL . $output2;
	}

	/**
	 * Test to verify that the plugin activates correctly.
	 */
	public function test_plugin_activation() {

		activate_plugin( $this->plugin_file );
		echo PHP_EOL . PHP_EOL . '1.3) ---- Test for the simple addon plugin activation' . PHP_EOL;

		// Path to the main plugin file.
		$plugin_pathfile = WP_PLUGIN_DIR . '/' . $this->plugin_file;

		// Ensure the plugin file exists.
		$this->assertFileExists( $plugin_pathfile, '❌ FAIL 1.3. The main plugin file does not exist.' );

		// Verify the plugin is active.
		$this->assertTrue(
				is_plugin_active( $this->plugin_file ),
				"❌ FAIL 1.3: The plugin {$this->plugin_file} did not activate correctly." . PHP_EOL . '---------' . PHP_EOL
			);

		echo PHP_EOL . '✅ OK 1.3: Plugin activated correctly' . PHP_EOL . '---------' . PHP_EOL;

	}

}

<?php
require_once 'helpers.php';

class Test_Map_Field extends WP_UnitTestCase {

	public $plugin_file;

	public function setUp(): void {
		parent::setUp();

		Coco_TestCase_Helpers::setup( $this );

		echo '2) ---- Activating plugins' . PHP_EOL;
		activate_plugin( 'gravityforms/gravityforms.php' );
		activate_plugin( $this->plugin_file );
	}

	/**
	 * Test que verifica que el campo está registrado correctamente en GF
	 */
	public function test_field_is_registered() {
		// Verificar que el tipo de campo está registrado

		echo PHP_EOL . PHP_EOL . '2.1) ---- Test for test_field_is_registered' . PHP_EOL;

		$this->assertTrue(
			is_plugin_active( $this->plugin_file ),
			"❌ FAIL 2.1.1: The plugin {$this->plugin_file} did not activate correctly." . PHP_EOL . '---------' . PHP_EOL
		);

		$this->assertTrue(
			class_exists('GF_Fields'),
			'❌ 2.1.2 CLASS GF_Fields not exists after activation plugin'
		);


		// Activar el addon
		do_action( 'gform_loaded', array( 'path' => $this->plugin_file ) );
		$field_types = GF_Fields::get_all();
		$this->assertArrayHasKey( 'coco-map', $field_types, '❌ 2.1.3 GF_Fields. MAL!' );

		// Verificar que la clase registrada es la correcta
		$registered_field = GF_Fields::get( 'coco-map' );
		$this->assertInstanceOf( '\Coco_Gravity_Form_Map_Field\GF_Field_CocoMap', $registered_field, '❌ 2.1.4 GF_Fields does not contain our new registered field coco-map. MAL!' );

		echo PHP_EOL . '✅ OK 2.1: New Addon field "coco-map" is registered ok' . PHP_EOL . '---------' . PHP_EOL;
	}

	/**
	 * Test que verifica las propiedades básicas del campo
	 */
	public function test_field_properties()
	{
		echo PHP_EOL . PHP_EOL . '2.2) ---- test_field_properties' . PHP_EOL;
		do_action( 'gform_loaded', array( 'path' => $this->plugin_file ) );

		$field = new \Coco_Gravity_Form_Map_Field\GF_Field_CocoMap();

		// Verificar tipo de campo
		$this->assertEquals( 'coco-map', $field->type, '❌ The type of the field is not "coco-map"' );

		// Verificar el título del campo en el editor
		$this->assertEquals( esc_attr__( 'Coco Map', 'coco-gravity-forms-map-addon' ), $field->get_form_editor_field_title(), '❌ The title of the field in the form editor is not "Map"' );

		echo PHP_EOL . '✅ OK 2.2: Field properties are correct' . PHP_EOL . '---------' . PHP_EOL;
	}

	/**
	 * Test que verifica la generación correcta del HTML del input
	 */
	public function test_field_input_generation()
	{
		echo PHP_EOL . PHP_EOL . '2.3) ---- Test for test_field_input_generation' . PHP_EOL;
		do_action( 'gform_loaded', array( 'path' => $this->plugin_file ) );

		$field = new \Coco_Gravity_Form_Map_Field\GF_Field_CocoMap();
		$field->google_maps_api_key = 'test-just-so-it-s-not-empty';

		$form = Coco_TestCase_Helpers::create_test_form();
		$value = Coco_TestCase_Helpers::get_valid_coordinates();

		// Generate the HTML
		$html = $field->get_field_input($form, $value);

		// Verificar que contiene los elementos necesarios
		$this->assertStringContainsString( 'type="text"', $html, '❌  2.3.1: The HTML does not contain the correct type attribute: ' );
		$this->assertStringContainsString( 'readonly', $html, '❌  2.3.2: The HTML does not contain the readonly attribute: ' );
		$this->assertStringContainsString( 'class="gform-field-coco-map"', $html, '❌  2.3.3: The HTML does not contain the correct class: ' );
		$this->assertStringContainsString( $value, $html, '❌  2.3.4: The HTML does not contain the value: ' );
		$this->assertStringContainsString( 'map-container', $html, '❌  2.3.5: The HTML does not contain the map container element: ' );

		echo PHP_EOL . '✅ OK 2.3:' . PHP_EOL . substr($html, 0, 25) . '...' .  PHP_EOL . '---------' . PHP_EOL;
	}

	/**
	 * Test que verifica el formato de las coordenadas
	 */
	public function test_coordinates_validation()
	{
		echo PHP_EOL . PHP_EOL . '2.4) ---- Test for test_coordinates_validation' . PHP_EOL;
		do_action( 'gform_loaded', array( 'path' => $this->plugin_file ) );

		$field = new \Coco_Gravity_Form_Map_Field\GF_Field_CocoMap();

		// Coordenadas válidas
		$valid_coordinates = array(
			'40.416775,-3.703790',
			'-90.000000,180.000000',
			'90.000000,-180.000000',
			'0.000000,0.000000'
		);

		foreach ($valid_coordinates as $coordinates) {
			$this->assertTrue(
				\Coco_Gravity_Form_Map_Field\Validations::validate_coordinates($coordinates),
				"Las coordenadas $coordinates deberían ser válidas"
			);
		}

		// Coordenadas inválidas
		$invalid_coordinates = array(
			'invalid',                    // No son coordenadas
			'90.1,180',                  // Latitud fuera de rango
			'90,-180.1',                 // Longitud fuera de rango
			'40.416775',                 // Falta la longitud
			',40.416775',                // Falta la latitud
			'40.416775,-3.703790,0',     // Demasiados valores
			'abc,def'                    // No son números
		);

		foreach ($invalid_coordinates as $coordinates) {
			$this->assertFalse(
				\Coco_Gravity_Form_Map_Field\Validations::validate_coordinates($coordinates),
				"Las coordenadas $coordinates deberían ser inválidas"
			);
		}

		echo PHP_EOL . '✅ OK 2.4.' . PHP_EOL . '---------' . PHP_EOL;
	}

	/**
	 * Test que verifica que el campo maneja correctamente los valores vacíos
	 */
	public function test_empty_value_handling()
	{
		echo PHP_EOL . PHP_EOL . '2.5) ---- Test for test_empty_value_handling' . PHP_EOL;
		do_action( 'gform_loaded', array( 'path' => $this->plugin_file ) );

		$field = new \Coco_Gravity_Form_Map_Field\GF_Field_CocoMap();
		$field->google_maps_api_key = 'test-just-so-it-s-not-empty';
		$form = Coco_TestCase_Helpers::create_test_form();

		// Try empty value
		$html_empty = $field->get_field_input( $form, '' );
		$this->assertStringContainsString('value=""', $html_empty, '❌ 2.5.1:. The HTML does not contain empty value attribute: ' );

		echo PHP_EOL . '✅ OK 2.5:' . PHP_EOL . '---------' . PHP_EOL;
	}

	/**
	 * Test que verifica la sanitización de las coordenadas
	 */
	public function test_coordinates_sanitization()
	{
		echo PHP_EOL . PHP_EOL . '2.6) --- Test for test_coordinates_sanitization' . PHP_EOL;
		do_action( 'gform_loaded', array( 'path' => $this->plugin_file ) );

		$field = new \Coco_Gravity_Form_Map_Field\GF_Field_CocoMap();

		// Coordenadas con espacios extra
		$this->assertEquals(
			'40.416775,-3.703790',
			$field->sanitize_coordinates(' 40.416775 , -3.703790 ')
		);

		// Coordenadas con precisión extra
		$this->assertEquals(
			'40.416775,-3.703790',
			$field->sanitize_coordinates('40.41677532140,-3.7037904532')
		);

		echo PHP_EOL . '✅ OK 2.6:' . PHP_EOL . '---------' . PHP_EOL;
	}
}

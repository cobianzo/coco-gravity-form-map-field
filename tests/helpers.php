<?php

// not in use yet. As we will expand the unit testing, we might need this.
class Coco_TestCase_Helpers {
    protected $api_key;


	public static function setup( $WP_UnitTestCase_instance ) {

		echo PHP_EOL . PHP_EOL . '🎬🏁 SETUP BEFORE TEST CASE' . PHP_EOL . '=================' . PHP_EOL ;
		echo PHP_EOL . ' setUp ' . PHP_EOL . '____________' . PHP_EOL ;

		$WP_UnitTestCase_instance->plugin_file = 'coco-gravity-form-map-field/coco-gravity-form-map-field.php';
	}

    // Create a fake form
    public static function create_test_form() {
		$form = array(
			'id' => 1,
			'fields' => array(
				array(
					'type' => 'text',
					'id' => 6,
					'formId' => 1,
					'label' => 'PARRAFO',
					'isRequired' => 0,
				),
				array(
					'type' => 'coco-map',
					'google_maps_api_key' => '__',
					'id' => 7,
					'formId' => 1,
					'label' => 'This is a map',
					'isRequired' => 1,
					'inputType' => 'coco-map',
					'defaultValue' => '31.846360552448978,21.461425781250004',
				),
			),
		);

        return $form;
    }

    public static function get_valid_coordinates() {
        return '40.416775,-3.703790';
    }
}

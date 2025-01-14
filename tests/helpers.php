<?php

// not in use yet. As we will expand the unit testing, we might need this.
class Asim_TestCase_Helpers {
    protected $api_key;

    // Helpers comunes que pueden usar todos los tests
    protected function create_test_form() {
        return array(
            'id' => 1,
            'fields' => array(
                array(
                    'id' => 1,
                    'type' => 'asim-map'
                )
            )
        );
    }

    protected function get_valid_coordinates() {
        return '40.416775,-3.703790';
    }
}

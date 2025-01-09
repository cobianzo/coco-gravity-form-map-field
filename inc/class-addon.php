<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Asim_Gravity_Forms_Map_Addon extends GFAddOn {
	protected $_version = '1.0';
	protected $_min_gravityforms_version = '2.5';
	protected $_slug = 'asim-gravity-forms-map-addon';
	protected $_path = 'asim-gravity-form-map-field/asim-gravity-form-map-field.php';
	protected $_full_path = __FILE__;
	protected $_title = 'Asim Gravity Forms Map Addon';
	protected $_short_title = 'Map Addon';

	private static $_instance = null;

	public static function get_instance() {
			if ( self::$_instance === null ) {
					self::$_instance = new self();
			}
			return self::$_instance;
	}

	public function init() {
			parent::init();

			// Registrar el nuevo tipo de campo.
			add_filter( 'gform_field_types', [ $this, 'register_custom_field' ] );
	}

	public function register_custom_field( $field_types ) {
			$field_types['asim-map'] = __( 'Asim Google Maps', 'asim-gravity-forms-map-addon' );
			return $field_types;
	}
}

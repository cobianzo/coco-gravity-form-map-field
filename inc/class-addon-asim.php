<?php

// phpcs:disable PSR2.Classes.PropertyDeclaration.Underscore

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Addon_Asim extends GFAddOn {
	protected $_version                  = '1.0';
	protected $_min_gravityforms_version = '2.5';
	protected $_slug                     = 'asim-gravity-forms-map-addon';
	protected $_path                     = 'asim-gravity-form-map-field/asim-gravity-form-map-field.php';
	protected $_full_path                = __FILE__;
	protected $_title                    = 'Asim Gravity Forms Map Addon';
	protected $_short_title              = 'Asim Map';

	private static $_instance = null;

	public static function get_instance() {
		if ( null === self::$_instance ) {
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

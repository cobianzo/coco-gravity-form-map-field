# Use this description to put an AI AGENT into context

I have developed an addon in WordPress for the plugin Gravity Forms. It adds a new field for Google Maps
This plugin checks that Gravity Forms is installed, first of all, and this is the structure.
The addon creates a 
- new Setting tab in the GF settings page /wp-admin/admin.php?page=gf_settings&subview=coco-gravity-forms-map-addon
	- that settings tab add an input field for the Google Maps API: 'google_maps_api_key'
- The creation of a form adds now a new field: public $type = 'coco-map';
	- that field in an input readonly field and will introduce an interactive map with the Google Maps API
	- Depending on the settings of the field, the user can
		- select a point in the map, marking it up with a marker. When selecting the point,
	the input will be filled up with latitude,logitude (comma separated).
		- create a polygon in the map. As the polygon is being drawn, the corrdinates of the vertex are saved in the readonly input.

Overview of folders:
1. Root project files:
	 Main plugin file: coco-gravity-form-map-field.php
	 Configuration files: composer.json, package.json, webpack.config.js, etc.
	 Documentation: README.md, TODO.md, AI-AGENT.md
2. Key directories:
	 /inc/: Contains PHP classes and core functionality
	 /src/: JavaScript source files
	 /dist/: zip file for the exported plugin
	 /assets/: Media files
	 /tests/: Testing suite including PHPUnit and Playwright tests
	 /bin/: Build and utility scripts
	 /.github/workflows/: CI/CD configuration

This is the structure of the plugin.

├── coco-gravity-form-map-field.php
├── assets
│   └── location.svg
├── inc
│   ├── class-addon-coco.php  ( class Addon_Coco extends GFAddOn )
│   ├── class-gf-field-cocomap.php ( class class GF_Field_CocoMap extends GF_Field )
│   ├── plugin-verifications.php (checks GF is installed otherwise shows a notice message )
│   └── render.php ( calls fn coco_render_map_field( GF_Field_CocoMap $instance, array $form, string $value ) ))
├── src
│   └── coco-gravity-form-map-field.js (some helpers to create the Google Map)
├── tests
│   ├── test-plugin-activation.php
│   └── wp-config.php

<?php // phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders a map field for a Gravity Form.
 *
 * This function outputs the HTML and JavaScript necessary to display a map field within a Gravity Form.
 * It initializes a Google Map with specified coordinates and binds event listeners for interacting with the map.
 * The map and input field are linked, allowing users to select a location on the map which updates the input field.
 *
 * @param object $instance An instance of the GF field class.
 * @param array  $form     The GF form array containing form details.
 * @param string $value    The initial value for the map field, typically a comma-separated latitude and longitude.
 *
 * @return string The rendered HTML and JavaScript for the map field.
 */
function coco_render_map_field( object $instance, array $form, string $value ): string {


	$input_id = sprintf( 'input_%d_%d', $form['id'], $instance->id );
	$name_id  = sprintf( 'input_%d', $instance->id );
	$field_id = sprintf( 'field_%d_%d', $form['id'], $instance->id );

	$is_entry_detail = $instance->is_entry_detail();
	$is_form_editor  = $instance->is_form_editor();
	$is_admin        = $is_form_editor || $is_entry_detail;

	$disabled_text = $is_form_editor ? 'disabled="disabled"' : '';

	// attributes of the input associated to the value that we'll save in the db
	$field_type         = 'text';
	$class_attribute    = $is_entry_detail || $is_form_editor ? '' : "class='gform_coco_map'";
	$required_attribute = $instance->isRequired ? 'aria-required="true"' : '';
	$invalid_attribute  = $instance->failed_validation ? 'aria-invalid="true"' : 'aria-invalid="false"';

	// options of the field
	$map_type           = $instance->mapType ?? 'terrain'; // Default to Terrain (change to  satellite if you want)
	$autocomplete_types = $instance->autocompleteTypes ?? '';
	$interaction_type   = $instance->interactionType ?? 'marker';


	// We can, with hooks, setup the default value of the map (coords and zoom) if we want to:
	$default_zoom = apply_filters( 'coco_gravity_form_map_field_default_zoom', null, $form );
	$default_lat  = apply_filters( 'coco_gravity_form_map_field_default_lat', null, $form );
	$default_lng  = apply_filters( 'coco_gravity_form_map_field_default_lng', null, $form );

	// there are two types of values, dingle coordinates or set of coordinates to defina a polygon

	ob_start();

	if ( empty( $instance->google_maps_api_key ) ) {
		if ( current_user_can( 'manage_options' ) ) {
			echo '<div class=""><p>The map field requires a Google Maps API key.
				<a style="text-decoration: underline;" href="'
				. esc_url( admin_url( 'admin.php?page=gf_settings&subview=coco-gravity-forms-map-addon' ) )
				. '">Configure</a></p></div>';
		}
		return ob_get_clean();
	}


	// start the html elements:

	// a fil
	do_action( 'coco_gravity_form_map_field_previous_to_field', $instance, $form, $value );

	?>


	<div id="map-container-<?php echo esc_attr( $field_id ); ?>" class="gform-field-coco-map"
		style="height: 300px; margin-bottom: 1rem;"></div>

	<input type="<?php echo esc_attr( $field_type ); ?>"
		readonly
		placeholder="<?php esc_attr_e( 'Latitude, Longitude', 'coco-gravity-form-map-field' ); ?>"
		name="<?php echo esc_attr( $name_id ); ?>"
		id="<?php echo esc_attr( $input_id ); ?>"
		value="<?php echo esc_attr( $value ); ?>"
		<?php
		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $class_attribute;
		?>
		<?php echo $required_attribute; ?>
		<?php echo $invalid_attribute; ?>
		<?php echo $disabled_text; ?>
		/>


	<?php
	// We don't continue if we are in a paginated form and this field is not shown in the current page
	$current_page = GFFormDisplay::get_current_page( $form['id'] );
	if ( $instance->pageNumber !== $current_page ) {
		return ob_get_clean();
	}
	?>

	<script>
		window.cocoVars = window.cocoVars || null;
		if ( null === window.cocoVars ) {
			window.cocoVars = {};
			cocoVars.cocoLocationIcon = <?php echo wp_json_encode( dirname( plugin_dir_url( __FILE__ ) ) . '/assets/location.svg' ); ?>;
			cocoVars.cocoClearPolygonIcon = <?php echo wp_json_encode( dirname( plugin_dir_url( __FILE__ ) ) . '/assets/clear-polygon.webp' ); ?>;
			cocoVars.cocoMarkerIcon = <?php echo wp_json_encode( dirname( plugin_dir_url( __FILE__ ) ) . '/assets/coco-marker.png' ); ?>;
			cocoVars.placesAPILoaded = false;
		}
		<?php
		// we need to know, in case there are more than 1 coco maps field , if any of them uses places autocomplete.
		echo ( ! empty( $autocomplete_types ) ) ? 'cocoVars.placesAPILoaded = true;' : '';
		?>

		window.cocoMaps = window.cocoMaps || {};

		cocoMaps['<?php echo esc_js( $input_id ); ?>'] = {
			map: null,
			inputElement: null,
			polygonArea: null,
			marker: null,
			initMap: () => {
				const input = document.getElementById('<?php echo esc_js( $input_id ); ?>');
				cocoMaps['<?php echo esc_js( $input_id ); ?>'].inputElement = input;
				const coordinatesInput = window.coordinatesFromInput(input); // {lat, lng} or null
				const coordinatesInitMap = coordinatesInput || {
					lat: <?php echo $default_lat ?? 41.77444381030458; ?>,
					lng: <?php echo $default_lng ?? 9.697649902343759; ?>
				};

				// Init the map calling google maps methods
				const mapContainerEl = document.getElementById('map-container-<?php echo esc_js( $field_id ); ?>');
				const map = new window.google.maps.Map(mapContainerEl, {
					center: coordinatesInitMap,
					disableDefaultUI: true, // Desactiva la interfaz predeterminada
					zoomControl: true,      // Activa los controles de zoom
					mapTypeControl: true,
					mapTypeIds: ['roadmap', 'terrain'],
					mapTypeId: '<?php echo esc_attr( $map_type ); ?>',
					mapTypeControlOptions: {
						style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
						position: google.maps.ControlPosition.BOTTOM_LEFT,
					},
					zoom: <?php
						echo $default_zoom ?? ' coordinatesInput ? 6 : 1 ';
					?>,
					mapId: '<?php echo esc_js( $instance->google_map_id ); ?>',
				});
				map.setTilt(0); // deactivate the view at 45º when zoom is big and view is satellite.
				cocoMaps['<?php echo esc_js( $input_id ); ?>'].map = map;
				window.gotoLocationButton('<?php echo esc_js( $input_id ); ?>');

				// Hook in JS to exectute code after the map is ready
				let tries = 0;
				const maxTries = 20; // in small connections we might need 20 tries
				function checkIfMapIsReady(callback) {
					if (map.getBounds()) callback();
					else if (tries++ < maxTries) setTimeout(() => checkIfMapIsReady(callback), 500);
					else console.error('Map did not render in time', '<?php echo esc_js( $input_id ); ?>');
				}
				checkIfMapIsReady(() => {
					console.log('Map <?php echo esc_js( $input_id ); ?> is ready');
					document.dispatchEvent(new CustomEvent("solarMapReady", { detail: cocoMaps['<?php echo esc_js( $input_id ); ?>'] }));
				});
				<?php
				// Now the hooks, one in php and just in case one in js (with a custom event)
				// BOOK:ROOF
				do_action( 'coco_gravity_form_script_after_map_created', $instance, $form, $value );
				?>

				<?php
				if ( 'marker' === $interaction_type ) :
					?>
					// Add initial marker if the coordinates are valid.
					if (coordinatesInput) {
						window.addMarker('<?php echo esc_js( $input_id ); ?>', coordinatesInput, cocoVars.cocoMarkerIcon);
						window.centerMapAtInputCoordinates(input, map);
					}
					// CLICK on the map > sets a market
					cocoMaps['<?php echo esc_js( $input_id ); ?>'].map.addListener('click', function(e) {
						const clickedCoordinates = e.latLng;
						const inputElement = document.getElementById('<?php echo esc_js( $input_id ); ?>');
						inputElement.value = `${clickedCoordinates.lat()},${clickedCoordinates.lng()}`;
						window.addMarker('<?php echo esc_js( $input_id ); ?>', clickedCoordinates, cocoVars.cocoMarkerIcon);
					});
				<?php endif; ?>

				<?php
				if ( 'polygon' === $interaction_type ) :
					?>

					<?php
					// @TODO: validate input value to polygon or remove the input value
					// add default polygon if input value is valid polygon
					// add interacion of creaging a polygon
					?>
					window.initPolygonSetup('<?php echo esc_js( $input_id ); ?>');
				<?php endif; ?>

				// Add the search input for the map
				<?php
				if ( ! empty( $autocomplete_types ) ) :
					?>
					const autocompleteTypes = [ '<?php echo esc_js( $autocomplete_types ); ?>' ];
					window.initPlacesAutocomplete(map, '<?php esc_attr_e( 'Search location', 'coco-gravity-form-map-field' ); ?>', autocompleteTypes);
				<?php endif; ?>
			}
		}

		// ---------------------------------------------------------------
		// Call to GoogleMapsAPI
		window.loadGoogleMapsAPI = function() {
			const script = document.createElement('script');
			script.src = 'https://maps.googleapis.com/maps/api/js?key=<?php
				echo esc_js( $instance->google_maps_api_key );
			?>&loading=async&callback=initAllMaps';
			if (cocoVars.placesAPILoaded) {
				script.src += '&libraries=places,drawing,marker'; // TODO:?
			}
			script.async = true;
			script.loading = 'async';
			document.head.appendChild(script);
		}

		// starting point to build every map
		window.initAllMaps = window.initAllMaps || function() {
			Object.keys(cocoMaps).forEach(function(key) {
				cocoMaps[key].initMap();
			});
		}

		// After GoogleMapsAPILoads
		// Executed only once.
		document.addEventListener("DOMContentLoaded", function() {
			if ( window.googleMapsAPILoaded !== true ) {

				setTimeout(function() {
					loadGoogleMapsAPI();
				}, 500);

			} // end of the code exectued only once in the page.
			window.googleMapsAPILoaded = true;
		});

	</script>


	<?php
	return ob_get_clean();
}


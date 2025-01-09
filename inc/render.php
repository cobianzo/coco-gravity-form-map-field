<?php

// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renderiza el campo personalizado del mapa.
 *
 * todo
 * @param string $value    El valor actual del campo.
 * @return string          El HTML y JS del campo.
 */
function asim_render_map_field( $instance, $form, $value ) {

	$field_id = absint( $form['id'] );

	$input_id = sprintf( 'input_%d_%d', $form['id'], $instance->id );
	$name_id  = sprintf( 'input_%d', $instance->id );
	$field_id = sprintf( 'field_%d_%d', $form['id'], $instance->id );

	$is_entry_detail = $instance->is_entry_detail();
	$is_form_editor  = $instance->is_form_editor();

	$disabled_text = $is_form_editor ? 'disabled="disabled"' : '';

	$field_type         = $is_entry_detail || $is_form_editor ? 'text' : 'hidden';
	$field_type         = 'text'; //todelete
	$class_attribute    = $is_entry_detail || $is_form_editor ? '' : "class='gform_asim_map'";
	$required_attribute = $instance->isRequired ? 'aria-required="true"' : '';
	$invalid_attribute  = $instance->failed_validation ? 'aria-invalid="true"' : 'aria-invalid="false"';




	// Salir si estamos en el administrador.
	$latlng = explode( ',', $value );

	[$lat, $lng] = [ -34.397, 150.644 ]; // default
	if ( 2 === count( $latlng ) ) {
		[$lat, $lng] = $latlng;
	}


	ob_start();

	if ( empty( $instance->google_maps_api_key ) ) {
		if ( current_user_can( 'manage_options' ) ) {
			echo '<div class="notice notice-error"><p>El campo de mapas requiere una clave de API de Google Maps. <a href="' . esc_url( admin_url( 'admin.php?page=gf_settings&subview=asim-gravity-form-map-field' ) ) . '">Configurar</a></p></div>';
		}
		return ob_get_clean();
	}

	?>
	<div id="map-container-<?php echo esc_attr( $field_id ); ?>" style="height: 300px;"></div>

	<input type="<?php echo esc_attr( $field_type ); ?>"
		placeholder="lat,lng"
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
		<?php
		// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
		?> />

	<script>
		let map;
		let marker;
		let inputElement;
		function initMap_<?php echo esc_js( $field_id ); ?>() {

			const inputElement = document.getElementById('<?php echo esc_js( $input_id ); ?>');

			const coordinatesInput = coordinatesFromInput();

			const coordinates = coordinatesInput || {
				lat: -34.397,
				lng: 150.644
			};

			// Inicializar el mapa.
			map = new google.maps.Map(document.getElementById('map-container-<?php echo esc_js( $field_id ); ?>'), {
				center: coordinates,
				disableDefaultUI: true, // Desactiva la interfaz predeterminada
				zoomControl: true,      // Activa los controles de zoom
				zoom: 8,
			});

			// Agregar marcador inicial si las coordenadas son válidas.
			if (coordinatesInput) {
				addMarker(coordinatesInput);
			}

			// Listener para agregar coordenadas al input y marcador al mapa.
			map.addListener('click', function(e) {
				const clickedCoordinates = e.latLng;
				inputElement.value = `${clickedCoordinates.lat()},${clickedCoordinates.lng()}`;
				addMarker(clickedCoordinates);
			});
		}

		// Función para agregar un marcador al mapa en las coordenadas proporcionadas.
		function addMarker(position) {
			if (marker) {
				marker.setMap(null); // Eliminar el marcador anterior.
			}

			marker = new google.maps.Marker({
				position,
				map,
			});
		}

		// Función para centrar el mapa en las coordenadas del input.
		function centerMapAtInputCoordinates() {
			const coordinates = coordinatesFromInput();
			if (coordinates) {
				map.setCenter(coordinates);
			} else {
				alert('Por favor, introduce coordenadas válidas en el formato "lat,lng".');
			}
		}

		// Función para extraer coordenadas del valor del input.
		function coordinatesFromInput() {
			const inputElement = document.getElementById('<?php echo esc_js( $input_id ); ?>');
			const value = inputElement.value;
			if (!value) return null;
			const [lat, lng] = value.split(',').map(parseFloat);
			if (!isNaN(lat) && !isNaN(lng)) {
				return {
					lat,
					lng
				};
			}
			return null;
		}

		(function() {


			// Cargar el script de Google Maps.
			(function loadGoogleMapsAPI() {
				const script = document.createElement('script');
				script.src = 'https://maps.googleapis.com/maps/api/js?key=<?php echo esc_js( $instance->google_maps_api_key ); ?>&callback=initMap_<?php echo esc_js( $field_id ); ?>';
				script.async = true;
				document.head.appendChild(script);
			})();

			// Exponer las funciones globalmente (opcional, si necesitas llamarlas desde otro lugar).
			window.centerMapAtInputCoordinates = centerMapAtInputCoordinates;
		})();
	</script>
	<?php
	return ob_get_clean();
}

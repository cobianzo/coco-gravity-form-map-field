<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renderiza el campo personalizado del mapa.
 *
 * todo
 * @param int    $input_id El ID del input.
 * @param string $value    El valor actual del campo.
 * @return string          El HTML y JS del campo.
 */
function asim_render_map_field( $instance, $input_id, $value ) {

	// Salir si estamos en el administrador.
	return '
	<input type="text" '
		. ' placeholder="lat,  lng"'
		. ' 	name="input_' . esc_attr( $input_id ) . '"'
		. ' 	id="input_' . esc_attr( $input_id ) . '"'
		. ' 	value="' . esc_attr( $value ) . '" />';

	if ( is_admin() ) {
		return '';
	}

	$latlng = explode( ',', $value );

	[$lat, $lng] = [ -34.397, 150.644 ]; // default
	if ( 2 === count( $latlng ) ) {
		[$lat, $lng] = $latlng;
	}


	ob_start();
	?>
	<div id="map-container-<?php echo esc_attr( $instance->id ); ?>" style="height: 300px;"></div>

	<input type="text"
		placeholder="lat,lng"
		name="input_<?php echo esc_attr( $input_id ); ?>"
		id="input_<?php echo esc_attr( $input_id ); ?>"
		value="<?php echo esc_attr( $value ); ?>" />

	<script>
		function initMap_<?php echo esc_js( $instance->id ); ?>() {
			var map = new google.maps.Map(document.getElementById('map-container-<?php echo esc_js( $instance->id ); ?>'), {
				center: {
					lat: <?php echo esc_attr( $lat ); ?>,
					lng: <?php echo esc_attr( $lng ); ?>
				},
				zoom: 8
			});

			map.addListener('click', function(e) {
				document.getElementById('input_<?php echo esc_js( $input_id ); ?>').value = e.latLng.lat() + ',' + e.latLng.lng();
			});
		}


		// Cargar Google Maps Script.
		(function loadGoogleMapsAPI() {
			var script = document.createElement('script');
			script.src = 'https://maps.googleapis.com/maps/api/js?key=<?php echo esc_attr( $instance->google_maps_api_key ); ?>&callback=initMap_<?php echo esc_js( $instance->id ); ?>';
			script.async = true;
			document.head.appendChild(script);
		})();
	</script>
	<?php
	return ob_get_clean();
}

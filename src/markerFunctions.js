/**
 * Adds a marker to the map. If the map already has a marker, it is removed.
 *
 * @param {string} inputName
 * @param {Object} position   The position of the marker. (google.maps.LatLng)
 * @param {string} markerIcon
 *
 * @return {Object} The marker added to the map. {google.maps.Marker}
 */
window.addMarker = (inputName, position, markerIcon = 'marker_yellow') => {
	const mapSetup = window.cocoMaps[inputName];
	if (mapSetup.marker) {
		mapSetup.marker.map = null; // Remove the previous marker.
	}
	window
		.paintAMarker(mapSetup.map, position, markerIcon, {
			scaledSize: new window.google.maps.Size(25, 30),
			anchor: new window.google.maps.Point(15, 15),
		})
		.then((marker) => {
			mapSetup.marker = marker;
		});
	return mapSetup.marker;
};

window.removeMarker = (inputName) => {
	const mapSetup = window.cocoMaps[inputName];
	if (mapSetup.marker) {
		mapSetup.marker.map = null; // Remove the previous marker.
	}
};

// Variable para almacenar la promesa de la librería cargada
let markerLibraryPromise = null;

// Función para cargar la librería solo una vez
async function loadMarkerLibrary() {
	if (!markerLibraryPromise) {
		markerLibraryPromise = window.google.maps.importLibrary('marker');
	}
	return markerLibraryPromise;
}

// Función para pintar un marker usando AdvancedMarkerElement
window.paintAMarker = async function (map, position, markerIcon = 'marker_yellow', extraOptions = {}) {
	try {
		// Cargar la librería (solo se cargará una vez)
		const { AdvancedMarkerElement } = await loadMarkerLibrary();

		// Construir la URL del ícono
		const icon =
			markerIcon.includes('http') || markerIcon.includes('wp-content')
				? markerIcon
				: `https://maps.google.com/mapfiles/ms/icons/${markerIcon}.png`;

		// Crear el marker con AdvancedMarkerElement
		const marker = new AdvancedMarkerElement({
			map, // El mapa donde se colocará el marker
			position, // {lat, lng}
			title: extraOptions.title || '', // Título del marker
			content: buildMarkerContent(icon, extraOptions), // Contenido personalizado
		});

		return marker;
	} catch (error) {
		// eslint-disable-next-line no-console
		console.error('Error al cargar la librería o crear el marker:', error);
		throw error; // Relanzar el error para manejarlo fuera de la función
	}
};

// Función para construir el contenido del marker
function buildMarkerContent(icon, extraOptions) {
	const container = document.createElement('div');
	container.style.backgroundImage = `url(${icon})`;
	container.style.backgroundSize = 'cover';
	container.style.width = '32px'; // Tamaño del ícono
	container.style.height = '32px';
	container.style.cursor = 'pointer';

	// Aplicar opciones adicionales (si las hay)
	if (extraOptions.size) {
		container.style.width = `${extraOptions.size}px`;
		container.style.height = `${extraOptions.size}px`;
	}

	if (extraOptions.style) Object.assign(container.style, extraOptions.style);

	return container;
}

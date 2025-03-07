window.initPolygonSetup = function (inputName) {
	// create the button that deletes any started polygon
	window.createClearPolygonButton(inputName);

	// crates the Google Maps polygon object
	window.createPolygonArea(inputName);

	// CLICK on the map >creates a new vertex for the polygon
	window.cocoMaps[inputName].map.addListener('click', function (e) {
		const clickedCoordinates = `${e.latLng.lat()},${e.latLng.lng()}`;
		const inputElement = document.getElementById(inputName);
		inputElement.value += ' ' + clickedCoordinates;
		inputElement.value = inputElement.value.trim();

		window.paintPolygonFromInput(inputName);
	});

	// if there is a value in the input, we paint the polygon on page load.
	window.paintPolygonFromInput(inputName);
};

/**
 * Creates a button to clear the polygon drawn on the map and removes it from the input field.
 * The button is added to the bottom left of the map.
 *
 * @param {string} inputName - The name of the input field associated with the map, eg 'input_1_3'.
 *
 * @since 3.0.0
 */
window.createClearPolygonButton = function (inputName) {
	const mapSetup = window.cocoMaps[inputName];
	const cocoVars = window.cocoVars || {};
	const clearPolygonButtonEl = document.createElement('button');
	clearPolygonButtonEl.innerHTML =
		'<img style="width:24px;" width="24" height="24" src="' + cocoVars.cocoClearPolygonIcon + '}" />';
	const id = `coco-clear-polygon-button-${inputName}`;
	clearPolygonButtonEl.id = id;
	clearPolygonButtonEl.classList.add('custom-map-control-button');
	clearPolygonButtonEl.title = 'Click to clear the area';
	clearPolygonButtonEl.style.margin = '0 0 10px';
	clearPolygonButtonEl.style.aspectRatio = '1 / 1';
	clearPolygonButtonEl.style.padding = '2px';
	clearPolygonButtonEl.style.border = '3px solid white';
	clearPolygonButtonEl.style.background = 'white';
	clearPolygonButtonEl.style.borderRadius = '50%';
	clearPolygonButtonEl.style.boxShadow = '3px 3px 10px black';

	const inputValue = document.getElementById(inputName).value;
	clearPolygonButtonEl.style.display = inputValue.trim().length ? 'block' : 'none';

	clearPolygonButtonEl.addEventListener('click', (e) => {
		e.preventDefault();
		window.clearPolygon(inputName);
	});

	window.google = window.google || null;
	mapSetup.map.controls[window.google.maps.ControlPosition.BOTTOM_LEFT].push(clearPolygonButtonEl);

	mapSetup.map.setOptions({ draggableCursor: 'crosshair' });
};

/**
 * Creates the polygon object associated to the map.
 *
 * @param {string} inputName - The name of the input field associated with the map, eg 'input_1_3'.
 *
 * @since 3.0.0
 */
window.createPolygonArea = function (inputName) {
	const mapSetup = window.cocoMaps[inputName];

	mapSetup.polygonArea = new window.google.maps.Polygon({
		// paths: mapSetup.polygonCoords,
		strokeColor: '#FF0000',
		strokeOpacity: 0.8,
		strokeWeight: 2,
		fillColor: '#FF0000',
		fillOpacity: 0.35,
		map: mapSetup.map,
		editable: true,
	});

	mapSetup.map.getDiv().addEventListener('mouseup', function () {
		setTimeout(() => window.polygonCoordsToInput(inputName), 500);
	});
};

/**
 * Clears the polygon map and input value.
 *
 * @param {string} inputName
 *
 * @return {void}
 */
window.clearPolygon = function (inputName) {
	const mapSetup = window.cocoMaps[inputName];
	const inputElement = document.getElementById(inputName);

	mapSetup.polygonArea.setPaths([]);
	inputElement.value = '';

	// remove also de marker
	window.removeMarker(inputName);

	mapSetup.map.setOptions({ draggableCursor: 'crosshair' });

	const clearPoligonBtn = document.getElementById(`coco-clear-polygon-button-${inputName}`);
	clearPoligonBtn.style.display = 'none';
};

// Helper
window.convertCoordinatesIntoGMapCoordinates = function (coordinatesAsString) {
	const coordinatesArray = coordinatesAsString.split(' ');
	const newPolygonCoords = coordinatesArray.map((coord) => {
		const [lat, lng] = coord.split(',');
		return new window.google.maps.LatLng({ lat: parseFloat(lat), lng: parseFloat(lng) });
	});
	return newPolygonCoords;
};

window.paintPolygonFromInput = function (inputName) {
	const value = document.getElementById(inputName).value;
	if ('' === value) {
		return;
	}
	const mapSetup = window.cocoMaps[inputName];
	const { polygonArea } = mapSetup;

	const coordinatesArray = value.split(' ');
	const newPolygonCoords = window.convertCoordinatesIntoGMapCoordinates(value);

	polygonArea.setPath(newPolygonCoords);

	const clearPoligonBtn = document.getElementById(`coco-clear-polygon-button-${inputName}`);
	if (clearPoligonBtn) clearPoligonBtn.style.display = coordinatesArray.length ? 'block' : 'none';
};

// Función para extraer las coordenadas del polígono en el formato deseado
window.polygonCoordsToInput = function (inputName) {
	const mapSetup = window.cocoMaps[inputName];
	const path = mapSetup.polygonArea.getPath();
	if (!path) return;
	const coordenadas = [];
	path.forEach((latlng) => {
		coordenadas.push(latlng.lat() + ',' + latlng.lng());
	});

	const inputElement = document.getElementById(inputName);
	inputElement.value = coordenadas.join(' ').trim();
};

/**
 * Paints a polygon in the map. We use it to show the profile of the selected roof. @BOOK:ROOF
 * @param {Object} gMap                - El mapa de Google.
 * @param {string} coordinatesAsString - Las coordenadas del pol gono en formato de string.
 * @param {Object} extraparams
 * @since 3.0.0
 */
window.paintAPoygonInMap = function (gMap, coordinatesAsString, extraparams = {}) {
	const newPolygonCoords = window.convertCoordinatesIntoGMapCoordinates(coordinatesAsString);
	// Crear el polígono
	const params = {
		paths: newPolygonCoords,
		strokeColor: '#FFAA00',
		strokeOpacity: 0.8,
		strokeWeight: 2,
		fillColor: '#FF0000',
		fillOpacity: 0.35,
		clickable: false, // Evita que capture eventos
		...extraparams,
	};
	const buildingPolygon = new window.google.maps.Polygon(params);

	buildingPolygon.setMap(gMap);

	return buildingPolygon;
};

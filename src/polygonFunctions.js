window.initPolygonSetup = function (inputName) {
	console.log('>>>>> 1.START');
	// create the button that deletes any started polygon
	window.clearPolygonButton(inputName);

	console.log('>>>>> 2.btn created, Creagin poly');
	// crates the Google Maps polygon object
	window.createPolygonMap(inputName);

	// CLICK on the map >creates a new vertex for the polygon
	window.asimMaps[inputName].map.addListener('click', function (e) {
		console.log('>>>>> CLCICK');
		const clickedCoordinates = e.latLng;
		window.addVertexPolygonMap(inputName, clickedCoordinates);
	});

	console.log('>>>>> 3.paiting pol');
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
window.clearPolygonButton = function (inputName) {
	const mapSetup = window.asimMaps[inputName];
	const clearPolygonButtonEl = document.createElement('button');
	clearPolygonButtonEl.innerHTML =
		'<img style="width:24px;" width="24" height="24" src="' + window.asimClearPolygonIcon + '}" />';
	clearPolygonButtonEl.id = `asim-clear-polygon-button-${inputName}`;
	clearPolygonButtonEl.classList.add('custom-map-control-button');
	clearPolygonButtonEl.title = 'Click to clear the area';
	clearPolygonButtonEl.style.margin = '0 0 5px';
	clearPolygonButtonEl.style.background = 'transparent';
	clearPolygonButtonEl.style.aspectRatio = '1';
	clearPolygonButtonEl.style.aspectRatio = '1 / 1';
	clearPolygonButtonEl.style.padding = '2px';
	clearPolygonButtonEl.style.border = '3px solid white';
	clearPolygonButtonEl.style.background = 'rgba(255, 0, 0, 0.3)';
	clearPolygonButtonEl.style.borderRadius = '50%';
	clearPolygonButtonEl.style.boxShadow = '3px 3px 10px black';

	clearPolygonButtonEl.style.display = 'none';

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
window.createPolygonMap = function (inputName) {
	const mapSetup = window.asimMaps[inputName];

	mapSetup.polygon = new window.google.maps.Polygon({
		paths: mapSetup.polygonCoords,
		strokeColor: '#FF0000',
		strokeOpacity: 0.8,
		strokeWeight: 2,
		fillColor: '#FF0000',
		fillOpacity: 0.35,
		map: mapSetup.map,
	});
	mapSetup.polygon.setOptions({ editable: true });
};

/**
 * Adds a new vertex to the polygon map.
 *
 * @param {string} inputName
 * @param {Object} clickedCoordinates
 *
 * @return {void}
 */
window.addVertexPolygonMap = function (inputName, clickedCoordinates) {
	const mapSetup = window.asimMaps[inputName];
	const inputElement = document.getElementById(inputName);
	mapSetup.polygonCoords.push(clickedCoordinates);
	mapSetup.polygon.setPath(mapSetup.polygonCoords);
	const coordinatesArray = mapSetup.polygonCoords.map((coord) => `${coord.lat()},${coord.lng()}`);
	inputElement.value = coordinatesArray.join(' ');

	// if the vertex is the first one, add a marker to shown the user that he did smthng
	if (1 === coordinatesArray.length) {
		const marker = window.addMarker(inputName, clickedCoordinates, 'red-pushpin');
		marker.addListener('click', () => window.clearPolygon(inputName));
		marker.setCursor('not-allowed');
		const clearPoligonBtn = document.getElementById(`asim-clear-polygon-button-${inputName}`);
		clearPoligonBtn.style.display = 'block';
		mapSetup.map.setOptions({ draggableCursor: 'copy' });
	}
};

/**
 * Clears the polygon map and input value.
 *
 * @param {string} inputName
 *
 * @return {void}
 */
window.clearPolygon = function (inputName) {
	const mapSetup = window.asimMaps[inputName];
	const inputElement = document.getElementById(inputName);

	mapSetup.polygonCoords = [];
	mapSetup.polygon.setPaths([]);
	inputElement.value = '';

	// remove also de marker
	window.removeMarker(inputName);

	mapSetup.map.setOptions({ draggableCursor: 'crosshair' });

	const clearPoligonBtn = document.getElementById(`asim-clear-polygon-button-${inputName}`);
	clearPoligonBtn.style.display = 'none';
};

window.paintPolygonFromInput = function (inputName) {
	const value =  document.getElementById(inputName).value;
	if ('' === value) {
		return;
	}
	const mapSetup = window.asimMaps[inputName];
	const { polygon } = mapSetup;

	const coordinatesArray = value.split(' ');
	const newPolygonCoords = coordinatesArray.map((coord) => {
		const [lat, lng] = coord.split(',');
		return new window.google.maps.LatLng({ lat: parseFloat(lat), lng: parseFloat(lng) });
	});

	mapSetup.polygonCoords = newPolygonCoords;
	polygon.setPath(newPolygonCoords);
};

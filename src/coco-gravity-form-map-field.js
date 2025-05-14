/* eslint-disable jsdoc/require-returns-type */

// The loading of the maps happens after this script.
// Here we add the helpers functions used and binded to the map
// created in render.php
import './gotoLocation';
import './searchInput';
import './markerFunctions';
import './polygonFunctions';

/**
 * Extracts coordinates from an input element.
 * The input value can be "34.345,144.4334" or a polygon like "34.3,144.43 33.1 143.1 35.5 155.3"
 *
 * @param {HTMLInputElement} input
 * @return {Object|null} {lat, lng} or null if the input is empty or if the value is not a valid lat,lng pair.
 */
window.coordinatesFromInput = (input) => {
	let value = input.value;
	if (!value) return null;

	// check if type of value is a polygon like "34.3,144.43 33.1 143.1 35.5 155.3"
	if (value.includes(' ')) {
		const vertexes = value.split(' ');
		value = vertexes[0]; // we return the first vertex as the valid coordinates (we could make an average too)
	}

	const [lat, lng] = value.split(',').map(parseFloat);
	if (!isNaN(lat) && !isNaN(lng)) {
		return { lat, lng };
	}
	return null;
};

/**
 * Centers the map at the coordinates input on the element.
 *
 * @param {HTMLInputElement} input The input element containing the coordinates
 * @param {Object}           map   The map element (google.maps.Map)
 */
window.centerMapAtInputCoordinates = (input, map) => {
	const coordinatesInput = window.coordinatesFromInput(input);
	if (coordinatesInput) {
		map.setCenter(coordinatesInput);
	} else {
		// eslint-disable-next-line
		alert('Please use format "lat,lng".');
	}
};


/**
 * eslint-disable jsdoc/require-returns-type
 * Returns the first Google Map found in the page or null if none is found.
 *
 * @return {window.google.maps.Map|null} The first map found in page.
 */
window.getFirstGoogleMapInPage = () => {
	for (const key in window) {
		if (window[key] instanceof window.google.maps.Map) {
			return window[key];
		}
	}
	return null;
}

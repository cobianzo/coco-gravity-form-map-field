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
	const icon = 'http://maps.google.com/mapfiles/ms/icons/' + markerIcon + '.png';
	const mapSetup = window.asimMaps[inputName];
	if (mapSetup.marker) {
		mapSetup.marker.setMap(null); // Remove the previous marker.
	}

	return (mapSetup.marker = new window.google.maps.Marker({
		position,
		map: mapSetup.map,
		icon,
	}));
};

window.removeMarker = (inputName) => {
	const mapSetup = window.asimMaps[inputName];
	if (mapSetup.marker) {
		mapSetup.marker.setMap(null); // Remove the previous marker.
	}
};

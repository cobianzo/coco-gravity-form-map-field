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
		mapSetup.marker.setMap(null); // Remove the previous marker.
	}
	mapSetup.marker = window.paintAMarker(mapSetup.map, position, markerIcon, {
		scaledSize: new window.google.maps.Size(25, 30),
		anchor: new window.google.maps.Point(15, 15),
	});
	return mapSetup.marker;
};

window.removeMarker = (inputName) => {
	const mapSetup = window.cocoMaps[inputName];
	if (mapSetup.marker) {
		mapSetup.marker.setMap(null); // Remove the previous marker.
	}
};

window.paintAMarker = function (map, position, markerIcon, extraOptions = {}) {
	const icon =
		markerIcon.includes('http') || markerIcon.includes('wp-content')
			? markerIcon
			: `https://maps.google.com/mapfiles/ms/icons/${markerIcon}.png`;

	const marker = new window.google.maps.Marker({
		position, // {lat, lng}
		map,
		icon: {
			url: icon,
			...extraOptions,
		},
	});
	return marker;
};

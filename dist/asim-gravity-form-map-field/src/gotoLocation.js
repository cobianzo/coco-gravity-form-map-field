/**
 * Adds a custom "Location" button to a Google Map that centers the map
 * on the user's current geolocation when clicked. The button is styled
 * and positioned at the top left of the map. If geolocation is supported
 * and permitted by the user, the map is centered on their location,
 * ~~ and a marker is added ~~ (we dont add the marker anymore, but we can do it easily).
 * The input field associated with the map is updated
 * with the latitude and longitude of the user's location. If geolocation
 * is not supported or an error occurs, an alert message is displayed.
 *
 * @param {string} inputName - The gf input name used to reference the specific map and input element. eg input_1_3
 */
window.gotoLocationButton = function (inputName) {
	const mapSetup = window.asimMaps[inputName];

	// Create a control to center the map on the user's current location
	const centerControlDiv = document.createElement('div');
	const centerControlButton = document.createElement('button');
	centerControlButton.innerHTML =
		'<img style="filter:invert(1);width:24px;" width="24" height="24" src="' +
		window.asimLocationIcon +
		'}" />';
	centerControlButton.classList.add('custom-map-control-button');
	centerControlDiv.appendChild(centerControlButton);

	// Optional styling for the button
	centerControlButton.style.margin = '10px';
	centerControlButton.style.cursor = 'pointer';

	window.google = window.google || null;
	mapSetup.map.controls[window.google.maps.ControlPosition.TOP_LEFT].push(centerControlDiv);

	// Add event to the button to center the map
	centerControlButton.addEventListener('click', (e) => {
		e.preventDefault();

		if (e.target.ownerDocument.activeElement.classList.contains('pac-target-input')) {
			return;
		}

		const btn = e.currentTarget;
		const currentText = btn.innerHTML;
		window.navigator = window.navigator || null;
		if (window.navigator.geolocation) {
			btn.innerHTML = 'Loading...';
			window.calculateCurrentLocation = Math.floor(Date.now() / 1000);
			window.navigator.geolocation.getCurrentPosition(
				(position) => {
					btn.innerHTML = currentText;
					if (window.cancelCalculateCurrentLocation === window.calculateCurrentLocation) {
						window.calculateCurrentLocation = null;
						return;
					}
					window.calculateCurrentLocation = null;

					const userLocation = {
						lat: position.coords.latitude,
						lng: position.coords.longitude,
					};
					mapSetup.map.setCenter(userLocation);
					// OPTIONAL: we can set the markeet when location is found
					// window.addMarker(inputName, userLocation);
					mapSetup.inputElement.value = `${userLocation.lat},${userLocation.lng}`;
				},
				(err) => {
					/* eslint-disable */
					alert('Could not get the user location.');
					console.error('Error when coudl not get user location', err);
					/* eslint-enable */
					btn.innerHTML = currentText;
				}
			);
		} else {
			// eslint-disable-next-line
			alert('The browser does not support geolocation.');
		}
	});
};

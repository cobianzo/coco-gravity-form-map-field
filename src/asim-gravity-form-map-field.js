// The loading of the maps happens after this script.
// Here we add the helpers functions used and binded to the map
// created in render.php

/**
 * Extracts coordinates from an input element.
 *
 * @param {HTMLInputElement} input
 * @return {Object|null} {lat, lng} or null if the input is empty or if the value is not a valid lat,lng pair.
 */
window.coordinatesFromInput = (
	input
) => {
	const value = input.value;
	if (!value) return null;
	const [lat, lng] = value
		.split(',')
		.map(parseFloat);
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
window.centerMapAtInputCoordinates = (
	input,
	map
) => {
	const coordinatesInput =
		window.coordinatesFromInput(
			input
		);
	if (coordinatesInput) {
		map.setCenter(coordinatesInput);
	} else {
		// eslint-disable-next-line
		alert(
			'Please use format "lat,lng".'
		);
	}
};

/**
 * Adds a marker to the map. If the map already has a marker, it is removed.
 *
 * @param {Object} position The position of the marker. (google.maps.LatLng)
 * @param {Object} map      The map to add the marker to. (google.maps.Map)
 */
window.addMarker = (position, map) => {
	if (map.marker) {
		map.marker.setMap(null); // Remove the previous marker.
	}

	map.marker =
		new window.google.maps.Marker({
			position,
			map,
		});
};

/**
 * Adds a custom "Location" button to a Google Map that centers the map
 * on the user's current geolocation when clicked. The button is styled
 * and positioned at the top left of the map. If geolocation is supported
 * and permitted by the user, the map is centered on their location, and
 * a marker is added. The input field associated with the map is updated
 * with the latitude and longitude of the user's location. If geolocation
 * is not supported or an error occurs, an alert message is displayed.
 *
 * @param {string} inputName - The name used to reference the specific map and input element.
 */

window.locationButton = function (
	inputName
) {
	const mapSetup =
		window.asimMaps[inputName];

	// Create a control to center the map on the user's current location
	const centerControlDiv =
		document.createElement('div');
	const centerControlButton =
		document.createElement(
			'button'
		);
	centerControlButton.innerHTML = `<img style="filter:invert(1);width:24px;" width="24" height="24" src="${window.asimLocationIcon}" />`;
	centerControlButton.classList.add(
		'custom-map-control-button'
	);
	centerControlDiv.appendChild(
		centerControlButton
	);

	// Optional styling for the button
	centerControlButton.style.margin =
		'10px';
	centerControlButton.style.cursor =
		'pointer';

	window.google =
		window.google || null;
	mapSetup.map.controls[
		window.google.maps
			.ControlPosition.TOP_LEFT
	].push(centerControlDiv);

	// Add event to the button to center the map
	centerControlButton.addEventListener(
		'click',
		(e) => {
			e.preventDefault();

			if (
				e.target.ownerDocument.activeElement.classList.contains(
					'pac-target-input'
				)
			) {
				return;
			}

			const btn = e.currentTarget;
			const currentText =
				btn.innerHTML;
			window.navigator =
				window.navigator ||
				null;
			if (
				window.navigator
					.geolocation
			) {
				btn.innerHTML =
					'Loading...';
				window.calculateCurrentLocation =
					Math.floor(
						Date.now() /
							1000
					);
				window.navigator.geolocation.getCurrentPosition(
					(position) => {
						btn.innerHTML =
							currentText;
						if (
							window.cancelCalculateCurrentLocation ===
							window.calculateCurrentLocation
						) {
							window.calculateCurrentLocation =
								null;
							return;
						}
						window.calculateCurrentLocation =
							null;

						const userLocation =
							{
								lat: position
									.coords
									.latitude,
								lng: position
									.coords
									.longitude,
							};
						mapSetup.map.setCenter(
							userLocation
						);
						window.addMarker(
							userLocation,
							mapSetup.map
						);
						mapSetup.inputElement.value = `${userLocation.lat},${userLocation.lng}`;
					},
					() => {
						// eslint-disable-next-line
						alert(
							'Could not get the user location.'
						);
						btn.innerHTML =
							currentText;
					}
				);
			} else {
				// eslint-disable-next-line
				alert(
					'The browser does not support geolocation.'
				);
			}
		}
	);
};

/**
 * Initializes the Google Places autocomplete feature on a map.
 * Creates a container and an input in the map and adds an autocomplete
 * that is centered on cities and shows the formatted address in the
 * input. When a place is selected, the map is centered on that place and
 * a marker is added. The input is updated with the formatted address.
 *
 * @param {Object}   map         google.maps.Map: The map to add the autocomplete to.
 * @param { string } placeholder
 * @return {Object} An object with the properties input, autocomplete and container.
 */
window.initPlacesAutocomplete =
	function (
		map,
		placeholder = 'Search place'
	) {
		const searchContainer =
			document.createElement(
				'div'
			);
		searchContainer.style.position =
			'absolute';
		searchContainer.style.top =
			'10px';
		searchContainer.style.left =
			'50%';
		searchContainer.style.transform =
			'translateX(-50%)';
		searchContainer.style.zIndex =
			'1000';
		searchContainer.style.width =
			'calc( 100% - 250px )';
		searchContainer.style.maxWidth =
			'200px';

		const input =
			document.createElement(
				'input'
			);
		input.setAttribute(
			'type',
			'text'
		);
		input.setAttribute(
			'placeholder',
			placeholder
		);
		input.style.width = '100%';
		input.style.padding = '12px';
		input.style.borderRadius =
			'4px';
		input.style.border =
			'1px solid #ccc';
		input.style.boxShadow =
			'0 2px 6px rgba(0,0,0,0.3)';

		searchContainer.appendChild(
			input
		);
		map.getDiv().appendChild(
			searchContainer
		);

		// Initialize the autocomplete
		const autocomplete =
			new window.google.maps.places.Autocomplete(
				input,
				{
					types: ['(cities)'],
					fields: [
						'formatted_address',
						'geometry',
						'name',
					],
				}
			);

		// Bind the autocomplete to the map
		autocomplete.bindTo(
			'bounds',
			map
		);

		// Handle the place change event
		autocomplete.addListener(
			'place_changed',
			() => {
				const place =
					autocomplete.getPlace();

				if (
					!place.geometry ||
					!place.geometry
						.location
				) {
					// eslint-disable-next-line
					console.log(
						'Selected location not found'
					);
					return;
				}

				// Center the map
				if (
					place.geometry
						.viewport
				) {
					map.fitBounds(
						place.geometry
							.viewport
					);
				} else {
					map.setCenter(
						place.geometry
							.location
					);
					map.setZoom(15);
				}

				// Use the existing addMarker function (so far we don't show the marker)
				// window.addMarker(place.geometry.location, map);
				if (
					window.calculateCurrentLocation
				) {
					window.cancelCalculateCurrentLocation =
						window.calculateCurrentLocation;
				}

				// Update the input with the formatted address
				input.value =
					place.formatted_address;
			}
		);

		return {
			input,
			autocomplete,
			container: searchContainer,
		};
	};

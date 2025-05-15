/**
 * Initializes the Google Places autocomplete feature on a map.
 * Creates a container and an input in the map and adds an autocomplete
 * that is centered on cities and shows the formatted address in the
 * input. When a place is selected, the map is centered on that place and
 * a marker is added. The input is updated with the formatted address.
 *
 * @param {Object}   map               google.maps.Map: The map to add the autocomplete to.
 * @param { string } placeholder
 * @param {Array}    autocompleteTypes
 * @return {Object} An object with the properties input, autocomplete and container.
 */
window.initPlacesAutocomplete = function (map, placeholder = 'Search place', autocompleteTypes = ['geocode']) {
	// creation of the div container, centered top in over the map
	const searchContainer = document.createElement('div');
	searchContainer.style.position = 'absolute';
	searchContainer.style.paddingTop = '10px';
	searchContainer.style.zIndex = '1000';
	searchContainer.style.width = 'calc( 100% - 250px )';
	searchContainer.style.maxWidth = '300px';

	const input = document.createElement('input');
	input.setAttribute('type', 'text');
	input.setAttribute('placeholder', placeholder);
	input.style.width = '100%';
	input.style.padding = '12px';
	input.style.borderRadius = '4px';
	input.style.border = '1px solid #ccc';
	input.style.boxShadow = '0 2px 6px rgba(0,0,0,0.3)';

	searchContainer.appendChild(input);
	// map.getDiv().appendChild(searchContainer);

	map.controls[window.google.maps.ControlPosition.TOP_CENTER].push(searchContainer);

	// Initialize the autocomplete
	const autocomplete = new window.google.maps.places.Autocomplete(input, {
		types: autocompleteTypes,
		fields: ['formatted_address', 'geometry', 'name'],
	});

	// Bind the autocomplete to the map
	autocomplete.bindTo('bounds', map);

	// Handle the place change event
	autocomplete.addListener('place_changed', () => {
		const place = autocomplete.getPlace();

		if (!place.geometry || !place.geometry.location) {
			// eslint-disable-next-line
			console.log('Selected location not found');
			return;
		}

		// Center the map
		if (place.geometry.viewport) {
			map.fitBounds(place.geometry.viewport);
		} else {
			map.setCenter(place.geometry.location);
			map.setZoom(16);
		}

		// Use the existing addMarker function (so far we don't show the marker)
		if (window.calculateCurrentLocation) {
			window.cancelCalculateCurrentLocation = window.calculateCurrentLocation;
		}

		// Update the input with the formatted address
		input.value = place.formatted_address;
	});

	return {
		input,
		autocomplete,
		container: searchContainer,
	};
};

(function() {
	'use strict';

	let GeoJSON = {};

	// https://github.com/Leaflet/Leaflet/blob/f8e09f993292579a1af88261c9b461730f22e4e6/src/layer/GeoJSON.js#L49-L57
	// https://github.com/mapbox/simplestyle-spec/tree/master/1.1.0
	// https://leafletjs.com/reference-1.6.0.html#path
	GeoJSON.simpleStyleToLeafletPathOptions = function(featureProperties) {
		let simpleStyleToLeaflet = {
			'stroke': 'color',
			'stroke-width': 'weight',
			'stroke-opacity': 'opacity',
			'fill': 'fillColor',
			'fill-opacity': 'fillOpacity',
		};

		let pathOptions = {};

		for (let [key, value] of Object.entries(simpleStyleToLeaflet)) {
			if (featureProperties[key]) {
				pathOptions[value] = featureProperties[key];
			}
		}

		return pathOptions;
	};

	if (!window.maps) {
		window.maps = {};
	}

	window.maps.GeoJSON = GeoJSON;
})();

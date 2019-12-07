(function() {
	'use strict';

	let GeoJson = {};

	// https://github.com/Leaflet/Leaflet/blob/f8e09f993292579a1af88261c9b461730f22e4e6/src/layer/GeoJSON.js#L49-L57
	// https://github.com/mapbox/simplestyle-spec/tree/master/1.1.0
	// https://leafletjs.com/reference-1.6.0.html#path
	GeoJson.simpleStyleToLeafletPathOptions = function(featureProperties) {
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

	function escapeHTML(unsafeText) {
		let div = document.createElement('div');
		div.innerText = unsafeText;
		return div.innerHTML;
	}

	GeoJson.popupContentFromProperties = function(properties) {
		if (!properties.title && !properties.description) {
			return '';
		}

		if (!properties.description) {
			return escapeHTML(properties.title);
		}

		if (!properties.title) {
			return escapeHTML(properties.description);
		}

		return '<strong>' + escapeHTML(properties.title) + '</strong><br>'
			+ escapeHTML(properties.description || '');
	};

	GeoJson.newGeoJsonLayer = function(L, json) {
		return L.geoJSON(
			json,
			{
				style: function (feature) {
					return GeoJson.simpleStyleToLeafletPathOptions(feature.properties);
				},
				onEachFeature: function (feature, layer) {
					let popupContent = GeoJson.popupContentFromProperties(feature.properties);
					if (popupContent !== '') {
						layer.bindPopup(popupContent);
					}
				}
			}
		);
	};

	if (!window.maps) {window.maps = {};}
	if (!window.maps.leaflet) {window.maps.leaflet = {};}

	window.maps.leaflet.GeoJson = GeoJson;
})();

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

	GeoJson.createColoredIcon = function(L, color) {
		if (!/^#?[a-fA-F0-9]{3,6}$/.test(color)) {
			return null;
		}

		if (color.charAt(0) !== '#') {
			color = '#' + color;
		}

		let svg = '<svg xmlns="http://www.w3.org/2000/svg" width="25" height="41" viewBox="0 0 25 41">'
			+ '<path d="M12.5 0C5.5 0 0 5.5 0 12.3c0 2.4.7 4.6 1.9 6.5L12.5 41l10.6-22.2c1.2-1.9 1.9-4.1 1.9-6.5C25 5.5 19.5 0 12.5 0z" '
			+ 'fill="' + color + '" stroke="#333" stroke-width="1"/>'
			+ '<circle cx="12.5" cy="12.5" r="5" fill="#fff" opacity="0.6"/>'
			+ '</svg>';
		return new L.Icon({
			iconUrl: 'data:image/svg+xml;base64,' + btoa(svg),
			iconSize: [25, 41],
			iconAnchor: [12, 41],
			popupAnchor: [1, -34]
		});
	};

	GeoJson.pointToLayer = function(feature, latlng) {
		let options = {};
		let color = feature.properties && feature.properties['marker-color'];
		if (color) {
			let icon = GeoJson.createColoredIcon(L, color);
			if (icon) {
				options.icon = icon;
			}
		}
		return L.marker(latlng, options);
	};

	GeoJson.newGeoJsonLayer = function(L, json) {
		return L.geoJSON(
			json,
			{
				style: function (feature) {
					return GeoJson.simpleStyleToLeafletPathOptions(feature.properties);
				},
				pointToLayer: GeoJson.pointToLayer,
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

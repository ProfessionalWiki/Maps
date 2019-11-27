// Builds markers, polygons, etc from the options object (serialized data coming from PHP)
(function($, mw) {
	'use strict';

	/**
	 * Creates a new marker with the provided data and returns it.
	 * @param {Object} properties Contains the fields lat, lon, title, text and icon
	 * @param {Object} options Map options
	 * @return {L.Marker}
	 */
	function createMarker(properties, options) {
		let markerOptions = {
			title:properties.title
		};

		let marker = L.marker( [properties.lat, properties.lon], markerOptions );

		if (properties.hasOwnProperty('icon') && properties.icon !== '') {
			marker.setOpacity(0);

			let img = new Image();
			img.onload = function() {
				let icon = new L.Icon({
					iconUrl: properties.icon,
					iconSize: [ img.width, img.height ],
					iconAnchor: [ img.width / 2, img.height ],
					popupAnchor: [ -img.width % 2, -img.height*2/3 ]
				});

				marker.setIcon(icon);
				marker.setOpacity(1);
			};
			img.src = properties.icon;
		}

		if( properties.hasOwnProperty('text') && properties.text.length > 0 ) {
			marker.bindPopup( properties.text );
		}

		if ( options.copycoords ) {
			marker.on(
				'contextmenu',
				function( e ) {
					prompt(mw.msg('maps-copycoords-prompt'), e.latlng.lat + ',' + e.latlng.lng);
				}
			);
		}

		return marker;
	}

	function newLineFromProperties(properties) {
		var latlngs = [];

		for (var x = 0; x < properties.pos.length; x++) {
			latlngs.push([properties.pos[x].lat, properties.pos[x].lon]);
		}

		let line = L.polyline(
			latlngs,
			{
				color: properties.strokeColor,
				weight: properties.strokeWeight,
				opacity: properties.strokeOpacity
			}
		);

		// TODO: maybe bind via feature group
		if ( properties.hasOwnProperty('text') && properties.text.trim().length > 0 ) {
			line.bindPopup( properties.text );
		}

		return line;
	}

	function newPolygonFromProperties(properties) {
		let polygon = L.polygon(
			properties.pos.map(function(position) {
				return [position.lat, position.lon];
			}),
			{
				color: properties.strokeColor,
				weight:properties.strokeWeight,
				opacity:properties.strokeOpacity,
				fillColor:properties.fillColor,
				fillOpacity:properties.fillOpacity
			}
		);

		if( properties.hasOwnProperty('text') && properties.text.trim().length > 0 ) {
			polygon.bindPopup( properties.text );
		}

		return polygon;
	}

	function newCircleFromProperties(properties) {
		let circle = L.circle(
			[properties.centre.lat, properties.centre.lon],
			{
				radius: properties.radius,
				color: properties.strokeColor,
				weight:properties.strokeWeight,
				opacity:properties.strokeOpacity,
				fillColor:properties.fillColor,
				fillOpacity:properties.fillOpacity,
			}
		);

		if( properties.hasOwnProperty('text') && properties.text.trim().length > 0 ) {
			circle.bindPopup( properties.text );
		}

		return circle;
	}

	function newRectangleFromProperties(properties) {
		let rectangle = L.rectangle(
			[
				[properties.sw.lat, properties.sw.lon],
				[properties.ne.lat, properties.ne.lon]
			],
			{
				color: properties.strokeColor,
				weight: properties.strokeWeight,
				opacity: properties.strokeOpacity,
				fillColor: properties.fillColor,
				fillOpacity: properties.fillOpacity
			}
		);

		if( properties.hasOwnProperty('text') && properties.text.trim().length > 0 ) {
			rectangle.bindPopup( properties.text );
		}

		return rectangle;
	}

	/**
	 * Caution: mutates markerLayer
	 * @param {Object} options
	 * @param {L.LayerGroup} markerLayer
	 * @return {L.GeoJSON}
	 */
	function newGeoJsonLayer(options, markerLayer) {
		return L.geoJSON(
			options.geojson,
			{
				style: function (feature) {
					return maps.leaflet.GeoJson.simpleStyleToLeafletPathOptions(feature.properties);
				},
				pointToLayer: function(feature, latlng) {
					markerLayer.addLayer(
						createMarker(
							{
								lat: latlng.lat,
								lon: latlng.lng,
								title: feature.properties.title || '',
								text: maps.leaflet.GeoJson.popupContentFromProperties(feature.properties),
								icon: ''
							},
							options
						)
					);
				},
				onEachFeature: function (feature, layer) {
					if (feature.geometry.type !== 'Point') {
						let popupContent = maps.leaflet.GeoJson.popupContentFromProperties(feature.properties);
						if (popupContent !== '') {
							layer.bindPopup(popupContent);
						}
					}
				}
			}
		);
	}

	function getMarkersAndShapes(options) {
		let features = L.featureGroup();

		$.each(options.lines, function(index, properties) {
			features.addLayer(newLineFromProperties(properties));
		});

		$.each(options.polygons, function(index, properties) {
			features.addLayer(newPolygonFromProperties(properties));
		});

		$.each(options.circles, function(index, properties) {
			features.addLayer(newCircleFromProperties(properties));
		});

		$.each(options.rectangles, function(index, properties) {
			features.addLayer(newRectangleFromProperties(properties));
		});

		let markers = options.cluster ? maps.leaflet.LeafletCluster.newLayer(options) : L.featureGroup();

		features.addLayer(markers);
		features.markerLayer = markers;

		$.each(options.locations, function(index, properties) {
			markers.addLayer(createMarker(properties, options));
		});

		if (options.geojson !== '') {
			features.addLayer(newGeoJsonLayer(options, markers));
		}

		return features
	}

	if (!window.maps) {window.maps = {};}
	if (!window.maps.leaflet) {window.maps.leaflet = {};}

	window.maps.leaflet.FeatureBuilder = {
		contentLayerFromOptions: getMarkersAndShapes,
		createMarker: createMarker
	};
})(window.jQuery, window.mediaWiki);

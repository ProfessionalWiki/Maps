(function( $, mw ) {

	function initializeMessages() {
		let toolbar = L.drawLocal.draw.toolbar;

		toolbar.buttons.marker = 'Place a marker';
		toolbar.buttons.polyline = 'Draw a line';
		toolbar.buttons.polygon = 'Draw a polygon';
		toolbar.buttons.rectangle = 'Place a rectangle';
		toolbar.buttons.circle = 'Place a circle';

		toolbar.handlers.marker.tooltip.start = 'Click map to place marker.';
		toolbar.handlers.polyline.tooltip.start = 'Click map to draw line.';
		toolbar.handlers.polygon.tooltip.start = 'Click map to draw polygon.';
		toolbar.handlers.rectangle.tooltip.start = 'Click map to place rectangle.';
		toolbar.handlers.circle.tooltip.start = 'Click map to place circle.';
	}

	function addTitleLayer(map) {
		L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
		}).addTo(map);
	}

	function addDrawControl(map, geoJsonLayer) {
		map.addControl(new L.Control.Draw({
			edit: {
				featureGroup: geoJsonLayer,
				poly: {
					allowIntersection: false
				}
			},
			draw: {
				polygon: {
					allowIntersection: false,
					showArea: true
				},
				circlemarker: false
			}
		}));
	}

	function addNewLayersToJsonLayer(map, geoJsonLayer) {
		map.on(L.Draw.Event.CREATED, function (event) {
			var layer = event.layer;

			geoJsonLayer.addLayer(layer);
		});
	}

	$( document ).ready( function() {
		let map = L.map('GeoJsonMap');

		addTitleLayer(map);

		const geoJsonLayer = L.geoJSON(window.GeoJson).addTo(map);
		map.fitBounds(geoJsonLayer.getBounds());

		addDrawControl(map, geoJsonLayer);
		addNewLayersToJsonLayer(map, geoJsonLayer);

		initializeMessages();
	} );

})( window.jQuery, mediaWiki );

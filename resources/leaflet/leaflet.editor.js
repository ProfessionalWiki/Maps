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

	function userCanEdit() {
		return true;
	}

	let MapEditor = function(mapId, json) {
		let self = {};

		self.initialize = function() {
			self.map = L.map(mapId);

			self.geoJsonLayer = L.geoJSON(json).addTo(self.map);

			self.addTitleLayer();
			self.fitBounds();

			if (userCanEdit()) {
				self.addDrawControl();
				self.addNewLayersToJsonLayer();
			}
		};

		self.addTitleLayer = function() {
			L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
				attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
			}).addTo(self.map);
		};

		self.fitBounds = function() {
			self.map.fitBounds(self.geoJsonLayer.getBounds());
		};

		self.addDrawControl = function() {
			self.map.addControl(new L.Control.Draw({
				edit: {
					featureGroup: self.geoJsonLayer,
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
		};

		self.addNewLayersToJsonLayer = function() {
			self.map.on(L.Draw.Event.CREATED, function (event) {
				var layer = event.layer;

				self.geoJsonLayer.addLayer(layer);
			});
		};

		return self;
	};

	$( document ).ready( function() {
		let editor = MapEditor('GeoJsonMap', window.GeoJson);
		editor.initialize();

		initializeMessages();
	} );

})( window.jQuery, mediaWiki );

(function( $, mw ) {

	$( document ).ready( function() {
		var map = L.map('GeoJsonMap');

		L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
		}).addTo(map);

		var geoJsonLayer = L.geoJSON(GeoJson).addTo(map);

		map.fitBounds(geoJsonLayer.getBounds());

		var drawnItems = L.featureGroup().addTo(map);

		map.addControl(new L.Control.Draw({
			edit: {
				featureGroup: drawnItems,
				poly: {
					allowIntersection: false
				}
			},
			draw: {
				polygon: {
					allowIntersection: false,
					showArea: true
				}
			}
		}));

		map.on(L.Draw.Event.CREATED, function (event) {
			var layer = event.layer;

			drawnItems.addLayer(layer);
		});
	} );

})( window.jQuery, mediaWiki );

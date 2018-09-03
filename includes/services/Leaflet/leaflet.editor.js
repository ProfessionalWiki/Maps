(function( $, mw ) {

	$( document ).ready( function() {
		var map = L.map('GeoJsonMap'/*, {editable: true}*/);

		L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
		}).addTo(map);

		var geoJsonLayer = L.geoJSON(GeoJson).addTo(map);

		map.fitBounds(geoJsonLayer.getBounds());
	} );

})( window.jQuery, mediaWiki );

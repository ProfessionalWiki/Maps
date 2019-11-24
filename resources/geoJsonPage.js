(function( $, mw, maps ) {

	$( document ).ready( function() {
		let editor = maps.leaflet.LeafletEditor(
			'GeoJsonMap',
			window.GeoJson,
			new maps.MapSaver()
		);

		editor.initialize();
	} );

})( window.jQuery, window.mediaWiki, window.maps );

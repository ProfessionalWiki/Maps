
mediaWiki.loader.using( [ 'ext.maps.leaflet' ] ).done( function () {
	( new maps.services( jQuery( document ) ) ).leaflet();
} );
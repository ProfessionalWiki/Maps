window.mapsLeafletList = [];

(function( $ ) {
	$( document ).ready( function() {
		$( '.maps-leaflet' ).each( function() {
			let $this = $( this );

			let jqueryMap = $this.leafletmaps(
				JSON.parse( $this.find( 'div.mapdata' ).text() )
			);

			window.mapsLeafletList.push(jqueryMap);
		} );
	} );
})( window.jQuery );

window.mapsLeafletList = [];

(function( $, mw ) {
	mw.hook( 'wikipage.content' ).add( function ( $content ) {
		$content.find( '.maps-leaflet' ).each( function() {
			let $this = $( this );

			let jqueryMap = $this.leafletmaps(
				JSON.parse( $this.find( 'div.mapdata' ).text() )
			);

			jqueryMap.setup();

			window.mapsLeafletList.push(jqueryMap);
		} );
	} );
})( window.jQuery, window.mediaWiki );

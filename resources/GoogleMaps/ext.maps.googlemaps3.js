window.mapsGoogleList = [];

(function( $, mw ) {
	mw.hook( 'wikipage.content' ).add( function ( $content ) {
		if( typeof google === 'undefined' ) {
			$content.find( '.maps-googlemaps3' ).text( mw.msg( 'maps-googlemaps3-incompatbrowser' ) );
		} else {
			$content.find( '.maps-googlemaps3' ).each( function() {
				var $this = $( this );
				window.mapsGoogleList.push(
					$this.googlemaps( JSON.parse( $this.find( 'div.mapdata' ).text() ) )
				);
			} );
		}
	} );
})( window.jQuery, window.mediaWiki );

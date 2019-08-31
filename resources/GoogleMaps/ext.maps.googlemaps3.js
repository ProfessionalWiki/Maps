window.mapsGoogleList = [];

(function( $, mw ) {
	$( document ).ready( function() {
		if( typeof google === 'undefined' ) {
			$( '.maps-googlemaps3' ).text( mw.msg( 'maps-googlemaps3-incompatbrowser' ) );
		} else {
			$( '.maps-googlemaps3' ).each( function() {
				var $this = $( this );
				window.mapsGoogleList.push(
					$this.googlemaps( $.parseJSON( $this.find( 'div' ).text() ) )
				);
			} );
		}
	} );
})( window.jQuery, mediaWiki );

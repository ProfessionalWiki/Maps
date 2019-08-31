window.mapsLeafletList = [];

(function( $ ) {
	$( document ).ready( function() {
		$( '.maps-leaflet' ).each( function() {
			var $this = $( this );
			window.mapsLeafletList.push(
				$this.leafletmaps( $.parseJSON( $this.find( 'div' ).text() ) )
			);
		} );
	} );
})( window.jQuery );

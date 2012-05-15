/**
 * JavaScript for Google Maps v3 maps in the Maps extension.
 * @see https://www.mediawiki.org/wiki/Extension:Maps
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw <jeroendedauw at gmail dot com>
 */
(function( $ ) {

	$( document ).ready( function() {

		if ( typeof google === 'undefined' ) {
			alert( mediaWiki.msg( 'maps-load-failed' ) + ' ' + mediaWiki.msg( 'maps-googlemaps3-incompatbrowser' ) );
		}
		else {
			$( '.maps-googlemaps3' ).each( function() {
				var $this = $( this );
				$this.googlemaps( jQuery.parseJSON( $this.find( 'div').text() ) );
			} );
		}

	} );

})( window.jQuery );
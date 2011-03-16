/**
 * JavasSript for Google Maps v3 maps in the Maps extension.
 * @see http://www.mediawiki.org/wiki/Extension:Maps
 * 
 * @author Jeroen De Dauw <jeroendedauw at gmail dot com>
 */

jQuery(document).ready(function() {
	if ( true ) {
		for ( i in window.maps.googlemaps3 ) {
			jQuery( '#' + i ).googlemaps( window.maps.googlemaps3[i] );
		}
	}
	else {
		alert( mediaWiki.msg( 'maps-googlemaps3-incompatbrowser' ) );
		
		for ( i in window.maps.googlemaps3 ) {
			jQuery( '#' + i ).text( mediaWiki.msg( 'maps-load-failed' ) );
		}
	}	
});

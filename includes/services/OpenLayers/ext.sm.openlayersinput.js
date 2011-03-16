/**
 * JavasSript for the Google Maps v3 form input in the Semantic Maps extension.
 * @see http://www.mediawiki.org/wiki/Extension:Semantic_Maps
 * 
 * @since 0.8
 * @ingroup SemanticMaps
 * 
 * @licence GNU GPL v3
 * @author Jeroen De Dauw <jeroendedauw at gmail dot com>
 */

jQuery(document).ready(function() {
	if ( true ) {
		for ( i in window.maps.openlayers_forminputs ) {
			jQuery( '#' + i + '_forminput' ).openlayersinput( i, window.maps.openlayers_forminputs[i] );
		}
	}
	else {
		alert( mediaWiki.msg( 'maps-openlayers-incompatbrowser' ) );
		
		for ( i in window.maps.openlayers_forminputs ) {
			jQuery( '#' + i + '_forminput' ).text( mediaWiki.msg( 'maps-load-failed' ) );
		}
	}	
});

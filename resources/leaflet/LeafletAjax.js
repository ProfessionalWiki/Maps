/**
 * Ajax query support for Maps + SMW
 * @see https://www.mediawiki.org/wiki/Extension:Maps
 *
 * @licence GNU GPL v2+
 * @author Peter Grassberger < petertheone@gmail.com >
 */
(function( $, sm ) {
	var ajaxRequest = null;

	$( document ).ready( function() {
		// todo: find a way to remove setTimeout.
		setTimeout( function() {
			$( window.mapsLeafletList ).each( function( index, jqueryMap ) {
				if( !jqueryMap.options.ajaxquery || !jqueryMap.options.ajaxcoordproperty ) {
					return;
				}
				jqueryMap.map.on( 'dragend zoomend', function() {
					var bounds = jqueryMap.map.getBounds();
					var query = sm.buildQueryString(
						decodeURIComponent( jqueryMap.options.ajaxquery.replace( /\+/g, ' ' ) ),
						jqueryMap.options.ajaxcoordproperty,
						bounds.getNorthEast().lat,
						bounds.getNorthEast().lng,
						bounds.getSouthWest().lat,
						bounds.getSouthWest().lng
					);

					if( ajaxRequest !== null ) {
						ajaxRequest.abort();
					}
					ajaxRequest = sm.ajaxUpdateMarker( jqueryMap, query, jqueryMap.options.icon ).done( function() {
						jqueryMap.createMarkerCluster();
						ajaxRequest = null;
					} );
				} );
			} );
		}, 1000 );
	} );
})( window.jQuery, window.sm );

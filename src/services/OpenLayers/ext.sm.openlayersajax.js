/**
 * JavaScript for OpenLayers maps in the Semantic Maps extension.
 * @see https://www.mediawiki.org/wiki/Extension:Semantic_Maps
 *
 * @licence GNU GPL v2+
 * @author Peter Grassberger < petertheone@gmail.com >
 */


(function( $, mw ) {
    var ajaxRequest = null;

    function getQueryString( map, ajaxcoordproperty ) {
        var bounds = map.map.getExtent().transform(map.map.projection, map.map.displayProjection);

        var query = map.options.ajaxquery.join( ' ' ) + ' ';
        query += '[[' + ajaxcoordproperty + '::+]] ';
        query += '[[' + ajaxcoordproperty + '::>' + bounds.bottom + '°, ' + bounds.left + '°]] ';
        query += '[[' + ajaxcoordproperty + '::<' + bounds.top + '°, ' + bounds.right + '°]]';
        query += '|?' + ajaxcoordproperty;
        return query;
    }

    function ajaxUpdateMarker( map ) {
        var ajaxcoordproperty = map.options.ajaxcoordproperty;
        var query = getQueryString( map, ajaxcoordproperty );

        if ( ajaxRequest !== null ) {
            ajaxRequest.abort();
        }

        ajaxRequest = $.ajax( {
            method: 'GET',
            url: '/w/api.php?',
            data: {
                'action': 'ask',
                'query': query,
                'format': 'json'
            },
            dataType: 'json'
        } ).done( function( data ) {
            ajaxRequest = null;

            // todo: don't remove and recreate all markers..
            // only add new ones.
            map.removeMarkers();
            for ( var property in data.query.results ) {
                if ( data.query.results.hasOwnProperty( property ) ) {
                    var location = data.query.results[property];
                    var coordinates = location.printouts[ajaxcoordproperty][0];
                    map.addMarker( coordinates );
                }
            }
        } );
    }

    $( document ).ready( function() {
        // todo: find a way to remove setTimeout.
        setTimeout(function() {
            $( window.maps.openlayersList ).each( function( index, map ) {
                if (!map.options.ajaxquery && !map.options.ajaxcoordproperty) {
                    return;
                }
                console.log();
                map.map.events.register( 'moveend', map.map, function () {
                    // todo: fix this
                    ajaxUpdateMarker( map );
                } );
            } );
        }, 500 );
    } );
})( window.jQuery, window.mediaWiki );

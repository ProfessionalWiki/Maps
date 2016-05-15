/**
 * JavaScript for Leaflet in the Semantic Maps extension.
 * @see https://www.mediawiki.org/wiki/Extension:Semantic_Maps
 *
 * @licence GNU GPL v2+
 * @author Peter Grassberger < petertheone@gmail.com >
 */


(function( $ ) {
    var ajaxRequest = null;

    // todo: maybe move to Maps
    function getQueryString( map, coordinatesproperty ) {
        var bounds = map.map.getBounds();

        var query = map.options.ajaxquery.join( ' ' ) + ' ';
        query += '[[' + coordinatesproperty + '::+]] ';
        query += '[[' + coordinatesproperty + '::>' + bounds.getSouthWest().lat + '°, ' + bounds.getSouthWest().lng + '°]] ';
        query += '[[' + coordinatesproperty + '::<' + bounds.getNorthEast().lat + '°, ' + bounds.getNorthEast().lng + '°]]';
        query += '|?' + coordinatesproperty;
        return query;
    }

    function ajaxUpdateMarker( map ) {
        var coordinatesproperty = map.options.coordinatesproperty;
        var query = getQueryString( map, coordinatesproperty );

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
            // todo: implement removeMarkers() in Maps
            //map.removeMarkers();
            for ( var property in data.query.results ) {
                if ( data.query.results.hasOwnProperty( property ) ) {
                    var location = data.query.results[property];
                    var coordinates = location.printouts[coordinatesproperty][0];
                    map.addMarker( coordinates );
                }
            }
        } );
    }

    var mapEvents = ['dragend', 'zoomend'];

    $( document ).ready( function() {
        // todo: find a way to remove setTimeout.
        setTimeout(function() {
            $( window.maps.leafletList ).each( function( index, map ) {
                if (!map.options.ajaxquery && !map.options.coordinatesproperty) {
                    return;
                }
                map.map.on( mapEvents.join( ' ' ), function() {
                    console.log('event!');
                    ajaxUpdateMarker( map );
                } );
            } );
        }, 500 );
    } );
})( window.jQuery );

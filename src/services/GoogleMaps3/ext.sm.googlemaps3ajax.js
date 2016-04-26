/**
 * JavaScript for Google Maps v3 maps in the Maps extension.
 * @see https://www.mediawiki.org/wiki/Extension:Maps
 *
 * @licence GNU GPL v2+
 * @author Peter Grassberger <petertheone@gmail.com>
 */

var ajaxRequest = null;

function ajaxUpdateMarker(map) {
    var bounds = map.map.getBounds();

    var coordinatesproperty = map.options.coordinatesproperty;

    var query = map.options.ajaxquery.join(' ') + ' ';
    query += '[[' + coordinatesproperty + '::+]] ';
    query += '[[' + coordinatesproperty + '::>' + bounds.getSouthWest().lat() + '°, ' + bounds.getSouthWest().lng() + '°]] ';
    query += '[[' + coordinatesproperty + '::<' + bounds.getNorthEast().lat() + '°, ' + bounds.getNorthEast().lng() + '°]]';
    query += '|?' + coordinatesproperty;

    if (ajaxRequest !== null) {
        ajaxRequest.abort();
    }

    ajaxRequest = $.ajax({
        method: 'GET',
        url: '/w/api.php?',
        data: {
            'action': 'ask',
            'query': query,
            'format': 'json'
        },
        dataType: 'json'
    }).done(function(data) {
        ajaxRequest = null;

        // todo: don't remove and recreate all markers..
        // only add new ones.
        map.removeMarkers();
        for (var property in data.query.results) {
            if (data.query.results.hasOwnProperty(property)) {
                var location = data.query.results[property];
                var coordinates = location.printouts[coordinatesproperty][0];
                map.addMarker(coordinates);
            }
        }
    });
}

(function( $, mw ) {
    $( document ).ready( function() {
        if ( typeof google !== 'undefined' ) {
            //todo: timeout?
            setTimeout(function() {
                $(window.maps.mapList).each( function(index, map) {
                    if (map.options.ajaxquery) {
                        ajaxUpdateMarker(map);

                        google.maps.event.addListener(map.map, 'dragend', function () {
                            ajaxUpdateMarker(map);
                        });
                        google.maps.event.addListener(map.map, 'zoom_changed', function () {
                            ajaxUpdateMarker(map);
                        });
                    }

                });
            }, 500);
        }
    } );
})( window.jQuery, mediaWiki );

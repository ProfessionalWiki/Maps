/**
 * JavaScript for Google Maps v3 maps in the Maps extension.
 * @see https://www.mediawiki.org/wiki/Extension:Maps
 *
 * @licence GNU GPL v2+
 * @author Peter Grassberger <petertheone@gmail.com>
 */
(function( $, mw ) {

    $( document ).ready( function() {

        console.log('test init');

        // todo: trigger fetch once on load.
        // todo: add 'zoom_changed'
        /*google.maps.event.addListener(map, 'dragend', function () {
            var bounds = map.getBounds();

            $.ajax({
                method: 'GET',
                url: '/w/api.php?',
                data: {
                    'action': 'coordinates',
                    'locations': '',
                    'bbSouth': bounds.getSouthWest().lat,
                    'bbWest': bounds.getSouthWest().lng,
                    'bbNorth': bounds.getNorthEast().lat,
                    'bbEast': bounds.getNorthEast().lng,
                    'format': 'json'
                },
                dataType: 'json'
            }).done(function(data) {
                // todo: don't add the same marker multiple times
                // maybe remove all old markers
                for (var i = data.results.locations.length - 1; i >= 0; i--) {
                    self.addMarker(data.results.locations[i]);
                }
            });
        });*/

    } );

})( window.jQuery, mediaWiki );

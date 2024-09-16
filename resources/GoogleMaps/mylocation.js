let markerElement;
let myLocationWatchId;
let myLocationMarker;

(function( $ ) {
	$( document ).ready( async function() {
		// Request needed libraries.
		const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");
		markerElement = AdvancedMarkerElement;

		// todo: find a way to remove setTimeout.
			if( typeof google === 'undefined' ) {
				return;
			}
			$( window.mapsGoogleList ).each( function( index, map ) {
				/*if( !map.options.ajaxquery || !map.options.ajaxcoordproperty ) {
					return;
				}*/

				console.log(map);

				// Control for toggling the live location function
				let myLocationToggleEl = document.querySelector( '#mylocation' );
				if ( myLocationToggleEl ) {
					addEventListener( 'change', (event) => {
						if ( event.target.checked ) {
							console.log('activateMyLocation', map);
							activateMyLocation( map.map );
						} else {
							deactivateMyLocation();
						}
					} );
				}
			} );
	} );
})( window.jQuery );

function handleLocationError( browserHasGeolocation, pos ) {
	console.log( browserHasGeolocation ?
		'Error: The Geolocation service failed.' :
		'Error: Your browser doesn\'t support geolocation.' );
}

function drawMyLocation( position, map ) {
	var pos = {
		lat: position.coords.latitude,
		lng: position.coords.longitude
	};

	// Center the map on the user's location
	console.log('center map', map);
	map.setCenter( pos );

	// Add a marker at the user's location
	const locationMarker = document.createElement( 'div' );
	locationMarker.className = 'my-location-marker';

	if ( ! myLocationMarker ) {
		// TODO: Radius based on accuracy
		myLocationMarker = new markerElement( {
			map,
			position: pos,
			content: locationMarker,
		} );
	} else {
		myLocationMarker.position = pos;
	} 
}

function activateMyLocation( map ) {
	// Check if geolocation is supported
	if ( navigator.geolocation ) {
		myLocationWatchId = navigator.geolocation.watchPosition(
			function( position ) {
				drawMyLocation( position, map )
			},
			// Error handling
			function() {
				handleLocationError( true, map.getCenter() );
			},
			// Geolocation options
			{
				enableHighAccuracy: false,
				timeout: 5000,
				maximumAge: 0,
			}
		);
	} else {
		// Browser doesn't support geolocation
		handleLocationError( false, map.getCenter() );
	}
}

function deactivateMyLocation() {
	// Check if geolocation is supported
	if ( navigator.geolocation ) {
		navigator.geolocation.clearWatch( myLocationWatchId );
	}

	// Remove marker from the map and remove
	myLocationMarker.setMap( null );
	myLocationMarker = null;
}

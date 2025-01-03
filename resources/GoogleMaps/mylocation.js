(function( $ ) {

/* Track user's location with marker on map */

function activateTrackMyLocation() {
	localStorage.setItem( 'mapsTrackMyLocation', 'yes' );
}

function getTrackMyLocation() {
	return localStorage.getItem( 'mapsTrackMyLocation' );
}

function isTrackMyLocationSet() {
	return getTrackMyLocation() && getTrackMyLocation() === 'yes';
}

function clearTrackMyLocation() {
	localStorage.removeItem( 'mapsTrackMyLocation' );
}

/* Follow: Center map on user's location */

function activateFollowMyLocation( mapDiv ) {
	mapDiv.data( 'followMyLocation', 'locked' );
}

function getFollowMyLocation( mapDiv ) {
	return mapDiv.data( 'followMyLocation' );
}

function isFollowMyLocationSet( mapDiv ) {
	return getFollowMyLocation( mapDiv ) && getFollowMyLocation( mapDiv ) === 'locked';
}

function clearFollowMyLocation( mapDiv ) {
	mapDiv.removeData( 'followMyLocation' );
}

function updateMapsTrackMyLocation( centerOnMyLocation = false ) {
	$( window.mapsGoogleList ).each( function( index, map ) {
		if ( ! map.options.mylocation ) {
			return;
		}

		let mapDiv = $( map.map.getDiv() );

		if( isTrackMyLocationSet() ) {
			mapDiv.data( 'myLocationIconUI' ).style.backgroundPosition = '-144px 0';
			activateMyLocation( map.map, centerOnMyLocation );
		} else {
			mapDiv.data( 'myLocationIconUI' ).style.backgroundPosition = '0 0';
			deactivateMyLocation( map.map );
		}
	} );
}

$( document ).ready( function() {
	// todo: find a way to remove setTimeout.
	setTimeout( function() {
		if( typeof google === 'undefined' ) {
			return;
		}

		updateMapsTrackMyLocation( false );
	}, 500 );
} );

// Control for toggling the user location function
function MyLocationControl( map, followMyLocation, zoom ) {
	var controlDiv = document.createElement('div');
	controlDiv.style.padding = '10px 10px 0px 10px';
	controlDiv.index = 1;

	var controlUI = document.createElement('div');
	controlUI.style.padding = '6px 6px';
	controlUI.style.backgroundColor = 'white';
	controlUI.style.borderStyle = 'solid';
	controlUI.style.borderColor = 'rgba(0, 0, 0, 0.14902)';
	controlUI.style.borderWidth = '1px';
	controlUI.style.borderRadius = '2px';
	controlUI.style.cursor = 'pointer';
	controlUI.style.textAlign = 'center';
	controlUI.style.boxShadow = 'rgba(0, 0, 0, 0.298039) 0px 1px 4px -1px';
	controlUI.style.backgroundClip = 'padding-box';
	controlUI.title = mw.msg('maps-mylocation-button-tooltip');
	controlDiv.appendChild(controlUI);

	var controlText = document.createElement('div');
	controlText.style.backgroundPosition = '0 0';
	controlText.style.backgroundImage = 'url(' + mw.config.get( 'egMapsScriptPath' ) + '/resources/GoogleMaps/img/mylocation-sprite-2x.png)';
	controlText.style.backgroundSize = '180px 18px';
	controlText.style.display = 'block';
	controlText.style.height = '18px';
	controlText.style.left = '6px';
	controlText.style.margin = '0';
	controlText.style.padding = '0';
	controlText.style.width = '18px';
	controlUI.appendChild(controlText);

	let mapDiv = $( map.getDiv() );

	// Store for later access
	mapDiv.data( 'myLocationIconUI', controlText );
	mapDiv.data( 'myLocationZoom', zoom === -1 ? false : zoom );

	// Handle toggle button click
	google.maps.event.addDomListener( controlUI, 'click', function() {
		if ( isTrackMyLocationSet() ) {
			clearTrackMyLocation();
		} else {
			if ( followMyLocation ) {
				activateFollowMyLocation( mapDiv );
			}

			activateTrackMyLocation();
		}

		updateMapsTrackMyLocation( true );
	} );

	// Handle dragged map
	google.maps.event.addDomListener( map, 'dragend', function() {
		// Stop centering on user's location
		if ( isFollowMyLocationSet( mapDiv ) ) {
			clearFollowMyLocation( mapDiv );
		}
	} );

	return controlDiv;
}
window.MyLocationControl = MyLocationControl;

function handleLocationError( browserHasGeolocation, pos ) {
	console.log( browserHasGeolocation ?
		'Error: The Geolocation service failed.' :
		'Error: Your browser doesn\'t support geolocation.' );
}

function drawMyLocation( position, map ) {
	let pos = {
		lat: position.coords.latitude,
		lng: position.coords.longitude
	};
	let radius = position.coords.accuracy * 0.5;

	let mapDiv = $( map.getDiv() );

	if ( typeof mapDiv.data( 'myLocationMarker' ) === 'undefined' ) {
		// Add a circle to visualize geolocation accuracy
		let myLocationCircle = new google.maps.Circle( {
			strokeWeight: 0,
			fillColor: "#5383EC",
			fillOpacity: 0.2,
			map,
			center: pos,
			radius: radius,
		} );

		// Add a marker at the user's location
		const svgMarker = {
			path: "M 11, 11 m 10, 0 a 10,10 0 1,0 -20,0 a 10,10 0 1,0 20,0",
			fillColor: "#5383EC",
			fillOpacity: 1,
			strokeWeight: 2,
			strokeColor: "white",
			anchor: new google.maps.Point( 11, 11 ),
			scale: 0.75,
		};

		let myLocationMarker = new google.maps.Marker( {
			position: pos,
			icon: svgMarker,
			map: map,
		} );

		// Store for later access
		mapDiv.data( 'myLocationMarker', myLocationMarker );
		mapDiv.data( 'myLocationCircle', myLocationCircle );
	} else {
		// Update position and radius
		mapDiv.data( 'myLocationMarker' ).setPosition( pos );
		mapDiv.data( 'myLocationCircle' ).setCenter( pos );
		mapDiv.data( 'myLocationCircle' ).setRadius( radius );
	}

	if ( isFollowMyLocationSet( mapDiv ) ) {
		// Center the map on the user's location
		map.setCenter( pos );
	}
}

function activateMyLocation( map, centerOnMyLocation ) {
	let mapDiv = $( map.getDiv() );

	let geolocationOptions = {
		enableHighAccuracy: true,
		timeout: 5000,
		maximumAge: 0,
	};

	// Check if geolocation is supported
	if ( navigator.geolocation ) {
		if ( centerOnMyLocation ) {
			// Center map only once
			navigator.geolocation.getCurrentPosition(
				function( position ) {
					let pos = {
						lat: position.coords.latitude,
						lng: position.coords.longitude
					};
					map.setCenter( pos );

					// Zoom into user's location
					if ( mapDiv.data( 'myLocationZoom' ) !== false ) {
						map.setZoom( mapDiv.data( 'myLocationZoom' ) );
					}
				}, 
				// Error handling
				function() {
					handleLocationError( true, map.getCenter() );
				},
				geolocationOptions
			);
		}

		// Continously track user's location
		let myLocationWatchId = navigator.geolocation.watchPosition(
			function( position ) {
				drawMyLocation( position, map );
			},
			// Error handling
			function() {
				handleLocationError( true, map.getCenter() );
			},
			geolocationOptions
		);
		mapDiv.data( 'myLocationWatchId', myLocationWatchId );
	} else {
		// Browser doesn't support geolocation
		handleLocationError( false, map.getCenter() );
	}
}

function deactivateMyLocation( map ) {
	let mapDiv = $( map.getDiv() );

	// Check if geolocation is supported
	if ( navigator.geolocation ) {
		// Stop tracking location
		navigator.geolocation.clearWatch( mapDiv.data( 'myLocationWatchId' ) );
		mapDiv.removeData( 'myLocationWatchId' );
	}

	// Remove marker from the map
	if ( typeof mapDiv.data( 'myLocationMarker' ) !== 'undefined' ) {
		mapDiv.data( 'myLocationMarker' ).setMap( null );
		mapDiv.removeData( 'myLocationMarker' );
	}

	// Remove circle from the map
	if ( typeof mapDiv.data( 'myLocationCircle' ) !== 'undefined' ) {
		mapDiv.data( 'myLocationCircle' ).setMap( null );
		mapDiv.removeData( 'myLocationCircle' );
	}
}

})( window.jQuery );

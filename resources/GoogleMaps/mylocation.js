// Control for toggling the user location function
function MyLocationControl( map ) {
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

	google.maps.event.addDomListener( controlUI, 'click', function() {
		let mapDiv = $( map.getDiv() );

		if ( mapDiv.data( 'followMyLocation' ) != null ) {
			mapDiv.removeData( 'followMyLocation' );
			controlText.style.backgroundPosition = '0 0';
			deactivateMyLocation( map );
		} else {
			mapDiv.data( 'followMyLocation', 'on' );
			controlText.style.backgroundPosition = '-144px 0';
			activateMyLocation( map );
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
		// Add a circle at the user's location
		let myLocationMarker = new google.maps.Circle( {
			strokeColor: "#0000FF",
			strokeOpacity: 0.5,
			strokeWeight: 2,
			fillColor: "#0000FF",
			fillOpacity: 0.35,
			map,
			center: pos,
			radius: radius,
		} );

		// Center the map on the user's location
		map.setCenter( pos );

		mapDiv.data( 'myLocationMarker', myLocationMarker );
	} else {
		// Update circle position and radius
		mapDiv.data( 'myLocationMarker' ).setCenter( pos );
		mapDiv.data( 'myLocationMarker' ).setRadius( radius );
	}
}

function activateMyLocation( map ) {
	mapDiv = $( map.getDiv() );

	// Check if geolocation is supported
	if ( navigator.geolocation ) {
		let myLocationWatchId = navigator.geolocation.watchPosition(
			function( position ) {
				drawMyLocation( position, map );
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
		mapDiv.data( 'myLocationWatchId', myLocationWatchId );
	} else {
		// Browser doesn't support geolocation
		handleLocationError( false, map.getCenter() );
	}
}

function deactivateMyLocation( map ) {
	mapDiv = $( map.getDiv() );

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
}

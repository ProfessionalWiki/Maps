 /**
  * Javascript functions for Google Maps v3 functionallity in Maps.
  *
  * @file GoogleMap3Functions.js
  * @ingroup MapsGoogleMaps3
  *
  * @author Jeroen De Dauw
  */

/**
 * Created a new Map object with the provided properties and markers.
 */
function initGMap3(name, options, markerData) {
	options.center = new google.maps.LatLng(options.lat, options.lon);
	
	var map = new google.maps.Map(document.getElementById(name), options);
	
	// TODO: types - http://code.google.com/apis/maps/documentation/v3/reference.html#MapTypeRegistry
	
	// TODO: markers
}

function getGMaps3MarkerData(lat, lon, title, label, icon) {
	return {position: new google.maps.LatLng(lat, lon), title: title, label: label, icon: icon};
}
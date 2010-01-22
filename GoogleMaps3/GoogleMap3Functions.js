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
function initGMap3(name, options, markers) {
	options.center = new google.maps.LatLng(options.lat, options.lon);
	
	var map = new google.maps.Map(document.getElementById(name), options);
	
	map.mapTypes = options.types;
	map.setMapTypeId(options.type);
}
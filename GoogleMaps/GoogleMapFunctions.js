 /**
  * Javascript functions for Google Maps functionallity in Maps and it's extensions
  *
  * @file GoogleMapFunctions.js
  * @ingroup Maps
  *
  * @author Robert Buzink
  * @author Yaron Koren   
  * @author Jeroen De Dauw
  */


/**
 * Returns GMarker object on the provided location.
 * It will show a popup baloon with title and label when clicked, if either of these is set.
 */
function createGMarker(point, title, label, icon) {
	var marker;
	
	if (icon != '') {
		var iconObj = new GIcon(G_DEFAULT_ICON);
		iconObj.image = icon;
		marker = new GMarker(point, {icon:iconObj});
	} else {
		marker = new GMarker(point);
	}
	
	if ((title + label).length > 0) {
		var bothTxtAreSet = title.length > 0 && label.length > 0;
		var popupText = bothTxtAreSet ? '<b>' + title + '</b><hr />' + label : title + label;	

		GEvent.addListener(marker, 'click',
			function() {
				marker.openInfoWindowHtml(popupText, {maxWidth:350});
			}
		);		
	}

	return marker;
}

/**
 * Returns GMap2 object with the provided properties.
 */
function createGoogleMap(mapElement, size, center, zoom, type, control, scrollWheelZoom, earthEnabled) {
	var map = new GMap2(mapElement, {size: size});
	
	if (earthEnabled) map.addMapType(G_SATELLITE_3D_MAP);
	
	map.setCenter(center, zoom, type);
	map.addControl(new GMapTypeControl());
	
	if (typeof(control) != 'undefined') map.addControl(control);
	if (scrollWheelZoom) map.enableScrollWheelZoom();
	
	return map;
}

/**
 * This function holds spesific functionallity for the Google Maps form input of Semantic Maps
 * TODO: Refactor as much code as possible to non specific functions
 * TODO: Centralize geocoding functionallity, and use that code instead of local GG
 */
function makeFormInputGoogleMap(mapName, locationFieldName, width, height, lat, lon, zoom, marker_lat, marker_lon, type, control, scrollWheelZoom, earthEnabled) {
	if (GBrowserIsCompatible()) { // TODO: This function should probably be used after the loading of the G Maps API
		var map = createGoogleMap(document.getElementById(mapName), new GSize(width, height), new GLatLng(lat, lon), zoom, type, control, scrollWheelZoom, earthEnabled);

		// Show a starting marker only if marker coordinates are provided
		if (marker_lat != null && marker_lon != null) {
			map.addOverlay(new GMarker(new GLatLng(marker_lat, marker_lon)));
		}
		
		// Click event handler for updating the location of the marker
		GEvent.addListener(map, "click",
			function(overlay, point) {
				if (overlay) {
					map.removeOverlay (overlay);
				} else {
					map.clearOverlays();
					document.getElementById(locationFieldName).value = convertLatToDMS(point.y)+', '+convertLngToDMS(point.x);
					map.addOverlay(new GMarker(point));
					map.panTo(point);
				}
			}
		);
		
		// Make the map variable available for other functions
		if (!window.GMaps) window.GMaps = new Object;
		eval("window.GMaps." + mapName + " = map;"); 
	}
}

/**
 * This function holds spesific functionallity for the Google Maps form input of Semantic Maps
 * TODO: Refactor as much code as possible to non specific functions
 */
function showGAddress(address, mapName, outputElementName, notFoundFormat) {
	var map = GMaps[mapName];
	var geocoder = new GClientGeocoder();

	geocoder.getLatLng(address,
		function(point) {
			if (!point) {
				window.alert(address + ' ' + notFoundFormat);
			} else {
				map.clearOverlays();
				map.setCenter(point, 14);
				var marker = new GMarker(point);
				map.addOverlay(marker);
				document.getElementById(outputElementName).value = convertLatToDMS(point.y) + ', ' + convertLngToDMS(point.x);
			}
		}
	);

}
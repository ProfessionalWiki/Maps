 /**
  * Javascript functions for Yahoo! Maps functionallity in Maps and it's extensions
  *
  * @file YahooMapFunctions.js
  * @ingroup Maps
  *
  * @author Jeroen De Dauw
  */


/**
 * Returns YMarker object on the provided location.
 * It will show a popup baloon with title and label when clicked, if either of these is set.
 */
function createYMarker(geoPoint, title, label){
	var newMarker= new YMarker(geoPoint);
	
	if ((title + label).length > 0) {
		var bothTxtAreSet = title.length > 0 && label.length > 0;
		var markerMarkup = bothTxtAreSet ? '<b>' + title + '</b><hr />' + label : title + label;
		YEvent.Capture(newMarker, EventsList.MouseClick, 
			function(){
				newMarker.openSmartWindow(markerMarkup);
			}
		);
	}

	return newMarker;
}

/**
 * Returns YMap object with the provided properties.
 */
function createYahooMap(mapElement, center, zoom, type, controls, scrollWheelZoom) {
	var map = new YMap(mapElement); 
	
	map.addTypeControl();
	map.setMapType(type);
	
	for (i in controls){
		switch (controls[i]) {
			case 'pan' :
				map.addPanControl();
				break;
			case 'zoom' : 
				map.addZoomLong();
				break;
		}
	}
	
	if (!scrollWheelZoom) map.disableKeyControls();
	
	map.drawZoomAndCenter(center, zoom);
	
	return map;
}

/**
 * This function holds spesific functionallity for the Yahoo! Maps form input of Semantic Maps
 * TODO: Refactor as much code as possible to non specific functions
 */
function makeFormInputYahooMap(mapName, locationFieldName, lat, lon, zoom, marker_lat, marker_lon, type, controls, scrollWheelZoom) {
	if (GBrowserIsCompatible()) {
		var map = createYahooMap(document.getElementById(mapName), new YGeoPoint(lat, lon), zoom, type, controls, scrollWheelZoom);

		// Show a starting marker only if marker coordinates are provided
		if (marker_lat != null && marker_lon != null) {
			map.addOverlay(createYMarker(new YGeoPoint(marker_lat, marker_lon)));
		}
		
		// Click event handler for updating the location of the marker
			YEvent.Capture(map, EventsList.MouseClick,
			function(_e, point) {
				var loc = new YGeoPoint(point.Lat, point.Lon)
				map.removeMarkersAll();
				document.getElementById(locationFieldName).value = convertLatToDMS(point.Lat)+', '+convertLngToDMS(point.Lon);
				map.addMarker(loc);
				map.panToLatLon(loc);
			}
		);
		
		// Make the map variable available for other functions
		if (!window.YMaps) window.YMaps = new Object;
		eval("window.YMaps." + mapName + " = map;"); 
	}
}

/**
 * This function holds spesific functionallity for the Yahoo! Maps form input of Semantic Maps
 * TODO: Refactor as much code as possible to non specific functions
 * TODO: Centralize geocoding functionallity, and use that code instead of local GG
 */
function showYAddress(address, mapName, outputElementName, notFoundFormat) {
	var map = YMaps[mapName];
	var geocoder = new GClientGeocoder();

	geocoder.getLatLng(address,
		function(point) {
			if (!point) {
				window.alert(address + ' ' + notFoundFormat);
			} else {
				var ypoint = new YGeoPoint(point.y, point.x)
				map.removeMarkersAll();
				map.drawZoomAndCenter(ypoint, 14);
				
				var marker = new YMarker(ypoint);
				map.addOverlay(marker);
				document.getElementById(outputElementName).value = convertLatToDMS(point.y) + ', ' + convertLngToDMS(point.x);
			}
		}
	);

}
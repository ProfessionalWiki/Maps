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
 * Returns YMap object with the provided properties and markers.
 */
function initializeYahooMap(mapName, lat, lon, zoom, type, types, controls, scrollWheelZoom, markers) {
	 var centre = (lon != null && lat != null) ? new YGeoPoint(lat, lon) : null;
	 return createYahooMap(document.getElementById(mapName), centre, zoom, type, types, controls, scrollWheelZoom, markers);
}

/**
 * Returns YMap object with the provided properties.
 */
function createYahooMap(mapElement, centre, zoom, type, types controls, scrollWheelZoom, markers) {
	var typesContainType = false;

	for (var i = 0; i < types.length; i++) {
		if (types[i] == type) typesContainType = true;
	}
	
	if (! typesContainType) types.push(type);	 
	 
	var map = new YMap(mapElement, type); 
	
	for (i in controls){
		switch (controls[i]) {
			case 'type' :
				map.addTypeControl(types);
				break;		
			case 'pan' :
				map.addPanControl();
				break;
			case 'zoom' : 
				map.addZoomLong();
				break;				
			case 'short' : 
				map.addZoomShort();				
				break;				
		}
	}
	
	map.setMapType();
	
	if (!scrollWheelZoom) map.disableKeyControls();
	
	var map_locations = ((zoom == null || centre == null) && markers.length > 1) ? Array() : null;
	
	for (i in markers) {
		var marker = markers[i];
		map.addOverlay(createYMarker(marker.point, marker.title, marker.label));
		if (map_locations != null) map_locations.push(marker.point);
	}

	if (map_locations != null) {
		var centerAndZoom = map.getBestZoomAndCenter(map_locations);
		map.drawZoomAndCenter(centerAndZoom.YGeoPoint, centerAndZoom.zoomLevel);
	}
	
	if (zoom != null) map.setZoomLevel(zoom);
	
	// FIXME: the code after this line REFUSES to be executed
	// This is probably caused by the YGeoPoint
	// Notice that the map object will therefore NOT BE RETURNED!
	if (centre != null) map.drawZoomAndCenter(centre);
	
	return map;
}

/**
 * This function holds spesific functionallity for the Yahoo! Maps form input of Semantic Maps
 * TODO: Refactor as much code as possible to non specific functions
 */
function makeFormInputYahooMap(mapName, locationFieldName, lat, lon, zoom, marker_lat, marker_lon, type, controls, scrollWheelZoom) {
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

/**
 * This function holds spesific functionallity for the Yahoo! Maps form input of Semantic Maps
 * TODO: Refactor as much code as possible to non specific functions
 */
function showYAddress(address, mapName, outputElementName, notFoundFormat) {
	var map = YMaps[mapName];
	
	map.removeMarkersAll();
	map.drawZoomAndCenter(address);
	
	YEvent.Capture(map, EventsList.onEndGeoCode,
		function(resultObj) {
			map.addOverlay(new YMarker(resultObj.GeoPoint));
			document.getElementById(outputElementName).value = convertLatToDMS(resultObj.GeoPoint.Lat) + ', ' + convertLngToDMS(resultObj.GeoPoint.Lon);				
		}
	);
}
 
function getYMarkerData(lat, lon, title, label, icon) {
		return {point: new YGeoPoint(lat, lon), title: title, label: label, icon: icon};
	}
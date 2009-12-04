 /**
  * Javascript functions for OpenStreetMap functionallity in Semantic Maps
  *
  * @file SM_OSMFunctions.js
  * @ingroup SMOSM
  * 
  * @author Jeroen De Dauw
  */

/**
 * This function holds spesific functionallity for the OpenStreetMap form input of Semantic Maps
 * TODO: Refactor as much code as possible to non specific functions
 */
function makeOSMFormInput(mapName, locationFieldName, lat, lon, zoom, marker_lat, marker_lon, layers, controls, height) {
	var markers = Array();

	// Show a starting marker only if marker coordinates are provided
	if (marker_lat != null && marker_lon != null) {
		markers.push(getOSMMarkerData(marker_lon, marker_lat, '', ''));
	}
	
	// Click event handler for updating the location of the marker
	// TODO / FIXME: This will probably cause problems when used for multiple maps on one page.
     OpenLayers.Control.Click = OpenLayers.Class(OpenLayers.Control, {                
         defaultHandlerOptions: {
             'single': true,
             'double': false,
             'pixelTolerance': 0,
             'stopSingle': false,
             'stopDouble': false
         },

         initialize: function(options) {
             this.handlerOptions = OpenLayers.Util.extend(
                 {}, this.defaultHandlerOptions
             );
             OpenLayers.Control.prototype.initialize.apply(
                 this, arguments
             ); 
             this.handler = new OpenLayers.Handler.Click(
                 this, {
                     'click': this.trigger
                 }, this.handlerOptions
             );
         }, 

         trigger: function(e) {
             replaceMarker(mapName, map.getLonLatFromViewPortPx(e.xy));
             document.getElementById(locationFieldName).value = convertLatToDMS(map.getLonLatFromViewPortPx(e.xy).lat)+', '+convertLngToDMS(map.getLonLatFromViewPortPx(e.xy).lon);
         }

     });
     
	var clickHanler = new OpenLayers.Control.Click();
     controls.push(clickHanler);
     
     var map = initOpenLayer(mapName, lon, lat, zoom, layers, controls, markers, height);
	
	// Make the map variable available for other functions
	if (!window.OSMMaps) window.OSMMaps = new Object;
	eval("window.OSMMaps." + mapName + " = map;"); 
}


/**
 * This function holds spesific functionallity for the OpenStreetMap form input of Semantic Maps
 * TODO: Refactor as much code as possible to non specific functions
 */
function showOSMAddress(address, mapName, outputElementName, notFoundFormat) {

	var map = OSMMaps[mapName];
	var geocoder = new GClientGeocoder();

	geocoder.getLatLng(address,
		function(point) {
			if (!point) {
				window.alert(address + ' ' + notFoundFormat);
			} else {
				var loc = new OpenLayers.LonLat(point.x, point.y);
				
				replaceMarker(mapName, loc);
				document.getElementById(outputElementName).value = convertLatToDMS(point.y) + ', ' + convertLngToDMS(point.x);
			}
		}
	);

}
 
/**
 * Remove all markers from an OSM map (that's in window.OSMMaps), and place a new one.
 * 
 * @param mapName Name of the map as in OSMMaps[mapName].
 * @param newLocation The location for the new marker.
 * @return
 */
function replaceMarker(mapName, newLocation) {
	var map = OSMMaps[mapName];
	var markerLayer = map.getLayer('markerLayer');
	
	removeOSMMarkers(markerLayer);
	markerLayer.addMarker(getOSMMarker(markerLayer, getOSMMarkerData(newLocation.lon, newLocation.lat, '', ''), map.getProjectionObject()));
	
	map.panTo(newLocation);
}
 
/**
 * Removes all markers from a marker layer.
 * 
 * @param markerLayer The layer to remove all markers from.
 * @return
 */
function removeOSMMarkers(markerLayer) {
	var markerCollection = markerLayer.markers;
	
	for (i in markerCollection) {
		markerLayer.removeMarker(markerCollection[i]);
	}
}
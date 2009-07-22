 /**
  * Javascript functions for Open Layers functionallity in Maps and it's extensions
  *
  * @file OpenLayerFunctions.js
  * @ingroup Maps
  *
  * @author Jeroen De Dauw
  */
  

/**
 * Create and initialize an OpenLayers map. 
 * The resulting map is returned by the function but no further handling is required in most cases.
 */
function initOpenLayer(mapName, lon, lat, zoom, mapTypes, controls, marker_data){
	// Create a new OpenLayers map without any controls on it
	var map = new OpenLayers.Map( mapName, {controls: []} );
	
	// Add the controls
	for (i in controls) {
		
		// TODO: simply eval the given control name. Would scratch the switch, allow all OL controls, but would be more error prone.
		if (typeof controls[i] == 'string') {
			switch(controls[i]) {
				case 'layerswitcher' :
					map.addControl( new OpenLayers.Control.LayerSwitcher() ); // Layer switch control
					break;
				case 'mouseposition' :
					map.addControl( new OpenLayers.Control.MousePosition() ); // Coordinates at lower right corner
					break;	
				case 'navigation' :
					map.addControl( new OpenLayers.Control.Navigation() ); // Mouse wheel zoom & map drag abilities
					break;	
				case 'panzoom' :
					map.addControl( new OpenLayers.Control.PanZoom() ); // Pan control + short zoom
					break;					
				case 'panzoombar' :
					map.addControl( new OpenLayers.Control.PanZoomBar() ); // Pan control + long (vertical) zoom
					break;	
				case 'permalink' :
					map.addControl( new OpenLayers.Control.Permalink() ); // Adds permalink (with coordniates, zoom and layers encoded)
					break;	
				case 'scaleline' :
					map.addControl( new OpenLayers.Control.ScaleLine() ); // Cale indication at lower left corner
					break;
				case 'overviewmap' :
					map.addControl( new OpenLayers.Control.OverviewMap() ); // Minimap at lower right corner
					break;	
				case 'keyboarddefaults' :
					map.addControl( new OpenLayers.Control.KeyboardDefaults() ); // Map movement with arrow keys
					break;																							
			}
		}
		else {
			map.addControl(controls[i]); // If a control is provided, instead a string, just add it
			controls[i].activate(); // And activate it
		}
		
	}
	
	// Variables for whowing an error when the Google Maps API is not loaded
	var googleAPILoaded = typeof(G_NORMAL_MAP) != 'undefined'; var shownApiError = false;
	
	// Variables to prevent double adding of a base layer
	var usedNor = false; var usedSat = false; var usedHyb = false; var usedPhy = false;  // Google types
	var usedBing = false; var usedYahoo = false; var usedOLWMS = false; var usedNasa = false; var usedOSM = false;
	var isDefaultBaseLayer = false;

	// Add the base layers
	for (i in mapTypes) {
		//if (mapTypes[i].substring(0, 1) == '+') {
		//	mapTypes[i] = mapTypes[i].substring(1);
		//	isDefaultBaseLayer = true;
		//}
		
		var newLayer = null;
		
		// TODO: allow adding of custom layers somehow
		switch(mapTypes[i]) {
			case 'google' : case 'google-normal' : case 'google-satellite' : case 'google-hybrid' : case 'google-physical' :
				if (googleAPILoaded) {
					switch(mapTypes[i]) {
						case 'google-normal' :
							if (!usedNor){ newLayer = new OpenLayers.Layer.Google( 'Google Maps' ); usedNor = true; }
							break;
						case 'google-satellite' :
							if (!usedSat){ newLayer = new OpenLayers.Layer.Google( 'Google Satellite' , {type: G_SATELLITE_MAP }); usedSat = true; }
							break;		
						case 'google-hybrid' :
							if (!usedHyb){ newLayer = new OpenLayers.Layer.Google( 'Google Hybrid' , {type: G_HYBRID_MAP }); usedHyb = true; } 
							break;
						case 'google-physical' :
							if (!usedPhy){ newLayer = new OpenLayers.Layer.Google( 'Google Physical' , {type: G_PHYSICAL_MAP }); usedPhy = true; }
							break;						
						case 'google' :
							if (!usedNor){ map.addLayer(new OpenLayers.Layer.Google( 'Google Maps' )); usedNor = true; }
							if (!usedSat){ map.addLayer(new OpenLayers.Layer.Google( 'Google Satellite' , {type: G_SATELLITE_MAP })); usedSat = true; }
							if (!usedHyb){ map.addLayer(new OpenLayers.Layer.Google( 'Google Hybrid' , {type: G_HYBRID_MAP })); usedHyb = true; } 
							if (!usedPhy){ map.addLayer(new OpenLayers.Layer.Google( 'Google Physical' , {type: G_PHYSICAL_MAP })); usedPhy = true; }
							break;	
					}
				}
				else {
					if (!shownApiError) { window.alert('Please enter your Google Maps API key to use the Google Maps layers'); shownApiError = true; }
				}
				break;
			case 'bing' : case 'virtual-earth' :
				if (!usedBing){ newLayer = new OpenLayers.Layer.VirtualEarth( 'Virtual Earth'); usedBing = true; }
				break;
			case 'yahoo' : case 'yahoo-maps' :
				if (!usedYahoo){ newLayer = new OpenLayers.Layer.Yahoo( 'Yahoo Maps'); usedYahoo = true; }
				break;
			case 'openlayers' : case 'open-layers' :
				if (!usedOLWMS){ newLayer = new OpenLayers.Layer.WMS( 'OpenLayers WMS', 'http://labs.metacarta.com/wms/vmap0', {layers: 'basic'} ); usedOLWMS = true; }
				break;		
			case 'nasa' :
				if (!usedNasa){ newLayer = new OpenLayers.Layer.WMS("NASA Global Mosaic", "http://t1.hypercube.telascience.org/cgi-bin/landsat7",  {layers: "landsat7"} ); usedNasa = true; }
				break;	
			// FIXME: this will cause the OL API to mess itself up - unknown reason	
			//case 'osm' : case 'openstreetmap' :
			//	if (!usedOSM){ newLayer = new OpenLayers.Layer.OSM.Mapnik("Open Street Map", { displayOutsideMaxExtent: true, wrapDateLine: true} ); usedOSM = true; }
			//	break;						
		}
		
		if (newLayer != null) {
			map.addLayer(newLayer);
			
			/*
			if (isDefaultBaseLayer) {
				// FIXME: This messed up the layer for some reason
				// Probably fixed by adding this code to an onload event (problem that other layer gets loaded first?) 
				map.setBaseLayer(newLayer);
				isDefaultBaseLayer = false;
			}
			*/
		}
		

	}	
		
	
	/*
	
   var mapnik = new OpenLayers.Layer.OSM.Mapnik("Mapnik", {
      displayOutsideMaxExtent: true,
      wrapDateLine: true
   });
   map.addLayer(mapnik);

   var osmarender = new OpenLayers.Layer.OSM.Osmarender("Osmarender", {
      displayOutsideMaxExtent: true,
      wrapDateLine: true
   });
   map.addLayer(osmarender);

   var cyclemap = new OpenLayers.Layer.OSM.CycleMap("Cycle Map", {
      displayOutsideMaxExtent: true,
      wrapDateLine: true
   });
   map.addLayer(cyclemap);
	
	*/
	
	// Layer to hold the markers
	var markerLayer = new OpenLayers.Layer.Markers('Markers');
	markerLayer.id= 'markerLayer';
	map.addLayer(markerLayer);
	
	var centerIsSet = lon != null && lat != null;
	
	var bounds = null;
	
	if (marker_data.length > 1 && (!centerIsSet || zoom == null)) {
		var bounds = new OpenLayers.Bounds();
	}
	
	for (i in marker_data) {
		if (bounds != null) bounds.extend(marker_data[i].lonlat); // Extend the bounds when no center is set
		markerLayer.addMarker(getOLMarker(markerLayer, marker_data[i])); // Create and add the marker
	}
		
	if (bounds != null) map.zoomToExtent(bounds); // If a bounds object has been created, use it to set the zoom and center
	if (centerIsSet) map.setCenter(new OpenLayers.LonLat(lon, lat)); // When the center is provided, set it
	if (zoom != null) map.zoomTo(zoom); // When the zoom is provided, set it
	
	return map;
}


	
function getOLMarker(markerLayer, markerData) {
	var marker = new OpenLayers.Marker(markerData.lonlat);
	
	if (markerData.title.length + markerData.label.length > 0 ) {
		
		// This is the handler for the mousedown event on the marker, and displays the popup
		marker.events.register('mousedown', marker,
			function(evt) { 
				var popup = new OpenLayers.Feature(markerLayer, markerData.lonlat).createPopup(true);
				
				if (markerData.title.length > 0 && markerData.label.length > 0) { // Add the title and label to the popup text
					popup.setContentHTML('<b>' + markerData.title + "</b><hr />" + markerData.label);
				}
				else {
					popup.setContentHTML(markerData.title + markerData.label);
				}
				
				popup.setOpacity(0.85);
				markerLayer.map.addPopup(popup);
				OpenLayers.Event.stop(evt); // Stop the event
			}
		);
		
	}	
	
	return marker;
}
	

function getOLMarkerData(lon, lat, title, label) {
	return {lonlat: new OpenLayers.LonLat(lon, lat), title: title, label: label};
}


function setOLPopupType(minWidth, minHeight) {
	OpenLayers.Feature.prototype.popupClass = OpenLayers.Class(OpenLayers.Popup.FramedCloud, {'autoSize': true, 'minSize': new OpenLayers.Size(minWidth, minHeight)});
}

/**
 * This function holds spesific functionallity for the Open Layers form input of Semantic Maps
 * TODO: Refactor as much code as possible to non specific functions
 * TODO: Centralize geocoding functionallity, and use that code instead of local GG
 */
function makeFormInputOpenLayer(mapName, locationFieldName, lat, lon, zoom, marker_lat, marker_lon, layers, controls) {
	if (GBrowserIsCompatible()) {
		var markers = Array();

		// Show a starting marker only if marker coordinates are provided
		if (marker_lat != null && marker_lon != null) {
			markers.push(getOLMarkerData(marker_lon, marker_lat, '', ''));
		}		
		
		// Click event handler for updating the location of the marker
		// TODO/FIXME: This will probably cause problems when used for multiple maps on one page.
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
	     
	     var map = initOpenLayer(mapName, lon, lat, zoom, layers, controls, markers);
		
		// Make the map variable available for other functions
		if (!window.OLMaps) window.OLMaps = new Object;
		eval("window.OLMaps." + mapName + " = map;"); 
	}	
}


/**
 * This function holds spesific functionallity for the Open Layers form input of Semantic Maps
 * TODO: Refactor as much code as possible to non specific functions
 */
function showOLAddress(address, mapName, outputElementName, notFoundFormat) {

	var map = OLMaps[mapName];
	var geocoder = new GClientGeocoder();

	geocoder.getLatLng(address,
		function(point) {
			if (!point) {
				window.alert(address + ' ' + notFoundFormat);
			} else {
				var loc = new OpenLayers.LonLat(point.x, point.y)
				
				replaceMarker(mapName, loc);
				document.getElementById(outputElementName).value = convertLatToDMS(point.y) + ', ' + convertLngToDMS(point.x);
			}
		}
	);

}
 
/**
 * Remove all markers from an OL map (that's in window.OLMaps), and pplace a new one.
 * 
 * @param mapName Name of the map as in OLMaps[mapName].
 * @param newLocation The location for the new marker.
 * @return
 */
function replaceMarker(mapName, newLocation) {
	var map = OLMaps[mapName];
	var markerLayer = map.getLayer('markerLayer');
	
	removeMarkers(markerLayer);
	markerLayer.addMarker(getOLMarker(markerLayer, getOLMarkerData(newLocation.lon, newLocation.lat, '', '')));
	
	map.panTo(newLocation);
}
 
/**
 * Removes all markers from a marker layer.
 * 
 * @param markerLayer The layer to remove all markers from.
 * @return
 */
function removeMarkers(markerLayer) {
	var markerCollection = markerLayer.markers
	
	for (i in markerCollection) {
		markerLayer.removeMarker(markerCollection[i]);
	}
}


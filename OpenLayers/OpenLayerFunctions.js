 /**
  * Javascript functions for Open Layers functionallity in Maps and it's extensions
  *
  * @file OpenLayerFunctions.js
  * @ingroup Maps
  *
  * @author Jeroen De Dauw
  */
  

/**
 * Get a valid control name (with excat lower and upper case letters),
 * or return false when the control is not allowed.
 */
function getValidControlName(control) {
	var OLControls = ['ArgParser', 'Attribution', 'Button', 'DragFeature', 'DragPan', 
	                  'DrawFeature', 'EditingToolbar', 'GetFeature', 'KeyboardDefaults', 'LayerSwitcher',
	                  'Measure', 'ModifyFeature', 'MouseDefaults', 'MousePosition', 'MouseToolbar',
	                  'Navigation', 'NavigationHistory', 'NavToolbar', 'OverviewMap', 'Pan',
	                  'Panel', 'PanPanel', 'PanZoom', 'PanZoomBar', 'Permalink',
	                  'Scale', 'ScaleLine', 'SelectFeature', 'Snapping', 'Split', 
	                  'WMSGetFeatureInfo', 'ZoomBox', 'ZoomIn', 'ZoomOut', 'ZoomPanel',
	                  'ZoomToMaxExtent'];
	
	for (i in OLControls) {
		if (control == OLControls[i].toLowerCase()) {
			return OLControls[i];
		}
	}
	
	return false;
}

/**
 * Create and initialize an OpenLayers map. 
 * The resulting map is returned by the function but no further handling is required in most cases.
 */
function initOpenLayer(mapName, lon, lat, zoom, mapTypes, controls, marker_data){
	// Create a new OpenLayers map without any controls on it
	var mapOptions = 	{ /*
						projection: new OpenLayers.Projection("EPSG:900913"), 
						displayProjection: new OpenLayers.Projection("EPSG:900913"),
						units: "m",
						*/
						controls: []
						}

	var map = new OpenLayers.Map(mapName, mapOptions);
	
	// Add the controls
	for (i in controls) {
		
		// If a string is provided, find the correct name for the control, and use eval to create the object itself
		if (typeof controls[i] == 'string') {
			control = getValidControlName(controls[i]);
			
			if (control) {
				eval(' map.addControl( new OpenLayers.Control.' + control + '() ); ')
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
		// TODO: layer name alliasing system? php or js based?
		switch(mapTypes[i]) {
			case 'google' : case 'google-normal' : case 'google-satellite' : case 'google-hybrid' : case 'google-physical' :
				if (googleAPILoaded) {
					switch(mapTypes[i]) {
						case 'google-normal' :
							if (!usedNor){ newLayer = new OpenLayers.Layer.Google( 'Google Maps' /*, {sphericalMercator:true} */ ); usedNor = true; }
							break;
						case 'google-satellite' :
							if (!usedSat){ newLayer = new OpenLayers.Layer.Google( 'Google Satellite' , {type: G_SATELLITE_MAP /*, sphericalMercator:true */}); usedSat = true; }
							break;		
						case 'google-hybrid' :
							if (!usedHyb){ newLayer = new OpenLayers.Layer.Google( 'Google Hybrid' , {type: G_HYBRID_MAP /*, sphericalMercator:true */}); usedHyb = true; } 
							break;
						case 'google-physical' :
							if (!usedPhy){ newLayer = new OpenLayers.Layer.Google( 'Google Physical' , {type: G_PHYSICAL_MAP /*, sphericalMercator:true */}); usedPhy = true; }
							break;						
						case 'google' :
							if (!usedNor){ map.addLayer(new OpenLayers.Layer.Google( 'Google Maps' /*, {sphericalMercator:true} */)); usedNor = true; }
							if (!usedSat){ map.addLayer(new OpenLayers.Layer.Google( 'Google Satellite' , {type: G_SATELLITE_MAP /*, sphericalMercator:true */})); usedSat = true; }
							if (!usedHyb){ map.addLayer(new OpenLayers.Layer.Google( 'Google Hybrid' , {type: G_HYBRID_MAP /*, sphericalMercator:true */})); usedHyb = true; } 
							if (!usedPhy){ map.addLayer(new OpenLayers.Layer.Google( 'Google Physical' , {type: G_PHYSICAL_MAP /*, sphericalMercator:true */})); usedPhy = true; }
							break;	
					}
				}
				else {
					if (!shownApiError) { window.alert('Please enter your Google Maps API key to use the Google Maps layers'); shownApiError = true; }
				}
				break;
			case 'bing' : case 'virtual-earth' :
				if (!usedBing){ newLayer = new OpenLayers.Layer.VirtualEarth( 'Virtual Earth' /* , {sphericalMercator:true} */); usedBing = true; }
				break;
			case 'yahoo' : case 'yahoo-maps' :
				if (!usedYahoo){ newLayer = new OpenLayers.Layer.Yahoo( 'Yahoo Maps' /*, {sphericalMercator:true} */); usedYahoo = true; }
				break;
			case 'openlayers' : case 'open-layers' :
				if (!usedOLWMS){ newLayer = new OpenLayers.Layer.WMS( 'OpenLayers WMS', 'http://labs.metacarta.com/wms/vmap0', {layers: 'basic'} ); usedOLWMS = true; }
				break;		
			case 'nasa' :
				if (!usedNasa){ newLayer = new OpenLayers.Layer.WMS("NASA Global Mosaic", "http://t1.hypercube.telascience.org/cgi-bin/landsat7",  {layers: "landsat7" /*, sphericalMercator:true */} ); usedNasa = true; }
				break;	
			// FIXME: this will cause the OL API to mess itself up - other coordinate system?
			/*
			case 'osm' : case 'openstreetmap' :
				if (!usedOSM){ newLayer = new OpenLayers.Layer.OSM.Osmarender("Open Street Map"); usedOSM = true; }
				break;	
			case 'osm-nik' : case 'osm-mapnik' :
				if (!usedOSM){ newLayer = new OpenLayers.Layer.OSM.Mapnik("OSM Mapnik"); usedOSM = true; }
				break;	
			case 'osm-cycle' : case 'osm-cyclemap' :
				if (!usedOSM){ newLayer = new OpenLayers.Layer.OSM.CycleMap("Cycle Map"); usedOSM = true; }
				break;		
			*/			
		}
		
		if (newLayer != null) {
			map.addLayer(newLayer);
			
			/*
			if (isDefaultBaseLayer) {
				// FIXME: This messes up the layer for some reason
				// Probably fixed by adding this code to an onload event (problem that other layer gets loaded first?) 
				map.setBaseLayer(newLayer);
				isDefaultBaseLayer = false;
			}
			*/
		}
		

	}	
	
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
		markerLayer.addMarker(getOLMarker(markerLayer, marker_data[i], map.getProjectionObject())); // Create and add the marker
	}
		
	if (bounds != null) map.zoomToExtent(bounds); // If a bounds object has been created, use it to set the zoom and center
	if (centerIsSet) map.setCenter(new OpenLayers.LonLat(lon, lat)); // When the center is provided, set it
	if (zoom != null) map.zoomTo(zoom); // When the zoom is provided, set it
	
	return map;
}

	
	
function getOLMarker(markerLayer, markerData, projectionObject) {
	//markerData.lonlat.transform(new OpenLayers.Projection("EPSG:4326"), new OpenLayers.Projection("EPSG:900913")); 
	
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
	lonLat = new OpenLayers.LonLat(lon, lat)
	//lonLat.transform(new OpenLayers.Projection("EPSG:4326"), new OpenLayers.Projection("EPSG:900913")); 
	return {
		lonlat: lonLat,
		title: title,
		label: label};
}


function setOLPopupType(minWidth, minHeight) {
	OpenLayers.Feature.prototype.popupClass = OpenLayers.Class(OpenLayers.Popup.FramedCloud, {'autoSize': true, 'minSize': new OpenLayers.Size(minWidth, minHeight)});
}

/**
 * This function holds spesific functionallity for the Open Layers form input of Semantic Maps
 * TODO: Refactor as much code as possible to non specific functions
 */
function makeFormInputOpenLayer(mapName, locationFieldName, lat, lon, zoom, marker_lat, marker_lon, layers, controls) {
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
	markerLayer.addMarker(getOLMarker(markerLayer, getOLMarkerData(newLocation.lon, newLocation.lat, '', ''), map.getProjectionObject()));
	
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


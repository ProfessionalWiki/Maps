/**
 * Javascript functions for Google Maps functionality in Maps.
 * 
 * @file GoogleMapFunctions.js
 * @ingroup MapsGoogleMaps
 * 
 * @author Robert Buzink
 * @author Yaron Koren
 * @author Jeroen De Dauw
 */

var GOverlays = [ new GLayer("com.panoramio.all"),
		new GLayer("com.youtube.all"), new GLayer("org.wikipedia.en"),
		new GLayer("com.google.webcams") ];

/**
 * Returns GMarker object on the provided location. It will show a popup baloon
 * with title and label when clicked, if either of these is set.
 */
function createGMarker(markerData) {
	var marker;

	if (markerData.icon != '') {
		var iconObj = new GIcon(G_DEFAULT_ICON);
		iconObj.image = markerData.icon;

		var newimg = new Image();
		newimg.src = markerData.icon;

		// Only do these things when there is an actual width, which there won,t
		// the first time the image is loaded.
		// FIXME: this means the image won't have it's correct size when it
		// differs from the default on first load!
		if (newimg.width > 0) {
			/* Determine size of icon and pass it in */
			iconObj.iconSize.width = newimg.width;
			iconObj.iconSize.height = newimg.height;
			iconObj.shadow = null;

			/* Anchor the icon on bottom middle */
			var anchor = new GPoint();
			anchor.x = Math.floor(newimg.width / 2);
			anchor.y = newimg.height;
			iconObj.iconAnchor = anchor;
		}

		marker = new GMarker(markerData.point, {
			icon : iconObj
		});
	} else {
		marker = new GMarker(markerData.point);
	}

	if ( markerData.title + markerData.label != '' ) {
		var bothTxtAreSet = markerData.title != ''
				&& markerData.label != '';
		var popupText = bothTxtAreSet ? '<b>' + markerData.title + '</b><hr />'
				+ markerData.label : markerData.title + markerData.label;
		popupText = '<div style="overflow:auto;max-height:130px;">' + popupText
				+ '</div>';

		GEvent.addListener(marker, 'click', function() {
			marker.openInfoWindowHtml(popupText, {
				maxWidth : 350
			});
		});
	}

	return marker;
}

/**
 * Returns GMap2 object with the provided properties and markers. This is done
 * by setting the map centre and size, and passing the arguments to function
 * createGoogleMap.
 */
function initializeGoogleMap(mapName, mapOptions, markers) {
	if (GBrowserIsCompatible()) {
		mapOptions.centre = (mapOptions.lat != null && mapOptions.lon != null) ? new GLatLng(
				mapOptions.lat, mapOptions.lon)
				: null;
		// mapOptions.size = new GSize(mapOptions.width, mapOptions.height);
		return createGoogleMap(mapName, mapOptions, markers);
	} else {
		return false;
	}
}

/**
 * Returns GMap2 object with the provided properties.
 */
function createGoogleMap(mapName, mapOptions, markers) {
	var mapElement = document.getElementById(mapName);
	var typesContainType = false;

	for ( var i = 0; i < mapOptions.types.length; i++) {
		if (mapOptions.types[i] == mapOptions.type)
			typesContainType = true;
	}

	if (!typesContainType)
		mapOptions.types.push(mapOptions.type);

	var map = new GMap2(mapElement, {
		mapTypes : mapOptions.types
	});
	map.name = mapName;

	map.setMapType(mapOptions.type);

	var hasSearchBar = false;

	for (i = mapOptions.controls.length - 1; i >= 0; i--) {
		if (mapOptions.controls[i] == 'searchbar') {
			hasSearchBar = true;
			break;
		}
	}

	// List of GControls:
	// http://code.google.com/apis/maps/documentation/reference.html#GControl
	for (i = 0; i < mapOptions.controls.length; i++) {
		if (mapOptions.controls[i].toLowerCase() == 'auto') {
			if (mapElement.offsetHeight > 75)
				mapOptions.controls[i] = mapElement.offsetHeight > 320 ? 'large'
						: 'small';
		}

		switch (mapOptions.controls[i]) {
		case 'large':
			map.addControl(new GLargeMapControl3D());
			break;
		case 'small':
			map.addControl(new GSmallZoomControl3D());
			break;
		case 'large-original':
			map.addControl(new GLargeMapControl());
			break;
		case 'small-original':
			map.addControl(new GSmallMapControl());
			break;
		case 'zoom':
			map.addControl(new GSmallZoomControl());
			break;
		case 'type':
			map.addControl(new GMapTypeControl());
			break;
		case 'type-menu':
			map.addControl(new GMenuMapTypeControl());
			break;
		case 'overlays':
			map.addControl(new MoreControl());
			break;
		case 'overview':
		case 'overview-map':
			map.addControl(new GOverviewMapControl());
			break;
		case 'scale':
			if (hasSearchBar) {
				map.addControl(new GScaleControl(), new GControlPosition(
						G_ANCHOR_BOTTOM_LEFT, new GSize(5, 37)));
			} else {
				map.addControl(new GScaleControl());
			}
			break;
		case 'nav-label':
		case 'nav':
			map.addControl(new GNavLabelControl());
			break;
		case 'searchbar':
			map.enableGoogleBar();
			break;
		}
	}

	// Get center and zoom
	var myCenter = null;
	var myZoom = null;
	if (mapOptions.centre != null) {
		myCenter = mapOptions.centre;
	}
	if (mapOptions.zoom != null) {
		myZoom = mapOptions.zoom;
	}

	// possibly, create bounds from markers
	if ((mapOptions.zoom == null || mapOptions.centre == null)
			&& markers.length >= 1) {
		var bounds = new GLatLngBounds();

		for (i = markers.length - 1; i >= 0; i--) {
			var marker = markers[i];
			marker.point = new GLatLng(marker.lat, marker.lon);
			map.addOverlay(createGMarker(marker));
			bounds.extend(marker.point);
		}
		if (myCenter == null) {
			myCenter = bounds.getCenter();
		}
		if (myZoom == null) {
			myZoom = map.getBoundsZoomLevel(bounds);
		}
	}

	//TODO: retrieve standard values dynamically?
	if ((myCenter == null || myZoom == null) || ((myCenter.lat() == 0 && myCenter.lng() == 0) && myZoom == 14)) {
		map.setCenter(new GLatLng(0, 0), 14);
		map.isDefaultBound = true;
	} else {
		map.setCenter(myCenter, myZoom);
		map.isDefaultBound = false;
	}

	// other options of map
	if (mapOptions.scrollWheelZoom)
		map.enableScrollWheelZoom();

	map.enableContinuousZoom();

	// We add Listener for goDraw event
	GEvent.addListener(map, "goDraw", function() {
		// by now, we have all points from the kml files
		
		var mykmlOverlays = window.GKMLOverlays[map.name];
		
		var bounds;
		// if center and zoom are set
		if (map.isDefaultBound) {
			// use bounds of first overlay
			bounds = mykmlOverlays[0].getDefaultBounds();			
		} else {
			bounds = map.getBounds();
		}
		
		for (i = mykmlOverlays.length - 1; i >= 0; i--) {
			var point = mykmlOverlays[i].getDefaultCenter();
			// extending to the same point should not make a difference
			bounds.extend(point);
		}

		map.setCenter(bounds.getCenter(),map.getBoundsZoomLevel(bounds));
		
	});

	// Code to add KML files.
	var kmlOverlays = [];
	// How many kml files are there, save this.
	if (!window.Gkmlln) {
		window.Gkmlln = new Array();
	}
	window.Gkmlln[mapName] = mapOptions.kml.length;
	// How many kml files have we already loaded? Save this.
	if (!window.Gkmlnr) {
		window.Gkmlnr = new Array();
	}
	window.Gkmlnr[mapName] = 0;
	for (i = mapOptions.kml.length - 1; i >= 0; i--) {
		// if only names are given
		kmlOverlays[i] = new GGeoXml(mapOptions.kml[i]);
		var myoverlay = kmlOverlays[i];
		myoverlay.mapName = mapName;
		map.addOverlay(myoverlay);

		GEvent.addListener(myoverlay, "load", function() {

			// What we only do here is, we count the number of loaded kml
			// overlays
			++window.Gkmlnr[myoverlay.mapName];
			if (window.Gkmlnr[myoverlay.mapName] >= window.Gkmlln[myoverlay.mapName]) {
				
				var mymap = window.GMaps[myoverlay.mapName];
				// we have to make sure that the map is loaded.
				if (mymap.isLoaded()) {
					GEvent.trigger(mymap, "goDraw");
				} else {
					GEvent.addListener(mymap, "load", function() {
						GEvent.trigger(mymap, "goDraw");
					});
				}
			} else {
				return;
			}
		});

		// Make the overlay variable available for other functions
		if (!window.GKMLOverlays) {
			window.GKMLOverlays = new Array();
		}
		if (!window.GKMLOverlays[mapName]) {
			window.GKMLOverlays[mapName] = new Array();
		}
		window.GKMLOverlays[mapName][i] = myoverlay;
	}

	// TODO: GMaps now have another name, check whether this is a problem somewhere.
	// Make the map variable available for other functions.
	// if (!window.GMaps) window.GMaps = new Object;
	// eval("window.GMaps." + mapName + " = map;");

	if (!window.GMaps) {
		window.GMaps = new Array();
	}
	window.GMaps[mapName] = map;

	return map;
}

function setupCheckboxShiftClick() {
	return true;
}

function MoreControl() {
};
MoreControl.prototype = new GControl();

MoreControl.prototype.initialize = function(map) {
	this.map = map;

	var more = document.getElementById(map.name + "-outer-more");

	var buttonDiv = document.createElement("div");
	buttonDiv.id = map.name + "-more-button";
	buttonDiv.title = "Show/Hide Overlays";
	buttonDiv.style.border = "1px solid black";
	buttonDiv.style.width = "86px";

	var textDiv = document.createElement("div");
	textDiv.id = map.name + "-inner-more";
	textDiv.setAttribute('class', 'inner-more');
	textDiv.appendChild(document.createTextNode(msgOverlays));

	buttonDiv.appendChild(textDiv);

	// Register Event handlers
	more.onmouseover = showGLayerbox;
	more.onmouseout = setGLayerboxClose;

	// Insert the button just after outer_more div.
	more.insertBefore(buttonDiv, document
			.getElementById(map.name + "-more-box").parentNode);

	// Remove the whole div from its location and reinsert it to the map.
	map.getContainer().appendChild(more);

	return more;
};

MoreControl.prototype.getDefaultPosition = function() {
	return new GControlPosition(G_ANCHOR_TOP_RIGHT, new GSize(7, 35));
};

function checkGChecked(mapName) {
	// Returns true if a checkbox is still checked otherwise false.
	var boxes = document.getElementsByName(mapName + "-overlay-box");
	for ( var i = 0; i < boxes.length; i++) {
		if (boxes[i].checked)
			return true;
	}
	return false;
}

function showGLayerbox() {
	var mapName = this.id.split('-')[0];
	eval("if(window.timer_" + mapName + ") clearTimeout(timer_" + mapName
			+ ");");
	document.getElementById(mapName + "-more-box").style.display = "block";
	var button = document.getElementById(mapName + "-inner-more");
	button.style.borderBottomWidth = "4px";
	button.style.borderBottomColor = "white";
}

function setGLayerboxClose() {
	var mapName = this.id.split('-')[0];
	var layerbox = document.getElementById(mapName + "-more-box");
	var button = document.getElementById(mapName + "-inner-more");
	var bottomColor = checkGChecked(mapName) ? "#6495ed" : "#c0c0c0";
	eval("timer_"
			+ mapName
			+ " = window.setTimeout(function() { layerbox.style.display = 'none'; button.style.borderBottomWidth = '1px'; button.style.borderBottomColor = bottomColor; },	400);");
}

function switchGLayer(map, checked, layer) {
	var layerbox = document.getElementById(map.name + "-more-box");
	var button = document.getElementById(map.name + "-inner-more");

	if (checked) {
		map.addOverlay(layer);
	} else {
		map.removeOverlay(layer);
	}

}

function initiateGOverlay(elementId, mapName, urlNr) {
	document.getElementById(elementId).checked = true;
	switchGLayer(GMaps[mapName], true, GOverlays[urlNr]);
}
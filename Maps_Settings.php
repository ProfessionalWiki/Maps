<?php
/**
 * File defining the settings for the Maps extension.
 * More info can be found at http://www.mediawiki.org/wiki/Extension:Maps#Settings
 *
 *                          NOTICE:
 * Changing one of these settings can be done by copieng or cutting it,
 * and placing it in LocalSettings.php, AFTER the inclusion of Maps.
 *
 * @file Maps_Settings.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 *
 * TODO: clean up, update docs
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}



# Features configuration
# Commenting out the inclusion of any feature will make Maps completely ignore it, and so improve performance.

	# Include the features you want to have available.
	# Functionality in the features directory uses the Maps framework to support multiple mapping services.

		# General function support, required for #display_map and #display_point(s).
		include_once $egMapsDir . 'Features/Maps_ParserFunctions.php'; 		
		# Required for #display_map.
		include_once $egMapsDir . 'Features/DisplayMap/Maps_DisplayMap.php';
		# Required for #display_point and #display_points.
		include_once $egMapsDir . 'Features/DisplayPoint/Maps_DisplayPoint.php';

	# Include the additional features such geocoding and stand alone parser functions that should be loaded into Maps.
		# Geocoding support, required for the geocoding parser functions and smart geocoding support in all other parser functions.
		include_once $egMapsDir . 'Geocoders/Maps_Geocoders.php';
		# Geocoding parser functions: #geocode, #geocodelat, #geocodelon.
		include_once $egMapsDir . 'ParserFunctions/Maps_GeocodeFunctions.php';
		# Required for #coordinates.
		include_once $egMapsDir . 'ParserFunctions/Maps_Coordinates.php';
		# Required for #distance.
		include_once $egMapsDir . 'ParserFunctions/Maps_Distance.php';		
		# Geographic parser functions: #geodistance, #finddestination
		include_once $egMapsDir . 'ParserFunctions/Maps_GeoFunctions.php';

		
		
# Mapping services configuration
# Note: You can not use aliases in the settings. Use the main service names.

	# Include the mapping services that should be loaded into Maps.
	# Commenting or removing a mapping service will make Maps completely ignore it, and so improve performance.
	
	# Google Maps API v2
	include_once $egMapsDir . 'Services/GoogleMaps/GoogleMaps.php';
	
	# Google Maps API v3
	include_once $egMapsDir . 'Services/GoogleMaps3/GoogleMaps3.php';
	
	# OpenLayers API
	include_once $egMapsDir . 'Services/OpenLayers/OpenLayers.php';
	
	# Yahoo! Maps API
	include_once $egMapsDir . 'Services/YahooMaps/YahooMaps.php';
	
	# Yahoo! Maps API
	include_once $egMapsDir . 'Services/OSM/OSM.php';	

	# Array of String. Array containing all the mapping services that will be made available to the user.
	# Currently Maps provides the following services: googlemaps, yahoomaps, openlayers, osm.
	$egMapsAvailableServices = array(
		'googlemaps2',
		'googlemaps3',
		'yahoomaps',
		'openlayers',
		'osm'
	);

	# String. The default mapping service, which will be used when no default
	# service is present in the $egMapsDefaultServices array for a certain feature.
	# A service that supports all features is recommended. This service needs to be
	# enabled, if not, the first one from the available services will be taken.
	$egMapsDefaultService = 'googlemaps2';
	
	# Array of String. The default mapping service for each feature, which will be
	# used when no valid service is provided by the user. Each service needs to be
	# enabled, if not, the first one from the available services will be taken.
	# Note: The default service needs to be available for the feature you set it
	# for, since it's used as a fallback mechanism.
	$egMapsDefaultServices = array(
		'display_point' => 'googlemaps2',
		'display_map' => 'googlemaps2'
	);



# General configuration

	# Boolean. Indicates if minified js files should be used where available.
	# Do not change this value unless you know what you are doing!
	$egMapsUseMinJs = false;


	
# Geocoding services configuration

	# Array of String. Array containing all the geocoding services that will be
	# made available to the user. Currently Maps provides the following services:
	# geonames, google, yahoo
	$egMapsAvailableGeoServices = array(
		'geonames',
		'google',
		'yahoo'
	);
	
	# String. The default geocoding service, which will be used when no service is
	# is provided by the user. This service needs to be enabled, if not, the first
	# one from the available services will be taken.
	$egMapsDefaultGeoService = 'geonames';
	
	$egMapsUserGeoOverrides = true;



# Coordinate configuration

	# The coordinate notations that should be available.
	$egMapsAvailableCoordNotations = array(
		Maps_COORDS_FLOAT,
		Maps_COORDS_DMS,
		Maps_COORDS_DM,
		Maps_COORDS_DD
	);
	
	# Enum. The default output format of coordinates.
	# Possible values: Maps_COORDS_FLOAT, Maps_COORDS_DMS, Maps_COORDS_DM, Maps_COORDS_DD
	$egMapsCoordinateNotation = Maps_COORDS_DMS;
	
	# Boolean. Indicates if coordinates should be outputted in directional notation by default.
	# Recommended to be true for Maps_COORDS_DMS and false for Maps_COORDS_FLOAT.
	$egMapsCoordinateDirectional = true;
	
	# Boolean. Sets if coordinates should be allowed in geocoding calls.
	$egMapsAllowCoordsGeocoding = true;
	
	# Boolean. Sets if geocoded addresses should be stored in a cache.
	$egMapsEnableGeoCache = true;



# Distance configuration
	
	# Array. A list of units (keys) and how many meters they represent (value).
	$egMapsDistanceUnits = array(
		'm' => 1,
		'meter' => 1,
		'meters' => 1,
		'km' => 1000,
		'kilometers' => 1000,
		'kilometres' => 1000,
		'mi' => 1609.344,
		'mile' => 1609.344,
		'miles' => 1609.344,
		'nm' => 1852,
		'nautical mile' => 1852,
		'nautical miles' => 1852,
	);
	
	# String. The default unit for distances.
	$egMapsDistanceUnit = 'km';
	
	# Integer. The default limit of fractal digits in a distance.
	$egMapsDistanceDecimals = 2;	
	
	
	
# General map configuration

	# Integer or string. The default width and height of a map. These values will
	# only be used when the user does not provide them.
	$egMapsMapWidth = '100%';
	$egMapsMapHeight = 350;

	# Array. The minimum and maximum width and height for all maps. First min and
	# max for absolute values, then min and max for percentage values. When the
	# height or width exceed their limits, they will be changed to the closest
	# allowed value.
	$egMapsSizeRestrictions = array(
		'width'  => array( 50, 1020, 1, 100 ),
		'height' => array( 50, 1000, 1, 100 ),
	);
	
	# Strings. The default coordinates for the map. Must be in floating point
	# notation. This value will only be used when the user does not provide one.
	$egMapsMapLat = '1';
	$egMapsMapLon = '1';
	
	# Strings. The default content for all pop-ups. This value will only be used
	# when the user does not provide one.
	$egMapsDefaultTitle = '';
	$egMapsDefaultLabel = '';



# Specific mapping service configuration

	# Google Maps
	
		# Your Google Maps API key. Required for displaying Google Maps, and using the
		# Google Geocoder services.
		$egGoogleMapsKey = ''; # http://code.google.com/apis/maps/signup.html
		
		# String. The Google Maps map name prefix. It can not be identical to the one
		# of another mapping service.
		$egMapsGoogleMapsPrefix = 'map_google';
		
		# Integer. The default zoom of a map. This value will only be used when the
		# user does not provide one.
		$egMapsGoogleMapsZoom = 14;
		
		# Array of String. The Google Maps v2 default map types. This value will only
		# be used when the user does not provide one.
		$egMapsGoogleMapsTypes = array(
			'normal',
			'satellite',
			'hybrid',
			'physical'
		);
	
		# String. The default map type. This value will only be used when the user does
		# not provide one.
		$egMapsGoogleMapsType = 'normal';
		
		# Boolean. The default value for enabling or disabling the autozoom of a map.
		# This value will only be used when the user does not provide one.
		$egMapsGoogleAutozoom = true;
		
		# Array of String. The default controls for Google Maps v2. This value will
		# only be used when the user does not provide one.
		# Available values: auto, large, small, large-original, small-original, zoom,
		# type, type-menu, overview-map, scale, nav-label, overlays
		$egMapsGMapControls = array(
			'auto',
			'scale',
			'type',
			//'overlays' # Temporary disabled untill css issue has been fixed
		);
		
		# Array. The default overlays for the Google Maps v2 overlays control, and
		# whether they should be shown at pageload. This value will only be used when
		# the user does not provide one.
		# Available values: photos, videos, wikipedia, webcams
		$egMapsGMapOverlays = array(
			'photos',
			'videos',
			'wikipedia',
			'webcams'
		);
	
	
	
	# Google Maps v3
	
		# String. The Google Maps v3 map name prefix. It can not be identical to the
		# one of another mapping service.
		$egMapsGMaps3Prefix = 'map_google3';
		
		# Integer. The default zoom of a map. This value will only be used when the
		# user does not provide one.
		$egMapsGMaps3Zoom = 14;
		
		# Array of String. The Google Maps v3 default map types. This value will only
		# be used when the user does not provide one.
		$egMapsGMaps3Types = array(
			'roadmap',
			'satellite',
			'hybrid',
			'terrain'
		);
		
		# String. The default map type. This value will only be used when the user
		# does not provide one.
		$egMapsGMaps3Type = 'roadmap';
	
	
	
	# Yahoo! Maps
	
		# Your Yahoo! Maps API key. Required for displaying Yahoo! Maps.
		# Haven't got an API key yet? Get it here: https://developer.yahoo.com/wsregapp/
		$egYahooMapsKey = '';
		
		# String. The Yahoo! maps map name prefix. It can not be identical to the one
		# of another mapping service.
		$egMapsYahooMapsPrefix = 'map_yahoo';
		
		# Array of String. The Google Maps default map types. This value will only be
		# used when the user does not provide one.
		$egMapsYahooMapsTypes = array(
			'normal',
			'satellite',
			'hybrid'
		);
		
		# String. The default map type. This value will only be used when the user does
		# not provide one.
		$egMapsYahooMapsType = 'normal';
		
		# Integer. The default zoom of a map. This value will only be used when the
		# user does not provide one.
		$egMapsYahooMapsZoom = 4;
		
		# Boolean. The default value for enabling or disabling the autozoom of a map.
		# This value will only be used when the user does not provide one.
		$egMapsYahooAutozoom = true;
		
		# Array of String. The default controls for Yahoo! Maps. This value will only
		# be used when the user does not provide one.
		# Available values: type, pan, zoom, zoom-short, auto-zoom
		$egMapsYMapControls = array(
			'type',
			'pan',
			'auto-zoom'
		);
	
	
	
	# OpenLayers
	
		# String. The OpenLayers map name prefix. It can not be identical to the one of
		# another mapping service.
		$egMapsOpenLayersPrefix = 'open_layer';
		
		# Integer. The default zoom of a map. This value will only be used when the
		# user does not provide one.
		$egMapsOpenLayersZoom = 13;
		
		# Array of String. The default controls for Open Layers. This value will only
		# be used when the user does not provide one.
		# Available values: layerswitcher, mouseposition, autopanzoom, panzoom,
		# panzoombar, scaleline, navigation, keyboarddefaults, overviewmap, permalink
		$egMapsOLControls = array(
			'layerswitcher',
			'mouseposition',
			'autopanzoom',
			'scaleline',
			'navigation'
		);
		
		# Array of String. The default layers for Open Layers. This value will only be
		# used when the user does not provide one.
		# Available values: google, bing, yahoo, openlayers, nasa
		$egMapsOLLayers = array(
			'openlayers-wms'
		);
		
		# The difinitions for the layers that should be available for the user.
		$egMapsOLAvailableLayers = array(
			'bing-normal' => array( 'OpenLayers.Layer.VirtualEarth( "Bing Streets", {type: VEMapStyle.Shaded, "sphericalMercator":true} )', 'bing' ),
			'bing-satellite' => array( 'OpenLayers.Layer.VirtualEarth( "Bing Satellite", {type: VEMapStyle.Aerial, "sphericalMercator":true} )', 'bing' ),
			'bing-hybrid' => array( 'OpenLayers.Layer.VirtualEarth( "Bing Hybrid", {type: VEMapStyle.Hybrid, "sphericalMercator":true} )', 'bing' ),
		
			'yahoo-normal' => array( 'OpenLayers.Layer.Yahoo( "Yahoo! Streets", {"sphericalMercator":true} )', 'yahoo' ),
			'yahoo-hybrid' => array( 'OpenLayers.Layer.Yahoo( "Yahoo! Hybrid", {"type": YAHOO_MAP_HYB, "sphericalMercator":true} )', 'yahoo' ),
			'yahoo-satellite' => array( 'OpenLayers.Layer.Yahoo( "Yahoo! Satellite", {"type": YAHOO_MAP_SAT, "sphericalMercator":true} )', 'yahoo' ),
		
			'osmarender' => array( 'OpenLayers.Layer.OSM.Osmarender("OSM arender")', 'osm' ),
			'osm-mapnik' => array( 'OpenLayers.Layer.OSM.Mapnik("OSM Mapnik")', 'osm' ),
			'osm-cyclemap' => array( 'OpenLayers.Layer.OSM.CycleMap("OSM Cycle Map")', 'osm' ),
		
			'openlayers-wms' => array( 'OpenLayers.Layer.WMS( "OpenLayers WMS", "http://labs.metacarta.com/wms/vmap0",
				{layers: "basic", "sphericalMercator":true} )', 'ol-wms' ),
		
			'nasa' => 'OpenLayers.Layer.WMS("NASA Global Mosaic", "http://t1.hypercube.telascience.org/cgi-bin/landsat7",
				{layers: "landsat7", "sphericalMercator":true} )',
		
			/* FIXME: does not work properly yet
			'wikipediaworld' => 'OpenLayers.Layer.Vector("Wikipedia World", {
		strategies: [new OpenLayers.Strategy.BBOX( { ratio : 1.1, resFactor: 1 })],
		protocol: new OpenLayers.Protocol.HTTP({
				url: "http://toolserver.org/~kolossos/geoworld/marks.php?LANG=de",
				format: new OpenLayers.Format.KML({
                           extractStyles: true, 
                           extractAttributes: true
                })		
        })
	})'
	*/
		);
		
		# Layer group definitions. Group names must be different from layer names, and
		# must only contain layers that are present in $egMapsOLAvailableLayers.
		$egMapsOLLayerGroups = array(
			'yahoo' => array( 'yahoo-normal', 'yahoo-satellite', 'yahoo-hybrid' ),
			'bing' => array( 'bing-normal', 'bing-satellite', 'bing-hybrid' ),
			'osm' => array( 'osmarender', 'osm-mapnik', 'osm-cyclemap' ),
		);
		
		# Layer dependencies.
		$egMapsOLLayerDependencies = array(
			'yahoo' => "<style type='text/css'> #controls {width: 512px;}</style><script src='http://api.maps.yahoo.com/ajaxymap?v=3.0&appid=euzuro-openlayers'></script>",
			'bing' => "<script type='$wgJsMimeType' src='http://dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6.1'></script>",
			'ol-wms' => "<script type='$wgJsMimeType' src='http://clients.multimap.com/API/maps/1.1/metacarta_04'></script>",
			'osm' => "<script type='$wgJsMimeType' src='$egMapsScriptPath/Services/OpenLayers/OSM/OpenStreetMap.js?$egMapsStyleVersion'></script>",
		);
	
	
	
	# OpenStreetMap
	
		# Integer. The default zoom of a map. This value will only be used when the
		# user does not provide one.
		$egMapsOSMZoom = 13;		
		
		# String. The OSM map name prefix. It can not be identical to the one of
		# another mapping service.
		$egMapsOSMPrefix = 'map_osm';		
		
		/*
		# Array of String. The default controls for OSM maps. This value will only be
		# used when the user does not provide one.
		# Available values: layerswitcher, mouseposition, autopanzoom, panzoom,
		# panzoombar, scaleline, navigation, keyboarddefaults, overviewmap, permalink
		$egMapsOSMControls = array(
			'layerswitcher',
			'mouseposition',
			'autopanzoom',
			'scaleline',
			'navigation'
		);
		
		# Boolean. Indicates whether you want to get a static map (image) or not.
		# This value will only be used when the user does not provide one.
		$egMapsOSMStaticAsDefault = false;
		
		# Boolean. Indicates whether the user should be able to activate a static map.
		# This value will only be used when the user does not provide one.
		$egMapsOSMStaticActivatable = true;
		*/
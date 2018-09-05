<?php

/**
 * File defining the settings for the Maps extension.
 *
 *                          NOTICE:
 * Changing one of these settings can be done by copying or cutting it,
 * and placing it in LocalSettings.php, AFTER the inclusion of Maps.
 *
 * @author Jeroen De Dauw
 */

// Mapping services configuration

	// Array of String. Array containing all the mapping services that will be made available to the user.
	$GLOBALS['egMapsAvailableServices'] = [
		'leaflet',
		'openlayers',
		'googlemaps3'
	];

	// String. The default mapping service, which will be used when no default
	// service is present in the $GLOBALS['egMapsDefaultServices'] array for a certain feature.
	// A service that supports all features is recommended. This service needs to be
	// enabled, if not, the first one from the available services will be taken.
	$GLOBALS['egMapsDefaultService'] = 'leaflet';



// Enable/disable parts of the extension

        // Allows disabling the extension even when it is installed.
        //
        // CAUTION: this setting is intended for wiki farms. On single wiki installations,
        //          the recommended way to disable maps is to uninstall it via Composer. Disabling
        //          Maps via this setting undermines package management safety: extensions that depend
        //          on Maps will likely either break of disable themselves.
        $GLOBALS['egMapsDisableExtension'] = false;

        // Allows disabling the Semantic MediaWiki integration.
        $GLOBALS['egMapsDisableSmwIntegration'] = false;


// Geocoding

	// Sets which service should be used to turn addresses into coordinates
	// Available services: geonames, google, nominatim
	// The geonames service requires you to specify a geonames user (see below),
	// if you set this setting to geonames but do not specify the user, Maps will
	// fall back to using the google service.
	$GLOBALS['egMapsDefaultGeoService'] = 'nominatim';

	// String. GeoNames API user/application name.
	// Obtain an account here: http://www.geonames.org/login
	// Do not forget to activate your account for API usage!
	$GLOBALS['egMapsGeoNamesUser'] = '';

	// Boolean. Sets if geocoded addresses should be stored in a cache.
	$GLOBALS['egMapsEnableGeoCache'] = true;

	// Integer. If egMapsEnableGeoCache is true, determines the TTL of cached geocoded addresses.
	// Default value: 1 day.
	$GLOBALS['egMapsGeoCacheTtl'] = 24 * 3600;


// Coordinate configuration

	// The coordinate notations that should be available.
	$GLOBALS['egMapsAvailableCoordNotations'] = [
		'float',
		'dms',
		'dm',
		'dd'
	];

	// The default output format of coordinates.
	// Possible values: float, dms, dm, dd
	$GLOBALS['egMapsCoordinateNotation'] = 'dms';

	# The default output format of coordinates when displayed by Semantic MediaWiki.
	# Possible values: float, dms, dm, dd
	$GLOBALS['smgQPCoodFormat'] = $GLOBALS['egMapsCoordinateNotation'];

	// Boolean. Indicates if coordinates should be outputted in directional notation by default.
	// Recommended to be true for dms and false for float.
	$GLOBALS['egMapsCoordinateDirectional'] = true;

	# Boolean. Indicates if coordinates should be outputted in directional notation by default when
	# displayed by Semantic MediaWiki.
	$GLOBALS['smgQPCoodDirectional'] = $GLOBALS['egMapsCoordinateDirectional'];

	// Boolean. Sets if direction labels should be translated to their equivalent in the wiki language or not.
	$GLOBALS['egMapsInternatDirectionLabels'] = true;


// Distance configuration


	// Array. A list of units (keys) and how many meters they represent (value).
	// No spaces! If the unit consists out of multiple words, just write them together.
	$GLOBALS['egMapsDistanceUnits'] = [
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
		'nauticalmile' => 1852,
		'nauticalmiles' => 1852,
	];

	// String. The default unit for distances.
	$GLOBALS['egMapsDistanceUnit'] = 'm';

	// Integer. The default amount of fractal digits in a distance.
	$GLOBALS['egMapsDistanceDecimals'] = 2;


// General map configuration

	// Integer or string. The default width and height of a map. These values will
	// only be used when the user does not provide them.
	$GLOBALS['egMapsMapWidth'] = 'auto';
	$GLOBALS['egMapsMapHeight'] = 350;

	// Array. The minimum and maximum width and height for all maps. First min and
	// max for absolute values, then min and max for percentage values. When the
	// height or width exceed their limits, they will be changed to the closest
	// allowed value.
	$GLOBALS['egMapsSizeRestrictions'] = [
		'width'  => [ 50, 1020, 1, 100 ],
		'height' => [ 50, 1000, 1, 100 ],
	];

	// Strings. The default content for all pop-ups. This value will only be used
	// when the user does not provide one.
	$GLOBALS['egMapsDefaultTitle'] = '';
	$GLOBALS['egMapsDefaultLabel'] = '';

	$GLOBALS['egMapsResizableByDefault'] = false;

	$GLOBALS['egMapsRezoomForKML'] = false;


# Semantic MediaWiki queries

	# Boolean. The default value for the showtitle parameter. Will hide the title in the marker pop-ups when set to false.
	# This value will only be used when the user does not provide one.
	$GLOBALS['smgQPShowTitle'] = true;

	# Boolean. The default value for the hidenamespace parameter. Will hide the namespace in the marker pop-ups when set to true.
	# This value will only be used when the user does not provide one.
	$GLOBALS['smgQPHideNamespace'] = false;

	# String or false. Allows you to define the content and it's layout of marker pop-ups via a template.
	# This value will only be used when the user does not provide one.
	$GLOBALS['smgQPTemplate'] = false;

	// Default mapping service to use for maps showing query results. Defaults to the default service.
	$GLOBALS['egMapsDefaultServices'] = [ 'qp' => $GLOBALS['egMapsDefaultService'] ];


// Other general configuration

	// Boolean. Sets if pages with maps should be put in special category
	$GLOBALS['egMapsEnableCategory'] = false;

	// When true, debugging messages will be logged using mw.log(). Do not use on production wikis.
	$GLOBALS['egMapsDebugJS'] = false;


// Mapping service specific configuration

	// Google Maps v3

		// String. Google Maps v3 API Key
		$GLOBALS['egMapsGMaps3ApiKey'] = '';

		// String. Google Maps v3 API version number
		$GLOBALS['egMapsGMaps3ApiVersion'] = '';

		// Integer. The default zoom of a map. This value will only be used when the
		// user does not provide one.
		$GLOBALS['egMapsGMaps3Zoom'] = 14;

		// Array of String. The Google Maps v3 default map types. This value will only
		// be used when the user does not provide one.
		$GLOBALS['egMapsGMaps3Types'] = [
			'roadmap',
			'satellite',
			'hybrid',
			'terrain'
		];

		// String. The default map type. This value will only be used when the user
		// does not provide one.
		$GLOBALS['egMapsGMaps3Type'] = 'roadmap';

		// Array. List of controls to display onto maps by default.
		$GLOBALS['egMapsGMaps3Controls'] = [
			'pan',
			'zoom',
			'type',
			'scale',
			'streetview'
		];

		// String. The default style for the type control.
		// horizontal, vertical or default
		$GLOBALS['egMapsGMaps3DefTypeStyle'] = 'default';

		// String. The default style for the zoom control.
		// small, large or default
		$GLOBALS['egMapsGMaps3DefZoomStyle'] = 'default';

		// Boolean. Open the info windows on load by default?
		$GLOBALS['egMapsGMaps3AutoInfoWindows'] = false;

		// Array. Layers to load by default.
		// traffic and bicycling
		$GLOBALS['egMapsGMaps3Layers'] = [];

		// Integer. Default tilt when using Google Maps.
		$GLOBALS['egMapsGMaps3DefaultTilt'] = 0;

		// Show points of interest or not.
		$GLOBALS['egMapsShowPOI'] = true;

		// String. Set the language when rendering Google Maps.
		$GLOBALS['egMapsGMaps3Language'] = '';

	// OpenLayers

		// Integer. The default zoom of a map. This value will only be used when the
		// user does not provide one.
		$GLOBALS['egMapsOpenLayersZoom'] = 13;

		// Array of String. The default controls for Open Layers. This value will only
		// be used when the user does not provide one.
		// Available values: layerswitcher, mouseposition, autopanzoom, panzoom,
		// panzoombar, scaleline, navigation, keyboarddefaults, overviewmap, permalink
		$GLOBALS['egMapsOLControls'] = [
			'layerswitcher',
			'mouseposition',
			'autopanzoom',
			'scaleline',
			'navigation'
		];

		// Array of String. The default layers for Open Layers. This value will only be
		// used when the user does not provide one.
		$GLOBALS['egMapsOLLayers'] = [
			'osm-mapnik',
			'osm-cyclemap'
		];

		// The definitions for the layers that should be available for the user.
		$GLOBALS['egMapsOLAvailableLayers'] = [
			'bing-normal'    => [ 'OpenLayers.Layer.VirtualEarth( "Bing Streets", {type: VEMapStyle.Shaded, "sphericalMercator":true} )', 'bing' ],
			'bing-satellite' => [ 'OpenLayers.Layer.VirtualEarth( "Bing Satellite", {type: VEMapStyle.Aerial, "sphericalMercator":true} )', 'bing' ],
			'bing-hybrid'    => [ 'OpenLayers.Layer.VirtualEarth( "Bing Hybrid", {type: VEMapStyle.Hybrid, "sphericalMercator":true} )', 'bing' ],

			'osm-mapnik'   => [ 'OpenLayers.Layer.OSM.Mapnik("OSM Mapnik")', 'osm' ],
			'osm-cyclemap' => [ 'OpenLayers.Layer.OSM.CycleMap("OSM Cycle Map")', 'osm' ],
			'osm-mapquest' => [ 'OpenLayers.Layer.OSM.Mapquest("Mapquest OSM")', 'osm' ],

			'google-normal'    => [ 'OpenLayers.Layer.Google("Google Streets", {type: google.maps.MapTypeId.STREETS, numZoomLevels: 20})', 'google' ],
			'google-satellite' => [ 'OpenLayers.Layer.Google("Google Satellite", {type: google.maps.MapTypeId.SATELLITE, numZoomLevels: 22})', 'google' ],
			'google-hybrid'    => [ 'OpenLayers.Layer.Google("Google Hybrid", {type: google.maps.MapTypeId.HYBRID, numZoomLevels: 20})', 'google' ],
			'google-terrain'  => [ 'OpenLayers.Layer.Google("Google Terrain", {type: google.maps.MapTypeId.TERRAIN, numZoomLevels: 22})', 'google' ],

			'nasa' => 'OpenLayers.Layer.WMS("NASA Global Mosaic", "http://t1.hypercube.telascience.org/cgi-bin/landsat7",
				{layers: "landsat7", "sphericalMercator":true} )',
		];

		// Layer group definitions. Group names must be different from layer names, and
		// must only contain layers that are present in $GLOBALS['egMapsOLAvailableLayers'].
		$GLOBALS['egMapsOLLayerGroups'] = [
			'bing' => [ 'bing-normal', 'bing-satellite', 'bing-hybrid' ],
			'google' => [ 'google-normal', 'google-satellite', 'google-terrain', 'google-hybrid' ],
			'osm' => [ 'osm-mapnik', 'osm-cyclemap' ],
		];

		global $wgJsMimeType;

		// Layer dependencies
		$GLOBALS['egMapsOLLayerDependencies'] = [
			'bing' => "<script type='$wgJsMimeType' src='http://dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6.1'></script>",
			'ol-wms' => "<script type='$wgJsMimeType' src='http://clients.multimap.com/API/maps/1.1/metacarta_04'></script>",
			'google' => "<script src='http://maps.google.com/maps/api/js'></script>",
		];


	// Leaflet

		// Integer. The default zoom of a map. This value will only be used when the
		// user does not provide one.
		$GLOBALS['egMapsLeafletZoom'] = 14;

		// String. The default layer for Leaflet. This value will only be
		// used when the user does not provide one.
		$GLOBALS['egMapsLeafletLayer'] = 'OpenStreetMap';

		$GLOBALS['egMapsLeafletLayers'] = [ $GLOBALS['egMapsLeafletLayer'] ];

		$GLOBALS['egMapsLeafletOverlayLayers'] = [

		];

		// The definitions for the layers that should be available for the user.
		$GLOBALS['egMapsLeafletAvailableLayers'] = [
			'OpenStreetMap' => true,
			'OpenStreetMap.DE' => true,
			'OpenStreetMap.BlackAndWhite' => true,
			'OpenStreetMap.HOT' => true,
			'OpenTopoMap' => true,
			'Thunderforest.OpenCycleMap' => true,
			'Thunderforest.Transport' => true,
			'Thunderforest.TransportDark' => true,
			'Thunderforest.SpinalMap' => true,
			'Thunderforest.Landscape' => true,
			'Thunderforest.Outdoors' => true,
			'Thunderforest.Pioneer' => true,
			'OpenMapSurfer.Roads' => true,
			'OpenMapSurfer.Grayscale' => true,
			'Hydda.Full' => true,
			'Hydda.Base' => true,
			//'MapBox' => false, // todo: implement setting api key
			'Stamen.Toner' => true,
			'Stamen.TonerBackground' => true,
			'Stamen.TonerHybrid' => true,
			'Stamen.TonerLines' => true,
			'Stamen.TonerLabels' => true,
			'Stamen.TonerLite' => true,
			'Stamen.Watercolor' => true,
			'Stamen.Terrain' => true,
			'Stamen.TerrainBackground' => true,
			'Stamen.TopOSMRelief' => true,
			'Stamen.TopOSMFeatures' => true,
			'Esri.WorldStreetMap' => true,
			'Esri.DeLorme' => true,
			'Esri.WorldTopoMap' => true,
			'Esri.WorldImagery' => true,
			'Esri.WorldTerrain' => true,
			'Esri.WorldShadedRelief' => true,
			'Esri.WorldPhysical' => true,
			'Esri.OceanBasemap' => true,
			'Esri.NatGeoWorldMap' => true,
			'Esri.WorldGrayCanvas' => true,
			'MapQuestOpen' => true,
			//'HERE' => false, // todo: implement setting api key
			'FreeMapSK' => true,
			'MtbMap' => true,
			'CartoDB.Positron' => true,
			'CartoDB.PositronNoLabels' => true,
			'CartoDB.PositronOnlyLabels' => true,
			'CartoDB.DarkMatter' => true,
			'CartoDB.DarkMatterNoLabels' => true,
			'CartoDB.DarkMatterOnlyLabels' => true,
			'HikeBike.HikeBike' => true,
			'HikeBike.HillShading' => true,
			'BasemapAT.basemap' => true,
			'BasemapAT.grau' => true,
			'BasemapAT.overlay' => true,
			'BasemapAT.highdpi' => true,
			'BasemapAT.orthofoto' => true,
			'NASAGIBS.ModisTerraTrueColorCR' => true,
			'NASAGIBS.ModisTerraBands367CR' => true,
			'NASAGIBS.ViirsEarthAtNight2012' => true,
			'NLS' => true,
			'GeoportailFrance' => true,
			'GeoportailFrance.parcels' => true,
			'GeoportailFrance.ignMaps' => true,
			'GeoportailFrance.orthos' => true
		];

		$GLOBALS['egMapsLeafletAvailableOverlayLayers'] = [
			'OpenMapSurfer.AdminBounds' => true,
			'OpenSeaMap' => true,
			'OpenWeatherMap.Clouds' => true,
			'OpenWeatherMap.CloudsClassic' => true,
			'OpenWeatherMap.Precipitation' => true,
			'OpenWeatherMap.PrecipitationClassic' => true,
			'OpenWeatherMap.Rain' => true,
			'OpenWeatherMap.RainClassic' => true,
			'OpenWeatherMap.Pressure' => true,
			'OpenWeatherMap.PressureContour' => true,
			'OpenWeatherMap.Wind' => true,
			'OpenWeatherMap.Temperature' => true,
			'OpenWeatherMap.Snow' => true,
			'Hydda.RoadsAndLabels' => true,
			'NASAGIBS.ModisTerraLSTDay' => true,
			'NASAGIBS.ModisTerraSnowCover' => true,
			'NASAGIBS.ModisTerraAOD' => true,
			'NASAGIBS.ModisTerraChlorophyll' => true
		];

		$GLOBALS['egMapsLeafletLayersApiKeys'] = [
			'MapBox' => '',
			'MapQuestOpen' => '',
			'Thunderforest' => '',
			'GeoportailFrance' => ''
		];

		// Layer dependencies
		$GLOBALS['egMapsLeafletLayerDependencies'] = [
			'MapQuestOpen' => 'https://open.mapquestapi.com/sdk/leaflet/v2.2/mq-map.js?key=',
		];


	$GLOBALS['egMapsGlobalJSVars'] = [];

if ( !defined( 'Maps_SETTINGS_LOADED' ) ) {
	define( 'Maps_SETTINGS_LOADED', true );
}
<?php

return [
	// Mapping services that will be available in the wiki.
	// These can be used in #display_map with service=leaflet or in #ask with format=leaflet
	'egMapsAvailableServices' => [
		'leaflet',
		'googlemaps3'
	],

	// The mapping service that will be used when no service is specified by the user.
	'egMapsDefaultService' => 'leaflet',

	// Allows disabling the extension even when it is installed.
	// CAUTION: this setting is intended for wiki farms. On single wiki installations,
	//          the recommended way to disable maps is to uninstall it via Composer. Disabling
	//          Maps via this setting undermines package management safety: extensions that depend
	//          on Maps will likely either break or disable themselves.
	'egMapsDisableExtension' => false,

	// Allows disabling the Semantic MediaWiki integration.
	'egMapsDisableSmwIntegration' => false,





	/**
	 * GENERAL MAP CONFIGURATION
	 */

	// Integer or string. The default width and height of a map. These values will
	// only be used when the user does not provide them.
	'egMapsMapWidth' => 'auto',
	'egMapsMapHeight' => 350,

	// Array. The minimum and maximum width and height for all maps. First min and
	// max for absolute values, then min and max for percentage values. When the
	// height or width exceed their limits, they will be changed to the closest
	// allowed value.
	'egMapsSizeRestrictions' => [
		'width'  => [ 50, 1020, 1, 100 ],
		'height' => [ 50, 1000, 1, 100 ],
	],

	// Strings. The default content for all pop-ups. This value will only be used
	// when the user does not provide one.
	'egMapsDefaultTitle' => '',
	'egMapsDefaultLabel' => '',

	'egMapsResizableByDefault' => false,
	'egMapsRezoomForKML' => false,

	// Boolean. Sets if pages with maps should be put in special category
	'egMapsEnableCategory' => false,

	// Integer. Determines the TTL of cached GeoJson.
	// Default value: 0 (no caching).
	'egMapsGeoJsonCacheTtl' => 0,





	/**
	 * SEMANTIC MEDIAWIKI INTEGRATION
	 */

	// Boolean. The default value for the showtitle parameter. Will hide the title in the marker pop-ups when set to false.
	// This value will only be used when the user does not provide one.
	'smgQPShowTitle' => true,

	// Boolean. The default value for the hidenamespace parameter. Will hide the namespace in the marker pop-ups when set to true.
	// This value will only be used when the user does not provide one.
	'smgQPHideNamespace' => false,

	// String or false. Allows you to define the content and it's layout of marker pop-ups via a template.
	// This value will only be used when the user does not provide one.
	'smgQPTemplate' => false,





	/**
	 * COORDINATE CONFIGURATION
	 */

	// The coordinate notations that should be available.
	'egMapsAvailableCoordNotations' => [
		'float',
		'dms',
		'dm',
		'dd'
	],

	// The default output format of coordinates.
	// Possible values: float, dms, dm, dd
	'egMapsCoordinateNotation' => 'dms',

	// Boolean. Indicates if coordinates should be outputted in directional notation by default.
	// Recommended to be true for dms and false for float.
	'egMapsCoordinateDirectional' => true,

	// The default output format of coordinates when displayed by Semantic MediaWiki.
	// Possible values: float, dms, dm, dd
	'smgQPCoodFormat' => 'dms',

	// Boolean. Indicates if coordinates should be outputted in directional notation by default when
	// displayed by Semantic MediaWiki.
	'smgQPCoodDirectional' => true,

	// Boolean. Sets if direction labels should be translated to their equivalent in the wiki language or not.
	'egMapsInternatDirectionLabels' => true,

	// Boolean. When false, the #coordinates parser function will not be enabled.
	// This is useful for people using the GeoData extension and want to use its #coordinates function instead.
	'egMapsEnableCoordinateFunction' => true,





	/**
	 * GEOCODING CONFIGURATION
	 */

	// Sets which service should be used to turn addresses into coordinates
	// Available services: geonames, google, nominatim
	// The geonames service requires you to specify a geonames user (see below),
	// if you set this setting to geonames but do not specify the user, Maps will
	// fall back to using the google service.
	'egMapsDefaultGeoService' => 'nominatim',

	// String. GeoNames API user/application name.
	// Obtain an account here: http://www.geonames.org/login
	// Do not forget to activate your account for API usage!
	'egMapsGeoNamesUser' => '',

	// Boolean. Sets if geocoded addresses should be stored in a cache.
	'egMapsEnableGeoCache' => true,

	// Integer. If egMapsEnableGeoCache is true, determines the TTL of cached geocoded addresses.
	// Default value: 1 day.
	'egMapsGeoCacheTtl' => 24 * 3600,





	/**
	 * LEAFLET CONFIGURATION
	 */

	// Integer. The default zoom of a map. This value will only be used when the
	// user does not provide one.
	'egMapsLeafletZoom' => 14,

	// String. The default layer for Leaflet. This value will only be
	// used when the user does not provide one.
	'egMapsLeafletLayer' => 'OpenStreetMap',
	'egMapsLeafletLayers' => [ 'OpenStreetMap' ],

	'egMapsLeafletOverlayLayers' => [],

	// The definitions for the layers that should be available for the user.
	'egMapsLeafletAvailableLayers' => [
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
	],

	'egMapsLeafletAvailableOverlayLayers' => [
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
	],

	'egMapsLeafletLayersApiKeys' => [
		'MapBox' => '',
		'MapQuestOpen' => '',
		'Thunderforest' => '',
		'GeoportailFrance' => ''
	],

	'egMapsLeafletLayerDependencies' => [
		'MapQuestOpen' => 'https://open.mapquestapi.com/sdk/leaflet/v2.2/mq-map.js?key=',
	],





	/**
	 * GOOGLE MAPS CONFIGURATION
	 */

	// String. Google Maps v3 API Key
	'egMapsGMaps3ApiKey' => '',

	// String. Google Maps v3 API version number
	'egMapsGMaps3ApiVersion' => '',

	// Integer. The default zoom of a map. This value will only be used when the
	// user does not provide one.
	'egMapsGMaps3Zoom' => 14,

	// Array of String. The Google Maps v3 default map types. This value will only
	// be used when the user does not provide one.
	'egMapsGMaps3Types' => [
		'roadmap',
		'satellite',
		'hybrid',
		'terrain'
	],

	// String. The default map type. This value will only be used when the user
	// does not provide one.
	'egMapsGMaps3Type' => 'roadmap',

	// Array. List of controls to display onto maps by default.
	'egMapsGMaps3Controls' => [
		'pan',
		'zoom',
		'type',
		'scale',
		'streetview',
		'rotate'
	],

	// String. The default style for the type control.
	// horizontal, vertical or default
	'egMapsGMaps3DefTypeStyle' => 'default',

	// String. The default style for the zoom control.
	// small, large or default
	'egMapsGMaps3DefZoomStyle' => 'default',

	// Boolean. Open the info windows on load by default?
	'egMapsGMaps3AutoInfoWindows' => false,

	// Array. Layers to load by default.
	// traffic, bicycling and transit
	'egMapsGMaps3Layers' => [],

	// Show points of interest or not.
	'egMapsShowPOI' => true,

	// String. Set the language when rendering Google Maps.
	'egMapsGMaps3Language' => '',





	/**
	 * DISTANCE CONFIGURATION
	 */

	// Array. A list of units (keys) and how many meters they represent (value).
	// No spaces! If the unit consists out of multiple words, just write them together.
	'egMapsDistanceUnits' => [
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
	],

	// String. The default unit for distances.
	'egMapsDistanceUnit' => 'm',

	// Integer. The default amount of fractal digits in a distance.
	'egMapsDistanceDecimals' => 2,





	/**
	 * DEBUGGING
	 */

	// When true, debugging messages will be logged using mw.log(). Do not use on production wikis.
	'egMapsDebugJS' => false,

	'egMapsGlobalJSVars' => [],
];
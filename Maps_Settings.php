<?php

/**
 * File defining the settings for the Maps extension
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
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

# API keys configuration

# Your Google Maps API key. Required for displaying Google Maps, and using the Google Geocoder services.
# Haven't got an API key yet? Get it here: http://code.google.com/apis/maps/signup.html
if (empty($egGoogleMapsKey)) $egGoogleMapsKey = ""; 

# Your Yahoo! Maps API key. Required for displaying Yahoo! Maps.
# Haven't got an API key yet? Get it here: https://developer.yahoo.com/wsregapp/
if (empty($egYahooMapsKey)) $egYahooMapsKey = ""; 





# Map services configuration

# Array of String. Array containing all the mapping services that will be made available to the user.
# Currently Maps provides the following services: googlemaps, yahoomaps, openlayers
$egMapsAvailableServices = array('googlemaps', 'yahoomaps', 'openlayers');

# String. The default mapping service, which will be used when no service is provided by the user.
# This service needs to be enabled, if not, the first one from the available services will be taken.
$egMapsDefaultService = 'googlemaps';





# Geocoding services configuration

# Array of String. Array containing all the geocoding services that will be made available to the user.
# Currently Maps provides the following services: googlemaps, yahoomaps, openlayers
$egMapsAvailableGeoServices = array('google', 'yahoo');

# String. The default geocoding service, which will be used when no service is provided by the user.
# This service needs to be enabled, if not, the first one from the available services will be taken.
$egMapsDefaultGeoService = 'google';





# General map properties configuration

# Integer. The default width and height of a map. These values will only be used when the user does not provide them.
$egMapsMapWidth = 600;
$egMapsMapHeight = 350;

# Strings. The default coordinates of the marker. This value will only be used when the user does not provide one.
$egMapsMapLat = '1';
$egMapsMapLon = '1';





# Specific map properties configuration

# Google maps

# String. The Google Maps map name prefix. It can not be identical to the one of another mapping service.
$egMapsGoogleMapsPrefix = 'map_google';

# Integer. The default zoom of a map. This value will only be used when the user does not provide one.
$egMapsGoogleMapsZoom = 14;

# Array of String. The Google Maps default map types. This value will only be used when the user does not provide one.
$egMapsGoogleMapsTypes = array('normal', 'satellite', 'hybrid', 'physical');

# String. The default map type. This value will only be used when the user does not provide one.
$egMapsGoogleMapsType = 'normal';

# Boolean. The default value for enabling or disabling the autozoom of a map.
# This value will only be used when the user does not provide one.
$egMapsGoogleAutozoom = true;

# Array of String. The default controls for Google Maps. This value will only be used when the user does not provide one.
# Available values: large, small, large-original, small-original, zoom, type, type-menu, overview-map, scale, nav-label
$egMapsGMapControls = array('large', 'scale', 'type');



# Yahoo maps

# String. The Yahoo! maps map name prefix. It can not be identical to the one of another mapping service.
$egMapsYahooMapsPrefix = 'map_yahoo';

# Array of String. The Google Maps default map types. This value will only be used when the user does not provide one.
$egMapsYahooMapsTypes = array('normal', 'satellite', 'hybrid');

# String. The default map type. This value will only be used when the user does not provide one.
$egMapsYahooMapsType = 'normal';

# Integer. The default zoom of a map. This value will only be used when the user does not provide one.
$egMapsYahooMapsZoom = 4;

# Boolean. The default value for enabling or disabling the autozoom of a map.
# This value will only be used when the user does not provide one.
$egMapsYahooAutozoom = true;

# Array of String. The default controls for Yahoo! Maps. This value will only be used when the user does not provide one.
# Available values: type, pan, zoom, zoom-short
$egMapsYMapControls = array('type', 'pan', 'zoom');



# OpenLayers

# String. The OpenLayers map name prefix. It can not be identical to the one of another mapping service.
$egMapsOpenLayersPrefix = 'open_layer';

# Integer. The default zoom of a map. This value will only be used when the user does not provide one.
$egMapsOpenLayersZoom = 10;

# Array of String. The default controls for Open Layers. This value will only be used when the user does not provide one.
# Available values: layerswitcher, mouseposition, panzoom, panzoombar, scaleline, navigation, keyboarddefaults, overviewmap, permalink
# Note: panzoom and panzoombar can NOT be used together
$egMapsOLControls = array('layerswitcher', 'mouseposition', 'panzoombar', 'scaleline', 'navigation');

# Array of String. The default layers for Open Layers. This value will only be used when the user does not provide one.
# Available values: google, bing, yahoo, openlayers, nasa
$egMapsOLLayers = array('openlayers');
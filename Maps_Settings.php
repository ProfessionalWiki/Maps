<?php

/**
 * File defining the settings for the Maps extension
 * More info can be found at http://www.mediawiki.org/wiki/Extension:Maps#Settings
 *
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
$egGoogleMapsKey = ""; 

# Your Yahoo! Maps API key. Required for displaying Yahoo! Maps.
# Haven't got an API key yet? Get it here: https://developer.yahoo.com/wsregapp/
$egYahooMapsKey = ""; 



# Map services configuration

# Array of String. Array containing all the services that will be made available to the user.
# Currently Maps provides the following services: googlemaps, yahoomaps, openlayers
$egMapsAvailableServices = array('googlemaps', 'yahoomaps', 'openlayers');

# String. The default mapping service, which will be used when no service is provided by the user.
# This service needs to be enabled, if not, the first one from the available services will be taken.
$egMapsDefaultService = 'googlemaps';



# General map properties configuration

# Integer. The default width and height of a map. These values will only be used when the user does not provide them.
$egMapsMapWidth = 600;
$egMapsMapHeight = 350;

# String. The default coordinates of the marker. This value will only be used when the user does not provide one.
$egMapsMapCoordinates = '1,1';



# Specific map properties configuration

# Integer. The default zoom of a map. This value will only be used when the user does not provide one.
$egMapsGoogleMapsZoom = 14; # Google Maps
$egMapsYahooMapsZoom = 4; # Yahoo! Maps
$egMapsOpenLayersZoom = 10; # OpenLayers

# Boolean. The default value for enabling or disabling the autozoom of a map.
# This is for Google Maps and Yahoo! Maps only. This value will only be used when the user does not provide one.
$egMapsEnableAutozoom = true;

# Boolean. The default value for enabling or disabling the earth map type for Google Maps.
# This value will only be used when the user does not provide one.
$egMapsEnableEarth = false;

# String. The default control for Google Maps. This value will only be used when the user does not provide one.
# Available short values: large, small. Other values: http://code.google.com/apis/maps/documentation/controls.html#Controls_overview
$egMapsGMapControl = 'large';

# Array of String. The default controls for Yahoo! Maps. This value will only be used when the user does not provide one.
# Available values: pan, zoom
$egMapsYMapControls = array('pan', 'zoom');

# Array of String. The default controls for Open Layers. This value will only be used when the user does not provide one.
# Available values: layerswitcher, mouseposition, panzoom, panzoombar, scaleline, navigation, keyboarddefaults, overviewmap, permalink
# Note: panzoom and panzoombar can NOT be used together
$egMapsOLControls = array('layerswitcher', 'mouseposition', 'panzoombar', 'scaleline', 'navigation');

# Array of String. The default layers for Open Layers. This value will only be used when the user does not provide one.
# Available values: google, bing, yahoo, openlayers, nasa
$egMapsOLLayers = array('openlayers');



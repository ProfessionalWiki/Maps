<?php

/**
 * File defining the settings for the Semantic Maps extension
 * More info can be found at http://www.mediawiki.org/wiki/Extension:Semantic_Maps#Settings
 *
 *                          NOTICE:
 * Changing one of these settings can be done by copieng or cutting it, 
 * and placing it in LocalSettings.php, AFTER the inclusion of Semantic Maps.
 *
 * @file SM_Settings.php
 * @ingroup SemanticMaps
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}




# Map features configuration
# (named) Array of String. This array contains the available features for Maps.
# The array element name contains an abbriviation, used for code references,
# and in the service data arrays, the value is the human readible version for displaying purpouses.
$egMapsAvailableFeatures['qp'] = array(
							'name' => 'Query Printer',
							'class' => 'SMQueryPrinters',
							'file' => 'SemanticMaps/QueryPrinters/SM_QueryPrinters.php',
							'local' => false
							);

$egMapsAvailableFeatures['fi'] = array(
							'name' => 'Form input',
							'class' => 'SMFormInputs',
							'file' => 'SemanticMaps/FormInputs/SM_FormInputs.php',
							'local' => false
							);		





# Mapping services configuration

# Include the mapping services that should be loaded into Semantic Maps. 
# Commenting or removing a mapping service will cause Semantic Maps to completely ignore it, and so improve performance.
include_once $smgIP . '/GoogleMaps/SM_GoogleMaps.php'; 	// Google Maps
include_once $smgIP . '/OpenLayers/SM_OpenLayers.php'; 	// OpenLayers
include_once $smgIP . '/YahooMaps/SM_YahooMaps.php'; 	// Yahoo! Maps
include_once $smgIP . '/OpenStreetMap/SM_OSM.php'; 		// OpenLayers optimized for OSM





# Array of String. The default mapping service for each feature, which will be used when no valid service is provided by the user.
# Each service needs to be enabled, if not, the first one from the available services will be taken.
# Note: The default service needs to be available for the feature you set it for, since it's used as a fallback mechanism.
$egMapsDefaultServices['qp'] = 'googlemaps';
$egMapsDefaultServices['fi'] = 'googlemaps';

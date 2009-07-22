<?php

/**
 * Initialization file for the Maps extension.
 * Extension documentation: http://www.mediawiki.org/wiki/Extension:Maps
 *
 * @file Maps.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

define('Maps_VERSION', '0.1');

$egMapsScriptPath = $wgScriptPath . '/extensions/Maps';
$egMapsIP = $IP . '/extensions/Maps';
$egMapsIncludePath = 'http://' . $_SERVER["HTTP_HOST"] . $egMapsScriptPath;

// Include the settings file
require_once($egMapsIP . '/Maps_Settings.php');

$wgExtensionFunctions[] = 'efMapsSetup';

$wgExtensionMessagesFiles['Maps'] = $egMapsIP . '/Maps.i18n.php';

$wgHooks['LanguageGetMagic'][] = 'efMapsFunctionMagic';

// Autoload the general classes
$wgAutoloadClasses['MapsBaseMap'] = $egMapsIP . '/Maps_BaseMap.php';
$wgAutoloadClasses['MapsMapper'] = $egMapsIP . '/Maps_Mapper.php';
$wgAutoloadClasses['MapsUtils'] = $egMapsIP . '/Maps_Utils.php';
$wgAutoloadClasses['MapsGeocoder'] = $egMapsIP . '/Maps_Geocoder.php';

// Array containing all map services made available by Maps.
// This does not reflect the enabled mapping services, see $egMapsAvailableServices in Maps_Settings.php for this.
$egMapsServices = array('googlemaps', 'openlayers', 'yahoomaps');

function efMapsSetup() {
	global $egMapsDefaultService, $egMapsAvailableServices, $egMapsServices, $egMapsScriptPath;
	global $wgExtensionCredits, $wgParser, $wgExtensionCredits, $wgOut;

	$egMapsDefaultService = in_array($egMapsDefaultService, $egMapsAvailableServices) ? $egMapsDefaultService : $egMapsAvailableServices[0];
	
	foreach($egMapsServices as $fn) efMapsInitFormat($fn);
	$services_list = implode(', ', $egMapsServices);

	wfLoadExtensionMessages( 'Maps' );
	
	$wgExtensionCredits['parserhook'][]= array(
		'name' => wfMsg('maps_name'),
		'version' => Maps_VERSION,
		'author' => array("[http://bn2vs.com Jeroen De Dauw]", "[http://www.mediawiki.org/wiki/User:Yaron_Koren Yaron Koren]", "Robert Buzink", "Matt Williamson", "[http://www.sergeychernyshev.com Sergey Chernyshev]"),
		'url' => 'http://www.mediawiki.org/wiki/Extension:Maps',
		'description' =>  wfMsg('maps_desc') . $services_list
	);

	// A hook to enable the '#display_point' parser function
	$wgParser->setFunctionHook( 'display_point', array('MapsMapper', 'displayPointRender' ));

	// A hook to enable the '#display_adress' parser function
	$wgParser->setFunctionHook( 'display_address', array('MapsMapper', 'displayAddressRender' ));

	// A hook to enable the geocoder parser functions
	$wgParser->setFunctionHook( 'geocode', array('MapsGeocoder', 'renderGeocoder' ));
	$wgParser->setFunctionHook( 'geocodelat' , array('MapsGeocoder', 'renderGeocoderLat' ));
	$wgParser->setFunctionHook( 'geocodelng' , array('MapsGeocoder', 'renderGeocoderLng' ));
	
	$wgOut->addScriptFile($egMapsScriptPath . '/MapUtilityFunctions.js');
}

/**
 * Initialize the result format depending on the map service
 */
function efMapsInitFormat( $format ) {
	global $wgAutoloadClasses, $egMapsIP;

	switch ($format) {
		case 'googlemaps':
			$class = 'MapsGoogleMaps';
			$file = $egMapsIP . '/GoogleMaps/Maps_GoogleMaps';
		break;
		case 'openlayers':
			$class = 'MapsOpenLayers';
			$file = $egMapsIP . '/OpenLayers/Maps_OpenLayers';
		break;
		case 'yahoomaps':
			$class = 'MapsYahooMaps';
			$file = $egMapsIP . '/YahooMaps/Maps_YahooMaps';
		break;
	}

	if (isset($class) && isset($file)) {
		$wgAutoloadClasses[$class] = $file . '.php';
	}

}

/**
 * Add the magic words for the parser functions
 */
function efMapsFunctionMagic( &$magicWords, $langCode ) {
	$magicWords['display_point'] = array( 0, 'display_point' );
	$magicWords['display_address'] = array( 0, 'display_address' );

	$magicWords['geocode'] = array( 0, 'geocode' );
	$magicWords['geocodelat']	= array ( 0, 'geocodelat' );
	$magicWords['geocodelng']	= array ( 0, 'geocodelng' );
	
	return true; // Unless we return true, other parser functions won't get loaded
}


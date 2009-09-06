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

/**
 * This documenation group collects source code files belonging to Maps.
 *
 * Please do not use this group name for other code. If you have an extension to 
 * Maps, please use your own group defenition.
 * 
 * @defgroup Maps Maps
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

define('Maps_VERSION', '0.3.4');

$egMapsScriptPath 	= $wgScriptPath . '/extensions/Maps';
$egMapsIP 			= $IP . '/extensions/Maps';
$egMapsIncludePath 	= $wgServer . $egMapsScriptPath;

// Include the settings file
require_once($egMapsIP . '/Maps_Settings.php');

// Add the extensions initializing function
if ( defined( 'MW_SUPPORTS_PARSERFIRSTCALLINIT' ) ) {
	$wgHooks['ParserFirstCallInit'][] = 'efMapsSetup';
} else {
	$wgExtensionFunctions[] = 'efMapsSetup'; // Legacy support
}

$wgExtensionMessagesFiles['Maps'] = $egMapsIP . '/Maps.i18n.php';

$wgHooks['LanguageGetMagic'][] = 'efMapsFunctionMagic';
$wgHooks['AdminLinks'][] = 'efMapsAddToAdminLinks';

// Autoload the general classes
$wgAutoloadClasses['MapsMapFeature'] 		= $egMapsIP . '/Maps_MapFeature.php';
$wgAutoloadClasses['MapsBaseMap'] 			= $egMapsIP . '/Maps_BaseMap.php';
$wgAutoloadClasses['MapsMapper'] 			= $egMapsIP . '/Maps_Mapper.php';
$wgAutoloadClasses['MapsParserFunctions'] 	= $egMapsIP . '/Maps_ParserFunctions.php';
$wgAutoloadClasses['MapsUtils'] 			= $egMapsIP . '/Maps_Utils.php';
$wgAutoloadClasses['MapsGeocoder'] 			= $egMapsIP . '/Maps_Geocoder.php';
$wgAutoloadClasses['MapsBaseGeocoder'] 		= $egMapsIP . '/Maps_BaseGeocoder.php';

if (empty($egMapsServices)) $egMapsServices = array();

$egMapsServices['googlemaps'] = array(
									'pf' => array('class' => 'MapsGoogleMaps', 'file' => 'GoogleMaps/Maps_GoogleMaps.php', 'local' => true),
									'classes' => array(
											array('class' => 'MapsGoogleMapsUtils', 'file' => 'GoogleMaps/Maps_GoogleMapsUtils.php', 'local' => true)
											),
									'aliases' => array('google', 'googlemap', 'gmap', 'gmaps'),
									'parameters' => array(
											'type' => array('map-type', 'map type'),
											'types' => array('map-types', 'map types'),
											'earth' => array(),
											'autozoom' => array('auto zoom', 'mouse zoom', 'mousezoom'),
											'class' => array(),
											'style' => array()										
											)
									);
									
$egMapsServices['openlayers'] = array(
									'pf' => array('class' => 'MapsOpenLayers', 'file' => 'OpenLayers/Maps_OpenLayers.php', 'local' => true),
									'classes' => array(
											array('class' => 'MapsOpenLayersUtils', 'file' => 'OpenLayers/Maps_OpenLayersUtils.php', 'local' => true)
											),
									'aliases' => array('layers', 'openlayer'),
									'parameters' => array(
											'layers' => array(),
											'baselayer' => array()
											)
									);
									
$egMapsServices['yahoomaps'] = array(
									'pf' => array('class' => 'MapsYahooMaps', 'file' => 'YahooMaps/Maps_YahooMaps.php', 'local' => true),
									'classes' => array(
											array('class' => 'MapsYahooMapsUtils', 'file' => 'YahooMaps/Maps_YahooMapsUtils.php', 'local' => true)
											),
									'aliases' => array('yahoo', 'yahoomap', 'ymap', 'ymaps'),
									'parameters' => array(
											'type' => array('map-type'),
											'types' => array('map-types', 'map types'),
											'autozoom' => array('auto zoom', 'mouse zoom', 'mousezoom')
											)
									);

/**
 * Initialization function for the Maps extension
 */
function efMapsSetup() {
	global $wgExtensionCredits, $wgOut, $wgLang, $wgAutoloadClasses, $IP;	
	global $egMapsDefaultService, $egMapsAvailableServices, $egMapsServices, $egMapsScriptPath, $egMapsDefaultGeoService, $egMapsAvailableGeoServices, $egMapsIP;

	efMapsValidateGoogleMapsKey();
	
	// Make sure the default service is one of the enabled ones
	$egMapsDefaultService = in_array($egMapsDefaultService, $egMapsAvailableServices) ? $egMapsDefaultService : $egMapsAvailableServices[0];
	$egMapsDefaultGeoService = in_array($egMapsDefaultGeoService, $egMapsAvailableGeoServices) ? $egMapsDefaultGeoService : $egMapsAvailableGeoServices[0];
	
	wfLoadExtensionMessages( 'Maps' );
	
	// Creation of a list of internationalized service names
	$services = array();
	foreach (array_keys($egMapsServices) as $name) $services[] = wfMsg('maps_'.$name);
	$services_list = $wgLang->listToText($services);
	
	$wgExtensionCredits['parserhook'][] = array(
		'path' => __FILE__,
		'name' => wfMsg('maps_name'),
		'version' => Maps_VERSION,
		'author' => array('[http://bn2vs.com Jeroen De Dauw]', '[http://www.mediawiki.org/wiki/User:Yaron_Koren Yaron Koren]', 'Robert Buzink', 'Matt Williamson', '[http://www.sergeychernyshev.com Sergey Chernyshev]'),
		'url' => 'http://www.mediawiki.org/wiki/Extension:Maps',
		'description' =>  wfMsgExt( 'maps_desc', 'parsemag', $services_list ),
		'descriptionmsg' => wfMsgExt( 'maps_desc', 'parsemag', $services_list ),
	);

	efMapsAddParserHooks();
	
	$wgOut->addScriptFile($egMapsScriptPath . '/MapUtilityFunctions.js');
	
	foreach ($egMapsServices as  $serviceData) {
		if (array_key_exists('pf', $serviceData)) {
			$file = $serviceData['pf']['local'] ? $egMapsIP . '/' . $serviceData['pf']['file'] : $IP . '/extensions/' . $serviceData['pf']['file'];
			$wgAutoloadClasses[$serviceData['pf']['class']] = $file;			
		}
		
		if (array_key_exists('classes', $serviceData)) {
			foreach($serviceData['classes'] as $class) {
				$file = $class['local'] ? $egMapsIP . '/' . $class['file'] : $IP . '/extensions/' . $class['file'];
				$wgAutoloadClasses[$class['class']] = $file;
			}
		}
		
	}
	
	return true;
}

/**
 * Adds the parser function hooks
 */
function efMapsAddParserHooks() {
	global $wgParser;
	
	// A hooks to enable the '#display_point' and '#display_points' parser functions
	$wgParser->setFunctionHook( 'display_point', array('MapsParserFunctions', 'displayPointRender') );
	$wgParser->setFunctionHook( 'display_points', array('MapsParserFunctions', 'displayPointsRender') );

	// A hooks to enable the '#display_adress' and '#display_adresses' parser functions
	$wgParser->setFunctionHook( 'display_address', array('MapsParserFunctions', 'displayAddressRender') );
	$wgParser->setFunctionHook( 'display_addresses', array('MapsParserFunctions', 'displayAddressesRender') );

	// A hook to enable the geocoder parser functions
	$wgParser->setFunctionHook( 'geocode', array('MapsGeocoder', 'renderGeocoder') );
	$wgParser->setFunctionHook( 'geocodelat' , array('MapsGeocoder', 'renderGeocoderLat') );
	$wgParser->setFunctionHook( 'geocodelng' , array('MapsGeocoder', 'renderGeocoderLng') );
}

/**
 * Adds the magic words for the parser functions
 */
function efMapsFunctionMagic( &$magicWords, $langCode ) {
	$magicWords['display_point'] = array( 0, 'display_point' );
	$magicWords['display_points'] = array( 0, 'display_points' );
	$magicWords['display_address'] = array( 0, 'display_address' );
	$magicWords['display_addresses'] = array( 0, 'display_addresses' );

	$magicWords['geocode'] = array( 0, 'geocode' );
	$magicWords['geocodelat']	= array ( 0, 'geocodelat' );
	$magicWords['geocodelng']	= array ( 0, 'geocodelng' );
	
	return true; // Unless we return true, other parser functions won't get loaded
}

/**
 * This function ensures backward compatibility with Semantic Google Maps and other extensions
 * using $wgGoogleMapsKey instead of $egGoogleMapsKey.
 */
function efMapsValidateGoogleMapsKey() {
	global $egGoogleMapsKey, $wgGoogleMapsKey;
	
	if (strlen($egGoogleMapsKey) < 1 && isset($wgGoogleMapsKey)) $egGoogleMapsKey = $wgGoogleMapsKey;
}

/**
 * Adds a link to Admin Links page
 */
function efMapsAddToAdminLinks(&$admin_links_tree) {
	// TODO: move the documentation link to another section - and make it non dependant on SMW?
    $displaying_data_section = $admin_links_tree->getSection(wfMsg('smw_adminlinks_displayingdata'));
    // Escape if SMW hasn't added links
    if (is_null($displaying_data_section))
        return true;
    $smw_docu_row = $displaying_data_section->getRow('smw');
    wfLoadExtensionMessages('Maps');
    $maps_docu_label = wfMsg('adminlinks_documentation', wfMsg('maps_name'));
    $smw_docu_row->addItem(AlItem::newFromExternalLink('http://www.mediawiki.org/wiki/Extension:Maps', $maps_docu_label));
    return true;
}



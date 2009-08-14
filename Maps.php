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

define('Maps_VERSION', '0.4');

$egMapsScriptPath = $wgScriptPath . '/extensions/Maps';
$egMapsIP = $IP . '/extensions/Maps';
$egMapsIncludePath = $wgServer . $egMapsScriptPath;

// Include the settings file
require_once($egMapsIP . '/Maps_Settings.php');

$wgExtensionFunctions[] = 'efMapsSetup';

$wgExtensionMessagesFiles['Maps'] = $egMapsIP . '/Maps.i18n.php';
$wgExtensionMessagesFiles['MapsMagic'] = $egMapsIP . '/Maps.i18n.magic.php';

$wgHooks['AdminLinks'][] = 'efMapsAddToAdminLinks';
$wgHooks['ParserFirstCallInit'][] = 'efMapsAddParserHooks';

// Autoload the general classes
$wgAutoloadClasses['MapsMapFeature'] = $egMapsIP . '/Maps_MapFeature.php';
$wgAutoloadClasses['MapsBaseMap'] = $egMapsIP . '/Maps_BaseMap.php';
$wgAutoloadClasses['MapsMapper'] = $egMapsIP . '/Maps_Mapper.php';
$wgAutoloadClasses['MapsParserFunctions'] = $egMapsIP . '/Maps_ParserFunctions.php';
$wgAutoloadClasses['MapsUtils'] = $egMapsIP . '/Maps_Utils.php';
$wgAutoloadClasses['MapsGeocoder'] = $egMapsIP . '/Maps_Geocoder.php';
$wgAutoloadClasses['MapsBaseGeocoder'] = $egMapsIP . '/Maps_BaseGeocoder.php';

// TODO: document
$egMapsServices = array();

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

$services_list = implode(', ', array_keys($egMapsServices));

$wgExtensionCredits['parserhook'][] = array(
	'path' => __FILE__,
	'name' => wfMsg('maps_name'),
	'version' => Maps_VERSION,
	'author' => array("[http://bn2vs.com Jeroen De Dauw]", "[http://www.mediawiki.org/wiki/User:Yaron_Koren Yaron Koren]", "Robert Buzink", "Matt Williamson", "[http://www.sergeychernyshev.com Sergey Chernyshev]"),
	'url' => 'http://www.mediawiki.org/wiki/Extension:Maps',
	'description' =>  wfMsg( 'maps_desc', $services_list ),
	'descriptionmsg' => wfMsg( 'maps_desc', $services_list ),
);

/**
 * Initialization function for the Maps extension
 */
function efMapsSetup() {
	global $wgOut, $wgAutoloadClasses;
	global $egMapsDefaultService, $egMapsAvailableServices, $egMapsServices, $egMapsScriptPath, $egMapsDefaultGeoService, $egMapsAvailableGeoServices, $egMapsIP;

	efMapsValidateGoogleMapsKey();

	// Make sure the default service is one of the enabled ones
	$egMapsDefaultService = in_array($egMapsDefaultService, $egMapsAvailableServices) ? $egMapsDefaultService : $egMapsAvailableServices[0];
	$egMapsDefaultGeoService = in_array($egMapsDefaultGeoService, $egMapsAvailableGeoServices) ? $egMapsDefaultGeoService : $egMapsAvailableGeoServices[0];

	wfLoadExtensionMessages( 'Maps' );

	$wgOut->addScriptFile($egMapsScriptPath . '/MapUtilityFunctions.js');

	foreach ($egMapsServices as  $serviceData) {
		$file = $serviceData['pf']['local'] ? $egMapsIP . '/' . $serviceData['pf']['file'] : $serviceData['pf']['file'];
		$wgAutoloadClasses[$serviceData['pf']['class']] = $file;

		foreach($serviceData['classes'] as $class) {
			$file = $class['local'] ? $egMapsIP . '/' . $class['file'] : $class['file'];
			$wgAutoloadClasses[$class['class']] = $file;
		}
	}
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
	$smw_docu_row->addItem(AlItem::newFromExternalLink("http://www.mediawiki.org/wiki/Extension:Maps", $maps_docu_label));
	return true;
}

/**
 *  Add the parser function hooks
 */
function efMapsAddParserHooks( $parser ) {
	// Hooks to enable the '#display_point' and '#display_points' parser functions
	$parser->setFunctionHook( 'display_point', array('MapsParserFunctions', 'displayPointRender') );
	$parser->setFunctionHook( 'display_points', array('MapsParserFunctions', 'displayPointsRender') );

	// Hooks to enable the '#display_adress' and '#display_adresses' parser functions
	$parser->setFunctionHook( 'display_address', array('MapsParserFunctions', 'displayAddressRender') );
	$parser->setFunctionHook( 'display_addresses', array('MapsParserFunctions', 'displayAddressesRender') );

	// Hooks to enable the geocoder parser functions
	$parser->setFunctionHook( 'geocode', array('MapsGeocoder', 'renderGeocoder') );
	$parser->setFunctionHook( 'geocodelat' , array('MapsGeocoder', 'renderGeocoderLat') );
	$parser->setFunctionHook( 'geocodelng' , array('MapsGeocoder', 'renderGeocoderLng') );

	return true;
}

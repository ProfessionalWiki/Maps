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

$wgHooks['AdminLinks'][] = 'efMapsAddToAdminLinks';

// Autoload the general classes
$wgAutoloadClasses['MapsMapFeature'] 		= $egMapsIP . '/Maps_MapFeature.php';
$wgAutoloadClasses['MapsMapper'] 			= $egMapsIP . '/Maps_Mapper.php';
$wgAutoloadClasses['MapsUtils'] 			= $egMapsIP . '/Maps_Utils.php';

// TODO: change geocoder setup to feature hook, and load the relevant classes from it's init function
$wgAutoloadClasses['MapsGeocoder'] 			= $egMapsIP . '/Geocoders/Maps_Geocoder.php';
$wgAutoloadClasses['MapsBaseGeocoder'] 		= $egMapsIP . '/Geocoders/Maps_BaseGeocoder.php';

if (empty($egMapsServices)) $egMapsServices = array();

// TODO: move inclusions, or add init file to settings to allow auto load?
include_once $egMapsIP . '/GoogleMaps/Maps_GoogleMaps.php';
include_once $egMapsIP . '/OpenLayers/Maps_OpenLayers.php';
include_once $egMapsIP . '/YahooMaps/Maps_YahooMaps.php';

// TODO: split Maps_ParserFunctions.php functionallity so this line is not required
include_once $egMapsIP . '/ParserFunctions/Maps_ParserFunctions.php';

/**
 * Initialization function for the Maps extension
 */
function efMapsSetup() { 
	global $wgExtensionCredits, $wgOut, $wgLang, $wgAutoloadClasses, $IP;	
	global $egMapsDefaultService, $egMapsAvailableServices, $egMapsServices, $egMapsScriptPath, $egMapsDefaultGeoService, $egMapsAvailableGeoServices, $egMapsIP, $egMapsAvailableFeatures;

	efMapsValidateGoogleMapsKey();
	
	// Enure that the default service is one of the enabled ones
	$egMapsDefaultService = in_array($egMapsDefaultService, $egMapsAvailableServices) ? $egMapsDefaultService : $egMapsAvailableServices[0];
	$egMapsDefaultGeoService = in_array($egMapsDefaultGeoService, $egMapsAvailableGeoServices) ? $egMapsDefaultGeoService : $egMapsAvailableGeoServices[0];
	
	// TODO: split for feature hook system?	
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

	$wgOut->addScriptFile($egMapsScriptPath . '/MapUtilityFunctions.js');
	
	foreach($egMapsAvailableFeatures as $key => $values) {
		// Load and optionally initizlize feature
		if (array_key_exists('class', $values) && array_key_exists('file', $values) && array_key_exists('local', $values)) {
			$wgAutoloadClasses[$values['class']] = $values['local'] ? $egMapsIP . '/' . $values['file'] : $IP . '/extensions/' . $values['file'];
			if (method_exists($values['class'], 'initialize')) {
				call_user_func(array($values['class'], 'initialize'));
			}
		}
		
		// Check for wich services there are handlers for the current fature, and load them
		foreach ($egMapsServices as  $serviceData) {
			if (array_key_exists($key, $serviceData)) {
				$file = $serviceData[$key]['local'] ? $egMapsIP . '/' . $serviceData[$key]['file'] : $IP . '/extensions/' . $serviceData[$key]['file'];
				$wgAutoloadClasses[$serviceData[$key]['class']] = $file;			
			}	

			if (array_key_exists('classes', $serviceData)) {
				foreach($serviceData['classes'] as $class) {
					$file = $class['local'] ? $egMapsIP . '/' . $class['file'] : $IP . '/extensions/' . $class['file'];
					$wgAutoloadClasses[$class['class']] = $file;
				}
			}			
		}
	}
	
	return true;
}



/**
 * This function ensures backward compatibility with Semantic Google Maps and other extensions
 * using $wgGoogleMapsKey instead of $egGoogleMapsKey.
 */ // TODO: move to gmaps code
function efMapsValidateGoogleMapsKey() {
	global $egGoogleMapsKey, $wgGoogleMapsKey;
	
	if (isset($wgGoogleMapsKey)){
		if (strlen(trim($egGoogleMapsKey)) < 1) $egGoogleMapsKey = $wgGoogleMapsKey;
	} 
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



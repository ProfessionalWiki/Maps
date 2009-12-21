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

// Include the Validator extension if that hasn't been done yet, since it's required for Maps to work.
if( !defined( 'Validator_VERSION' ) ) {
	@include_once('extensions/Validator/Validator.php');		
}

// Only initialize the extension when all dependencies are present.
if (! defined( 'Validator_VERSION' )) {
	echo '<b>Warning:</b> You need to have <a href="http://www.mediawiki.org/wiki/Extension:Validator">Validator</a> installed in order to use <a href="http://www.mediawiki.org/wiki/Extension:Maps">Maps</a>.';
}
else {
	define('Maps_VERSION', '0.5.1 a2');
	
	$egMapsScriptPath 	= $wgScriptPath . '/extensions/Maps';
	$egMapsIP 			= $IP . '/extensions/Maps';
	
	$egMapsStyleVersion = $wgStyleVersion . '-' . Maps_VERSION;
	
	// Include the settings file
	require_once($egMapsIP . '/Maps_Settings.php');
	
	// Register the initialization function of Maps.
	 $wgExtensionFunctions[] = 'efMapsSetup'; 
	
	$wgExtensionMessagesFiles['Maps'] = $egMapsIP . '/Maps.i18n.php';
	
	$wgHooks['AdminLinks'][] = 'efMapsAddToAdminLinks';
	
	// Autoload the general classes
	$wgAutoloadClasses['MapsMapFeature'] 			= $egMapsIP . '/Maps_MapFeature.php';
	$wgAutoloadClasses['MapsMapper'] 				= $egMapsIP . '/Maps_Mapper.php';
	$wgAutoloadClasses['MapsUtils'] 				= $egMapsIP . '/Maps_Utils.php';
	
	if (empty($egMapsServices)) $egMapsServices = array();
}

/**
 * Initialization function for the Maps extension.
 */
function efMapsSetup() {
	global $wgExtensionCredits, $wgLang, $wgAutoloadClasses, $IP;	
	global $egMapsDefaultService, $egMapsAvailableServices, $egMapsServices, $egMapsDefaultGeoService, $egMapsAvailableGeoServices, $egMapsIP, $egMapsAvailableFeatures;

	// Remove all hooked in services that should not be available.
	foreach($egMapsServices as $service => $data) {
		if (! in_array($service, $egMapsAvailableServices)) unset($egMapsServices[$service]);
	}
	
	// Enure that the default service and geoservice are one of the enabled ones.
	$egMapsDefaultService = in_array($egMapsDefaultService, $egMapsAvailableServices) ? $egMapsDefaultService : $egMapsAvailableServices[0];
	if (!in_array($egMapsDefaultGeoService, $egMapsAvailableGeoServices)) {
		reset($egMapsAvailableGeoServices);
		$egMapsDefaultGeoService = key($egMapsAvailableGeoServices);
	}
	
	wfLoadExtensionMessages( 'Maps' ); 
	
	// Creation of a list of internationalized service names.
	$services = array();
	foreach (array_keys($egMapsServices) as $name) $services[] = wfMsg('maps_'.$name);
	$services_list = $wgLang->listToText($services);
	
	$wgExtensionCredits['parserhook'][] = array(
		'path' => __FILE__,
		'name' => wfMsg('maps_name'),
		'version' => Maps_VERSION,
		'author' => array('[http://bn2vs.com Jeroen De Dauw]', '[http://www.mediawiki.org/wiki/User:Yaron_Koren Yaron Koren]', 'others'),
		'url' => 'http://www.mediawiki.org/wiki/Extension:Maps',
		'description' =>  wfMsgExt( 'maps_desc', 'parsemag', $services_list ),
		'descriptionmsg' => wfMsgExt( 'maps_desc', 'parsemag', $services_list ),
	);
	
	MapsMapper::initializeMainParams();
	
	// These loops take care of everything hooked into Maps.
	foreach($egMapsAvailableFeatures as $key => $values) {
		// Load and optionally initizlize feature.
		if (array_key_exists('class', $values) && array_key_exists('file', $values) && array_key_exists('local', $values)) {
			$wgAutoloadClasses[$values['class']] = $values['local'] ? $egMapsIP . '/' . $values['file'] : $IP . '/extensions/' . $values['file'];
			if (method_exists($values['class'], 'initialize')) call_user_func(array($values['class'], 'initialize'));
		}
		
		// Check for wich services there are handlers for the current fature, and load them
		foreach ($egMapsServices as  $serviceData) {
			if (array_key_exists($key, $serviceData)) {
				if (array_key_exists('class', $serviceData[$key]) && array_key_exists('file', $serviceData[$key]) && array_key_exists('local', $serviceData[$key])) {
					$file = $serviceData[$key]['local'] ? $egMapsIP . '/' . $serviceData[$key]['file'] : $IP . '/extensions/' . $serviceData[$key]['file'];
					$wgAutoloadClasses[$serviceData[$key]['class']] = $file;	
				}
			}

			if (array_key_exists('classes', $serviceData)) {
				foreach($serviceData['classes'] as $class) {
					$file = $class['local'] ? $egMapsIP . '/' . $class['file'] : $IP . '/extensions/' . $class['file'];
					$wgAutoloadClasses[$class['class']] = $file;
					if (method_exists($class['class'], 'initialize')) call_user_func(array($class['class'], 'initialize'));
				}
			}			
		}
	}
	
	return true;
}

/**
 * Adds a link to Admin Links page
 */
function efMapsAddToAdminLinks(&$admin_links_tree) {
    $displaying_data_section = $admin_links_tree->getSection(wfMsg('smw_adminlinks_displayingdata'));
    
    // Escape if SMW hasn't added links
    if (is_null($displaying_data_section)) return true;
    $smw_docu_row = $displaying_data_section->getRow('smw');
    
    $maps_docu_label = wfMsg('adminlinks_documentation', wfMsg('maps_name'));
    $smw_docu_row->addItem(AlItem::newFromExternalLink('http://www.mediawiki.org/wiki/Extension:Maps', $maps_docu_label));

    return true;
}
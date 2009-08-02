<?php
  
/**
 * Initialization file for the Semantic Maps extension.
 * Extension documentation: http://www.mediawiki.org/wiki/Extension:Semantic_Maps
 *
 * @file SemanticMaps.php
 * @ingroup SemanticMaps
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

define('SM_VERSION', '0.3');

$smgScriptPath = $wgScriptPath . '/extensions/SemanticMaps';
$smgIP = $IP . '/extensions/SemanticMaps';

$wgExtensionFunctions[] = 'smfSetup';

$wgHooks['AdminLinks'][] = 'smfAddToAdminLinks';

$wgExtensionMessagesFiles['SemanticMaps'] = $smgIP . '/SemanticMaps.i18n.php';

// Autoload the general classes
$wgAutoloadClasses['SMMapPrinter'] = $smgIP . '/SM_MapPrinter.php';
$wgAutoloadClasses['SMMapper'] = $smgIP . '/SM_Mapper.php';
$wgAutoloadClasses['SMFormInput'] = $smgIP . '/SM_FormInput.php';

// Add the services
$egMapsServices['googlemaps']['qp'] = array('class' => 'SMGoogleMaps', 'file' => 'GoogleMaps/SM_GoogleMaps.php');
$egMapsServices['googlemaps']['fi'] = array('class' => 'SMGoogleMapsFormInput', 'file' => 'GoogleMaps/SM_GoogleMapsFormInput.php');

$egMapsServices['yahoomaps']['qp'] = array('class' => 'SMYahooMaps', 'file' => 'YahooMaps/SM_YahooMaps.php');
$egMapsServices['yahoomaps']['fi'] = array('class' => 'SMYahooMapsFormInput', 'file' => 'YahooMaps/SM_YahooMapsFormInput.php');

$egMapsServices['openlayers']['qp'] = array('class' => 'SMOpenLayers', 'file' => 'OpenLayers/SM_OpenLayers.php');
$egMapsServices['openlayers']['fi'] = array('class' => 'SMOpenLayersFormInput', 'file' => 'OpenLayers/SM_OpenLayersFormInput.php');

function smfSetup() {
	global $wgExtensionCredits, $egMapsMainServices, $egMapsServices;

	$services_list = implode(', ', $egMapsMainServices);
	
	wfLoadExtensionMessages( 'SemanticMaps' );
	
	$wgExtensionCredits['other'][]= array(
		'path' => __FILE__,
		'name' => wfMsg('semanticmaps_name'),
		'version' => SM_VERSION,
		'author' => array("[http://bn2vs.com Jeroen De Dauw]", "Yaron Koren", "Robert Buzink"),
		'url' => 'http://www.mediawiki.org/wiki/Extension:Semantic_Maps',
		'description' => wfMsg( 'semanticmaps_desc', $services_list ),
		'descriptionmsg' => wfMsg( 'semanticmaps_desc', $services_list ),
	);

	smfInitFormHook('map');
	smfInitFormat('map', array('class' => 'SMMapper', 'file' => 'SM_Mapper.php'));
	
	foreach($egMapsServices as $serviceName => $serviceData) {
		$hasQP = array_key_exists('qp', $serviceData);
		$hasFI = array_key_exists('fi', $serviceData);
		
		if ($hasQP) smfInitFormat($serviceName, $serviceData['qp']);
		if ($hasFI) smfInitFormHook($serviceName, $serviceData['fi']);
		
		foreach ($serviceData['aliases'] as $alias) {
			if ($hasQP) smfInitFormat($alias, $serviceData['qp']);
			if ($hasFI) smfInitFormHook($alias, $serviceData['fi'], $serviceName);
		}
	}
	
}

/**
 * Adds a mapping service's form hook
 *
 * @param unknown_type $service
 * @param unknown_type $mainName
 */
function smfInitFormHook($service, $fi = null, $mainName = '') {
	global $wgAutoloadClasses, $sfgFormPrinter, $smgIP;

	if (isset($fi)) {
		if (! array_key_exists($fi['class'], $wgAutoloadClasses)) $wgAutoloadClasses[$fi['class']] = $smgIP . '/' . $fi['file'];
	}
	
	// Add the form input hook for the service
	$field_args = array();
	if (strlen($mainName) > 0) $field_args['service_name'] = $mainName;
	$sfgFormPrinter->setInputTypeHook($service, 'smfSelectFormInputHTML', $field_args);
}

/**
 * Add the result format for a mapping service or alias
 *
 * @param unknown_type $format
 * @param unknown_type $qp
 */
function smfInitFormat($format, $qp) {
	global $wgAutoloadClasses, $smwgResultFormats, $smgIP;
	
	if (! array_key_exists($qp['class'], $wgAutoloadClasses)) $wgAutoloadClasses[$qp['class']] = $smgIP . '/' . $qp['file'];
	
	if (isset($smwgResultFormats)) {
		$smwgResultFormats[$format] = $qp['class'];
	}
	else {
		SMWQueryProcessor::$formats[$format] = $qp['class'];
	}	
}

/**
 * Class for the form input type 'map'. The relevant form input class is called depending on the provided service.
 *
 * @param unknown_type $coordinates
 * @param unknown_type $input_name
 * @param unknown_type $is_mandatory
 * @param unknown_type $is_disabled
 * @param unknown_type $field_args
 * @return unknown
 */
function smfSelectFormInputHTML($coordinates, $input_name, $is_mandatory, $is_disabled, $field_args) {
	// If service_name is set, use this value, and ignore any given service parameters
	// This will prevent ..input type=googlemaps|service=yahoo.. from shwoing up a Yahoo! Maps map
	if (array_key_exists('service_name', $field_args)) $field_args['service'] = $field_args['service_name'];
		
	$field_args['service'] = MapsMapper::getValidService($field_args['service']);
	
	// TODO: get class from hook system afetr check if fi is available
	// Create an instace of 
	switch ($field_args['service']) {
		case 'googlemaps' :
			$formInput = new SMGoogleMapsFormInput();
			break;			
		case 'openlayers' :
			$formInput = new SMOpenLayersFormInput();
			break;
		case 'yahoomaps' :
			$formInput = new SMYahooMapsFormInput();
			break;				
	}
	
	// Get and return the form input HTML from the hook corresponding with the provided service
	return $formInput->formInputHTML($coordinates, $input_name, $is_mandatory, $is_disabled, $field_args);		
}

function smfGetDynamicInput($id, $value, $args='') {
	// By De Dauw Jeroen - November 2008 - http://code.bn2vs.com/viewtopic.php?t=120
	return '<input id="'.$id.'" '.$args.' value="'.$value.'" onfocus="if (this.value==\''.$value.'\') {this.value=\'\';}" onblur="if (this.value==\'\') {this.value=\''.$value.'\';}" />';
}

/**
 * Adds a link to Admin Links page
 */
function smfAddToAdminLinks(&$admin_links_tree) {
    $displaying_data_section = $admin_links_tree->getSection(wfMsg('smw_adminlinks_displayingdata'));
    // Escape if SMW hasn't added links
    if (is_null($displaying_data_section))
        return true;
    $smw_docu_row = $displaying_data_section->getRow('smw');
    wfLoadExtensionMessages('SemanticMaps');
    $sm_docu_label = wfMsg('adminlinks_documentation', wfMsg('semanticmaps_name'));
    $smw_docu_row->addItem(AlItem::newFromExternalLink("http://www.mediawiki.org/wiki/Extension:Semantic_Maps", $sm_docu_label));
    return true;
}


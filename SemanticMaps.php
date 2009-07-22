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

define('SM_VERSION', '0.1');

$smgScriptPath = $wgScriptPath . '/extensions/SemanticMaps';
$smgIP = $IP . '/extensions/SemanticMaps';

$wgExtensionFunctions[] = 'smfSetup';

$wgExtensionMessagesFiles['SemanticMaps'] = $smgIP . '/SemanticMaps.i18n.php';

// Autoload the general classes
$wgAutoloadClasses['SMMapPrinter'] = $smgIP . '/SM_MapPrinter.php';
$wgAutoloadClasses['SMMapper'] = $smgIP . '/SM_Mapper.php';
$wgAutoloadClasses['SMFormInput'] = $smgIP . '/SM_FormInput.php';

function smfSetup() {
	global $wgExtensionCredits, $egMapsServices, $wgParser, $wgExtensionCredits;

	foreach($egMapsServices as $fn) smfInitFormat($fn);
	$services_list = implode(', ', $egMapsServices);

	wfLoadExtensionMessages( 'SemanticMaps' );
	
	$wgExtensionCredits['other'][]= array(
		'name' => wfMsg('semanticmaps_name'),
		'version' => SM_VERSION,
		'author' => array("[http://bn2vs.com Jeroen De Dauw]", "Yaron Koren", "Robert Buzink"),
		'url' => 'http://www.mediawiki.org/wiki/Extension:Semantic_Maps',
		'description' => wfMsg('semanticmaps_desc') . $services_list
	);

	// Add the map services that have form input type hooks to Semantic Forms if it is installed
	global $sfgFormPrinter;
	if ($sfgFormPrinter) {
		$sfgFormPrinter->setInputTypeHook('map', 'selectFormInputHTML', array());
		
		$sfgFormPrinter->setInputTypeHook('googlemap', array('SMGoogleMapsFormInput', 'formInputHTML'), array());
		$sfgFormPrinter->setInputTypeHook('googlemaps', array('SMGoogleMapsFormInput', 'formInputHTML'), array());
		$sfgFormPrinter->setInputTypeHook('google', array('SMGoogleMapsFormInput', 'formInputHTML'), array());		
		
		$sfgFormPrinter->setInputTypeHook('yahoomap', array('SMYahooMapsFormInput', 'formInputHTML'), array());
		$sfgFormPrinter->setInputTypeHook('yahoomaps', array('SMYahooMapsFormInput', 'formInputHTML'), array());
		$sfgFormPrinter->setInputTypeHook('yahoo', array('SMYahooMapsFormInput', 'formInputHTML'), array());
		
		$sfgFormPrinter->setInputTypeHook('openlayer', array('SMOpenLayersFormInput', 'formInputHTML'), array());
		$sfgFormPrinter->setInputTypeHook('openlayers', array('SMOpenLayersFormInput', 'formInputHTML'), array());
		$sfgFormPrinter->setInputTypeHook('layers', array('SMOpenLayersFormInput', 'formInputHTML'), array());
	}

	// Check if $smwgResultFormats, a global variable introduced in SMW 1.2.2, is set
	global $smwgResultFormats;
	if (isset($smwgResultFormats)) {
		$smwgResultFormats['map'] = 'SMMapper';
		
		$smwgResultFormats['googlemap'] = 'SMGoogleMaps';
		$smwgResultFormats['googlemaps'] = 'SMGoogleMaps';
		$smwgResultFormats['google'] = 'SMGoogleMaps';
		
		$smwgResultFormats['openlayer'] = 'SMOpenLayers';
		$smwgResultFormats['openlayers'] = 'SMOpenLayers';
		$smwgResultFormats['layers'] = 'SMOpenLayers';
		
		$smwgResultFormats['yahoomap'] = 'SMYahooMaps';
		$smwgResultFormats['yahoomaps'] = 'SMYahooMaps';
		$smwgResultFormats['yahoo'] = 'SMYahooMaps';
	}
	else {
		SMWQueryProcessor::$formats['map'] = 'SMMapper';
		
		SMWQueryProcessor::$formats['googlemap'] = 'SMGoogleMaps';
		SMWQueryProcessor::$formats['googlemaps'] = 'SMGoogleMaps';
		SMWQueryProcessor::$formats['google'] = 'SMGoogleMaps';
		
		SMWQueryProcessor::$formats['openlayer'] = 'SMOpenLayers';
		SMWQueryProcessor::$formats['openlayers'] = 'SMOpenLayers';
		SMWQueryProcessor::$formats['layers'] = 'SMOpenLayers';
		
		SMWQueryProcessor::$formats['yahoomap'] = 'SMYahooMaps';
		SMWQueryProcessor::$formats['yahoomaps'] = 'SMYahooMaps';
		SMWQueryProcessor::$formats['yahoo'] = 'SMYahooMaps';
	}
}

/**
 * Initialize the result format depending on the map service
 */
function smfInitFormat( $format ) {
	global $smwgResultFormats, $wgAutoloadClasses, $smgIP;

	switch ($format) {
		case 'googlemaps':
			$class = 'SMGoogleMaps';
			$file = $smgIP . '/GoogleMaps/SM_GoogleMaps';
		break;
		case 'openlayers':
			$class = 'SMOpenLayers';
			$file = $smgIP . '/OpenLayers/SM_OpenLayers';
		break;
		case 'yahoomaps':
			$class = 'SMYahooMaps';
			$file = $smgIP . '/YahooMaps/SM_YahooMaps';
		break;
	}

	if (isset($class) && isset($file)) {
		$smwgResultFormats[$format] = $class;
		$wgAutoloadClasses[$class] = $file . ".php";
		$wgAutoloadClasses[$class . "FormInput"] = $file . "FormInput.php";
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
function selectFormInputHTML($coordinates, $input_name, $is_mandatory, $is_disabled, $field_args) {
	global $egMapsAvailableServices, $egMapsDefaultService;
	
	// If the provided service is not one of the allowed ones, use the default
	if(!in_array($field_args['service'], $egMapsAvailableServices)) $field_args['service'] = $egMapsDefaultService;
	
	// Get the form input HTML from the hook corresponding with the provided service
	switch ($field_args['service']) {
		case 'openlayers' : case 'layers' : 
			$output = SMOpenLayersFormInput::formInputHTML($coordinates, $input_name, $is_mandatory, $is_disabled, $field_args);
			break;
		case 'yahoomaps' : case 'yahoo' : 
			$output = SMYahooMapsFormInput::formInputHTML($coordinates, $input_name, $is_mandatory, $is_disabled, $field_args);
			break;	
		default:
			$output = SMGoogleMapsFormInput::formInputHTML($coordinates, $input_name, $is_mandatory, $is_disabled, $field_args);
			break;				
	}
	
	return $output;
			
}

function smfGetDynamicInput($id, $value, $args='') {
	// By De Dauw Jeroen - November 2008 - http://code.bn2vs.com/viewtopic.php?t=120
	return '<input id="'.$id.'" '.$args.' value="'.$value.'" onfocus="if (this.value==\''.$value.'\') {this.value=\'\';}" onblur="if (this.value==\'\') {this.value=\''.$value.'\';}" />';
}




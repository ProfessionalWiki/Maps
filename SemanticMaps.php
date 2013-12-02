<?php

/**
 * Initialization file for the Semantic Maps extension.
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

if ( defined( 'SM_VERSION' ) ) {
	// Do not initialize more than once.
	return 1;
}

define( 'SM_VERSION', '3.0 beta' );

if ( version_compare( $GLOBALS['wgVersion'], '1.19c', '<' ) ) {
	throw new Exception( 'This version of Semantic Maps requires MediaWiki 1.18 or above;'
		. 'use Semantic Maps 1.0.x for MediaWiki 1.17 and Semantic Maps 0.7.x for older versions.' );
}

if ( !defined( 'Maps_VERSION' ) && is_readable( __DIR__ . '/vendor/autoload.php' ) ) {
	include_once( __DIR__ . '/vendor/autoload.php' );
}

if ( !defined( 'Maps_VERSION' ) ) {
	throw new Exception( 'You need to have Maps installed in order to use Semantic Maps' );
}

$wgExtensionCredits['semantic'][] = array(
	'path' => __FILE__,
	'name' => 'Semantic Maps',
	'version' => SM_VERSION,
	'author' => array(
		'[https://www.mediawiki.org/wiki/User:Jeroen_De_Dauw Jeroen De Dauw]'
	),
	'url' => 'https://semantic-mediawiki.org/wiki/Semantic_Maps',
	'descriptionmsg' => 'semanticmaps-desc'
);

$GLOBALS['smgScriptPath'] = ( $GLOBALS['wgExtensionAssetsPath'] === false
		? $GLOBALS['wgScriptPath'] . '/extensions' : $GLOBALS['wgExtensionAssetsPath'] ) . '/SemanticMaps';

// Include the settings file.
require_once 'SM_Settings.php';

# (named) Array of String. This array contains the available features for Maps.
# Commenting out the inclusion of any feature will make Maps completely ignore it, and so improve performance.

	# Query printers
	include_once __DIR__ . '/src/queryprinters/SM_QueryPrinters.php';
	# Form imputs

$GLOBALS['wgResourceModules']['ext.sm.forminputs'] = array(
	'dependencies' => array( 'ext.maps.coord' ),
	'localBasePath' => __DIR__,
	'remoteBasePath' => $GLOBALS['smgScriptPath'] .  '/includes/forminputs',
	'group' => 'ext.semanticmaps',
	'scripts' => array(
		'jquery.mapforminput.js'
	),
	'messages' => array(
		'semanticmaps_enteraddresshere',
		'semanticmaps-updatemap',
		'semanticmaps_lookupcoordinates',
		'semanticmaps-forminput-remove',
		'semanticmaps-forminput-add',
		'semanticmaps-forminput-locations'
	)
);

# Include the mapping services that should be loaded into Semantic Maps.
# Commenting or removing a mapping service will cause Semantic Maps to completely ignore it, and so improve performance.

	# Google Maps API v3
	include_once __DIR__ . '/src/services/GoogleMaps3/SM_GoogleMaps3.php';

	# OpenLayers API
	include_once __DIR__ . '/src/services/OpenLayers/SM_OpenLayers.php';

$GLOBALS['wgExtensionMessagesFiles']['SemanticMaps'] = __DIR__ . '/SemanticMaps.i18n.php';

// Hook for initializing the Geographical Data types.
$GLOBALS['wgHooks']['SMW::DataType::initTypes'][] = 'SemanticMapsHooks::initGeoDataTypes';

// Hook for defining the default query printer for queries that ask for geographical coordinates.
$GLOBALS['wgHooks']['SMWResultFormat'][] = 'SemanticMapsHooks::addGeoCoordsDefaultFormat';

// Hook for adding a Semantic Maps links to the Admin Links extension.
$GLOBALS['wgHooks']['AdminLinks'][] = 'SemanticMapsHooks::addToAdminLinks';

$GLOBALS['wgHooks']['sfFormPrinterSetup'][] = 'SemanticMaps\FormInputsSetup::run';

/**
 * Calls the relevant form input class depending on the provided service.
 *
 * @param string $coordinates
 * @param string $input_name
 * @param boolean $is_mandatory
 * @param boolean $is_disabled
 * @param array $field_args
 *
 * @return array
 */
function smfSelectFormInputHTML( $coordinates, $input_name, $is_mandatory, $is_disabled, array $field_args ) {
	// Get the service name from the field_args, and set it to null if it doesn't exist.
	$serviceName = array_key_exists( 'service_name', $field_args ) ? $field_args['service_name'] : null;

	// Get the instance of the service class.
	$service = MapsMappingServices::getValidServiceInstance( $serviceName, 'fi' );

	// Get an instance of the class handling the current form input and service.
	$formInput = $service->getFeatureInstance( 'fi' );

	// Get and return the form input HTML from the hook corresponding with the provided service.
	return $formInput->getInputOutput( $coordinates, $input_name, $is_mandatory, $is_disabled, $field_args );
}

/**
 * Calls the relevant form Editor input class depending on the provided service.
 * NOTE: Currently only GoogleMaps is supported
 *
 * @since 2.0
 *
 * @param string $coordinates
 * @param string $input_name
 * @param boolean $is_mandatory
 * @param boolean $is_disabled
 * @param array $field_args
 *
 * @return array
 */
function smfSelectEditorFormInputHTML( $coordinates, $input_name, $is_mandatory, $is_disabled, array $field_args ) {
	// Get the service name from the field_args, and set it to null if it doesn't exist.
	$serviceName = array_key_exists( 'service_name', $field_args ) ? $field_args['service_name'] : null;
	// Get the instance of the service class.
	$service = MapsMappingServices::getValidServiceInstance( $serviceName, 'fi' );

	// Get an instance of the class handling the current form input and service.
	$formInput = $service->getFeatureInstance( 'fi' );
	// Get and return the form input HTML from the hook corresponding with the provided service.
	return $formInput->getEditorInputOutput( $coordinates, $input_name, $is_mandatory, $is_disabled, $field_args );
}

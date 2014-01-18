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

define( 'SM_VERSION', '3.0' );

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
	'url' => 'https://github.com/JeroenDeDauw/SemanticMaps/blob/master/README.md#semantic-maps',
	'descriptionmsg' => 'semanticmaps-desc'
);

// Include the settings file.
require_once 'SM_Settings.php';

include_once __DIR__ . '/src/queryprinters/SM_QueryPrinters.php';

$GLOBALS['wgResourceModules']['ext.sm.forminputs'] = array(
	'dependencies' => array( 'ext.maps.coord' ),
	'localBasePath' => __DIR__ . '/src/forminputs',
	'remoteExtPath' => 'SemanticMaps/src/forminputs',
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


include_once __DIR__ . '/src/services/GoogleMaps3/SM_GoogleMaps3.php';


$GLOBALS['wgHooks']['MappingServiceLoad'][] = function() {
	MapsMappingServices::registerServiceFeature( 'openlayers', 'qp', 'SMMapPrinter' );
	return true;
};


$GLOBALS['wgExtensionMessagesFiles']['SemanticMaps'] = __DIR__ . '/SemanticMaps.i18n.php';

// Hook for initializing the Geographical Data types.
$GLOBALS['wgHooks']['SMW::DataType::initTypes'][] = 'SemanticMapsHooks::initGeoDataTypes';

// Hook for defining the default query printer for queries that ask for geographical coordinates.
$GLOBALS['wgHooks']['SMWResultFormat'][] = 'SemanticMapsHooks::addGeoCoordsDefaultFormat';

// Hook for adding a Semantic Maps links to the Admin Links extension.
$GLOBALS['wgHooks']['AdminLinks'][] = 'SemanticMapsHooks::addToAdminLinks';

$GLOBALS['wgHooks']['sfFormPrinterSetup'][] = 'SemanticMaps\FormInputsSetup::run';

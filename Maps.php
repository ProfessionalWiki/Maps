<?php

/**
 * Initialization file for the Maps extension.
 *
 * On MediaWiki.org:         http://www.mediawiki.org/wiki/Extension:Maps
 * Official documentation:     http://mapping.referata.com/wiki/Maps
 * Examples/demo's:         http://mapping.referata.com/wiki/Maps_examples
 *
 * @file Maps.php
 * @ingroup Maps
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */

/**
 * This documentation group collects source code files belonging to Maps.
 *
 * Please do not use this group name for other code. If you have an extension to
 * Maps, please use your own group definition.
 *
 * @defgroup Maps Maps
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

if ( version_compare( $wgVersion , '1.18c' , '<' ) ) {
	die( '<b>Error:</b> This version of Maps requires MediaWiki 1.18 or above; use Maps 1.0.x for MediaWiki 1.17 and Maps 0.7.x for older versions.' );
}

// Include the Validator extension if that hasn't been done yet, since it's required for Maps to work.
if ( !defined( 'Validator_VERSION' ) ) {
	@include_once( __DIR__ . '/../Validator/Validator.php' );
}

// Only initialize the extension when all dependencies are present.
if ( !defined( 'ParamProcessor_VERSION' ) ) {
	die( '<b>Error:</b> You need to have <a href="http://www.mediawiki.org/wiki/Extension:Validator">Validator</a> 1.0 or later installed in order to use <a href="http://www.mediawiki.org/wiki/Extension:Maps">Maps</a>.<br />' );
}

define( 'Maps_VERSION' , '3.0 alpha' );

$wgExtensionCredits['parserhook'][] = array(
	'path' => __FILE__ ,
	'name' => 'Maps' ,
	'version' => Maps_VERSION ,
	'author' => array(
		'[http://www.mediawiki.org/wiki/User:Jeroen_De_Dauw Jeroen De Dauw]'
	) ,
	'url' => 'https://www.mediawiki.org/wiki/Extension:Maps' ,
	'descriptionmsg' => 'maps-desc'
);

// The different coordinate notations.
define( 'Maps_COORDS_FLOAT' , 'float' );
define( 'Maps_COORDS_DMS' , 'dms' );
define( 'Maps_COORDS_DM' , 'dm' );
define( 'Maps_COORDS_DD' , 'dd' );

$egMapsScriptPath = ( $wgExtensionAssetsPath === false ? $wgScriptPath . '/extensions' : $wgExtensionAssetsPath ) . '/Maps';
$egMapsDir = __DIR__ . '/';

$egMapsStyleVersion = $wgStyleVersion . '-' . Maps_VERSION;

$wgExtensionMessagesFiles['Maps'] 				= __DIR__ . '/Maps.i18n.php';
$wgExtensionMessagesFiles['MapsMagic'] 			= __DIR__ . '/Maps.i18n.magic.php';
$wgExtensionMessagesFiles['MapsNamespaces'] 	= __DIR__ . '/Maps.i18n.namespaces.php';
$wgExtensionMessagesFiles['MapsAlias'] 			= __DIR__ . '/Maps.i18n.alias.php';

$wgAutoloadClasses = array_merge( $wgAutoloadClasses, include 'Maps.classes.php' );

$wgResourceModules = array_merge( $wgResourceModules, include 'Maps.resources.php' );

$wgAPIModules['geocode'] = 'ApiGeocode';

// Register the initialization function of Maps.
$wgExtensionFunctions[] = function () {
	wfRunHooks( 'MappingServiceLoad' );
	wfRunHooks( 'MappingFeatureLoad' );

	if ( in_array( 'googlemaps3', $GLOBALS['egMapsAvailableServices'] ) ) {
		global $wgSpecialPages, $wgSpecialPageGroups;

		$wgSpecialPages['MapEditor'] = 'SpecialMapEditor';
		$wgSpecialPageGroups['MapEditor'] = 'maps';
	}

	return true;
};

// Since 0.2
$wgHooks['AdminLinks'][] = 'MapsHooks::addToAdminLinks';

// Since 0.6.5
$wgHooks['UnitTestsList'][] = 'MapsHooks::registerUnitTests';

// Since 0.7.1
$wgHooks['ArticleFromTitle'][] = 'MapsHooks::onArticleFromTitle';

// Since 1.0
$wgHooks['MakeGlobalVariablesScript'][] = 'MapsHooks::onMakeGlobalVariablesScript';

// Since ??
$wgHooks['CanonicalNamespaces'][] = 'MapsHooks::onCanonicalNamespaces';

// Parser hooks

// Required for #coordinates.
$wgHooks['ParserFirstCallInit'][] = function( Parser &$parser ) {
	$instance = new MapsCoordinates();
	return $instance->init( $parser );
};

$wgHooks['ParserFirstCallInit'][] = function( Parser &$parser ) {
	$instance = new MapsDisplayMap();
	return $instance->init( $parser );
};

$wgHooks['ParserFirstCallInit'][] = function( Parser &$parser ) {
	$instance = new MapsDistance();
	return $instance->init( $parser );
};

$wgHooks['ParserFirstCallInit'][] = function( Parser &$parser ) {
	$instance = new MapsFinddestination();
	return $instance->init( $parser );
};

$wgHooks['ParserFirstCallInit'][] = function( Parser &$parser ) {
	$instance = new MapsGeocode();
	return $instance->init( $parser );
};

$wgHooks['ParserFirstCallInit'][] = function( Parser &$parser ) {
	$instance = new MapsGeodistance();
	return $instance->init( $parser );
};

$wgHooks['ParserFirstCallInit'][] = function( Parser &$parser ) {
	$instance = new MapsMapsDoc();
	return $instance->init( $parser );
};

// Geocoders

// Registration of the GeoNames service geocoder.
$wgHooks['GeocoderFirstCallInit'][] = 'MapsGeonamesGeocoder::register';

// Registration of the Google Geocoding (v2) service geocoder.
$wgHooks['GeocoderFirstCallInit'][] = 'MapsGoogleGeocoder::register';

// Layers

// Registration of the image layer type.
$wgHooks['MappingLayersInitialization'][] = 'MapsImageLayer::register';

// Registration of the KML layer type.
$wgHooks['MappingLayersInitialization'][] = 'MapsKMLLayer::register';

// Mapping services

// Include the mapping services that should be loaded into Maps.
// Commenting or removing a mapping service will make Maps completely ignore it, and so improve performance.

// Google Maps API v3
// TODO: improve loading mechanism
include_once $egMapsDir . 'includes/services/GoogleMaps3/GoogleMaps3.php';

// OpenLayers API
// TODO: improve loading mechanism
include_once $egMapsDir . 'includes/services/OpenLayers/OpenLayers.php';


$egMapsSettings = array();

// Include the settings file.
require_once $egMapsDir . 'Maps_Settings.php';

define( 'Maps_NS_LAYER' , $egMapsNamespaceIndex + 0 );
define( 'Maps_NS_LAYER_TALK' , $egMapsNamespaceIndex + 1 );

$wgAvailableRights[] = 'geocode';

// Users that can geocode. By default the same as those that can edit.
foreach ( $wgGroupPermissions as $group => $rights ) {
	if ( array_key_exists( 'edit' , $rights ) ) {
		$wgGroupPermissions[$group]['geocode'] = $wgGroupPermissions[$group]['edit'];
	}
}

$egMapsGlobalJSVars = array();

$wgParamDefinitions['mappingservice'] = array(
	'definition'=> 'Maps\ServiceParam',
);

$wgParamDefinitions['mapslocation'] = array(
	'string-parser' => 'Maps\LocationParser',
);

$wgParamDefinitions['mapsline'] = array(
	'string-parser' => 'Maps\LineParser',
);

$wgParamDefinitions['mapspolygon'] = array(
	'string-parser' => 'Maps\PolygonParser',
);

<?php

/**
 * Initialization file for the Maps extension.
 * https://github.com/JeroenDeDauw/Maps
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

if ( defined( 'Maps_VERSION' ) ) {
	// Do not initialize more then once.
	return 1;
}

define( 'Maps_VERSION' , '3.0 alpha' );

// Include the composer autoloader if it is present.
if ( is_readable( __DIR__ . '/vendor/autoload.php' ) ) {
	include_once( __DIR__ . '/vendor/autoload.php' );
}

// Attempt to include the ParamProcessor lib if that has not been loaded yet.
if ( !defined( 'ParamProcessor_VERSION' ) && file_exists( __DIR__ . '/../Validator/Validator.php' ) ) {
	include_once( __DIR__ . '/../Validator/Validator.php' );
}

// Attempt to include the DataValues lib if that has not been loaded yet.
if ( !defined( 'DATAVALUES_VERSION' ) && is_readable( __DIR__ . '/../DataValues/DataValues.php' ) ) {
	include_once( __DIR__ . '/../DataValues/DataValues.php' );
}

// Include the DataValuesInterfaces library if that hasn't been done yet.
if ( !defined( 'DATAVALUES_INTERFACES_VERSION' ) ) {
	@include_once( __DIR__ . '/../DataValues/DataValuesInterfaces/DataValuesInterfaces.php' );
}

// Include the DataValuesCommon library if that hasn't been done yet.
if ( !defined( 'DATAVALUES_COMMON_VERSION' ) ) {
	@include_once( __DIR__ . '/../DataValues/DataValuesCommon/DataValuesCommon.php' );
}

// Only initialize the extension when all dependencies are present.
if ( !defined( 'ParamProcessor_VERSION' ) ) {
	throw new Exception( 'You need to have ParamProcessor (Validator) 1.0 or later installed in order to use Maps' );
}

// Only initialize the extension when all dependencies are present.
if ( !defined( 'DATAVALUES_VERSION' ) ) {
	throw new Exception( 'You need to have DataValues loaded in order to use Maps' );
}

// Only initialize the extension when all dependencies are present.
if ( !defined( 'DATAVALUES_INTERFACES_VERSION' ) ) {
	throw new Exception( 'You need to have DataValuesInterfaces loaded in order to use Maps' );
}

// Only initialize the extension when all dependencies are present.
if ( !defined( 'DATAVALUES_COMMON_VERSION' ) ) {
	throw new Exception( 'You need to have DataValuesCommon loaded in order to use Maps' );
}

if ( version_compare( $GLOBALS['wgVersion'], '1.18c' , '<' ) ) {
	throw new Exception( 'This version of Maps requires MediaWiki 1.18 or above; use Maps 1.0.x for MediaWiki 1.17 and Maps 0.7.x for older versions.' );
}

spl_autoload_register( function ( $className ) {
	$className = ltrim( $className, '\\' );
	$fileName = '';
	$namespace = '';

	if ( $lastNsPos = strripos( $className, '\\') ) {
		$namespace = substr( $className, 0, $lastNsPos );
		$className = substr( $className, $lastNsPos + 1 );
		$fileName  = str_replace( '\\', '/', $namespace ) . '/';
	}

	$fileName .= str_replace( '_', '/', $className ) . '.php';

	$namespaceSegments = explode( '\\', $namespace );

	if ( $namespaceSegments[0] === 'Maps' ) {
		if ( count( $namespaceSegments ) > 1 && $namespaceSegments[1] === 'Elements' ) {
			require_once __DIR__ . '/includes/' . substr( $fileName, 5 );
		}
	}
} );

call_user_func( function() {
	global $wgExtensionCredits, $wgExtensionAssetsPath, $wgScriptPath, $wgAutoloadClasses;
	global $wgResourceModules, $wgGroupPermissions, $egMapsNamespaceIndex, $wgStyleVersion;
	global $egMapsScriptPath, $egMapsStyleVersion, $wgHooks, $wgExtensionMessagesFiles;

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

	$wgAPIModules['geocode'] = 'Maps\Api\Geocode';

	// Register the initialization function of Maps.
	$GLOBALS['wgExtensionFunctions'][] = function () {
		wfRunHooks( 'MappingServiceLoad' );
		wfRunHooks( 'MappingFeatureLoad' );

		if ( in_array( 'googlemaps3', $GLOBALS['egMapsAvailableServices'] ) ) {
			global $wgSpecialPages, $wgSpecialPageGroups;

			$wgSpecialPages['MapEditor'] = 'SpecialMapEditor';
			$wgSpecialPageGroups['MapEditor'] = 'maps';
		}

		return true;
	};

	$wgHooks['AdminLinks'][] = 'MapsHooks::addToAdminLinks';
	$wgHooks['UnitTestsList'][] = 'MapsHooks::registerUnitTests';
	$wgHooks['ArticleFromTitle'][] = 'MapsHooks::onArticleFromTitle';
	$wgHooks['MakeGlobalVariablesScript'][] = 'MapsHooks::onMakeGlobalVariablesScript';
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

	// Registration of the geocoder.us service geocoder.
	$wgHooks['GeocoderFirstCallInit'][] = 'MapsGeocoderusGeocoder::register';

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

	// Leaflet API
	// TODO: improve loading mechanism
	include_once $egMapsDir . 'includes/services/Leaflet/Leaflet.php';


	require_once __DIR__ . '/Maps_Settings.php';

	define( 'Maps_NS_LAYER' , $egMapsNamespaceIndex + 0 );
	define( 'Maps_NS_LAYER_TALK' , $egMapsNamespaceIndex + 1 );

	$wgAvailableRights[] = 'geocode';

	// Users that can geocode. By default the same as those that can edit.
	foreach ( $wgGroupPermissions as $group => $rights ) {
		if ( array_key_exists( 'edit' , $rights ) ) {
			$wgGroupPermissions[$group]['geocode'] = $wgGroupPermissions[$group]['edit'];
		}
	}

	global $wgParamDefinitions;

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

	$wgParamDefinitions['distance'] = array(
		'string-parser' => 'Maps\DistanceParser',
	);

	$wgParamDefinitions['wmsoverlay'] = array(
		'string-parser' => 'Maps\WmsOverlayParser',
	);
} );

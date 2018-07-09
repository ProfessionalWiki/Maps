<?php

/**
 * Initialization file for the Maps extension.
 *
 * @links https://github.com/JeroenDeDauw/Maps/blob/master/README.md#maps Documentation
 * @links https://github.com/JeroenDeDauw/Maps/issues Support
 * @links https://github.com/JeroenDeDauw/Maps Source code
 *
 * @license https://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */

use DataValues\Geo\Parsers\LatLongParser;
use Maps\CircleParser;
use Maps\DistanceParser;
use Maps\ImageOverlayParser;
use Maps\LineParser;
use Maps\LocationParser;
use Maps\PolygonParser;
use Maps\RectangleParser;
use Maps\SemanticMaps;
use Maps\WmsOverlayParser;

if ( defined( 'Maps_COORDS_FLOAT' ) ) {
	// Do not initialize more than once.
	return 1;
}

// The different coordinate notations.
define( 'Maps_COORDS_FLOAT', 'float' );
define( 'Maps_COORDS_DMS', 'dms' );
define( 'Maps_COORDS_DM', 'dm' );
define( 'Maps_COORDS_DD', 'dd' );

require_once __DIR__ . '/Maps_Settings.php';

// Include the composer autoloader if it is present.
if ( is_readable( __DIR__ . '/vendor/autoload.php' ) ) {
	include_once( __DIR__ . '/vendor/autoload.php' );
}

// Internationalization
$GLOBALS['wgMessagesDirs']['Maps'] = __DIR__ . '/i18n';
$GLOBALS['wgExtensionMessagesFiles']['MapsMagic'] = __DIR__ . '/Maps.i18n.magic.php';
$GLOBALS['wgExtensionMessagesFiles']['MapsAlias'] = __DIR__ . '/Maps.i18n.alias.php';

$GLOBALS['wgExtensionFunctions'][] = function() {
	if ( $GLOBALS['egMapsDisableExtension'] ) {
		return true;
	}

	if ( defined( 'Maps_VERSION' ) ) {
		// Do not initialize more than once.
		return true;
	}

	// Only initialize the extension when all dependencies are present.
	if ( !defined( 'Validator_VERSION' ) ) {
		throw new Exception( 'You need to have Validator installed in order to use Maps' );
	}

	if ( version_compare( $GLOBALS['wgVersion'], '1.27c', '<' ) ) {
		throw new Exception(
			'This version of Maps requires MediaWiki 1.27 or above; use Maps 4.2.x for older versions.'
			. ' More information at https://github.com/JeroenDeDauw/Maps/blob/master/INSTALL.md'
		);
	}

	define( 'Maps_VERSION', '5.5.5' );
	define( 'SM_VERSION', Maps_VERSION );

	if ( $GLOBALS['egMapsGMaps3Language'] === '' ) {
		$GLOBALS['egMapsGMaps3Language'] = $GLOBALS['wgLang'];
	}

	if ( in_array( 'googlemaps3', $GLOBALS['egMapsAvailableServices'] ) ) {
		$GLOBALS['wgSpecialPages']['MapEditor'] = 'SpecialMapEditor';
		$GLOBALS['wgSpecialPageGroups']['MapEditor'] = 'maps';
	}

	$GLOBALS['wgExtensionCredits']['parserhook'][] = [
		'path' => __FILE__,
		'name' => 'Maps',
		'version' => Maps_VERSION,
		'author' => [
			'[https://www.mediawiki.org/wiki/User:Jeroen_De_Dauw Jeroen De Dauw]',
			'...'
		],
		'url' => 'https://github.com/JeroenDeDauw/Maps/blob/master/README.md#maps',
		'descriptionmsg' => 'maps-desc',
		'license-name' => 'GPL-2.0-or-later'
	];

	$GLOBALS['wgResourceModules'] = array_merge( $GLOBALS['wgResourceModules'], include 'Maps.resources.php' );

	$GLOBALS['wgAPIModules']['geocode'] = 'Maps\Api\Geocode';

	$GLOBALS['wgHooks']['AdminLinks'][] = 'MapsHooks::addToAdminLinks';
	$GLOBALS['wgHooks']['MakeGlobalVariablesScript'][] = 'MapsHooks::onMakeGlobalVariablesScript';

	// Parser hooks

	// Required for #coordinates.
	$GLOBALS['wgHooks']['ParserFirstCallInit'][] = function( Parser &$parser ) {
		$instance = new MapsCoordinates();
		return $instance->init( $parser );
	};

	$GLOBALS['wgHooks']['ParserFirstCallInit'][] = function( Parser &$parser ) {
		foreach ( [ 'display_map', 'display_point', 'display_points', 'display_line' ] as $hookName ) {
			$parser->setFunctionHook(
				$hookName,
				function( Parser $parser, PPFrame $frame, array $arguments ) {
					$hook = new MapsDisplayMap();

					$mapHtml = $hook->getMapHtmlForKeyValueStrings(
						$parser,
						array_map(
							function( $argument ) use ( $frame ) {
								return $frame->expand( $argument );
							},
							$arguments
						)
					);

					return [
						$mapHtml,
						'noparse' => true,
						'isHTML' => true,
					];
				},
				Parser::SFH_OBJECT_ARGS
			);

			$parser->setHook(
				$hookName,
				function( $text, array $arguments, Parser $parser ) {
					if ( $text !== null ) {
						$defaultParameters = MapsDisplayMap::getHookDefinition( "\n" )->getDefaultParameters();
						$defaultParam = array_shift( $defaultParameters );

						// If there is a first default parameter, set the tag contents as its value.
						if ( $defaultParam !== null ) {
							$arguments[$defaultParam] = $text;
						}
					}

					return ( new MapsDisplayMap() )->getMapHtmlForParameterList( $parser, $arguments );
				}
			);
		}
	};

	$GLOBALS['wgHooks']['ParserFirstCallInit'][] = function( Parser &$parser ) {
		$instance = new MapsDistance();
		return $instance->init( $parser );
	};

	$GLOBALS['wgHooks']['ParserFirstCallInit'][] = function( Parser &$parser ) {
		$instance = new MapsFinddestination();
		return $instance->init( $parser );
	};

	$GLOBALS['wgHooks']['ParserFirstCallInit'][] = function( Parser &$parser ) {
		$instance = new MapsGeocode();
		return $instance->init( $parser );
	};

	$GLOBALS['wgHooks']['ParserFirstCallInit'][] = function( Parser &$parser ) {
		$instance = new MapsGeodistance();
		return $instance->init( $parser );
	};

	$GLOBALS['wgHooks']['ParserFirstCallInit'][] = function( Parser &$parser ) {
		$instance = new MapsMapsDoc();
		return $instance->init( $parser );
	};

	// Google Maps API v3
	if ( $GLOBALS['egMapsGMaps3ApiKey'] === '' && array_key_exists( 'egGoogleJsApiKey', $GLOBALS ) ) {
		$GLOBALS['egMapsGMaps3ApiKey'] = $GLOBALS['egGoogleJsApiKey'];
	}

	include_once __DIR__ . '/includes/services/GoogleMaps3/GoogleMaps3.php';

	MapsMappingServices::registerService( 'googlemaps3', MapsGoogleMaps3::class );

	$googleMaps = MapsMappingServices::getServiceInstance( 'googlemaps3' );
	$googleMaps->addFeature( 'display_map', MapsDisplayMapRenderer::class );


	// OpenLayers API
	include_once __DIR__ . '/includes/services/OpenLayers/OpenLayers.php';

	MapsMappingServices::registerService(
		'openlayers',
		MapsOpenLayers::class
	);

	$openLayers = MapsMappingServices::getServiceInstance( 'openlayers' );
	$openLayers->addFeature( 'display_map', MapsDisplayMapRenderer::class );


	// Leaflet API
	include_once __DIR__ . '/includes/services/Leaflet/Leaflet.php';

	MapsMappingServices::registerService( 'leaflet', MapsLeaflet::class );
	$leafletMaps = MapsMappingServices::getServiceInstance( 'leaflet' );
	$leafletMaps->addFeature( 'display_map', MapsDisplayMapRenderer::class );

	$GLOBALS['wgAvailableRights'][] = 'geocode';

	// Users that can geocode. By default the same as those that can edit.
	foreach ( $GLOBALS['wgGroupPermissions'] as $group => $rights ) {
		if ( array_key_exists( 'edit', $rights ) ) {
			$GLOBALS['wgGroupPermissions'][$group]['geocode'] = $GLOBALS['wgGroupPermissions'][$group]['edit'];
		}
	}

	$GLOBALS['wgParamDefinitions']['coordinate'] = [
		'string-parser' => LatLongParser::class,
	];

	$GLOBALS['wgParamDefinitions']['mapslocation'] = [
		'string-parser' => LocationParser::class,
	];

	$GLOBALS['wgParamDefinitions']['mapsline'] = [
		'string-parser' => LineParser::class,
	];

	$GLOBALS['wgParamDefinitions']['mapscircle'] = [
		'string-parser' => CircleParser::class,
	];

	$GLOBALS['wgParamDefinitions']['mapsrectangle'] = [
		'string-parser' => RectangleParser::class,
	];

	$GLOBALS['wgParamDefinitions']['mapspolygon'] = [
		'string-parser' => PolygonParser::class,
	];

	$GLOBALS['wgParamDefinitions']['distance'] = [
		'string-parser' => DistanceParser::class,
	];

	$GLOBALS['wgParamDefinitions']['wmsoverlay'] = [
		'string-parser' => WmsOverlayParser::class,
	];

	$GLOBALS['wgParamDefinitions']['mapsimageoverlay'] = [
		'string-parser' => ImageOverlayParser::class,
	];

	if ( !$GLOBALS['egMapsDisableSmwIntegration'] && defined( 'SMW_VERSION' ) ) {
		SemanticMaps::newFromMediaWikiGlobals( $GLOBALS )->initExtension();
	}

	return true;
};


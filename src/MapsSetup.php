<?php

namespace Maps;

use DataValues\Geo\Parsers\LatLongParser;
use Maps\Api\Geocode;
use Maps\GeoJson\GeoJsonContent;
use Maps\GeoJson\GeoJsonContentHandler;
use Maps\Parsers\JsonFileParser;
use MapsCoordinates;
use MapsDisplayMap;
use MapsDisplayMapRenderer;
use MapsDistance;
use MapsFinddestination;
use MapsGeocode;
use MapsGeodistance;
use MapsGoogleMaps3;
use MapsLeaflet;
use MapsMappingServices;
use MapsMapsDoc;
use MapsOpenLayers;
use Parser;
use PPFrame;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MapsSetup {

	private $mwGlobals;

	public function __construct( array &$mwGlobals ) {
		$this->mwGlobals = $mwGlobals;
	}

	public function setup() {
		$this->defaultSettings();
		$this->registerAllTheThings();

		if ( !$this->mwGlobals['egMapsDisableSmwIntegration'] && defined( 'SMW_VERSION' ) ) {
			SemanticMaps::newFromMediaWikiGlobals( $this->mwGlobals )->initExtension();
		}
	}

	private function registerAllTheThings() {
		$this->registerWebResources();
		$this->registerApiModules();
		$this->registerParserHooks();
		$this->registerMappingServices();
		$this->registerPermissions();
		$this->registerParameterTypes();
		$this->registerHooks();

		$this->mwGlobals['wgContentHandlers'][GeoJsonContent::CONTENT_MODEL_ID] = GeoJsonContentHandler::class;
	}

	private function defaultSettings() {
		if ( $this->mwGlobals['egMapsGMaps3Language'] === '' ) {
			$this->mwGlobals['egMapsGMaps3Language'] = $this->mwGlobals['wgLang'];
		}

		if ( in_array( 'googlemaps3', $this->mwGlobals['egMapsAvailableServices'] ) ) {
			$this->mwGlobals['wgSpecialPages']['MapEditor'] = 'SpecialMapEditor';
			$this->mwGlobals['wgSpecialPageGroups']['MapEditor'] = 'Maps';
		}

		if ( $this->mwGlobals['egMapsGMaps3ApiKey'] === '' && array_key_exists( 'egGoogleJsApiKey', $this->mwGlobals ) ) {
			$this->mwGlobals['egMapsGMaps3ApiKey'] = $this->mwGlobals['egGoogleJsApiKey'];
		}
	}

	private function registerWebResources() {
		$this->mwGlobals['wgResourceModules'] = array_merge(
			$this->mwGlobals['wgResourceModules'],
			include __DIR__ . '/../Maps.resources.php'
		);
	}

	private function registerParserHooks() {
		$this->mwGlobals['wgHooks']['ParserFirstCallInit'][] = function( Parser &$parser ) {
			$instance = new MapsCoordinates();
			return $instance->init( $parser );
		};

		$this->mwGlobals['wgHooks']['ParserFirstCallInit'][] = function( Parser &$parser ) {
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

		$this->mwGlobals['wgHooks']['ParserFirstCallInit'][] = function( Parser &$parser ) {
			return ( new MapsDistance() )->init( $parser );
		};

		$this->mwGlobals['wgHooks']['ParserFirstCallInit'][] = function( Parser &$parser ) {
			return ( new MapsFinddestination() )->init( $parser );
		};

		$this->mwGlobals['wgHooks']['ParserFirstCallInit'][] = function( Parser &$parser ) {
			return ( new MapsGeocode() )->init( $parser );
		};

		$this->mwGlobals['wgHooks']['ParserFirstCallInit'][] = function( Parser &$parser ) {
			return ( new MapsGeodistance() )->init( $parser );
		};

		$this->mwGlobals['wgHooks']['ParserFirstCallInit'][] = function( Parser &$parser ) {
			return ( new MapsMapsDoc() )->init( $parser );
		};
	}

	private function registerMappingServices() {
		include_once __DIR__ . '/../includes/services/GoogleMaps3/GoogleMaps3.php';

		MapsMappingServices::registerService( 'googlemaps3', MapsGoogleMaps3::class );

		$googleMaps = MapsMappingServices::getServiceInstance( 'googlemaps3' );
		$googleMaps->addFeature( 'display_map', MapsDisplayMapRenderer::class );


		// OpenLayers API
		include_once __DIR__ . '/../includes/services/OpenLayers/OpenLayers.php';

		MapsMappingServices::registerService(
			'openlayers',
			MapsOpenLayers::class
		);

		$openLayers = MapsMappingServices::getServiceInstance( 'openlayers' );
		$openLayers->addFeature( 'display_map', MapsDisplayMapRenderer::class );


		// Leaflet API
		include_once __DIR__ . '/../includes/services/Leaflet/Leaflet.php';

		MapsMappingServices::registerService( 'leaflet', MapsLeaflet::class );
		$leafletMaps = MapsMappingServices::getServiceInstance( 'leaflet' );
		$leafletMaps->addFeature( 'display_map', MapsDisplayMapRenderer::class );
	}

	private function registerPermissions() {
		$this->mwGlobals['wgAvailableRights'][] = 'geocode';

		// Users that can geocode. By default the same as those that can edit.
		foreach ( $this->mwGlobals['wgGroupPermissions'] as $group => $rights ) {
			if ( array_key_exists( 'edit', $rights ) ) {
				$this->mwGlobals['wgGroupPermissions'][$group]['geocode'] = $this->mwGlobals['wgGroupPermissions'][$group]['edit'];
			}
		}
	}

	private function registerParameterTypes() {
		$this->mwGlobals['wgParamDefinitions']['coordinate'] = [
			'string-parser' => LatLongParser::class,
		];

		$this->mwGlobals['wgParamDefinitions']['mapslocation'] = [
			'string-parser' => LocationParser::class,
		];

		$this->mwGlobals['wgParamDefinitions']['mapsline'] = [
			'string-parser' => LineParser::class,
		];

		$this->mwGlobals['wgParamDefinitions']['mapscircle'] = [
			'string-parser' => CircleParser::class,
		];

		$this->mwGlobals['wgParamDefinitions']['mapsrectangle'] = [
			'string-parser' => RectangleParser::class,
		];

		$this->mwGlobals['wgParamDefinitions']['mapspolygon'] = [
			'string-parser' => PolygonParser::class,
		];

		$this->mwGlobals['wgParamDefinitions']['distance'] = [
			'string-parser' => DistanceParser::class,
		];

		$this->mwGlobals['wgParamDefinitions']['wmsoverlay'] = [
			'string-parser' => WmsOverlayParser::class,
		];

		$this->mwGlobals['wgParamDefinitions']['mapsimageoverlay'] = [
			'string-parser' => ImageOverlayParser::class,
		];

		$this->mwGlobals['wgParamDefinitions']['jsonfile'] = [
			'string-parser' => JsonFileParser::class,
		];
	}

	private function registerHooks() {
		$this->mwGlobals['wgHooks']['AdminLinks'][] = 'MapsHooks::addToAdminLinks';
		$this->mwGlobals['wgHooks']['MakeGlobalVariablesScript'][] = 'MapsHooks::onMakeGlobalVariablesScript';
	}

	private function registerApiModules() {
		$this->mwGlobals['wgAPIModules']['geocode'] = Geocode::class;
	}

}

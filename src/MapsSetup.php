<?php

namespace Maps;

use DataValues\Geo\Parsers\LatLongParser;
use Maps\DataAccess\JsonFileParser;
use Maps\MediaWiki\Api\Geocode;
use Maps\MediaWiki\Content\GeoJsonContent;
use Maps\MediaWiki\Content\GeoJsonContentHandler;
use Maps\MediaWiki\ParserHooks\CoordinatesFunction;
use Maps\MediaWiki\ParserHooks\DisplayMapFunction;
use Maps\MediaWiki\ParserHooks\DisplayMapRenderer;
use Maps\MediaWiki\ParserHooks\DistanceFunction;
use Maps\MediaWiki\ParserHooks\FindDestinationFunction;
use Maps\MediaWiki\ParserHooks\GeocodeFunction;
use Maps\MediaWiki\ParserHooks\GeoDistanceFunction;
use Maps\MediaWiki\ParserHooks\MapsDocFunction;
use Maps\Presentation\WikitextParsers\CircleParser;
use Maps\Presentation\WikitextParsers\DistanceParser;
use Maps\Presentation\WikitextParsers\ImageOverlayParser;
use Maps\Presentation\WikitextParsers\LineParser;
use Maps\Presentation\WikitextParsers\LocationParser;
use Maps\Presentation\WikitextParsers\PolygonParser;
use Maps\Presentation\WikitextParsers\RectangleParser;
use Maps\Presentation\WikitextParsers\WmsOverlayParser;
use MapsGoogleMaps3;
use MapsLeaflet;
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
			$this->mwGlobals['wgSpecialPages']['MapEditor'] = 'Maps\MediaWiki\Specials\SpecialMapEditor';
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
			$instance = new CoordinatesFunction();
			return $instance->init( $parser );
		};

		$this->mwGlobals['wgHooks']['ParserFirstCallInit'][] = function( Parser &$parser ) {
			foreach ( [ 'display_map', 'display_point', 'display_points', 'display_line' ] as $hookName ) {
				$parser->setFunctionHook(
					$hookName,
					function( Parser $parser, PPFrame $frame, array $arguments ) {
						$hook = new DisplayMapFunction();

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
							$defaultParameters = DisplayMapFunction::getHookDefinition( "\n" )->getDefaultParameters();
							$defaultParam = array_shift( $defaultParameters );

							// If there is a first default parameter, set the tag contents as its value.
							if ( $defaultParam !== null ) {
								$arguments[$defaultParam] = $text;
							}
						}

						return ( new DisplayMapFunction() )->getMapHtmlForParameterList( $parser, $arguments );
					}
				);
			}
		};

		$this->mwGlobals['wgHooks']['ParserFirstCallInit'][] = function( Parser &$parser ) {
			return ( new DistanceFunction() )->init( $parser );
		};

		$this->mwGlobals['wgHooks']['ParserFirstCallInit'][] = function( Parser &$parser ) {
			return ( new FindDestinationFunction() )->init( $parser );
		};

		$this->mwGlobals['wgHooks']['ParserFirstCallInit'][] = function( Parser &$parser ) {
			return ( new GeocodeFunction() )->init( $parser );
		};

		$this->mwGlobals['wgHooks']['ParserFirstCallInit'][] = function( Parser &$parser ) {
			return ( new GeoDistanceFunction() )->init( $parser );
		};

		$this->mwGlobals['wgHooks']['ParserFirstCallInit'][] = function( Parser &$parser ) {
			return ( new MapsDocFunction() )->init( $parser );
		};
	}

	private function registerMappingServices() {
		include_once __DIR__ . '/../includes/services/GoogleMaps3/GoogleMaps3.php';

		MappingServices::registerService( 'googlemaps3', MapsGoogleMaps3::class );

		$googleMaps = MappingServices::getServiceInstance( 'googlemaps3' );
		$googleMaps->addFeature( 'display_map', DisplayMapRenderer::class );


		// OpenLayers API
		include_once __DIR__ . '/../includes/services/OpenLayers/OpenLayers.php';

		MappingServices::registerService(
			'openlayers',
			MapsOpenLayers::class
		);

		$openLayers = MappingServices::getServiceInstance( 'openlayers' );
		$openLayers->addFeature( 'display_map', DisplayMapRenderer::class );


		// Leaflet API
		include_once __DIR__ . '/../includes/services/Leaflet/Leaflet.php';

		MappingServices::registerService( 'leaflet', MapsLeaflet::class );
		$leafletMaps = MappingServices::getServiceInstance( 'leaflet' );
		$leafletMaps->addFeature( 'display_map', DisplayMapRenderer::class );
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
		$this->mwGlobals['wgHooks']['AdminLinks'][] = 'Maps\MediaWiki\MapsHooks::addToAdminLinks';
		$this->mwGlobals['wgHooks']['MakeGlobalVariablesScript'][] = 'Maps\MediaWiki\MapsHooks::onMakeGlobalVariablesScript';
	}

	private function registerApiModules() {
		$this->mwGlobals['wgAPIModules']['geocode'] = Geocode::class;
	}

}

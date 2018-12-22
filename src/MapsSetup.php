<?php

declare( strict_types = 1 );

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
			$this->mwGlobals['wgSpecialPageGroups']['MapEditor'] = 'maps';
		}

		if ( $this->mwGlobals['egMapsGMaps3ApiKey'] === '' && array_key_exists(
				'egGoogleJsApiKey',
				$this->mwGlobals
			) ) {
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
		if ( $this->mwGlobals['egMapsEnableCoordinateFunction'] ) {
			$this->mwGlobals['wgHooks']['ParserFirstCallInit'][] = function ( Parser &$parser ) {
				return ( new CoordinatesFunction() )->init( $parser );
			};
		}

		$this->mwGlobals['wgHooks']['ParserFirstCallInit'][] = function ( Parser &$parser ) {
			foreach ( [ 'display_map', 'display_point', 'display_points', 'display_line' ] as $hookName ) {
				$parser->setFunctionHook(
					$hookName,
					function ( Parser $parser, PPFrame $frame, array $arguments ) {
						$hook = new DisplayMapFunction();

						$mapHtml = $hook->getMapHtmlForKeyValueStrings(
							$parser,
							array_map(
								function ( $argument ) use ( $frame ) {
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
					function ( $text, array $arguments, Parser $parser ) {
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

		$this->mwGlobals['wgHooks']['ParserFirstCallInit'][] = function ( Parser &$parser ) {
			return ( new DistanceFunction() )->init( $parser );
		};

		$this->mwGlobals['wgHooks']['ParserFirstCallInit'][] = function ( Parser &$parser ) {
			return ( new FindDestinationFunction() )->init( $parser );
		};

		$this->mwGlobals['wgHooks']['ParserFirstCallInit'][] = function ( Parser &$parser ) {
			return ( new GeocodeFunction() )->init( $parser );
		};

		$this->mwGlobals['wgHooks']['ParserFirstCallInit'][] = function ( Parser &$parser ) {
			return ( new GeoDistanceFunction() )->init( $parser );
		};

		$this->mwGlobals['wgHooks']['ParserFirstCallInit'][] = function ( Parser &$parser ) {
			return ( new MapsDocFunction() )->init( $parser );
		};
	}

	private function registerMappingServices() {
		$localBasePath = __DIR__ . '/../resources';
		$remoteExtPath = array_slice(
				explode( '/', str_replace( DIRECTORY_SEPARATOR, '/', __DIR__ ) ),
				-2,
				1
			)[0] . '/../resources';

		$this->registerGoogleMapsModules( $localBasePath, $remoteExtPath );

		MappingServices::registerService( 'googlemaps3', GoogleMapsService::class );

		$googleMaps = MappingServices::getServiceInstance( 'googlemaps3' );
		$googleMaps->addFeature( 'display_map', DisplayMapRenderer::class );


		// Leaflet API
		$this->registerLeafletModules( $localBasePath, $remoteExtPath );

		MappingServices::registerService( 'leaflet', LeafletService::class );
		$leafletMaps = MappingServices::getServiceInstance( 'leaflet' );
		$leafletMaps->addFeature( 'display_map', DisplayMapRenderer::class );
	}

	private function registerGoogleMapsModules( string $localBasePath, string $remoteExtPath ) {
		global $wgResourceModules;

		$localBasePath = $localBasePath . '/GoogleMaps';
		$remoteExtPath = $remoteExtPath . '/GoogleMaps';

		$wgResourceModules['ext.maps.googlemaps3'] = [
			'dependencies' => [ 'ext.maps.common' ],
			'localBasePath' => $localBasePath,
			'remoteExtPath' => $remoteExtPath,
			'group' => 'ext.maps',
			'targets' => [
				'mobile',
				'desktop'
			],
			'scripts' => [
				'jquery.googlemap.js',
				'ext.maps.googlemaps3.js'
			],
			'messages' => [
				'maps-googlemaps3-incompatbrowser',
				'maps-copycoords-prompt',
				'maps-searchmarkers-text',
				'maps-fullscreen-button',
				'maps-fullscreen-button-tooltip',
			]
		];

		$wgResourceModules['ext.maps.gm3.markercluster'] = [
			'localBasePath' => $localBasePath . '/gm3-util-library',
			'remoteExtPath' => $remoteExtPath . '/gm3-util-library',
			'group' => 'ext.maps',
			'targets' => [
				'mobile',
				'desktop'
			],
			'scripts' => [
				'markerclusterer.js',
			],
		];

		$wgResourceModules['ext.maps.gm3.markerwithlabel'] = [
			'localBasePath' => $localBasePath . '/gm3-util-library',
			'remoteExtPath' => $remoteExtPath  . '/gm3-util-library',
			'group' => 'ext.maps',
			'targets' => [
				'mobile',
				'desktop'
			],
			'scripts' => [
				'markerwithlabel.js',
			],
			'styles' => [
				'markerwithlabel.css',
			],
		];

		$wgResourceModules['ext.maps.gm3.geoxml'] = [
			'localBasePath' => $localBasePath . '/geoxml3',
			'remoteExtPath' => $remoteExtPath,
			'group' => 'ext.maps' . '/geoxml3',
			'targets' => [
				'mobile',
				'desktop'
			],
			'scripts' => [
				'geoxml3.js',
				'ZipFile.complete.js', //kmz handling
				'ProjectedOverlay.js', //Overlay handling
			],
		];

		$wgResourceModules['ext.maps.gm3.earth'] = [
			'localBasePath' => $localBasePath . '/gm3-util-library',
			'remoteExtPath' => $remoteExtPath  . '/gm3-util-library',
			'group' => 'ext.maps',
			'targets' => [
				'mobile',
				'desktop'
			],
			'scripts' => [
				'googleearth-compiled.js',
			],
		];
	}

	private function registerLeafletModules( string $localBasePath, string $remoteExtPath ) {
		global $wgResourceModules;

		$localBasePath = $localBasePath . '/leaflet';
		$remoteExtPath = $remoteExtPath . '/leaflet';

		$wgResourceModules['ext.maps.leaflet.base'] = [
			'localBasePath' => $localBasePath . '/leaflet',
			'remoteExtPath' => $remoteExtPath . '/leaflet',
			'group' => 'ext.maps',
			'targets' => [
				'mobile',
				'desktop'
			],
			'scripts' => [
				'leaflet.js',
			],
			'styles' => [
				'leaflet.css',
			],
		];

		$wgResourceModules['ext.maps.leaflet'] = [
			'dependencies' => [
				'ext.maps.common',
				'ext.maps.services',
				'ext.maps.leaflet.base'
			],
			'localBasePath' => $localBasePath,
			'remoteExtPath' => $remoteExtPath,
			'group' => 'ext.maps',
			'targets' => [
				'mobile',
				'desktop'
			],
			'scripts' => [
				'jquery.leaflet.js',
				'ext.maps.leaflet.js',
			],
			'messages' => [
				'maps-markers',
				'maps-copycoords-prompt',
				'maps-searchmarkers-text',
			],
		];

		$wgResourceModules['ext.maps.leaflet.fullscreen'] = [
			'dependencies' => [ 'ext.maps.leaflet' ],
			'localBasePath' => $localBasePath . '/leaflet.fullscreen',
			'remoteExtPath' => $remoteExtPath . '/leaflet.fullscreen',
			'group' => 'ext.maps',
			'targets' => [
				'mobile',
				'desktop'
			],
			'scripts' => [
				'Control.FullScreen.js',
			],
			'styles' => [
				'Control.FullScreen.css',
			],
		];

		$wgResourceModules['ext.maps.leaflet.markercluster'] = [
			'dependencies' => [ 'ext.maps.leaflet' ],
			'localBasePath' => $localBasePath . '/leaflet.markercluster',
			'remoteExtPath' => $remoteExtPath . '/leaflet.markercluster',
			'group' => 'ext.maps',
			'targets' => [
				'mobile',
				'desktop'
			],
			'scripts' => [
				'leaflet.markercluster.js',
			],
			'styles' => [
				'MarkerCluster.css',
			],
		];

		$wgResourceModules['ext.maps.leaflet.providers'] = [
			'dependencies' => [ 'ext.maps.leaflet' ],
			'localBasePath' => $localBasePath . '/leaflet-providers',
			'remoteExtPath' => $remoteExtPath . '/leaflet-providers',
			'group' => 'ext.maps',
			'targets' => [
				'mobile',
				'desktop'
			],
			'scripts' => [
				'leaflet-providers.js',
			],
		];

		$wgResourceModules['ext.maps.leaflet.editable'] = [
			'dependencies' => [ 'ext.maps.leaflet.base' ],
			'localBasePath' => $localBasePath . '/leaflet.editable',
			'remoteExtPath' => $remoteExtPath . '/leaflet.editable',
			'group' => 'ext.maps',
			'targets' => [
				'mobile',
				'desktop'
			],
			'scripts' => [
				'Leaflet.Editable.js',
			],
		];

		$wgResourceModules['ext.maps.leaflet.editor'] = [
			'dependencies' => [
				'ext.maps.leaflet.base',
				//'ext.maps.leaflet.editable'
			],
			'localBasePath' => $localBasePath,
			'remoteExtPath' => $remoteExtPath,
			'group' => 'ext.maps',
			'targets' => [
				'mobile',
				'desktop'
			],
			'scripts' => [
				'leaflet.editor.js',
			],
		];
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

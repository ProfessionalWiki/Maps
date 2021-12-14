<?php

declare( strict_types = 1 );

namespace Maps;

use DataValues\Geo\Parsers\LatLongParser;
use Maps\GeoJsonPages\GeoJsonContent;
use Maps\GeoJsonPages\GeoJsonContentHandler;
use Maps\LegacyMapEditor\SpecialMapEditor;
use Maps\Map\CargoFormat\CargoFormat;
use Maps\WikitextParsers\CircleParser;
use Maps\WikitextParsers\DistanceParser;
use Maps\WikitextParsers\ImageOverlayParser;
use Maps\WikitextParsers\LineParser;
use Maps\WikitextParsers\LocationParser;
use Maps\WikitextParsers\PolygonParser;
use Maps\WikitextParsers\RectangleParser;
use Maps\WikitextParsers\WmsOverlayParser;
use Parser;

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

		if ( MapsFactory::globalInstance()->smwIntegrationIsEnabled() ) {
			MapsFactory::globalInstance()->newSemanticMapsSetup( $this->mwGlobals )->initExtension();
		}
	}

	private function defaultSettings() {
		if ( $this->mwGlobals['egMapsGMaps3Language'] === '' ) {
			$this->mwGlobals['egMapsGMaps3Language'] = $this->mwGlobals['wgLang'];
		}

		if ( in_array( 'googlemaps3', $this->mwGlobals['egMapsAvailableServices'] ) ) {
			$this->mwGlobals['wgSpecialPages']['MapEditor'] = SpecialMapEditor::class;
			$this->mwGlobals['wgSpecialPageGroups']['MapEditor'] = 'maps';
		}

		if ( $this->mwGlobals['egMapsGMaps3ApiKey'] === '' && array_key_exists(
				'egGoogleJsApiKey',
				$this->mwGlobals
			) ) {
			$this->mwGlobals['egMapsGMaps3ApiKey'] = $this->mwGlobals['egGoogleJsApiKey'];
		}
	}

	private function registerAllTheThings() {
		$this->registerParserHooks();
		$this->registerParameterTypes();
		$this->registerHooks();
		$this->registerGeoJsonContentModel();
	}

	private function registerParserHooks() {
		$this->mwGlobals['wgHooks']['ParserFirstCallInit'][] = function ( Parser $parser ) {
			MapsFactory::globalInstance()->newParserHookSetup( $parser )->registerParserHooks();
		};
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
	}

	private function registerHooks() {
		$this->mwGlobals['wgHooks']['AdminLinks'][] = 'Maps\MapsHooks::addToAdminLinks';
		$this->mwGlobals['wgHooks']['MakeGlobalVariablesScript'][] = 'Maps\MapsHooks::onMakeGlobalVariablesScript';
		$this->mwGlobals['wgHooks']['SkinTemplateNavigation'][] = 'Maps\MapsHooks::onSkinTemplateNavigation';
		$this->mwGlobals['wgHooks']['BeforeDisplayNoArticleText'][] = 'Maps\MapsHooks::onBeforeDisplayNoArticleText';
		$this->mwGlobals['wgHooks']['ShowMissingArticle'][] = 'Maps\MapsHooks::onShowMissingArticle';
		$this->mwGlobals['wgHooks']['ListDefinedTags'][] = 'Maps\MapsHooks::onRegisterTags';
		$this->mwGlobals['wgHooks']['ChangeTagsListActive'][] = 'Maps\MapsHooks::onRegisterTags';
		$this->mwGlobals['wgHooks']['ChangeTagsAllowedAdd'][] = 'Maps\MapsHooks::onChangeTagsAllowedAdd';
		$this->mwGlobals['wgHooks']['ResourceLoaderTestModules'][] = 'Maps\MapsHooks::onResourceLoaderTestModules';

		$this->mwGlobals['wgHooks']['CargoSetFormatClasses'][] = function( array &$formatClasses ) {
			$formatClasses['map'] = CargoFormat::class;
		};
	}

	private function registerGeoJsonContentModel() {
		$this->mwGlobals['wgContentHandlers'][GeoJsonContent::CONTENT_MODEL_ID] = GeoJsonContentHandler::class;
	}

}

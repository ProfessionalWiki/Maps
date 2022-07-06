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

	public function setup() {
		$this->defaultSettings();
		$this->registerAllTheThings();

		if ( MapsFactory::globalInstance()->smwIntegrationIsEnabled() ) {
			MapsFactory::globalInstance()->newSemanticMapsSetup()->initExtension();
		}
	}

	private function defaultSettings() {
		if ( $GLOBALS['egMapsGMaps3Language'] === '' ) {
			$GLOBALS['egMapsGMaps3Language'] = $GLOBALS['wgLang'];
		}

		if ( in_array( 'googlemaps3', $GLOBALS['egMapsAvailableServices'] ) ) {
			$GLOBALS['wgSpecialPages']['MapEditor'] = SpecialMapEditor::class;
			$GLOBALS['wgSpecialPageGroups']['MapEditor'] = 'maps';
		}

		if ( $GLOBALS['egMapsGMaps3ApiKey'] === '' && array_key_exists( 'egGoogleJsApiKey', $GLOBALS ) ) {
			$GLOBALS['egMapsGMaps3ApiKey'] = $GLOBALS['egGoogleJsApiKey'];
		}
	}

	private function registerAllTheThings() {
		$this->registerParserHooks();
		$this->registerParameterTypes();
		$this->registerHooks();
	}

	private function registerParserHooks() {
		$GLOBALS['wgHooks']['ParserFirstCallInit'][] = function ( Parser $parser ) {
			MapsFactory::globalInstance()->newParserHookSetup( $parser )->registerParserHooks();
		};
	}

	private function registerParameterTypes() {
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
	}

	private function registerHooks() {
		$GLOBALS['wgHooks']['AdminLinks'][] = 'Maps\MapsHooks::addToAdminLinks';
		$GLOBALS['wgHooks']['MakeGlobalVariablesScript'][] = 'Maps\MapsHooks::onMakeGlobalVariablesScript';
		$GLOBALS['wgHooks']['SkinTemplateNavigation::Universal'][] = 'Maps\MapsHooks::onSkinTemplateNavigationUniversal';
		$GLOBALS['wgHooks']['BeforeDisplayNoArticleText'][] = 'Maps\MapsHooks::onBeforeDisplayNoArticleText';
		$GLOBALS['wgHooks']['ShowMissingArticle'][] = 'Maps\MapsHooks::onShowMissingArticle';
		$GLOBALS['wgHooks']['ListDefinedTags'][] = 'Maps\MapsHooks::onRegisterTags';
		$GLOBALS['wgHooks']['ChangeTagsListActive'][] = 'Maps\MapsHooks::onRegisterTags';
		$GLOBALS['wgHooks']['ChangeTagsAllowedAdd'][] = 'Maps\MapsHooks::onChangeTagsAllowedAdd';

		$GLOBALS['wgHooks']['CargoSetFormatClasses'][] = function( array &$formatClasses ) {
			$formatClasses['map'] = CargoFormat::class;
		};
	}

}

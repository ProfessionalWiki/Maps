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

		if ( empty( $GLOBALS['egMapsGoogleGeocodingApiKey'] ) && array_key_exists( 'egMapsGMaps3ApiKey', $GLOBALS ) ) {
			$GLOBALS['egMapsGoogleGeocodingApiKey'] = $GLOBALS['egMapsGMaps3ApiKey'];
		}
	}

	private function registerAllTheThings() {
		$this->registerParserHooks();
		$this->registerParameterTypes();
		$this->registerHooks();
	}

	private function registerParserHooks() {
		$hooks = [];
		$hooks['ParserFirstCallInit'][] = function ( Parser $parser ) {
			MapsFactory::globalInstance()->newParserHookSetup( $parser )->registerParserHooks();
		};

		MapsHooks::registerHookHandlers( $hooks );
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
		$hooks = [];
		$hooks['AdminLinks'][] = 'Maps\MapsHooks::addToAdminLinks';
		$hooks['MakeGlobalVariablesScript'][] = 'Maps\MapsHooks::onMakeGlobalVariablesScript';
		$hooks['SkinTemplateNavigation::Universal'][] = 'Maps\MapsHooks::onSkinTemplateNavigationUniversal';
		$hooks['BeforeDisplayNoArticleText'][] = 'Maps\MapsHooks::onBeforeDisplayNoArticleText';
		$hooks['ShowMissingArticle'][] = 'Maps\MapsHooks::onShowMissingArticle';
		$hooks['ListDefinedTags'][] = 'Maps\MapsHooks::onRegisterTags';
		$hooks['ChangeTagsListActive'][] = 'Maps\MapsHooks::onRegisterTags';
		$hooks['ChangeTagsAllowedAdd'][] = 'Maps\MapsHooks::onChangeTagsAllowedAdd';

		$hooks['CargoSetFormatClasses'][] = function( array &$formatClasses ) {
			$formatClasses['map'] = CargoFormat::class;
		};

		MapsHooks::registerHookHandlers( $hooks );
	}
}

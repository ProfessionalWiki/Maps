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

use DataValues\Geo\Parsers\GeoCoordinateParser;
use FileFetcher\SimpleFileFetcher;
use Maps\CircleParser;
use Maps\DistanceParser;
use Maps\ImageOverlayParser;
use Maps\LineParser;
use Maps\LocationParser;
use Maps\PolygonParser;
use Maps\RectangleParser;
use Maps\ServiceParam;
use Maps\WmsOverlayParser;

if ( defined( 'Maps_COORDS_FLOAT' ) ) {
	// Do not initialize more than once.
	return 1;
}

// The different coordinate notations.
define( 'Maps_COORDS_FLOAT' , 'float' );
define( 'Maps_COORDS_DMS' , 'dms' );
define( 'Maps_COORDS_DM' , 'dm' );
define( 'Maps_COORDS_DD' , 'dd' );

require_once __DIR__ . '/Maps_Settings.php';

// Include the composer autoloader if it is present.
if ( is_readable( __DIR__ . '/vendor/autoload.php' ) ) {
	include_once( __DIR__ . '/vendor/autoload.php' );
}

// Internationalization
$GLOBALS['wgMessagesDirs']['Maps'] = __DIR__ . '/i18n';
$GLOBALS['wgExtensionMessagesFiles']['MapsMagic'] = __DIR__ . '/Maps.i18n.magic.php';
$GLOBALS['wgExtensionMessagesFiles']['MapsAlias'] = __DIR__ . '/Maps.i18n.alias.php';


$GLOBALS['wgExtensionFunctions'][] = function () {
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

	if ( version_compare( $GLOBALS['wgVersion'], '1.23c' , '<' ) ) {
		throw new Exception(
			'This version of Maps requires MediaWiki 1.23 or above; use Maps 3.5.x for older versions.'
			. ' More information at https://github.com/JeroenDeDauw/Maps/blob/master/INSTALL.md'
		);
	}

	define( 'Maps_VERSION' , '4.0.0-RC1' );
	define( 'SM_VERSION', Maps_VERSION );

	if ( $GLOBALS['egMapsGMaps3Language'] === '' ) {
		$GLOBALS['egMapsGMaps3Language'] = $GLOBALS['wgLang'];
	}

	if ( in_array( 'googlemaps3', $GLOBALS['egMapsAvailableServices'] ) ) {
		$GLOBALS['wgSpecialPages']['MapEditor'] = 'SpecialMapEditor';
		$GLOBALS['wgSpecialPageGroups']['MapEditor'] = 'maps';
	}

	$GLOBALS['wgExtensionCredits']['parserhook'][] = [
		'path' => __FILE__ ,
		'name' => 'Maps' ,
		'version' => Maps_VERSION ,
		'author' => [
			'[https://www.mediawiki.org/wiki/User:Jeroen_De_Dauw Jeroen De Dauw]',
			'...'
		] ,
		'url' => 'https://github.com/JeroenDeDauw/Maps/blob/master/README.md#maps' ,
		'descriptionmsg' => 'maps-desc',
		'license-name' => 'GPL-2.0+'
	];

	$GLOBALS['egMapsStyleVersion'] = $GLOBALS['wgStyleVersion'] . '-' . Maps_VERSION;

	$GLOBALS['wgResourceModules'] = array_merge( $GLOBALS['wgResourceModules'], include 'Maps.resources.php' );

	$GLOBALS['wgAPIModules']['geocode'] = 'Maps\Api\Geocode';



	$GLOBALS['wgHooks']['AdminLinks'][]                = 'MapsHooks::addToAdminLinks';
	$GLOBALS['wgHooks']['MakeGlobalVariablesScript'][] = 'MapsHooks::onMakeGlobalVariablesScript';

	// Parser hooks

	// Required for #coordinates.
	$GLOBALS['wgHooks']['ParserFirstCallInit'][] = function( Parser &$parser ) {
		$instance = new MapsCoordinates();
		return $instance->init( $parser );
	};

	$GLOBALS['wgHooks']['ParserFirstCallInit'][] = function( Parser &$parser ) {
		$instance = new MapsDisplayMap();
		return $instance->init( $parser );
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

	// Geocoders

	// Registration of the GeoNames service geocoder.
	$GLOBALS['wgHooks']['GeocoderFirstCallInit'][] = 'MapsGeonamesGeocoder::register';

	// Registration of the Google Geocoding (v2) service geocoder.
	$GLOBALS['wgHooks']['GeocoderFirstCallInit'][] = 'MapsGoogleGeocoder::register';

	// Registration of the geocoder.us service geocoder.
	$GLOBALS['wgHooks']['GeocoderFirstCallInit'][] = 'MapsGeocoderusGeocoder::register';

	// Registration of the OSM Nominatim service geocoder.
	$GLOBALS['wgHooks']['GeocoderFirstCallInit'][] = function() {
		\Maps\Geocoders::registerGeocoder(
			'nominatim',
			new \Maps\Geocoders\NominatimGeocoder( new SimpleFileFetcher() )
		);
		return true;
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
		MapsOpenLayers::class,
		[ 'display_map' => MapsDisplayMapRenderer::class ]
	);



	// Leaflet API
	include_once __DIR__ . '/includes/services/Leaflet/Leaflet.php';

	MapsMappingServices::registerService( 'leaflet', MapsLeaflet::class );
	$leafletMaps = MapsMappingServices::getServiceInstance( 'leaflet' );
	$leafletMaps->addFeature( 'display_map', MapsDisplayMapRenderer::class );




	$GLOBALS['wgAvailableRights'][] = 'geocode';

	// Users that can geocode. By default the same as those that can edit.
	foreach ( $GLOBALS['wgGroupPermissions'] as $group => $rights ) {
		if ( array_key_exists( 'edit' , $rights ) ) {
			$GLOBALS['wgGroupPermissions'][$group]['geocode'] = $GLOBALS['wgGroupPermissions'][$group]['edit'];
		}
	}

	$GLOBALS['wgParamDefinitions']['coordinate'] = [
		'string-parser' => GeoCoordinateParser::class,
	];

	$GLOBALS['wgParamDefinitions']['mappingservice'] = [
		'definition'=> ServiceParam::class,
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

class SemanticMaps {

	private $mwGlobals;

	public static function newFromMediaWikiGlobals( array &$mwGlobals ) {
		return new self( $mwGlobals );
	}

	private function __construct( array &$mwGlobals ) {
		$this->mwGlobals =& $mwGlobals;
	}

	/**
	 * @since 3.4
	 */
	public function initExtension() {
		// Hook for initializing the Geographical Data types.
		$this->mwGlobals['wgHooks']['SMW::DataType::initTypes'][] = 'SemanticMapsHooks::initGeoDataTypes';

		// Hook for defining the default query printer for queries that ask for geographical coordinates.
		$this->mwGlobals['wgHooks']['SMWResultFormat'][] = 'SemanticMapsHooks::addGeoCoordsDefaultFormat';

		// Hook for adding a Semantic Maps links to the Admin Links extension.
		$this->mwGlobals['wgHooks']['AdminLinks'][] = 'SemanticMapsHooks::addToAdminLinks';

		$this->registerResourceModules();

		$this->registerGoogleMaps();
		$this->registerLeaflet();
		$this->registerOpenLayers();

		$this->mwGlobals['smwgResultFormats']['kml'] = SMKMLPrinter::class;

		$this->mwGlobals['smwgResultAliases'][$this->mwGlobals['egMapsDefaultServices']['qp']][] = 'map';
		SMMapPrinter::registerDefaultService( $this->mwGlobals['egMapsDefaultServices']['qp'] );

		// Internationalization
		$this->mwGlobals['wgMessagesDirs']['SemanticMaps'] = __DIR__ . '/i18n';
	}

	private function registerResourceModules() {
		$moduleTemplate = [
			'position' => 'bottom',
			'group' => 'ext.semanticmaps',
		];

		$this->mwGlobals['wgResourceModules']['ext.sm.common'] = $moduleTemplate + [
			'localBasePath' => __DIR__ . '/SemanticMaps/src',
			'remoteExtPath' => 'Maps/SemanticMaps/src',
			'scripts' => [
				'ext.sm.common.js'
			]
		];
	}

	private function registerGoogleMaps() {
		$this->mwGlobals['wgResourceModules']['ext.sm.googlemaps3ajax'] = [
			'localBasePath' => __DIR__ . '/SemanticMaps/src/services/GoogleMaps3',
			'remoteExtPath' => 'Maps/SemanticMaps/src/services/GoogleMaps3',
			'group' => 'ext.semanticmaps',
			'dependencies' => [
				'ext.maps.googlemaps3',
				'ext.sm.common'
			],
			'scripts' => [
				'ext.sm.googlemaps3ajax.js'
			]
		];

		/* @var MapsMappingService $googleMaps */
		$googleMaps = MapsMappingServices::getServiceInstance( 'googlemaps3' );
		$googleMaps->addResourceModules( array( 'ext.sm.googlemaps3ajax' ) );

		SMMapPrinter::registerService( $googleMaps );

		$this->mwGlobals['smwgResultFormats'][$googleMaps->getName()] = SMMapPrinter::class;
		$this->mwGlobals['smwgResultAliases'][$googleMaps->getName()] = $googleMaps->getAliases();
	}

	private function registerLeaflet() {
		$this->mwGlobals['wgResourceModules']['ext.sm.fi.leafletajax'] = [
			'localBasePath' => __DIR__ . '/SemanticMaps/src/services/Leaflet',
			'remoteExtPath' => 'Maps/SemanticMaps/src/services/Leaflet',
			'group' => 'ext.semanticmaps',
			'dependencies' => [
				'ext.maps.leaflet',
				'ext.sm.common'
			],
			'scripts' => [
				'ext.sm.leafletajax.js'
			]
		];

		/* @var MapsMappingService $leaflet */
		$leaflet = MapsMappingServices::getServiceInstance( 'leaflet' );
		$leaflet->addResourceModules( array( 'ext.sm.fi.leafletajax' ) );

		SMMapPrinter::registerService( $leaflet );

		$this->mwGlobals['smwgResultFormats'][$leaflet->getName()] = SMMapPrinter::class;
		$this->mwGlobals['smwgResultAliases'][$leaflet->getName()] = $leaflet->getAliases();
	}

	private function registerOpenLayers() {
		$this->mwGlobals['wgResourceModules']['ext.sm.fi.openlayersajax'] = [
			'localBasePath' => __DIR__ . '/SemanticMaps/src/services/OpenLayers',
			'remoteExtPath' => 'Maps/SemanticMaps/src/services/OpenLayers',
			'group' => 'ext.semanticmaps',
			'dependencies' => [
				'ext.maps.openlayers',
				'ext.sm.common'
			],
			'scripts' => [
				'ext.sm.openlayersajax.js'
			]
		];

		/* @var MapsMappingService $openLayers */
		$openLayers = MapsMappingServices::getServiceInstance( 'openlayers' );
		$openLayers->addResourceModules( array( 'ext.sm.fi.openlayersajax' ) );

		SMMapPrinter::registerService( $openLayers );

		$this->mwGlobals['smwgResultFormats'][$openLayers->getName()] = SMMapPrinter::class;
		$this->mwGlobals['smwgResultAliases'][$openLayers->getName()] = $openLayers->getAliases();
	}

	/**
	 * @since 3.4
	 *
	 * @return string|null
	 */
	public static function getVersion() {
		return SM_VERSION;
	}

}


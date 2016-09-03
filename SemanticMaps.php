<?php

/**
 * Initialization file for the Semantic Maps extension.
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

if ( defined( 'SM_VERSION' ) ) {
	// Do not initialize more than once.
	return 1;
}

if ( version_compare( $GLOBALS['wgVersion'], '1.23c', '<' ) ) {
	throw new Exception(
		'This version of Semantic Maps requires MediaWiki 1.23 or above; use Semantic Maps 3.3.x for older versions.'
		. ' See https://github.com/SemanticMediaWiki/SemanticMaps/blob/master/INSTALL.md for more info.'
	);
}

if ( !defined( 'Maps_VERSION' ) && is_readable( __DIR__ . '/vendor/autoload.php' ) ) {
	include_once( __DIR__ . '/vendor/autoload.php' );
}

if ( !defined( 'Maps_VERSION' ) ) {
	throw new Exception( 'You need to have Maps installed in order to use Semantic Maps' );
}

define( 'SM_VERSION', '3.4.0-alpha' );

require_once __DIR__ . '/DefaultSettings.php';

SemanticMaps::newFromMediaWikiGlobals( $GLOBALS )->initExtension();

$GLOBALS['wgExtensionFunctions'][] = function() {
	// Hook for initializing the Geographical Data types.
	$GLOBALS['wgHooks']['SMW::DataType::initTypes'][] = 'SemanticMapsHooks::initGeoDataTypes';

	// Hook for defining the default query printer for queries that ask for geographical coordinates.
	$GLOBALS['wgHooks']['SMWResultFormat'][] = 'SemanticMapsHooks::addGeoCoordsDefaultFormat';

	// Hook for adding a Semantic Maps links to the Admin Links extension.
	$GLOBALS['wgHooks']['AdminLinks'][] = 'SemanticMapsHooks::addToAdminLinks';

	$GLOBALS['wgHooks']['sfFormPrinterSetup'][] = 'SemanticMaps\FormInputsSetup::run';
};

/**
 * @codeCoverageIgnore
 */
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
		$this->mwGlobals['wgExtensionCredits']['semantic'][] = [
			'path' => __FILE__,
			'name' => 'Semantic Maps',
			'version' => SM_VERSION,
			'author' => [
				'[https://www.mediawiki.org/wiki/User:Jeroen_De_Dauw Jeroen De Dauw]'
			],
			'url' => 'https://github.com/SemanticMediaWiki/SemanticMaps/blob/master/README.md#semantic-maps',
			'descriptionmsg' => 'semanticmaps-desc',
			'license-name'   => 'GPL-2.0+'
		];

		include_once __DIR__ . '/src/queryprinters/SM_QueryPrinters.php';

		$this->registerResourceModules();

		$this->registerGoogleMaps();
		$this->registerLeaflet();
		$this->registerOpenLayers();

		// Internationalization
		$this->mwGlobals['wgMessagesDirs']['SemanticMaps'] = __DIR__ . '/i18n';
	}

	private function registerResourceModules() {
		$moduleTemplate = [
			'position' => 'bottom',
			'group' => 'ext.semanticmaps',
		];

		$this->mwGlobals['wgResourceModules']['ext.sm.forminputs'] = $moduleTemplate + [
			'dependencies' => [ 'ext.maps.coord' ],
			'localBasePath' => __DIR__ . '/src/forminputs',
			'remoteExtPath' => 'SemanticMaps/src/forminputs',
			'scripts' => [
				'jquery.mapforminput.js'
			],
			'messages' => [
				'semanticmaps_enteraddresshere',
				'semanticmaps-updatemap',
				'semanticmaps_lookupcoordinates',
				'semanticmaps-forminput-remove',
				'semanticmaps-forminput-add',
				'semanticmaps-forminput-locations'
			]
		];

		$this->mwGlobals['wgResourceModules']['ext.sm.common'] = $moduleTemplate + [
			'localBasePath' => __DIR__ . '/src',
			'remoteExtPath' => 'SemanticMaps/src',
			'scripts' => [
				'ext.sm.common.js'
			]
		];
	}

	private function registerGoogleMaps() {
		$moduleTemplate = [
			'localBasePath' => __DIR__ . '/src/services/GoogleMaps3',
			'remoteExtPath' => 'SemanticMaps/src/services/GoogleMaps3',
			'group' => 'ext.semanticmaps',
		];

		$this->mwGlobals['wgResourceModules']['ext.sm.fi.googlemaps3ajax'] = $moduleTemplate + [
				'dependencies' => [
					'ext.maps.googlemaps3',
					'ext.sm.common'
				],
				'scripts' => [
					'ext.sm.googlemaps3ajax.js'
				]
			];

		$this->mwGlobals['wgResourceModules']['ext.sm.fi.googlemaps3'] = $moduleTemplate + [
				'dependencies' => [
					'ext.sm.fi.googlemaps3.single',
				],
				'scripts' => [
					'ext.sm.googlemapsinput.js',
				],
			];

		$this->mwGlobals['wgResourceModules']['ext.sm.fi.googlemaps3.single'] = $moduleTemplate + [
				'dependencies' => [
					'ext.maps.googlemaps3',
					'ext.sm.forminputs',
				],
				'scripts' => [
					'jquery.googlemapsinput.js',
				],
				'messages' => [
				]
			];

		$this->mwGlobals['wgHooks']['MappingServiceLoad'][] = function() {
			global $wgAutoloadClasses;

			$wgAutoloadClasses['SMGoogleMaps3FormInput'] = __DIR__ . '/SM_GoogleMaps3FormInput.php';

			MapsMappingServices::registerServiceFeature( 'googlemaps3', 'qp', 'SMMapPrinter' );
			MapsMappingServices::registerServiceFeature( 'googlemaps3', 'fi', 'SMGoogleMaps3FormInput' );

			/* @var MapsMappingService $googleMaps */
			$googleMaps = MapsMappingServices::getServiceInstance( 'googlemaps3' );
			$googleMaps->addResourceModules( array( 'ext.sm.fi.googlemaps3ajax' ) );

			return true;
		};
	}

	private function registerLeaflet() {
		$this->mwGlobals['wgResourceModules']['ext.sm.fi.leafletajax'] = [
			'localBasePath' => __DIR__ . '/src/services/Leaflet',
			'remoteExtPath' => 'SemanticMaps/src/services/Leaflet',
			'group' => 'ext.semanticmaps',
			'dependencies' => [
				'ext.maps.leaflet',
				'ext.sm.common'
			],
			'scripts' => [
				'ext.sm.leafletajax.js'
			]
		];

		$this->mwGlobals['wgHooks']['MappingServiceLoad'][] = function() {
			MapsMappingServices::registerServiceFeature( 'leaflet', 'qp', 'SMMapPrinter' );

			/* @var MapsMappingService $leaflet */
			$leaflet = MapsMappingServices::getServiceInstance( 'leaflet' );
			$leaflet->addResourceModules( array( 'ext.sm.fi.leafletajax' ) );

			return true;
		};
	}

	private function registerOpenLayers() {
		$this->mwGlobals['wgResourceModules']['ext.sm.fi.openlayersajax'] = [
			'localBasePath' => __DIR__ . '/src/services/OpenLayers',
			'remoteExtPath' => 'SemanticMaps/src/services/OpenLayers',
			'group' => 'ext.semanticmaps',
			'dependencies' => [
				'ext.maps.openlayers',
				'ext.sm.common'
			],
			'scripts' => [
				'ext.sm.openlayersajax.js'
			]
		];

		$this->mwGlobals['wgHooks']['MappingServiceLoad'][] = function() {
			MapsMappingServices::registerServiceFeature( 'openlayers', 'qp', 'SMMapPrinter' );

			/* @var MapsMappingService $openlayers */
			$openlayers = MapsMappingServices::getServiceInstance( 'openlayers' );
			$openlayers->addResourceModules( array( 'ext.sm.fi.openlayersajax' ) );

			return true;
		};
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

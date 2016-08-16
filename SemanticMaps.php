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

SemanticMaps::initExtension();

$GLOBALS['wgExtensionFunctions'][] = function() {
	SemanticMaps::onExtensionFunction();
};

/**
 * @codeCoverageIgnore
 */
class SemanticMaps {

	/**
	 * @since 3.4
	 */
	public static function initExtension() {

		// Load DefaultSettings
		require_once __DIR__ . '/DefaultSettings.php';

		define( 'SM_VERSION', '3.4.0-alpha' );

		$GLOBALS['wgExtensionCredits']['semantic'][] = [
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

		$moduleTemplate = [
			'position' => 'bottom',
			'group' => 'ext.semanticmaps',
		];

		$GLOBALS['wgResourceModules']['ext.sm.forminputs'] = $moduleTemplate + [
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

		$GLOBALS['wgResourceModules']['ext.sm.common'] = $moduleTemplate + [
			'localBasePath' => __DIR__ . '/src',
			'remoteExtPath' => 'SemanticMaps/src',
			'scripts' => [
				'ext.sm.common.js'
			]
		];

		include_once __DIR__ . '/src/services/GoogleMaps3/SM_GoogleMaps3.php';
		include_once __DIR__ . '/src/services/Leaflet/SM_Leaflet.php';
		include_once __DIR__ . '/src/services/OpenLayers/SM_OpenLaysers.php';

		// Internationalization
		$GLOBALS['wgMessagesDirs']['SemanticMaps'] = __DIR__ . '/i18n';
	}

	/**
	 * @since 3.4
	 */
	public static function onExtensionFunction() {

		// Hook for initializing the Geographical Data types.
		$GLOBALS['wgHooks']['SMW::DataType::initTypes'][] = 'SemanticMapsHooks::initGeoDataTypes';

		// Hook for defining the default query printer for queries that ask for geographical coordinates.
		$GLOBALS['wgHooks']['SMWResultFormat'][] = 'SemanticMapsHooks::addGeoCoordsDefaultFormat';

		// Hook for adding a Semantic Maps links to the Admin Links extension.
		$GLOBALS['wgHooks']['AdminLinks'][] = 'SemanticMapsHooks::addToAdminLinks';

		$GLOBALS['wgHooks']['sfFormPrinterSetup'][] = 'SemanticMaps\FormInputsSetup::run';
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

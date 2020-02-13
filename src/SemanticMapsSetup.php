<?php

namespace Maps;

use Maps\SemanticMW\ResultPrinters\KmlPrinter;
use Maps\SemanticMW\ResultPrinters\MapPrinter;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SemanticMapsSetup {

	private $mwGlobals;
	private $mappingServices;

	private function __construct( array &$mwGlobals, MappingServices $mappingServices ) {
		$this->mwGlobals =& $mwGlobals;
		$this->mappingServices = $mappingServices;
	}

	public static function newFromMediaWikiGlobals( array &$mwGlobals, MappingServices $mappingServices ) {
		return new self( $mwGlobals, $mappingServices );
	}

	public function initExtension() {
		// Hook for initializing the Geographical Data types.
		$this->mwGlobals['wgHooks']['SMW::DataType::initTypes'][] = 'Maps\MediaWiki\SemanticMapsHooks::initGeoDataTypes';

		// Hook for defining the default query printer for queries that ask for geographical coordinates.
		$this->mwGlobals['wgHooks']['SMWResultFormat'][] = 'Maps\MediaWiki\SemanticMapsHooks::addGeoCoordsDefaultFormat';

		$this->registerGoogleMaps();
		$this->registerLeaflet();

		$this->mwGlobals['smwgResultFormats']['kml'] = KmlPrinter::class;

		$this->mwGlobals['smwgResultAliases'][$this->mwGlobals['egMapsDefaultService']][] = 'map';
		MapPrinter::registerDefaultService( $this->mwGlobals['egMapsDefaultService'] );

		// Internationalization
		$this->mwGlobals['wgMessagesDirs']['SemanticMaps'] = __DIR__ . '/i18n';
	}

	private function registerGoogleMaps() {
		if ( $this->mappingServices->nameIsKnown( 'googlemaps3' ) ) {
			$googleMaps = $this->mappingServices->getService( 'googlemaps3' );

			MapPrinter::registerService( $googleMaps );

			$this->mwGlobals['smwgResultFormats'][$googleMaps->getName()] = MapPrinter::class;
			$this->mwGlobals['smwgResultAliases'][$googleMaps->getName()] = $googleMaps->getAliases();
		}
	}

	private function registerLeaflet() {
		if ( $this->mappingServices->nameIsKnown( 'leaflet' ) ) {
			$leaflet = $this->mappingServices->getService( 'leaflet' );

			MapPrinter::registerService( $leaflet );

			$this->mwGlobals['smwgResultFormats'][$leaflet->getName()] = MapPrinter::class;
			$this->mwGlobals['smwgResultAliases'][$leaflet->getName()] = $leaflet->getAliases();
		}
	}

}

<?php

declare( strict_types = 1 );

namespace Maps;

use Maps\Map\SemanticFormat\MapPrinter;
use Maps\SemanticMW\CoordinateValue;
use Maps\SemanticMW\KmlPrinter;
use SMW\DataTypeRegistry;
use SMWDataItem;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SemanticMapsSetup {

	private MappingServices $mappingServices;

	public function __construct( MappingServices $mappingServices ) {
		$this->mappingServices = $mappingServices;
	}

	public function initExtension() {
		// Hook for initializing the Geographical Data types.
		$GLOBALS['wgHooks']['SMW::DataType::initTypes'][] = function() {
			DataTypeRegistry::getInstance()->registerDatatype(
				'_geo',
				CoordinateValue::class,
				SMWDataItem::TYPE_GEO
			);

			return true;
		};

		// Hook for defining the default query printer for queries that ask for geographical coordinates.
		$GLOBALS['wgHooks']['SMWResultFormat'][] = 'Maps\MapsHooks::addGeoCoordsDefaultFormat';

		$this->registerGoogleMaps();
		$this->registerLeaflet();

		$GLOBALS['smwgResultFormats']['kml'] = KmlPrinter::class;

		$GLOBALS['smwgResultAliases'][$GLOBALS['egMapsDefaultService']][] = 'map';
		MapPrinter::registerDefaultService( $GLOBALS['egMapsDefaultService'] );
	}

	private function registerGoogleMaps() {
		if ( $this->mappingServices->nameIsKnown( 'googlemaps3' ) ) {
			$googleMaps = $this->mappingServices->getService( 'googlemaps3' );

			MapPrinter::registerService( $googleMaps );

			$GLOBALS['smwgResultFormats'][$googleMaps->getName()] = MapPrinter::class;
			$GLOBALS['smwgResultAliases'][$googleMaps->getName()] = $googleMaps->getAliases();
		}
	}

	private function registerLeaflet() {
		if ( $this->mappingServices->nameIsKnown( 'leaflet' ) ) {
			$leaflet = $this->mappingServices->getService( 'leaflet' );

			MapPrinter::registerService( $leaflet );

			$GLOBALS['smwgResultFormats'][$leaflet->getName()] = MapPrinter::class;
			$GLOBALS['smwgResultAliases'][$leaflet->getName()] = $leaflet->getAliases();
		}
	}

}

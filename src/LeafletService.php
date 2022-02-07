<?php

declare( strict_types = 1 );

namespace Maps;

use Html;
use Maps\DataAccess\ImageRepository;
use Maps\Map\MapData;
use ParamProcessor\ParameterTypes;
use ParamProcessor\ProcessingResult;

/**
 * @licence GNU GPL v2+
 */
class LeafletService implements MappingService {

	private ImageRepository $imageFinder;
	private array $addedDependencies = [];

	public function __construct( ImageRepository $imageFinder ) {
		$this->imageFinder = $imageFinder;
	}

	public function getName(): string {
		return 'leaflet';
	}

	public function getAliases(): array {
		return [ 'leafletmaps', 'leaflet' ]; // TODO: main name should not be in here?
	}

	public function getParameterInfo(): array {
		$params = MapsFunctions::getCommonParameters();

		$params['zoom'] = [
			'type' => ParameterTypes::INTEGER,
			'range' => [ 0, 20 ],
			'default' => false,
			'message' => 'maps-par-zoom'
		];

		$params['defzoom'] = [
			'type' => ParameterTypes::INTEGER,
			'range' => [ 0, 20 ],
			'default' => self::getDefaultZoom(),
			'message' => 'maps-leaflet-par-defzoom'
		];

		$params['layers'] = [
			'aliases' => 'layer',
			'type' => 'string',
			'islist' => true,
			'values' => array_keys( $GLOBALS['egMapsLeafletAvailableLayers'], true, true ),
			'default' => $GLOBALS['egMapsLeafletLayers'],
			'message' => 'maps-leaflet-par-layers',
		];

		$params['image layers'] = [
			'aliases' => [ 'image layer', 'imagelayers', 'imagelayer' ],
			'type' => 'string',
			'islist' => true,
			'default' => [],
			'message' => 'maps-leaflet-par-image-layers',
		];

		$params['overlays'] = [
			'aliases' => [ 'overlaylayers' ],
			'type' => ParameterTypes::STRING,
			'islist' => true,
			'values' => array_keys( $GLOBALS['egMapsLeafletAvailableOverlayLayers'], true, true ),
			'default' => $GLOBALS['egMapsLeafletOverlayLayers'],
			'message' => 'maps-leaflet-par-overlaylayers',
		];

		$params['resizable'] = [
			'type' => ParameterTypes::BOOLEAN,
			'default' => $GLOBALS['egMapsResizableByDefault'],
			'message' => 'maps-par-resizable'
		];

		$params['fullscreen'] = [
			'aliases' => [ 'enablefullscreen' ],
			'type' => ParameterTypes::BOOLEAN,
			'default' => false,
			'message' => 'maps-par-enable-fullscreen',
		];

		$params['scrollwheelzoom'] = [
			'aliases' => [ 'scrollzoom' ],
			'type' => ParameterTypes::BOOLEAN,
			'default' => true,
			'message' => 'maps-par-scrollwheelzoom',
		];

		$params['cluster'] = [
			'aliases' => [ 'markercluster' ],
			'type' => ParameterTypes::BOOLEAN,
			'default' => false,
			'message' => 'maps-par-markercluster',
		];

		$params['clustermaxzoom'] = [
			'type' => ParameterTypes::INTEGER,
			'default' => 20,
			'message' => 'maps-par-clustermaxzoom',
		];

		$params['clusterzoomonclick'] = [
			'type' => ParameterTypes::BOOLEAN,
			'default' => true,
			'message' => 'maps-par-clusterzoomonclick',
		];

		$params['clustermaxradius'] = [
			'type' => ParameterTypes::INTEGER,
			'default' => 80,
			'message' => 'maps-par-maxclusterradius',
		];

		$params['clusterspiderfy'] = [
			'type' => ParameterTypes::BOOLEAN,
			'default' => true,
			'message' => 'maps-leaflet-par-clusterspiderfy',
		];

		$params['geojson'] = [
			'type' => ParameterTypes::STRING,
			'default' => '',
			'message' => 'maps-displaymap-par-geojson',
		];

		$params['clicktarget'] = [
			'type' => ParameterTypes::STRING,
			'default' => '',
			'message' => 'maps-leaflet-par-clicktarget',
		];

		return $params;
	}

	/**
	 * @since 3.0
	 */
	public function getDefaultZoom() {
		return $GLOBALS['egMapsLeafletZoom'];
	}

	public function newMapId(): string {
		static $mapsOnThisPage = 0;

		$mapsOnThisPage++;

		return 'map_leaflet_' . $mapsOnThisPage;
	}

	public function getResourceModules( array $params ): array {
		$modules = [];

		$modules[] = 'ext.maps.leaflet.loader';

		if ( $params['resizable'] ) {
			$modules[] = 'ext.maps.resizable';
		}

		if ( $params['cluster'] ) {
			$modules[] = 'ext.maps.leaflet.markercluster';
		}

		if ( $params['fullscreen'] ) {
			$modules[] = 'ext.maps.leaflet.fullscreen';
		}

		if ( array_key_exists( 'geojson', $params ) ) {
			$modules[] = 'ext.maps.leaflet.editor';
		}

		if ( array_key_exists( 'ajaxquery', $params ) && $params['ajaxquery'] !== '' ) {
			$modules[] = 'ext.maps.leaflet.leafletajax';
		}

		return $modules;
	}

	public function getDependencyHtml( array $params ): string {
		$dependencies = [];

		foreach ( $this->getDependencies( $params ) as $dependency ) {
			if ( !in_array( $dependency, $this->addedDependencies ) ) {
				$dependencies[] = $dependency;
				$this->addedDependencies[] = $dependency;
			}
		}

		return implode( '', $dependencies );
	}

	private function getDependencies( array $params ): array {
		return $this->getLayerDependencies( $params );
	}

	private function getLayerDependencies( array $params ) {
		global $egMapsLeafletLayerDependencies, $egMapsLeafletAvailableLayers,
			   $egMapsLeafletLayersApiKeys;

		$layerDependencies = [];

		foreach ( $params['layers'] as $layerName ) {
			if ( array_key_exists( $layerName, $egMapsLeafletAvailableLayers )
				&& $egMapsLeafletAvailableLayers[$layerName]
				&& array_key_exists( $layerName, $egMapsLeafletLayersApiKeys )
				&& array_key_exists( $layerName, $egMapsLeafletLayerDependencies ) ) {
				$layerDependencies[] = '<script src="' . $egMapsLeafletLayerDependencies[$layerName] .
					$egMapsLeafletLayersApiKeys[$layerName] . '"></script>';
			}
		}

		return array_unique( $layerDependencies );
	}

	public function newMapDataFromProcessingResult( ProcessingResult $processingResult ): MapData {
		return $this->newMapDataFromParameters( $processingResult->getParameterArray() );
	}

	public function newMapDataFromParameters( array $params ): MapData {
		if ( $params['geojson'] !== '' ) {
			$fetcher = MapsFactory::globalInstance()->newGeoJsonFetcher();

			$result = $fetcher->fetch( $params['geojson'] );

			$params['geojson'] = $result->getContent();
			$params['GeoJsonSource'] = $result->getTitleValue() === null ? null : $result->getTitleValue()->getText();
			$params['GeoJsonRevisionId'] = $result->getRevisionId();
		}

		$params['imageLayers'] = $this->getJsImageLayers( $params['image layers'] );

		return new MapData( $params );
	}

	private function getJsImageLayers( array $imageLayers ) {
		$jsImageLayers = [];

		foreach ( $imageLayers as $imageLayer ) {
			$image = $this->imageFinder->getByName( $imageLayer );

			if ( $image !== null ) {
				$jsImageLayers[] = [
					'name' => $imageLayer,
					'url' => $image->getUrl(),
					'width' => 100,
					'height' => $image->getHeightInPx() / $image->getWidthInPx() * 100
				];
			}
		}

		return $jsImageLayers;
	}

}

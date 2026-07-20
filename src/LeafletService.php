<?php

declare( strict_types = 1 );

namespace Maps;

use Maps\Config\EffectiveSettings;
use Maps\DataAccess\ImageRepository;
use Maps\Map\MapData;
use ParamProcessor\ParameterTypes;
use ParamProcessor\ProcessingResult;

/**
 * @licence GNU GPL v2+
 */
class LeafletService implements MappingService {

	private ImageRepository $imageFinder;
	private EffectiveSettings $config;
	private ?LeafletLayerDefinitions $layerDefinitions = null;
	private array $addedDependencies = [];

	public function __construct( ImageRepository $imageFinder, EffectiveSettings $config ) {
		$this->imageFinder = $imageFinder;
		$this->config = $config;
	}

	/**
	 * @return array<string, bool>
	 */
	private function availableLayers(): array {
		return $this->config->get( 'egMapsLeafletAvailableLayers' );
	}

	/**
	 * @return array<string, bool>
	 */
	private function availableOverlays(): array {
		return $this->config->get( 'egMapsLeafletAvailableOverlayLayers' );
	}

	/**
	 * Built lazily and memoized so the MediaWiki:Maps config page is only read at parse time, never
	 * during extension setup.
	 */
	private function getLayerDefinitions(): LeafletLayerDefinitions {
		$this->layerDefinitions ??= new LeafletLayerDefinitions( $this->config->get( 'egMapsLeafletLayerDefinitions' ) );

		return $this->layerDefinitions;
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
			'default' => $this->getDefaultZoom(),
			'message' => 'maps-leaflet-par-defzoom'
		];

		$params['layers'] = [
			'aliases' => 'layer',
			'type' => 'string',
			'islist' => true,
			'values' => $this->availableLayerNames( $this->availableLayers() ),
			'default' => $this->config->get( 'egMapsLeafletLayers' ),
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
			'values' => $this->availableLayerNames( $this->availableOverlays() ),
			'default' => $this->config->get( 'egMapsLeafletOverlayLayers' ),
			'message' => 'maps-leaflet-par-overlaylayers',
		];

		$params['resizable'] = [
			'type' => ParameterTypes::BOOLEAN,
			'default' => $this->config->get( 'egMapsResizableByDefault' ),
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

		$params['clusteranimate'] = [
			'type' => ParameterTypes::BOOLEAN,
			'default' => true,
			'message' => 'maps-par-clusteranimate',
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
	 * The enabled stock layer names plus the names of the custom layer definitions, which are
	 * valid values for both the layers and overlays parameters.
	 *
	 * @param array<string, bool> $availableStockLayers
	 * @return string[]
	 */
	private function availableLayerNames( array $availableStockLayers ): array {
		return array_values(
			array_unique(
				array_merge(
					array_keys( $availableStockLayers, true, true ),
					$this->getLayerDefinitions()->getLayerNames()
				)
			)
		);
	}

	/**
	 * @since 3.0
	 */
	public function getDefaultZoom() {
		return $this->config->get( 'egMapsLeafletZoom' );
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

		if ( $params['geojson'] !== '' ) {
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
		global $egMapsLeafletLayerDependencies, $egMapsLeafletLayersApiKeys;

		$availableLayers = $this->availableLayers();

		$layerDependencies = [];

		foreach ( $params['layers'] as $layerName ) {
			if ( array_key_exists( $layerName, $availableLayers )
				&& $availableLayers[$layerName]
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

		$params['overlays'] = $this->filterToAvailable(
			$params['overlays'],
			$this->availableWithDefinitions( $this->availableOverlays() )
		);
		$params['layers'] = $this->filterToAvailable(
			$params['layers'],
			$this->availableWithDefinitions( $this->availableLayers() )
		);

		$params = $this->addUsedLayerDefinitions( $params );

		return new MapData( $params );
	}

	/**
	 * Removes values that are not enabled in the available-layers whitelist. ParamProcessor only
	 * adds a non-fatal warning for such values rather than dropping them, so without this an editor
	 * can inject arbitrary strings that end up as Leaflet layer-control labels.
	 *
	 * @param string[] $values
	 * @param array<string, bool> $available
	 * @return string[]
	 */
	private function filterToAvailable( array $values, array $available ): array {
		return array_values(
			array_filter(
				$values,
				static fn ( string $value ): bool => ( $available[$value] ?? false ) === true
			)
		);
	}

	/**
	 * @param array<string, bool> $available
	 * @return array<string, bool>
	 */
	private function availableWithDefinitions( array $available ): array {
		// Union rather than array_merge: array_merge renumbers integer-like keys, which would drop
		// a custom layer whose name is purely numeric (e.g. "1904"). Custom names go on the left so
		// they take precedence over a same-named stock layer.
		return array_fill_keys( $this->getLayerDefinitions()->getLayerNames(), true ) + $available;
	}

	/**
	 * Serializes the custom layer definitions actually used by this map into the map data, so only
	 * the relevant definitions are shipped to the client rather than the whole catalog.
	 */
	private function addUsedLayerDefinitions( array $params ): array {
		$usedDefinitions = $this->getLayerDefinitions()->getDefinitions(
			array_merge( $params['layers'], $params['overlays'] )
		);

		if ( $usedDefinitions !== [] ) {
			$params['layerDefinitions'] = $usedDefinitions;
		}

		return $params;
	}

	private function getJsImageLayers( array $imageLayers ) {
		$jsImageLayers = [];

		foreach ( $imageLayers as $imageLayer ) {
			$image = $this->imageFinder->getByName( $imageLayer );

			if ( $image !== null && $image->getWidthInPx() > 0 ) {
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

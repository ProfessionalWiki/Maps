<?php

declare( strict_types = 1 );

namespace Maps;

/**
 * The resolved Leaflet layer configuration consumed by LeafletService, after combining the PHP
 * settings with the MediaWiki:Maps config page.
 *
 * @licence GNU GPL v2+
 */
class LeafletConfig {

	/**
	 * @param array<string, bool> $availableLayers
	 * @param array<string, bool> $availableOverlays
	 * @param string[] $defaultLayers
	 * @param string[] $defaultOverlays
	 */
	public function __construct(
		private LeafletLayerDefinitions $layerDefinitions,
		private array $defaultLayers,
		private array $defaultOverlays,
		private array $availableLayers,
		private array $availableOverlays
	) {
	}

	public function getLayerDefinitions(): LeafletLayerDefinitions {
		return $this->layerDefinitions;
	}

	/**
	 * @return string[]
	 */
	public function getDefaultLayers(): array {
		return $this->defaultLayers;
	}

	/**
	 * @return string[]
	 */
	public function getDefaultOverlays(): array {
		return $this->defaultOverlays;
	}

	/**
	 * @return array<string, bool>
	 */
	public function getAvailableLayers(): array {
		return $this->availableLayers;
	}

	/**
	 * @return array<string, bool>
	 */
	public function getAvailableOverlays(): array {
		return $this->availableOverlays;
	}

}

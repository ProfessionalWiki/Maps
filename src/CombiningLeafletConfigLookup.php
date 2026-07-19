<?php

declare( strict_types = 1 );

namespace Maps;

/**
 * Resolves the effective Leaflet configuration by combining the PHP settings with the
 * MediaWiki:Maps config page, with the wiki page taking precedence:
 *
 * * Layer definitions and availability maps are merged per name, the wiki value winning on
 *   collision.
 * * The default layer and overlay selections are replaced wholesale when the wiki page sets them.
 *
 * The wiki page is read lazily on first use and the result is memoized for the request. When wiki
 * config is disabled, or the page is missing or unreadable, the PHP settings are used unchanged.
 *
 * @licence GNU GPL v2+
 */
class CombiningLeafletConfigLookup implements LeafletConfigLookup {

	private ?LeafletConfig $config = null;

	/**
	 * @param array{
	 *     layerDefinitions: array,
	 *     defaultLayers: string[],
	 *     defaultOverlays: string[],
	 *     availableLayers: array<string, bool>,
	 *     availableOverlays: array<string, bool>
	 * } $phpConfig
	 */
	public function __construct(
		private array $phpConfig,
		private LeafletConfigSource $wikiConfigSource,
		private bool $wikiConfigEnabled
	) {
	}

	public function getConfig(): LeafletConfig {
		$this->config ??= $this->buildConfig();

		return $this->config;
	}

	private function buildConfig(): LeafletConfig {
		$raw = $this->phpConfig;

		if ( $this->wikiConfigEnabled ) {
			$wikiConfig = $this->wikiConfigSource->getLeafletConfig();

			if ( $wikiConfig !== null ) {
				$raw = $this->combine( $this->phpConfig, $wikiConfig );
			}
		}

		return new LeafletConfig(
			new LeafletLayerDefinitions( $raw['layerDefinitions'] ),
			$raw['defaultLayers'],
			$raw['defaultOverlays'],
			$raw['availableLayers'],
			$raw['availableOverlays']
		);
	}

	private function combine( array $php, array $wiki ): array {
		return [
			'layerDefinitions' => $this->mergeByName( $php['layerDefinitions'], $wiki['layerDefinitions'] ?? null ),
			'defaultLayers' => $this->stringList( $wiki['defaultLayers'] ?? null ) ?? $php['defaultLayers'],
			'defaultOverlays' => $this->stringList( $wiki['defaultOverlays'] ?? null ) ?? $php['defaultOverlays'],
			'availableLayers' => $this->mergeAvailability( $php['availableLayers'], $wiki['availableLayers'] ?? null ),
			'availableOverlays' => $this->mergeAvailability( $php['availableOverlays'], $wiki['availableOverlays'] ?? null ),
		];
	}

	private function mergeByName( array $php, mixed $wiki ): array {
		if ( !is_array( $wiki ) ) {
			return $php;
		}

		// Union rather than array_merge: array_merge renumbers integer-like keys, which would drop
		// a custom layer whose name is purely numeric (e.g. "1904"). Wiki entries go on the left so
		// they take precedence over a same-named PHP entry.
		return $wiki + $php;
	}

	private function mergeAvailability( array $php, mixed $wiki ): array {
		if ( !is_array( $wiki ) ) {
			return $php;
		}

		$coerced = [];

		foreach ( $wiki as $name => $enabled ) {
			$coerced[$name] = (bool)$enabled;
		}

		return $coerced + $php;
	}

	private function stringList( mixed $value ): ?array {
		if ( !is_array( $value ) ) {
			return null;
		}

		foreach ( $value as $item ) {
			if ( !is_string( $item ) ) {
				return null;
			}
		}

		return array_values( $value );
	}

}

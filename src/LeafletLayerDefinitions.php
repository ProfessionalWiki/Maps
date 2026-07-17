<?php

declare( strict_types = 1 );

namespace Maps;

/**
 * Admin-defined custom Leaflet base and overlay layers, configured via
 * $egMapsLeafletLayerDefinitions. Normalizes and validates the raw configuration and is the
 * single source of truth for which custom layer names exist and what their definition is.
 *
 * @licence GNU GPL v2+
 */
class LeafletLayerDefinitions {

	/**
	 * @var array<string, array{url: string, options: array, wms: bool}>
	 */
	private array $definitions;

	/**
	 * @param array $rawDefinitions Layer name to raw definition, as configured via
	 *        $egMapsLeafletLayerDefinitions. Invalid definitions are skipped.
	 */
	public function __construct( array $rawDefinitions ) {
		$this->definitions = $this->normalizeAll( $rawDefinitions );
	}

	private function normalizeAll( array $rawDefinitions ): array {
		$definitions = [];

		foreach ( $rawDefinitions as $name => $rawDefinition ) {
			$definition = $this->normalize( $rawDefinition );

			if ( $definition !== null ) {
				$definitions[$name] = $definition;
			}
		}

		return $definitions;
	}

	/**
	 * @return array{url: string, options: array, wms: bool}|null
	 */
	private function normalize( mixed $rawDefinition ): ?array {
		if ( !is_array( $rawDefinition ) ) {
			return null;
		}

		$url = $rawDefinition['url'] ?? null;

		if ( !is_string( $url ) || $url === '' ) {
			return null;
		}

		return [
			'url' => $url,
			'options' => is_array( $rawDefinition['options'] ?? null ) ? $rawDefinition['options'] : [],
			'wms' => (bool)( $rawDefinition['wms'] ?? false ),
		];
	}

	/**
	 * @return string[]
	 */
	public function getLayerNames(): array {
		return array_keys( $this->definitions );
	}

	/**
	 * @param string[] $names
	 * @return array<string, array{url: string, options: array, wms: bool}> Normalized definitions
	 *         for the requested names that are defined. Unknown names are omitted.
	 */
	public function getDefinitions( array $names ): array {
		return array_intersect_key(
			$this->definitions,
			array_fill_keys( $names, true )
		);
	}

}

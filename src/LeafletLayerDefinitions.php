<?php

declare( strict_types = 1 );

namespace Maps;

/**
 * Admin-defined custom Leaflet base and overlay layers, coming from $egMapsLeafletLayerDefinitions
 * and/or the MediaWiki:Maps config page. Normalizes and hardens the raw configuration and is the
 * single source of truth for which custom layer names exist and what their definition is.
 *
 * Hardening (see LeafletLayerContract) is applied identically to both config sources: the url and
 * errorTileUrl must be http(s) URLs, only allowlisted Leaflet options are kept, the attribution is
 * sanitized, and layer names that are empty, too long or prototype-polluting are rejected.
 *
 * @licence GNU GPL v2+
 */
class LeafletLayerDefinitions {

	private AttributionSanitizer $attributionSanitizer;

	/**
	 * @var array<string, array{url: string, options: array, wms: bool}>
	 */
	private array $definitions;

	/**
	 * @param array $rawDefinitions Layer name to raw definition. Invalid definitions are skipped.
	 */
	public function __construct( array $rawDefinitions ) {
		$this->attributionSanitizer = new AttributionSanitizer();
		$this->definitions = $this->normalizeAll( $rawDefinitions );
	}

	private function normalizeAll( array $rawDefinitions ): array {
		$definitions = [];

		foreach ( $rawDefinitions as $name => $rawDefinition ) {
			if ( !LeafletLayerContract::isValidLayerName( (string)$name ) ) {
				continue;
			}

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

		if ( !is_string( $url ) || !LeafletLayerContract::isValidLayerUrl( $url ) ) {
			return null;
		}

		$wms = (bool)( $rawDefinition['wms'] ?? false );

		return [
			'url' => $url,
			'options' => $this->normalizeOptions(
				is_array( $rawDefinition['options'] ?? null ) ? $rawDefinition['options'] : [],
				$wms
			),
			'wms' => $wms,
		];
	}

	private function normalizeOptions( array $options, bool $wms ): array {
		$allowed = array_fill_keys( LeafletLayerContract::allowedOptions( $wms ), true );

		$normalized = [];

		foreach ( $options as $key => $value ) {
			if ( !isset( $allowed[$key] ) ) {
				continue;
			}

			if ( $key === 'attribution' ) {
				if ( is_string( $value ) ) {
					$normalized[$key] = $this->attributionSanitizer->sanitize( $value );
				}
			} elseif ( in_array( $key, LeafletLayerContract::URL_OPTIONS, true ) ) {
				if ( is_string( $value ) && LeafletLayerContract::isValidLayerUrl( $value ) ) {
					$normalized[$key] = $value;
				}
			} else {
				$normalized[$key] = $value;
			}
		}

		return $normalized;
	}

	/**
	 * @return string[]
	 */
	public function getLayerNames(): array {
		return array_map( 'strval', array_keys( $this->definitions ) );
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

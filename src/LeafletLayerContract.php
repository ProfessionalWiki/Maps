<?php

declare( strict_types = 1 );

namespace Maps;

/**
 * The shared contract for custom Leaflet layer definitions: which layer names, URLs and Leaflet
 * options are permitted. Both the render-time normalization (LeafletLayerDefinitions) and the
 * save-time validation of the on-wiki config page (LeafletConfigValidator) build on these rules,
 * so the same restrictions apply to layers defined via $egMapsLeafletLayerDefinitions and via the
 * MediaWiki:Maps config page.
 *
 * @licence GNU GPL v2+
 */
class LeafletLayerContract {

	/**
	 * Leaflet options accepted on any layer, matching the safe subset of L.TileLayer options.
	 */
	public const TILE_OPTIONS = [
		'attribution',
		'minZoom',
		'maxZoom',
		'minNativeZoom',
		'maxNativeZoom',
		'subdomains',
		'errorTileUrl',
		'zoomOffset',
		'zoomReverse',
		'tms',
		'detectRetina',
		'bounds',
		'opacity',
		'zIndex',
		'noWrap',
	];

	/**
	 * Additional options accepted only on WMS layers, matching L.TileLayer.WMS.
	 */
	public const WMS_OPTIONS = [
		'layers',
		'styles',
		'format',
		'transparent',
		'version',
		'uppercase',
	];

	/**
	 * Options whose value must be an http(s) URL, validated like the layer url itself.
	 */
	public const URL_OPTIONS = [
		'errorTileUrl',
	];

	/**
	 * Layer names that are rejected to avoid JavaScript prototype pollution on the client.
	 */
	public const RESERVED_LAYER_NAMES = [
		'__proto__',
		'constructor',
		'prototype',
	];

	public const MAX_LAYER_NAME_LENGTH = 200;

	public const MAX_URL_LENGTH = 2000;

	/**
	 * @return string[]
	 */
	public static function allowedOptions( bool $wms ): array {
		return $wms ? array_merge( self::TILE_OPTIONS, self::WMS_OPTIONS ) : self::TILE_OPTIONS;
	}

	public static function isValidLayerName( string $name ): bool {
		return $name !== ''
			&& mb_strlen( $name ) <= self::MAX_LAYER_NAME_LENGTH
			&& !in_array( $name, self::RESERVED_LAYER_NAMES, true );
	}

	public static function isValidLayerUrl( string $url ): bool {
		return strlen( $url ) <= self::MAX_URL_LENGTH
			&& preg_match( '#^https?://#i', $url ) === 1;
	}

}

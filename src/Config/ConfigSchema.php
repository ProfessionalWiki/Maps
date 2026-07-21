<?php

declare( strict_types = 1 );

namespace Maps\Config;

/**
 * The declarative table of every setting that may be set on the MediaWiki:Maps config page. Each
 * entry ties a page group and key to the PHP setting it overrides, its value type and its merge
 * strategy. Exposing a new setting is a single entry here, consumed by both the save-time validator
 * and the effective-settings lookup.
 *
 * Settings deliberately left PHP-only (secrets, script-injection primitives, setup-time switches,
 * the default mapping service and the caches) are absent by design; see the extension README.
 */
class ConfigSchema {

	/**
	 * @var ConfigSetting[]
	 */
	private array $settings;

	/**
	 * @var array<string, array<string, ConfigSetting>> Group name to key to setting.
	 */
	private array $index;

	/**
	 * @param ConfigSetting[] $settings
	 */
	public function __construct( array $settings ) {
		$this->settings = $settings;
		$this->index = [];

		foreach ( $settings as $setting ) {
			$this->index[$setting->group][$setting->key] = $setting;
		}
	}

	/**
	 * @return ConfigSetting[]
	 */
	public function getSettings(): array {
		return $this->settings;
	}

	public function hasGroup( string $group ): bool {
		return array_key_exists( $group, $this->index );
	}

	public function getSetting( string $group, string $key ): ?ConfigSetting {
		return $this->index[$group][$key] ?? null;
	}

	public static function newDefault(): self {
		$notations = [ 'float', 'dms', 'dm', 'dd' ];
		$geoServices = [ 'geonames', 'google', 'nominatim' ];
		$mapTypes = [ 'normal', 'roadmap', 'satellite', 'hybrid', 'terrain', 'physical' ];
		$controls = [ 'pan', 'zoom', 'type', 'scale', 'streetview', 'rotate' ];
		$googleLayers = [ 'traffic', 'bicycling', 'transit' ];
		$typeControlStyles = [ 'default', 'horizontal', 'dropdown' ];
		$zoomControlStyles = [ 'default', 'small', 'large' ];
		// The D modifier keeps $ from matching before a trailing newline, so a value with one is
		// rejected rather than reaching the Google Maps API script URL.
		$language = new PatternType(
			'/^[a-zA-Z]{2,3}(-[a-zA-Z0-9]{2,8})?$/D',
			'maps-config-error-invalid-language',
			'maps-config-type-language'
		);

		return new self( [
			self::replace( 'general', 'mapWidth', 'egMapsMapWidth', new DimensionType( [ 'px', 'ex', 'em', '%' ], true ) ),
			self::replace( 'general', 'mapHeight', 'egMapsMapHeight', new DimensionType( [ 'px', 'ex', 'em' ], false ) ),
			self::replace( 'general', 'defaultTitle', 'egMapsDefaultTitle', new StringType() ),
			self::replace( 'general', 'defaultLabel', 'egMapsDefaultLabel', new StringType() ),
			self::replace( 'general', 'resizableByDefault', 'egMapsResizableByDefault', new BooleanType() ),
			self::replace( 'general', 'rezoomForKml', 'egMapsRezoomForKML', new BooleanType() ),
			self::replace( 'general', 'pagesWithMapsCategory', 'egMapsEnableCategory', new BooleanType() ),
			self::replace( 'general', 'distanceUnits', 'egMapsDistanceUnits', new NumberMapType() ),
			self::replace( 'general', 'distanceUnit', 'egMapsDistanceUnit', new StringType() ),
			self::replace( 'general', 'distanceDecimals', 'egMapsDistanceDecimals', new IntegerType( 0 ) ),

			self::replace( 'coordinates', 'availableNotations', 'egMapsAvailableCoordNotations', new EnumListType( $notations ) ),
			self::replace( 'coordinates', 'notation', 'egMapsCoordinateNotation', new EnumType( $notations ) ),
			self::replace( 'coordinates', 'directional', 'egMapsCoordinateDirectional', new BooleanType() ),

			self::replace( 'geocoding', 'service', 'egMapsDefaultGeoService', new EnumType( $geoServices ) ),

			self::replace( 'semanticMediaWiki', 'showTitle', 'smgQPShowTitle', new BooleanType() ),
			self::replace( 'semanticMediaWiki', 'hideNamespace', 'smgQPHideNamespace', new BooleanType() ),
			self::replace( 'semanticMediaWiki', 'template', 'smgQPTemplate', new StringType() ),
			self::replace( 'semanticMediaWiki', 'coordinateFormat', 'smgQPCoodFormat', new EnumType( $notations ) ),
			self::replace( 'semanticMediaWiki', 'coordinateDirectional', 'smgQPCoodDirectional', new BooleanType() ),

			self::union( 'leaflet', 'layerDefinitions', 'egMapsLeafletLayerDefinitions', new LayerDefinitionsType() ),
			self::replace( 'leaflet', 'defaultLayers', 'egMapsLeafletLayers', new StringListType() ),
			self::replace( 'leaflet', 'defaultOverlays', 'egMapsLeafletOverlayLayers', new StringListType() ),
			self::union( 'leaflet', 'availableLayers', 'egMapsLeafletAvailableLayers', new AvailabilityType() ),
			self::union( 'leaflet', 'availableOverlays', 'egMapsLeafletAvailableOverlayLayers', new AvailabilityType() ),
			self::replace( 'leaflet', 'defaultZoom', 'egMapsLeafletZoom', new IntegerType() ),

			self::replace( 'googleMaps', 'zoom', 'egMapsGMaps3Zoom', new IntegerType() ),
			self::replace( 'googleMaps', 'type', 'egMapsGMaps3Type', new EnumType( $mapTypes ) ),
			self::replace( 'googleMaps', 'types', 'egMapsGMaps3Types', new EnumListType( $mapTypes ) ),
			self::replace( 'googleMaps', 'controls', 'egMapsGMaps3Controls', new EnumListType( $controls ) ),
			self::replace( 'googleMaps', 'typeControlStyle', 'egMapsGMaps3DefTypeStyle', new EnumType( $typeControlStyles ) ),
			self::replace( 'googleMaps', 'zoomControlStyle', 'egMapsGMaps3DefZoomStyle', new EnumType( $zoomControlStyles ) ),
			self::replace( 'googleMaps', 'autoInfoWindows', 'egMapsGMaps3AutoInfoWindows', new BooleanType() ),
			self::replace( 'googleMaps', 'layers', 'egMapsGMaps3Layers', new EnumListType( $googleLayers ) ),
			self::replace( 'googleMaps', 'showPoi', 'egMapsShowPOI', new BooleanType() ),
			self::replace( 'googleMaps', 'language', 'egMapsGMaps3Language', $language ),
		] );
	}

	private static function replace( string $group, string $key, string $settingName, ConfigType $type ): ConfigSetting {
		return new ConfigSetting( $group, $key, $settingName, $type, MergeStrategy::Replace );
	}

	private static function union( string $group, string $key, string $settingName, ConfigType $type ): ConfigSetting {
		return new ConfigSetting( $group, $key, $settingName, $type, MergeStrategy::Union );
	}

}

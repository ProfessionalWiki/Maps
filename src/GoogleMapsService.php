<?php

namespace Maps;

use Html;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Peter Grassberger < petertheone@gmail.com >
 */
class GoogleMapsService implements MappingService {

	/**
	 * Maps user input map types to the Google Maps names for the map types.
	 */
	private const MAP_TYPES = [
		'normal' => 'ROADMAP',
		'roadmap' => 'ROADMAP',
		'satellite' => 'SATELLITE',
		'hybrid' => 'HYBRID',
		'terrain' => 'TERRAIN',
		'physical' => 'TERRAIN',
		'earth' => 'earth'
	];

	private const TYPE_CONTROL_STYLES = [
		'default' => 'DEFAULT',
		'horizontal' => 'HORIZONTAL_BAR',
		'dropdown' => 'DROPDOWN_MENU'
	];

	private $addedDependencies = [];

	public function getName(): string {
		return 'googlemaps3';
	}

	public function getAliases(): array {
		return [ 'googlemaps', 'google' ];
	}

	public function hasAlias( string $alias ): bool {
		return in_array( $alias, [ 'googlemaps', 'google' ] );
	}

	public function getParameterInfo(): array {
		global $egMapsGMaps3Type, $egMapsGMaps3Types, $egMapsGMaps3Controls, $egMapsGMaps3Layers;
		global $egMapsGMaps3DefTypeStyle, $egMapsGMaps3DefZoomStyle, $egMapsGMaps3AutoInfoWindows;
		global $egMapsResizableByDefault;

		$params = [];

		$params['zoom'] = [
			'type' => 'integer',
			'range' => [ 0, 20 ],
			'default' => $GLOBALS['egMapsGMaps3Zoom'],
			'message' => 'maps-par-zoom',
		];

		$params['type'] = [
			'default' => $egMapsGMaps3Type,
			'values' => self::getTypeNames(),
			'message' => 'maps-googlemaps3-par-type',
			'post-format' => function ( $value ) {
				return GoogleMapsService::MAP_TYPES[strtolower( $value )];
			},
		];

		$params['types'] = [
			'dependencies' => 'type',
			'default' => $egMapsGMaps3Types,
			'values' => self::getTypeNames(),
			'message' => 'maps-googlemaps3-par-types',
			'islist' => true,
			'post-format' => function ( array $value ) {
				foreach ( $value as &$part ) {
					$part = self::MAP_TYPES[strtolower( $part )];
				}

				return $value;
			},
		];

		$params['layers'] = [
			'default' => $egMapsGMaps3Layers,
			'values' => [
				'traffic',
				'bicycling',
				'transit'
			],
			'message' => 'maps-googlemaps3-par-layers',
			'islist' => true,
		];

		$params['controls'] = [
			'default' => $egMapsGMaps3Controls,
			'values' => [
				'pan',
				'zoom',
				'type',
				'scale',
				'streetview',
				'rotate'
			],
			'message' => 'maps-googlemaps3-par-controls',
			'islist' => true,
			'post-format' => function ( $value ) {
				return array_map( 'strtolower', $value );
			},
		];

		$params['zoomstyle'] = [
			'default' => $egMapsGMaps3DefZoomStyle,
			'values' => [ 'default', 'small', 'large' ],
			'message' => 'maps-googlemaps3-par-zoomstyle',
			'post-format' => 'strtoupper',
		];

		$params['typestyle'] = [
			'default' => $egMapsGMaps3DefTypeStyle,
			'values' => array_keys( self::TYPE_CONTROL_STYLES ),
			'message' => 'maps-googlemaps3-par-typestyle',
			'post-format' => function ( $value ) {
				return self::TYPE_CONTROL_STYLES[strtolower( $value )];
			},
		];

		$params['autoinfowindows'] = [
			'type' => 'boolean',
			'default' => $egMapsGMaps3AutoInfoWindows,
			'message' => 'maps-googlemaps3-par-autoinfowindows',
		];

		$params['resizable'] = [
			'type' => 'boolean',
			'default' => $egMapsResizableByDefault,
			'message' => 'maps-par-resizable',
		];

		$params['kmlrezoom'] = [
			'type' => 'boolean',
			'default' => $GLOBALS['egMapsRezoomForKML'],
			'message' => 'maps-googlemaps3-par-kmlrezoom',
		];

		$params['poi'] = [
			'type' => 'boolean',
			'default' => $GLOBALS['egMapsShowPOI'],
			'message' => 'maps-googlemaps3-par-poi',
		];

		$params['markercluster'] = [
			'type' => 'boolean',
			'default' => false,
			'message' => 'maps-par-markercluster',
		];

		$params['clustergridsize'] = [
			'type' => 'integer',
			'default' => 60,
			'message' => 'maps-googlemaps3-par-clustergridsize',
		];

		$params['clustermaxzoom'] = [
			'type' => 'integer',
			'default' => 20,
			'message' => 'maps-par-clustermaxzoom',
		];

		$params['clusterzoomonclick'] = [
			'type' => 'boolean',
			'default' => true,
			'message' => 'maps-par-clusterzoomonclick',
		];

		$params['clusteraveragecenter'] = [
			'type' => 'boolean',
			'default' => true,
			'message' => 'maps-googlemaps3-par-clusteraveragecenter',
		];

		$params['clusterminsize'] = [
			'type' => 'integer',
			'default' => 2,
			'message' => 'maps-googlemaps3-par-clusterminsize',
		];

		$params['imageoverlays'] = [
			'type' => 'mapsimageoverlay',
			'default' => [],
			'delimiter' => ';',
			'islist' => true,
			'message' => 'maps-googlemaps3-par-imageoverlays',
		];

		$params['kml'] = [
			'default' => [],
			'message' => 'maps-par-kml',
			'islist' => true,
			'post-format' => function( array $kmlFileNames ) {
				return array_map(
					function( string $fileName ) {
						return wfExpandUrl( MapsFunctions::getFileUrl( $fileName ) );
					},
					$kmlFileNames
				);
			}
		];

		$params['gkml'] = [
			'default' => [],
			'message' => 'maps-googlemaps3-par-gkml',
			'islist' => true,
		];

		$params['searchmarkers'] = [
			'default' => '',
			'message' => 'maps-par-searchmarkers',
			// new CriterionSearchMarkers() FIXME
		];

		$params['enablefullscreen'] = [
			'type' => 'boolean',
			'default' => false,
			'message' => 'maps-par-enable-fullscreen',
		];

		$params['scrollwheelzoom'] = [
			'type' => 'boolean',
			'default' => false,
			'message' => 'maps-par-scrollwheelzoom',
		];

		return $params;
	}

	/**
	 * Returns the names of all supported map types.
	 */
	private function getTypeNames(): array {
		return array_keys( self::MAP_TYPES );
	}

	public function newMapId(): string {
		static $mapsOnThisPage = 0;

		$mapsOnThisPage++;

		return 'map_google3_' . $mapsOnThisPage;
	}

	public function getResourceModules(): array {
		return [ 'ext.maps.googlemaps3', 'ext.sm.googlemaps3ajax' ];
	}

	public static function getApiScript( $langCode, array $urlArgs = [] ) {
		$urlArgs = array_merge(
			[
				'language' => self::getMappedLanguageCode( $langCode )
			],
			$urlArgs
		);
		if ( $GLOBALS['egMapsGMaps3ApiKey'] !== '' ) {
			$urlArgs['key'] = $GLOBALS['egMapsGMaps3ApiKey'];
		}
		if ( $GLOBALS['egMapsGMaps3ApiVersion'] !== '' ) {
			$urlArgs['v'] = $GLOBALS['egMapsGMaps3ApiVersion'];
		}

		return Html::linkedScript( '//maps.googleapis.com/maps/api/js?' . wfArrayToCgi( $urlArgs ) );
	}

	/**
	 * Maps language codes to Google Maps API v3 compatible values.
	 */
	private static function getMappedLanguageCode( string $code ): string {
		$mappings = [
			'en_gb' => 'en-gb',// v3 supports en_gb - but wants us to call it en-gb
			'he' => 'iw',      // iw is googlish for hebrew
			'fj' => 'fil',     // google does not support Fijian - use Filipino as close(?) supported relative
		];

		if ( array_key_exists( $code, $mappings ) ) {
			return $mappings[$code];
		}

		return $code;
	}

	public function getDependencyHtml( array $params ): string {
		$dependencies = [];

		// Only add dependencies that have not yet been added.
		foreach ( $this->getDependencies() as $dependency ) {
			if ( !in_array( $dependency, $this->addedDependencies ) ) {
				$dependencies[] = $dependency;
				$this->addedDependencies[] = $dependency;
			}
		}

		// If there are dependencies, put them all together in a string, otherwise return false.
		return $dependencies !== [] ? implode( '', $dependencies ) : false;
	}

	private function getDependencies(): array {
		return [
			self::getApiScript(
				is_string( $GLOBALS['egMapsGMaps3Language'] ) ?
					$GLOBALS['egMapsGMaps3Language'] : $GLOBALS['egMapsGMaps3Language']->getCode()
			)
		];
	}
}

<?php

namespace Maps;

use Html;

/**
 * @licence GNU GPL v2+
 */
class LeafletService extends MappingService {

	public function getName(): string {
		return 'leaflet';
	}

	public function getAliases(): array {
		return [ 'leafletmaps', 'leaflet' ]; // TODO: main name should not be in here?
	}

	public function hasAlias( string $alias ): bool {
		return in_array( $alias, [ 'leafletmaps', 'leaflet' ] );
	}

	public function getParameterInfo(): array {
		global $GLOBALS;

		$params = [];

		$params['zoom'] = [
			'type' => 'integer',
			'range' => [ 0, 20 ],
			'default' => false,
			'message' => 'maps-par-zoom'
		];

		$params['defzoom'] = [
			'type' => 'integer',
			'range' => [ 0, 20 ],
			'default' => self::getDefaultZoom(),
			'message' => 'maps-leaflet-par-defzoom'
		];

		$params['layers'] = [
			'aliases' => 'layer',
			'type' => 'string',
			'values' => array_keys( $GLOBALS['egMapsLeafletAvailableLayers'], true, true ),
			'default' => $GLOBALS['egMapsLeafletLayers'],
			'message' => 'maps-leaflet-par-layers',
			'islist' => true,
		];

		$params['overlaylayers'] = [
			'type' => 'string',
			'values' => array_keys( $GLOBALS['egMapsLeafletAvailableOverlayLayers'], true, true ),
			'default' => $GLOBALS['egMapsLeafletOverlayLayers'],
			'message' => 'maps-leaflet-par-overlaylayers',
			'islist' => true,
		];

		$params['resizable'] = [
			'type' => 'boolean',
			'default' => $GLOBALS['egMapsResizableByDefault'],
			'message' => 'maps-par-resizable'
		];

		$params['enablefullscreen'] = [
			'type' => 'boolean',
			'default' => false,
			'message' => 'maps-par-enable-fullscreen',
		];

		$params['scrollwheelzoom'] = [
			'type' => 'boolean',
			'default' => true,
			'message' => 'maps-par-scrollwheelzoom',
		];

		$params['markercluster'] = [
			'type' => 'boolean',
			'default' => false,
			'message' => 'maps-par-markercluster',
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

		$params['clustermaxradius'] = [
			'type' => 'integer',
			'default' => 80,
			'message' => 'maps-par-maxclusterradius',
		];

		$params['clusterspiderfy'] = [
			'type' => 'boolean',
			'default' => true,
			'message' => 'maps-leaflet-par-clusterspiderfy',
		];

		$params['geojson'] = [
			'type' => 'jsonfile',
			'default' => '',
			'message' => 'maps-displaymap-par-geojson',
		];

		return $params;
	}

	/**
	 * @since 3.0
	 */
	public function getDefaultZoom() {
		return $GLOBALS['egMapsLeafletZoom'];
	}

	public function getMapId(): string {
		static $mapsOnThisPage = 0;

		$mapsOnThisPage++;

		return 'map_leaflet_' . $mapsOnThisPage;
	}

	public function getResourceModules(): array {
		return [ 'ext.maps.leaflet', 'ext.sm.fi.leafletajax' ];
	}

	protected function getDependencies() {
		$leafletPath = $GLOBALS['wgScriptPath'] . '/extensions/Maps/resources/leaflet/leaflet';
		return [
			Html::linkedStyle( "$leafletPath/leaflet.css" ),
			Html::linkedScript( "$leafletPath/leaflet.js" ),
		];
	}

}

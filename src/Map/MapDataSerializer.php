<?php

declare( strict_types = 1 );

namespace Maps\Map;

class MapDataSerializer {

	public function toJson( MapData $mapData ): array {
		$json = $mapData->getParameters();

		$json['locations'] = array_merge(
			array_key_exists( 'locations', $json ) ? $json['locations'] : [],
			$this->getLocationJson( $mapData )
		);

		return $json;
	}

	private function getLocationJson( MapData $mapData ): array {
		return array_map(
			function( Marker $marker ) {
				return [
					'lat' => $marker->getCoordinates()->getLatitude(),
					'lon' => $marker->getCoordinates()->getLongitude(),
					'icon' => $marker->getIconUrl(),
					'text' => $marker->getText(),
				];
			},
			$mapData->getMarkers()
		);
	}

}

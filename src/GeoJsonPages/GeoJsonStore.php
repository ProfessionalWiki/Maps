<?php

declare( strict_types = 1 );

namespace Maps\GeoJsonPages;

interface GeoJsonStore {

	public function storeGeoJson( string $geoJson );

}

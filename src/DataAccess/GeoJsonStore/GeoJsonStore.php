<?php

declare( strict_types = 1 );

namespace Maps\DataAccess\GeoJsonStore;

interface GeoJsonStore {

	public function storeGeoJson( string $geoJson );

}

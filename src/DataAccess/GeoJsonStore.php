<?php

declare( strict_types = 1 );

namespace Maps\DataAccess;

interface GeoJsonStore {

	public function storeGeoJson( string $geoJson );

}

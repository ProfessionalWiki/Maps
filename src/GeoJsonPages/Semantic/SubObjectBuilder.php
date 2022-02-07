<?php

declare( strict_types = 1 );

namespace Maps\GeoJsonPages\Semantic;

use GeoJson\Feature\FeatureCollection;
use GeoJson\GeoJson;
use GeoJson\Geometry\Point;
use Title;

class SubObjectBuilder {

	private int $pointCount = 0;

	/**
	 * @return SubObject[]
	 */
	public function getSubObjectsFromGeoJson( string $jsonString ) {
		$json = json_decode( $jsonString );
		$geoJson = GeoJson::jsonUnserialize( $json );

		return iterator_to_array( $this->featureCollectionToSubObjects( $geoJson ) );
	}

	private function featureCollectionToSubObjects( FeatureCollection $featureCollection ) {
		foreach ( $featureCollection->getFeatures() as $feature ) {
			$geometry = $feature->getGeometry();

			if ( $geometry instanceof Point ) {
				yield $this->pointToSubobject( $geometry, $feature->getProperties() ?? [] );
			}
		}
	}

	private function pointToSubobject( Point $point, array $properties ): SubObject {
		$subObject = new SubObject( 'Point_' . ++$this->pointCount );

		$subObject->addPropertyValuePair(
			'HasCoordinates',
			new \SMWDIGeoCoord( $point->getCoordinates()[1], $point->getCoordinates()[0] )
		);

		if ( array_key_exists( 'description', $properties ) ) {
			$subObject->addPropertyValuePair(
				'HasDescription',
				new \SMWDIBlob( $properties['description'] )
			);
		}

		if ( array_key_exists( 'title', $properties ) ) {
			$subObject->addPropertyValuePair(
				'HasTitle',
				new \SMWDIBlob( $properties['title'] )
			);
		}

		return $subObject;
	}

}

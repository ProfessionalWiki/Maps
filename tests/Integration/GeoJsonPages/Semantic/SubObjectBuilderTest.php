<?php

declare( strict_types = 1 );

namespace Maps\Tests\Integration\GeoJsonPages\Semantic;

use Maps\GeoJsonPages\Semantic\SubObjectBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\GeoJsonPages\Semantic\SubObjectBuilder
 */
class SubObjectBuilderTest extends TestCase {

	public function setUp(): void {
		if ( !defined( 'SMW_VERSION' ) ) {
			$this->markTestSkipped( 'SMW is not available' );
		}
	}

	public function testEmptyGeoJson() {
		$objects = $this->newBuilder()->getSubObjectsFromGeoJson( '{"type": "FeatureCollection", "features": []}' );

		$this->assertSame( [], $objects );
	}

	private function newBuilder(): SubObjectBuilder {
		return new SubObjectBuilder();
	}

	public function testPoint() {
		$objects = $this->newBuilder()->getSubObjectsFromGeoJson(
			<<<'EOD'
{
    "type": "FeatureCollection",
    "features": [
        {
            "type": "Feature",
            "geometry": {
                "type": "Point",
                "coordinates": [
                    13.388729,
                    52.516524
                ]
            }
        }
    ]
}
EOD
		);

		$this->assertCount( 1, $objects );
		$this->assertSame( 'Point_1', $objects[0]->getName() );

		$this->assertEquals(
			[
				'HasCoordinates' => [ new \SMWDIGeoCoord( 52.516524, 13.388729 ) ],
			],
			$objects[0]->getValues()
		);
	}

	public function testPointWithTitleAndDescription() {
		$objects = $this->newBuilder()->getSubObjectsFromGeoJson(
			<<<'EOD'
{
    "type": "FeatureCollection",
    "features": [
        {
            "type": "Feature",
            "properties": {
                "title": "Berlin",
                "description": "The capital of Germany"
            },
            "geometry": {
                "type": "Point",
                "coordinates": [
                    13.388729,
                    52.516524
                ]
            }
        }
    ]
}
EOD
		);

		$this->assertCount( 1, $objects );

		$this->assertEquals(
			[
				'HasCoordinates' => [ new \SMWDIGeoCoord( 52.516524, 13.388729 ) ],
				'HasTitle' => [ new \SMWDIBlob( 'Berlin' ) ],
				'HasDescription' => [ new \SMWDIBlob( 'The capital of Germany' ) ],
			],
			$objects[0]->getValues()
		);
	}

}

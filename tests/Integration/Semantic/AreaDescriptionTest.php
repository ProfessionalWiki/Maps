<?php

declare( strict_types = 1 );

namespace Maps\Tests\Integration\Semantic;

use Maps\SemanticMW\AreaDescription;
use PHPUnit\Framework\TestCase;
use SMWDIGeoCoord;

/**
 * @covers \Maps\SemanticMW\AreaDescription
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class AreaDescriptionTest extends TestCase {

	public function setUp(): void {
		if ( !defined( 'SMW_VERSION' ) ) {
			$this->markTestSkipped( 'SMW is not available' );
		}
	}

	public function testGetBoundingBox() {
		$area = new AreaDescription(
			new SMWDIGeoCoord( 0, 5 ),
			SMW_CMP_EQ,
			'10 km'
		);

		$this->assertEquals(
			[
				'north' => 0.089932160591873,
				'east' => 5.089932160591873,
				'south' => -0.089932160591873,
				'west' => 4.9100678394081
			],
			$area->getBoundingBox()
		);
	}

	public function testGetSQLCondition() {
		$area = new AreaDescription(
			new SMWDIGeoCoord( 0, 5 ),
			SMW_CMP_EQ,
			'10 km'
		);

		$this->assertSame(
			'geo_table.lat_field < \'0.089932160591873\' AND geo_table.lat_field > \'-0.089932160591873\' '
			. 'AND geo_table.long_field < \'5.0899321605919\' AND geo_table.long_field > \'4.9100678394081\'',
			$area->getSQLCondition( 'geo_table', [ 'id_field', 'lat_field', 'long_field' ], wfGetDB( DB_PRIMARY ) )
		);
	}

	public function testWhenComparatorIsNotSupported_getSQLConditionReturnsFalse() {
		$area = new AreaDescription(
			new SMWDIGeoCoord( 0, 5 ),
			SMW_CMP_LIKE,
			'10 km'
		);

		$this->assertFalse(
			$area->getSQLCondition( 'geo_table', [ 'id_field', 'lat_field', 'long_field' ], wfGetDB( DB_PRIMARY ) )
		);
	}

	public function testGetQueryString() {
		$area = new AreaDescription(
			new SMWDIGeoCoord( 1, 5 ),
			SMW_CMP_EQ,
			'10 km'
		);

		$this->assertSame(
			'[[1° 0\' 0.00" N, 5° 0\' 0.00" E (10 km)]]',
			$area->getQueryString()
		);
	}

}

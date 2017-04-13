<?php

namespace Maps\Tests\Semantic\ValueDescriptions;

use Maps\Semantic\ValueDescriptions\AreaDescription;
use Maps\Semantic\ValueDescriptions\CoordinateDescription;
use CoordinateValue;
use SMW\DataValueFactory;
use SMWDataItem;
use SMWDIGeoCoord;

/**
 * @covers \Maps\Semantic\ValueDescriptions\AreaDescription
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class AreaDescriptionTest extends \PHPUnit_Framework_TestCase {

	public function setUp() {
		if ( !defined( 'SMW_VERSION' ) ) {
			$this->markTestSkipped( 'SMW is not available' );
		}
	}

	public function testGetBoundingBox() {
		$area = new AreaDescription(
			new SMWDIGeoCoord( 0, 0 ),
			SMW_CMP_EQ,
			'10 km'
		);

		$this->assertEquals(
			[
				'north' => 0.089932160591873,
				'east' => 0.089932160591873,
				'south' => -0.089932160591873,
				'west' => -0.089932160591873
			],
			$area->getBoundingBox()
		);
	}

	public function testGetSQLCondition() {
		$area = new AreaDescription(
			new SMWDIGeoCoord( 0, 0 ),
			SMW_CMP_EQ,
			'10 km'
		);

		$this->assertSame(
			'geo_table.lat_field < \'0.089932160591873\' AND geo_table.lat_field > \'-0.089932160591873\' '
				. 'AND geo_table.long_field < \'0.089932160591873\' AND geo_table.long_field > \'-0.089932160591873\'',
			$area->getSQLCondition( 'geo_table', ['id_field', 'lat_field', 'long_field'], wfGetDB( DB_MASTER ) )
		);
	}

}

<?php

namespace SM\Test;

use SMGeoCoordsValue;
use SMGeoCoordsValueDescription;
use SMW\DataValueFactory;
use SMWDataItem;
use SMWDIGeoCoord;

/**
 * @covers SMGeoCoordsValue
 *
 * @group SemanticMaps
 * @group SMWExtension
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SMGeoCoordsValueTest extends \PHPUnit_Framework_TestCase {

	public function testConstruct() {
		$geoDI = new SMWDIGeoCoord( 23, 42 );

		/**
		 * @var SMGeoCoordsValue $geoValue
		 */
		$geoValue = DataValueFactory::newDataItemValue( $geoDI );

		$this->assertInstanceOf( 'SMGeoCoordsValue', $geoValue );

		$this->assertEquals( $geoDI, $geoValue->getDataItem() );
		$this->assertEquals( '23° 0\' 0", 42° 0\' 0"', $geoValue->getShortWikiText() );
	}

	/**
	 * @dataProvider coordinateProvider
	 */
	public function testGetQueryDescription( $lat, $long, $serialization ) {
		$geoValue = $this->newInstance();

		$description = $geoValue->getQueryDescription( $serialization );

		$this->assertIsCorrectCoordValue( $description, $lat, $long );
	}

	protected function assertIsCorrectCoordValue( $description, $lat, $long ) {
		/**
		 * @var SMGeoCoordsValueDescription $description
		 */
		$this->assertInstanceOf( 'SMGeoCoordsValueDescription', $description );
		$this->assertEquals( $lat, $description->getDataItem()->getLatitude() );
		$this->assertEquals( $long, $description->getDataItem()->getLongitude() );
	}

	protected function newInstance() {
		return new SMGeoCoordsValue( SMWDataItem::TYPE_GEO );
	}

	public function coordinateProvider() {
		return [
			[
				23,
				42,
				'23° 0\' 0", 42° 0\' 0"',
			],
			[
				0,
				0,
				'0° 0\' 0", 0° 0\' 0"',
			],
			[
				-23.5,
				-42.5,
				'-23° 30\' 0", -42° 30\' 0"',
			],
		];
	}

	/**
	 * @dataProvider coordinateWithDistanceProvider
	 */
	public function testGetQueryDescriptionForArea( $serialization ) {
		$geoValue = $this->newInstance();

		$description = $geoValue->getQueryDescription( $serialization );

		$this->assertInstanceOf( 'SMAreaValueDescription', $description );
	}

	public function coordinateWithDistanceProvider() {
		return [
			[
				'23° 0\' 0", 42° 0\' 0"(1km)',
				1000,
			],
			[
				'0° 0\' 0", 0° 0\' 0" ( 1 m )',
				1,
			],
			[
				'-23° 30\' 0", -42° 30\' 0" (9001m)',
				9001,
			],
		];
	}

}

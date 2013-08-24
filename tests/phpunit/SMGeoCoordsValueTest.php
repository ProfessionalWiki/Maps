<?php

namespace SM\Test;

use SMGeoCoordsValue;
use SMW\DataValueFactory;
use SMWDIGeoCoord;

/**
 * @covers SMGeoCoordsValue
 *
 * @group SemanticMaps
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SMGeoCoordsValueTest extends \PHPUnit_Framework_TestCase {

	public function testConstruct() {
		$geoDI = new SMWDIGeoCoord( 23, 42 );
		$geoValue = DataValueFactory::newDataItemValue( $geoDI );

		$this->assertInstanceOf( 'SMGeoCoordsValue', $geoValue );

		/**
		 * @var SMGeoCoordsValue $geoValue
		 */

		$this->assertEquals( $geoDI, $geoValue->getDataItem() );
		$this->assertEquals( '23° 0\' 0", 42° 0\' 0"', $geoValue->getShortWikiText() );
	}

}

<?php

namespace Maps\Test;

/**
 * @covers MapsNominatimGeocoder
 *
 * @since 4.0
 *
 * @group Maps
 *
 * @licence GNU GPL v2+
 * @author Peter Grassberger < petertheone@gmail.com >
 */
class NominatimGeocoderTest extends \PHPUnit_Framework_TestCase {

	protected static function getMethod( $name ) {
		$class = new \ReflectionClass( 'MapsNominatimGeocoder' );
		$method = $class->getMethod( $name );
		$method->setAccessible( true );
		return $method;
	}

	public function addressProvider() {
		return [
			['New York', 'https://nominatim.openstreetmap.org/search?q=New%2BYork&format=jsonv2&limit=1'],
		];
	}

	/**
	 * @covers MapsNominatimGeocoder::getRequestUrl
	 *
	 * @dataProvider addressProvider
	 */
	public function testGetRequestUrl( $address, $expected ) {
		$getRequestUrl = self::getMethod('getRequestUrl');
		$geocoder = new \MapsNominatimGeocoder( 'nominatim' );
		$actual = $getRequestUrl->invokeArgs($geocoder, array( $address ));
		$this->assertSame( $expected, $actual );
	}

	public function responseProvider() {
		return [
			[
				'[{"place_id":"97961780","licence":"Data © OpenStreetMap contributors, ODbL 1.0. http:\/\/www.openstreetmap.org\/copyright","osm_type":"way","osm_id":"161387758","boundingbox":["40.763858","40.7642664","-73.9548572","-73.954092"],"lat":"40.7642499","lon":"-73.9545249","display_name":"NewYork Hospital Drive, Upper East Side, Manhattan, New York County, New York City, New York, 10021, United States of America","place_rank":"27","category":"highway","type":"service","importance":0.275}]',
				['lat' => 40.7642499, 'lon' => -73.9545249]
			]
		];
	}

	/**
	 * @covers MapsNominatimGeocoder::parseResponse
	 *
	 * @dataProvider responseProvider
	 */
	public function testParseResponse( $response, $expected ) {
		$parseResponse = self::getMethod('parseResponse');
		$geocoder = new \MapsNominatimGeocoder( 'nominatim' );
		$actual = $parseResponse->invokeArgs($geocoder, array( $response ));
		$this->assertSame( $expected, $actual );
	}
}

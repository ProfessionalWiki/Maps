<?php

namespace Maps\Test;

use DataValues\Geo\Values\LatLongValue;
use FileFetcher\InMemoryFileFetcher;
use Maps\Geocoders\InMemoryGeocoder;
use Maps\Geocoders\NominatimGeocoder;

/**
 * @covers MapsDecoratedGeocoder
 *
 * @group Maps
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DecoratedGeocoderTest extends \PHPUnit_Framework_TestCase {

	public function testWhenInnerGeocoderHasResult_itGetsReturnedInArrayForm() {
		$geocoder = new InMemoryGeocoder( [
			'New York' => new LatLongValue( 40.7642499, -73.9545249 )
		] );

		$decoratedGeocoder = new \MapsDecoratedGeocoder( $geocoder, 'maw' );

		$this->assertSame(
			[
				'lat' => 40.7642499,
				'lon' => -73.9545249,
			],
			$decoratedGeocoder->geocode( 'New York' )
		);
	}

	public function testWhenInnerGeocoderHasNoResult_falseIsReturned() {
		$geocoder = new InMemoryGeocoder( [
			'New York' => new LatLongValue( 40.7642499, -73.9545249 )
		] );

		$decoratedGeocoder = new \MapsDecoratedGeocoder( $geocoder, 'maw' );

		$this->assertFalse( $decoratedGeocoder->geocode( 'durkadurkastan' ) );
	}

//	public function testWhenInnerGeocoderHasNoResult_falseIsReturned() {
//		$decoratedGeocoder = new \MapsDecoratedGeocoder( new InMemoryGeocoder( [] ), 'maw' );
//
//		$this->assertSame( [ 'maw' ], $decoratedGeocoder->getAliases() );
//	}

	// TODO: test exception case if we decided to not use null in the interface

}

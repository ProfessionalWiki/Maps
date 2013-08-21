<?php

namespace Maps\Tests\Elements;

use DataValues\LatLongValue;
use Maps\Elements\Location;

/**
 * @covers Maps\Elements\Location
 *
 * @since 3.0
 *
 * @ingroup MapsTest
 *
 * @group Maps
 * @group MapsElement
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class LocationTest extends BaseElementTest {

	/**
	 * @see BaseElementTest::getClass
	 *
	 * @since 3.0
	 *
	 * @return string
	 */
	public function getClass() {
		return 'Maps\Elements\Location';
	}

	/**
	 * @see BaseElementTest::constructorProvider
	 *
	 * @since 3.0
	 *
	 * @return array
	 */
	public function constructorProvider() {
		$argLists = array();

		$argLists[] = array( false );
		$argLists[] = array( false, '' );
		$argLists[] = array( false, '4,2' );
		$argLists[] = array( false, array() );
		$argLists[] = array( false, array( new LatLongValue( 4, 2 ) ) );

		$argLists[] = array( true, new LatLongValue( 4, 2 ) );
		$argLists[] = array( true, new LatLongValue( 42, 42 ) );
		$argLists[] = array( true, new LatLongValue( -4.2, -42 ) );

		return $argLists;
	}

	/**
	 * @dataProvider instanceProvider
	 * @param Location $location
	 * @param array $arguments
	 */
	public function testGetLineCoordinates( Location $location, array $arguments ) {
		$coordinates = $location->getCoordinates();

		$this->assertType( 'DataValues\LatLongValue', $coordinates );
		$this->assertTrue( $coordinates->equals( $arguments[0] ) );
	}

}




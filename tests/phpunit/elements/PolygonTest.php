<?php

namespace Maps\Test;

use Maps\Polygon;

/**
 * Unit tests for the Maps\Polygon class.
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
class PolygonTest extends LineTest {

	/**
	 * @see BaseElementTest::getClass
	 *
	 * @since 3.0
	 *
	 * @return string
	 */
	public function getClass() {
		return 'Maps\Polygon';
	}

	/**
	 * @dataProvider instanceProvider
	 * @param \Maps\Polygon $polygon
	 * @param array $arguments
	 */
	public function testSetOnlyVisibleOnHover( Polygon $polygon, array $arguments ) {
		$this->assertFalse( $polygon->isOnlyVisibleOnHover() );

		$polygon->setOnlyVisibleOnHover( true );
		$this->assertTrue( $polygon->isOnlyVisibleOnHover() );

		$polygon->setOnlyVisibleOnHover( false );
		$this->assertFalse( $polygon->isOnlyVisibleOnHover() );
	}

}




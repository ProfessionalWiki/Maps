<?php

namespace Maps\Tests\Unit\Elements;

use Maps\Elements\Polygon;

/**
 * @covers \Maps\Elements\Polygon
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
		return Polygon::class;
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testSetOnlyVisibleOnHover( Polygon $polygon ) {
		$this->assertFalse( $polygon->isOnlyVisibleOnHover() );

		$polygon->setOnlyVisibleOnHover( true );
		$this->assertTrue( $polygon->isOnlyVisibleOnHover() );

		$polygon->setOnlyVisibleOnHover( false );
		$this->assertFalse( $polygon->isOnlyVisibleOnHover() );
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testSetFillOpacity( Polygon $polygon ) {
		$polygon->setFillOpacity( '0.42' );
		$this->assertHasJsonKeyWithValue( $polygon, 'fillOpacity', '0.42' );
	}

	protected function assertHasJsonKeyWithValue( Polygon $polygon, $key, $value ) {
		$json = $polygon->getJSONObject();

		$this->assertArrayHasKey( $key, $json );
		$this->assertEquals(
			$value,
			$json[$key]
		);
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testSetFillColor( Polygon $polygon ) {
		$polygon->setFillColor( '#FFCCCC' );
		$this->assertHasJsonKeyWithValue( $polygon, 'fillColor', '#FFCCCC' );
	}

}




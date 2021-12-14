<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit\LegacyModel;

use DataValues\Geo\Values\LatLongValue;
use Maps\LegacyModel\Line;

/**
 * @covers \Maps\LegacyModel\Line
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class LineTest extends BaseElementTest {

	/**
	 * @see BaseElementTest::getClass
	 *
	 * @since 3.0
	 *
	 * @return string
	 */
	public function getClass() {
		return Line::class;
	}

	public function validConstructorProvider() {
		$argLists = [];

		$argLists[] = [ [] ];
		$argLists[] = [ [ new LatLongValue( 4, 2 ) ] ];

		$argLists[] = [
			[
				new LatLongValue( 4, 2 ),
				new LatLongValue( 2, 4 ),
				new LatLongValue( 42, 42 ),
			]
		];

		return $argLists;
	}

	public function invalidConstructorProvider() {
		$argLists = [];

		$argLists[] = [ [ '~=[,,_,,]:3' ] ];
		$argLists[] = [ [ new LatLongValue( 4, 2 ), '~=[,,_,,]:3' ] ];
		$argLists[] = [ [ '~=[,,_,,]:3', new LatLongValue( 4, 2 ) ] ];

		return $argLists;
	}

	/**
	 * @dataProvider instanceProvider
	 *
	 * @param Line $line
	 * @param array $arguments
	 */
	public function testGetLineCoordinates( Line $line, array $arguments ) {
		$coordinates = $line->getLineCoordinates();

		$this->assertIsArray( $coordinates );
		$this->assertEquals( count( $arguments[0] ), count( $coordinates ) );

		foreach ( $coordinates as $geoCoordinate ) {
			$this->assertInstanceOf( LatLongValue::class, $geoCoordinate );
		}
	}

}

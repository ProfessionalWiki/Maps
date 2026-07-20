<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit\Config;

use Maps\Config\NumberMapType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\Config\NumberMapType
 */
class NumberMapTypeTest extends TestCase {

	/**
	 * @dataProvider validProvider
	 */
	public function testMapsOfNamesToPositiveNumbersAreValid( array $value ): void {
		$this->assertSame( [], ( new NumberMapType() )->validate( $value, 'general.distanceUnits' ) );
	}

	public function validProvider(): array {
		return [
			'integers' => [ [ 'm' => 1, 'km' => 1000 ] ],
			'floats' => [ [ 'mi' => 1609.344 ] ],
			'mixed' => [ [ 'm' => 1, 'nauticalmile' => 1852 ] ],
		];
	}

	/**
	 * @dataProvider invalidProvider
	 */
	public function testOtherValuesAreRejected( mixed $value ): void {
		$this->assertSame(
			[ [ 'maps-config-error-invalid-number-map', 'general.distanceUnits' ] ],
			( new NumberMapType() )->validate( $value, 'general.distanceUnits' )
		);
	}

	public function invalidProvider(): array {
		return [
			'empty' => [ [] ],
			'zero ratio' => [ [ 'm' => 0 ] ],
			'negative ratio' => [ [ 'm' => -1 ] ],
			'string ratio' => [ [ 'm' => '1' ] ],
			'name with space' => [ [ 'sea mile' => 1852 ] ],
			'name with symbol' => [ [ 'm.' => 1 ] ],
			'name with trailing newline' => [ [ "km\n" => 1 ] ],
			'name starting with digit' => [ [ '2m' => 2 ] ],
			'list' => [ [ 1, 2 ] ],
			'not an array' => [ 'm' ],
		];
	}

}

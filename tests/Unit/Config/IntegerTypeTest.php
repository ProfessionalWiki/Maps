<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit\Config;

use Maps\Config\IntegerType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\Config\IntegerType
 */
class IntegerTypeTest extends TestCase {

	/**
	 * @dataProvider validProvider
	 */
	public function testIntegersAreValid( mixed $value ): void {
		$this->assertSame( [], ( new IntegerType() )->validate( $value, 'leaflet.defaultZoom' ) );
	}

	public function validProvider(): array {
		return [ [ 0 ], [ 14 ], [ -3 ] ];
	}

	/**
	 * @dataProvider invalidProvider
	 */
	public function testNonIntegersAreRejected( mixed $value ): void {
		$this->assertSame(
			[ [ 'maps-config-error-invalid-integer', 'leaflet.defaultZoom' ] ],
			( new IntegerType() )->validate( $value, 'leaflet.defaultZoom' )
		);
	}

	public function invalidProvider(): array {
		return [
			'float' => [ 1.5 ],
			'numeric string' => [ '5' ],
			'boolean' => [ true ],
			'null' => [ null ],
		];
	}

	public function testValueAtOrAboveMinimumIsValid(): void {
		$this->assertSame( [], ( new IntegerType( 0 ) )->validate( 0, 'general.distanceDecimals' ) );
	}

	public function testValueBelowMinimumIsRejected(): void {
		$this->assertSame(
			[ [ 'maps-config-error-integer-too-small', 'general.distanceDecimals', 0 ] ],
			( new IntegerType( 0 ) )->validate( -1, 'general.distanceDecimals' )
		);
	}

}

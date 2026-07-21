<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit\Config;

use Maps\Config\BooleanType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\Config\BooleanType
 */
class BooleanTypeTest extends TestCase {

	/**
	 * @dataProvider validProvider
	 */
	public function testBooleansAreValid( mixed $value ): void {
		$this->assertSame( [], ( new BooleanType() )->validate( $value, 'general.resizableByDefault' ) );
	}

	public function validProvider(): array {
		return [ [ true ], [ false ] ];
	}

	/**
	 * @dataProvider invalidProvider
	 */
	public function testNonBooleansAreRejected( mixed $value ): void {
		$this->assertSame(
			[ [ 'maps-config-error-invalid-boolean', 'general.resizableByDefault' ] ],
			( new BooleanType() )->validate( $value, 'general.resizableByDefault' )
		);
	}

	public function invalidProvider(): array {
		return [
			'integer' => [ 1 ],
			'zero' => [ 0 ],
			'string' => [ 'true' ],
			'null' => [ null ],
			'array' => [ [] ],
		];
	}

	public function testDescribe(): void {
		$this->assertSame( [ 'maps-config-type-boolean' ], ( new BooleanType() )->describe() );
	}

}

<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit\Config;

use Maps\Config\StringType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\Config\StringType
 */
class StringTypeTest extends TestCase {

	/**
	 * @dataProvider validProvider
	 */
	public function testStringsAreValid( mixed $value ): void {
		$this->assertSame( [], ( new StringType() )->validate( $value, 'general.defaultTitle' ) );
	}

	public function validProvider(): array {
		return [ 'empty' => [ '' ], 'text' => [ 'A title' ] ];
	}

	/**
	 * @dataProvider invalidProvider
	 */
	public function testNonStringsAreRejected( mixed $value ): void {
		$this->assertSame(
			[ [ 'maps-config-error-invalid-string', 'general.defaultTitle' ] ],
			( new StringType() )->validate( $value, 'general.defaultTitle' )
		);
	}

	public function invalidProvider(): array {
		return [
			'integer' => [ 5 ],
			'boolean' => [ false ],
			'null' => [ null ],
			'array' => [ [] ],
		];
	}

}

<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit\Config;

use Maps\Config\EnumType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\Config\EnumType
 */
class EnumTypeTest extends TestCase {

	private function type(): EnumType {
		return new EnumType( [ 'float', 'dms', 'dm', 'dd' ] );
	}

	/**
	 * @dataProvider validProvider
	 */
	public function testAllowedValuesAreValid( string $value ): void {
		$this->assertSame( [], $this->type()->validate( $value, 'coordinates.notation' ) );
	}

	public function validProvider(): array {
		return [ [ 'float' ], [ 'dms' ], [ 'dd' ] ];
	}

	/**
	 * @dataProvider invalidProvider
	 */
	public function testOtherValuesAreRejected( mixed $value ): void {
		$this->assertSame(
			[ [ 'maps-config-error-invalid-enum', 'coordinates.notation', 'float, dms, dm, dd' ] ],
			$this->type()->validate( $value, 'coordinates.notation' )
		);
	}

	public function invalidProvider(): array {
		return [
			'unknown' => [ 'utm' ],
			'integer' => [ 5 ],
			'null' => [ null ],
			'list of one allowed' => [ [ 'float' ] ],
		];
	}

	public function testDescribeListsTheAllowedValues(): void {
		$this->assertSame(
			[ 'maps-config-type-enum', 'float, dms, dm, dd' ],
			$this->type()->describe()
		);
	}

}

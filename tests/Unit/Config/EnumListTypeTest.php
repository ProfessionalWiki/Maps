<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit\Config;

use Maps\Config\EnumListType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\Config\EnumListType
 */
class EnumListTypeTest extends TestCase {

	private function type(): EnumListType {
		return new EnumListType( [ 'traffic', 'bicycling', 'transit' ] );
	}

	/**
	 * @dataProvider validProvider
	 */
	public function testListsOfAllowedValuesAreValid( array $value ): void {
		$this->assertSame( [], $this->type()->validate( $value, 'googleMaps.layers' ) );
	}

	public function validProvider(): array {
		return [
			'empty' => [ [] ],
			'one' => [ [ 'traffic' ] ],
			'several' => [ [ 'traffic', 'transit' ] ],
		];
	}

	/**
	 * @dataProvider invalidProvider
	 */
	public function testOtherValuesAreRejected( mixed $value ): void {
		$this->assertSame(
			[ [ 'maps-config-error-invalid-enum-list', 'googleMaps.layers', 'traffic, bicycling, transit' ] ],
			$this->type()->validate( $value, 'googleMaps.layers' )
		);
	}

	public function invalidProvider(): array {
		return [
			'contains unknown' => [ [ 'traffic', 'weather' ] ],
			'not a list' => [ 'traffic' ],
			'map' => [ [ 'traffic' => true ] ],
			'non-string element' => [ [ 1 ] ],
		];
	}

}

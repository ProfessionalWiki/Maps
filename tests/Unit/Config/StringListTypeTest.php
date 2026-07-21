<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit\Config;

use Maps\Config\StringListType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\Config\StringListType
 */
class StringListTypeTest extends TestCase {

	/**
	 * @dataProvider validProvider
	 */
	public function testListsOfStringsAreValid( array $value ): void {
		$this->assertSame( [], ( new StringListType() )->validate( $value, 'leaflet.defaultLayers' ) );
	}

	public function validProvider(): array {
		return [
			'empty' => [ [] ],
			'one' => [ [ 'OpenStreetMap' ] ],
			'several' => [ [ 'OpenStreetMap', 'OpenTopoMap' ] ],
		];
	}

	/**
	 * @dataProvider invalidProvider
	 */
	public function testOtherValuesAreRejected( mixed $value ): void {
		$this->assertSame(
			[ [ 'maps-config-error-invalid-default-list', 'leaflet.defaultLayers' ] ],
			( new StringListType() )->validate( $value, 'leaflet.defaultLayers' )
		);
	}

	public function invalidProvider(): array {
		return [
			'single string' => [ 'OpenStreetMap' ],
			'non-string element' => [ [ 'OpenStreetMap', 5 ] ],
			'map' => [ [ 'OpenStreetMap' => true ] ],
		];
	}

	public function testDescribe(): void {
		$this->assertSame( [ 'maps-config-type-string-list' ], ( new StringListType() )->describe() );
	}

}

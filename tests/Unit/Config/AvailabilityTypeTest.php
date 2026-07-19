<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit\Config;

use Maps\Config\AvailabilityType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\Config\AvailabilityType
 */
class AvailabilityTypeTest extends TestCase {

	/**
	 * @dataProvider validProvider
	 */
	public function testMapsOfNamesToBooleansAreValid( array $value ): void {
		$this->assertSame( [], ( new AvailabilityType() )->validate( $value, 'leaflet.availableLayers' ) );
	}

	public function validProvider(): array {
		return [
			'empty' => [ [] ],
			'booleans' => [ [ 'OpenStreetMap' => true, 'Esri.WorldImagery' => false ] ],
			'numeric name' => [ [ '1904' => true ] ],
		];
	}

	/**
	 * @dataProvider invalidProvider
	 */
	public function testOtherValuesAreRejected( mixed $value ): void {
		$this->assertSame(
			[ [ 'maps-config-error-invalid-availability', 'leaflet.availableLayers' ] ],
			( new AvailabilityType() )->validate( $value, 'leaflet.availableLayers' )
		);
	}

	public function invalidProvider(): array {
		return [
			'string value' => [ [ 'OpenStreetMap' => 'yes' ] ],
			'integer value' => [ [ 'OpenStreetMap' => 1 ] ],
			'list' => [ [ true, false ] ],
			'not an array' => [ 'OpenStreetMap' ],
		];
	}

}

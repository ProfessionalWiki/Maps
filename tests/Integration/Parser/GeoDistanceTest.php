<?php

declare( strict_types = 1 );

namespace Maps\Tests\Integration\Parser;

use Maps\Tests\Util\TestFactory;
use PHPUnit\Framework\TestCase;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class GeoDistanceTest extends TestCase {

	private function parse( string $textToParse ): string {
		return TestFactory::newInstance()->parse( $textToParse );
	}

	public function testParameters(): void {
		$this->assertStringContainsString(
			'6,385 km',
			$this->parse( '{{#geodistance:52.5200° N, 13.4050° E|40.7128° N, 74.0060° W|unit=km}}' )
		);
	}

}

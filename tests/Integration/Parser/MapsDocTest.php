<?php

declare( strict_types = 1 );

namespace Maps\Tests\Integration\Parser;

use Maps\Tests\Util\TestFactory;
use PHPUnit\Framework\TestCase;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MapsDocTest extends TestCase {

	private function parse( string $textToParse ): string {
		return TestFactory::newInstance()->parse( $textToParse );
	}

	public function testSmoke(): void {
		$parsed = $this->parse( '{{#mapsdoc:leaflet|parameters=all}}' );

		$this->assertStringContainsString(
			'List of circles',
			$parsed
		);

		$this->assertStringContainsString(
			'OpenStreetMap',
			$parsed
		);

		$this->assertStringContainsString(
			'The maximum radius that a cluster will cover.',
			$parsed
		);
	}

}

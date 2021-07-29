<?php

declare( strict_types = 1 );

namespace Maps\Tests\Integration\Parser;

use Maps\Tests\Util\TestFactory;
use PHPUnit\Framework\TestCase;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DistanceTest extends TestCase {

	private function parse( string $textToParse ): string {
		return TestFactory::newInstance()->parse( $textToParse );
	}

	public function testFormatting(): void {
		$this->assertStringContainsString(
			'42,000 m',
			$this->parse( '{{#distance:42km}}' )
		);
	}

	public function testDecimals(): void {
		$this->assertStringContainsString(
			'42,123.45 m',
			$this->parse( '{{#distance:42.12345km|decimals=2}}' )
		);
	}

	public function testUnit(): void {
		$this->assertStringContainsString(
			'26.1 miles',
			$this->parse( '{{#distance:42km|unit=miles}}' )
		);
	}

}

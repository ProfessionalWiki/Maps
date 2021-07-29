<?php

declare( strict_types = 1 );

namespace Maps\Tests\Integration\Parser;

use Maps\Tests\Util\TestFactory;
use PHPUnit\Framework\TestCase;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class FindDestinationTest extends TestCase {

	private function parse( string $textToParse ): string {
		return TestFactory::newInstance()->parse( $textToParse );
	}

	public function testBaseCase(): void {
		$this->assertStringContainsString(
			'55° 45\' 20.83" N, 37° 36\' 34.71" W',
			$this->parse( '{{#finddestination:55.7557860° N, 37.6176330° W|90|500}}' )
		);
	}

	public function testParameters(): void {
		$this->assertStringContainsString(
			'40.712728 N, 74.000083 W',
			$this->parse( '{{#finddestination:New York|bearing=90|distance=500|format=float}}' )
		);
	}

}

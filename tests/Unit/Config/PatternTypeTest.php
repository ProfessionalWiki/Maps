<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit\Config;

use Maps\Config\PatternType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\Config\PatternType
 */
class PatternTypeTest extends TestCase {

	private function languageType(): PatternType {
		return new PatternType(
			'/^[a-zA-Z]{2,3}(-[a-zA-Z0-9]{2,8})?$/D',
			'maps-config-error-invalid-language',
			'maps-config-type-language'
		);
	}

	/**
	 * @dataProvider validProvider
	 */
	public function testMatchingStringsAreValid( string $value ): void {
		$this->assertSame( [], $this->languageType()->validate( $value, 'googleMaps.language' ) );
	}

	public function validProvider(): array {
		return [ [ 'en' ], [ 'de' ], [ 'en-GB' ], [ 'zh-Hans' ] ];
	}

	/**
	 * @dataProvider invalidProvider
	 */
	public function testNonMatchingValuesAreRejectedWithTheConfiguredKey( mixed $value ): void {
		$this->assertSame(
			[ [ 'maps-config-error-invalid-language', 'googleMaps.language' ] ],
			$this->languageType()->validate( $value, 'googleMaps.language' )
		);
	}

	public function invalidProvider(): array {
		return [
			'empty' => [ '' ],
			'script injection' => [ 'en"></script>' ],
			'trailing newline' => [ "en\n" ],
			'too long' => [ 'englishlanguage' ],
			'integer' => [ 5 ],
			'null' => [ null ],
		];
	}

	public function testDescribeReturnsTheConfiguredKey(): void {
		$this->assertSame( [ 'maps-config-type-language' ], $this->languageType()->describe() );
	}

}

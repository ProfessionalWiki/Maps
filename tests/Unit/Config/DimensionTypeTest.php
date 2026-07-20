<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit\Config;

use Maps\Config\DimensionType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\Config\DimensionType
 */
class DimensionTypeTest extends TestCase {

	private function width(): DimensionType {
		return new DimensionType( [ 'px', 'ex', 'em', '%' ], true );
	}

	private function height(): DimensionType {
		return new DimensionType( [ 'px', 'ex', 'em' ], false );
	}

	/**
	 * @dataProvider validWidthProvider
	 */
	public function testValidWidths( mixed $value ): void {
		$this->assertSame( [], $this->width()->validate( $value, 'general.mapWidth' ) );
	}

	public function validWidthProvider(): array {
		return [
			'auto' => [ 'auto' ],
			'integer' => [ 350 ],
			'float' => [ 12.5 ],
			'bare number string' => [ '350' ],
			'pixels' => [ '350px' ],
			'percent' => [ '50%' ],
			'em' => [ '10em' ],
			'ex' => [ '10ex' ],
		];
	}

	/**
	 * @dataProvider invalidWidthProvider
	 */
	public function testInvalidWidths( mixed $value ): void {
		$this->assertSame(
			[ [ 'maps-config-error-invalid-dimension', 'general.mapWidth' ] ],
			$this->width()->validate( $value, 'general.mapWidth' )
		);
	}

	public function invalidWidthProvider(): array {
		return [
			'negative' => [ -5 ],
			'style injection' => [ '350px; color: red' ],
			'trailing newline' => [ "350px\n" ],
			'disallowed unit' => [ '50vh' ],
			'unit without number' => [ '%' ],
			'internal space' => [ '1 px' ],
			'word' => [ 'wide' ],
			'boolean' => [ true ],
			'null' => [ null ],
		];
	}

	public function testHeightAllowsAllowedUnitsAndBareNumbers(): void {
		$this->assertSame( [], $this->height()->validate( 350, 'general.mapHeight' ) );
		$this->assertSame( [], $this->height()->validate( '350px', 'general.mapHeight' ) );
		$this->assertSame( [], $this->height()->validate( '10em', 'general.mapHeight' ) );
	}

	/**
	 * @dataProvider invalidHeightProvider
	 */
	public function testHeightRejectsAutoAndPercent( mixed $value ): void {
		$this->assertSame(
			[ [ 'maps-config-error-invalid-dimension', 'general.mapHeight' ] ],
			$this->height()->validate( $value, 'general.mapHeight' )
		);
	}

	public function invalidHeightProvider(): array {
		return [
			'auto not allowed' => [ 'auto' ],
			'percent not allowed' => [ '50%' ],
		];
	}

}

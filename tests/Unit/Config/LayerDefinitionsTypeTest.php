<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit\Config;

use Maps\Config\LayerDefinitionsType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\Config\LayerDefinitionsType
 */
class LayerDefinitionsTypeTest extends TestCase {

	private function validate( mixed $value ): array {
		return ( new LayerDefinitionsType() )->validate( $value, 'leaflet.layerDefinitions' );
	}

	/**
	 * @param array<string, mixed> $definition
	 */
	private function validateDefinition( array $definition ): array {
		return $this->validate( [ 'Historic' => $definition ] );
	}

	public function testEmptyMapIsValid(): void {
		$this->assertSame( [], $this->validate( [] ) );
	}

	public function testFullyPopulatedDefinitionsAreValid(): void {
		$this->assertSame(
			[],
			$this->validate( [
				'Historic 1904' => [
					'url' => 'https://tiles.example/{z}/{x}/{y}.png',
					'options' => [ 'attribution' => 'Historic', 'maxZoom' => 18 ],
				],
				'Weather' => [
					'wms' => true,
					'url' => 'https://example.org/wms',
					'options' => [ 'layers' => 'precip', 'format' => 'image/png', 'transparent' => true ],
				],
			] )
		);
	}

	public function testNonObjectIsRejected(): void {
		$this->assertSame(
			[ [ 'maps-config-error-not-object', 'leaflet.layerDefinitions' ] ],
			$this->validate( [ 1 ] )
		);
	}

	public function testReservedLayerNameIsRejected(): void {
		$this->assertSame(
			[ [ 'maps-config-error-invalid-layer-name', '__proto__' ] ],
			$this->validate( [
				'Good1' => [ 'url' => 'https://tiles.example/1/{z}/{x}/{y}.png' ],
				'__proto__' => [ 'url' => 'https://tiles.example/2/{z}/{x}/{y}.png' ],
				'Good2' => [ 'url' => 'https://tiles.example/3/{z}/{x}/{y}.png' ],
			] )
		);
	}

	public function testDefinitionMustBeAnObject(): void {
		$this->assertSame(
			[ [ 'maps-config-error-not-object', 'Historic' ] ],
			$this->validate( [ 'Historic' => 'not-an-object' ] )
		);
	}

	public function testUnknownDefinitionKeyIsRejected(): void {
		$this->assertSame(
			[ [ 'maps-config-error-unknown-layer-key', 'Historic', 'colour' ] ],
			$this->validateDefinition( [ 'url' => 'https://tiles.example/{z}/{x}/{y}.png', 'colour' => 'red' ] )
		);
	}

	public function testMissingUrlIsRejected(): void {
		$this->assertSame(
			[ [ 'maps-config-error-invalid-url', 'Historic' ] ],
			$this->validateDefinition( [ 'options' => [] ] )
		);
	}

	public function testNonHttpUrlIsRejected(): void {
		$this->assertSame(
			[ [ 'maps-config-error-invalid-url', 'Historic' ] ],
			$this->validateDefinition( [ 'url' => 'ftp://tiles.example/{z}/{x}/{y}.png' ] )
		);
	}

	public function testNonBooleanWmsIsRejected(): void {
		$this->assertSame(
			[ [ 'maps-config-error-invalid-wms', 'Historic' ] ],
			$this->validateDefinition( [ 'url' => 'https://tiles.example/{z}/{x}/{y}.png', 'wms' => 'yes' ] )
		);
	}

	public function testUnknownOptionIsRejected(): void {
		$this->assertSame(
			[ [ 'maps-config-error-unknown-option', 'Historic', 'evil' ] ],
			$this->validateDefinition( [
				'url' => 'https://tiles.example/{z}/{x}/{y}.png',
				'options' => [ 'maxZoom' => 18, 'evil' => 1, 'opacity' => 0.5 ],
			] )
		);
	}

	public function testWmsOnlyOptionIsRejectedForTileLayer(): void {
		$this->assertSame(
			[ [ 'maps-config-error-unknown-option', 'Historic', 'layers' ] ],
			$this->validateDefinition( [
				'url' => 'https://tiles.example/{z}/{x}/{y}.png',
				'options' => [ 'layers' => 'x' ],
			] )
		);
	}

	public function testWmsOptionIsAcceptedForWmsLayer(): void {
		$this->assertSame(
			[],
			$this->validateDefinition( [
				'url' => 'https://example.org/wms',
				'wms' => true,
				'options' => [ 'layers' => 'x' ],
			] )
		);
	}

	public function testNonStringAttributionIsRejected(): void {
		$this->assertSame(
			[ [ 'maps-config-error-invalid-attribution', 'Historic' ] ],
			$this->validateDefinition( [
				'url' => 'https://tiles.example/{z}/{x}/{y}.png',
				'options' => [ 'attribution' => 5 ],
			] )
		);
	}

	public function testAttributionMarkupIsAcceptedAtSaveTime(): void {
		$this->assertSame(
			[],
			$this->validateDefinition( [
				'url' => 'https://tiles.example/{z}/{x}/{y}.png',
				'options' => [ 'attribution' => '<script>x</script>' ],
			] )
		);
	}

	public function testNonHttpErrorTileUrlOptionIsRejected(): void {
		$this->assertSame(
			[ [ 'maps-config-error-invalid-option-url', 'Historic', 'errorTileUrl' ] ],
			$this->validateDefinition( [
				'url' => 'https://tiles.example/{z}/{x}/{y}.png',
				'options' => [ 'errorTileUrl' => 'javascript:alert(1)' ],
			] )
		);
	}

	public function testOptionsMustBeAnObject(): void {
		$this->assertSame(
			[ [ 'maps-config-error-not-object', 'Historic.options' ] ],
			$this->validateDefinition( [ 'url' => 'https://tiles.example/{z}/{x}/{y}.png', 'options' => [ 1 ] ] )
		);
	}

	public function testDescribe(): void {
		$this->assertSame( [ 'maps-config-type-layer-definitions' ], ( new LayerDefinitionsType() )->describe() );
	}

}

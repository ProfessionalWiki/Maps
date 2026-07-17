<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit;

use Maps\LeafletLayerDefinitions;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\LeafletLayerDefinitions
 */
class LeafletLayerDefinitionsTest extends TestCase {

	public function testTileDefinitionIsNormalized() {
		$definitions = new LeafletLayerDefinitions( [
			'Historic' => [
				'url' => 'https://tiles.example/{z}/{x}/{y}.png',
				'options' => [ 'attribution' => 'Example', 'maxZoom' => 18 ],
			],
		] );

		$this->assertSame(
			[
				'Historic' => [
					'url' => 'https://tiles.example/{z}/{x}/{y}.png',
					'options' => [ 'attribution' => 'Example', 'maxZoom' => 18 ],
					'wms' => false,
				],
			],
			$definitions->getDefinitions( [ 'Historic' ] )
		);
	}

	public function testWmsDefinitionKeepsWmsFlag() {
		$definitions = new LeafletLayerDefinitions( [
			'Weather' => [
				'wms' => true,
				'url' => 'https://example/wms',
				'options' => [ 'layers' => 'hist1904', 'format' => 'image/png', 'transparent' => true ],
			],
		] );

		$this->assertSame(
			[
				'Weather' => [
					'url' => 'https://example/wms',
					'options' => [ 'layers' => 'hist1904', 'format' => 'image/png', 'transparent' => true ],
					'wms' => true,
				],
			],
			$definitions->getDefinitions( [ 'Weather' ] )
		);
	}

	public function testOptionsDefaultToEmptyArray() {
		$definitions = new LeafletLayerDefinitions( [
			'Bare' => [ 'url' => 'https://tiles.example/{z}/{x}/{y}.png' ],
		] );

		$this->assertSame(
			[ 'url' => 'https://tiles.example/{z}/{x}/{y}.png', 'options' => [], 'wms' => false ],
			$definitions->getDefinitions( [ 'Bare' ] )['Bare']
		);
	}

	public function testDefinitionWithoutUrlIsSkipped() {
		$definitions = new LeafletLayerDefinitions( [
			'NoUrl' => [ 'options' => [ 'attribution' => 'x' ] ],
		] );

		$this->assertSame( [], $definitions->getLayerNames() );
		$this->assertSame( [], $definitions->getDefinitions( [ 'NoUrl' ] ) );
	}

	public function testDefinitionWithEmptyUrlIsSkipped() {
		$definitions = new LeafletLayerDefinitions( [
			'EmptyUrl' => [ 'url' => '' ],
		] );

		$this->assertSame( [], $definitions->getLayerNames() );
	}

	public function testDefinitionWithNonStringUrlIsSkipped() {
		$definitions = new LeafletLayerDefinitions( [
			'NumberUrl' => [ 'url' => 123 ],
		] );

		$this->assertSame( [], $definitions->getLayerNames() );
	}

	public function testNonArrayDefinitionIsSkipped() {
		$definitions = new LeafletLayerDefinitions( [
			'Weird' => 'not-an-array',
		] );

		$this->assertSame( [], $definitions->getLayerNames() );
	}

	public function testUnknownKeysAreIgnored() {
		$definitions = new LeafletLayerDefinitions( [
			'Extra' => [
				'url' => 'https://tiles.example/{z}/{x}/{y}.png',
				'unknown' => 'ignored',
			],
		] );

		$this->assertSame(
			[ 'url', 'options', 'wms' ],
			array_keys( $definitions->getDefinitions( [ 'Extra' ] )['Extra'] )
		);
	}

	public function testGetLayerNamesListsOnlyValidDefinitions() {
		$definitions = new LeafletLayerDefinitions( [
			'First' => [ 'url' => 'https://tiles.example/1/{z}/{x}/{y}.png' ],
			'Invalid' => [ 'options' => [] ],
			'Last' => [ 'url' => 'https://tiles.example/2/{z}/{x}/{y}.png' ],
		] );

		$this->assertSame( [ 'First', 'Last' ], $definitions->getLayerNames() );
	}

	public function testGetDefinitionsSelectsRequestedName() {
		$definitions = new LeafletLayerDefinitions( [
			'Before' => [ 'url' => 'https://tiles.example/b/{z}/{x}/{y}.png' ],
			'Wanted' => [ 'url' => 'https://tiles.example/w/{z}/{x}/{y}.png' ],
			'After' => [ 'url' => 'https://tiles.example/a/{z}/{x}/{y}.png' ],
		] );

		$this->assertSame(
			[ 'Wanted' ],
			array_keys( $definitions->getDefinitions( [ 'Wanted' ] ) )
		);
	}

	public function testGetDefinitionsOmitsUnknownNames() {
		$definitions = new LeafletLayerDefinitions( [
			'Known' => [ 'url' => 'https://tiles.example/{z}/{x}/{y}.png' ],
		] );

		$this->assertSame( [], $definitions->getDefinitions( [ 'Unknown' ] ) );
	}

}

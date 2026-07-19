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

	public function testAttributionHtmlIsSanitized() {
		$definitions = new LeafletLayerDefinitions( [
			'Historic' => [
				'url' => 'https://tiles.example/{z}/{x}/{y}.png',
				'options' => [
					'attribution' => '<a href="https://osm.org">OSM</a><script>alert(1)</script>',
				],
			],
		] );

		$attribution = $definitions->getDefinitions( [ 'Historic' ] )['Historic']['options']['attribution'];

		$this->assertStringContainsString( 'href="https://osm.org"', $attribution );
		$this->assertStringNotContainsString( '<script', $attribution );
	}

	public function testUnknownOptionIsDropped() {
		$definitions = new LeafletLayerDefinitions( [
			'Historic' => [
				'url' => 'https://tiles.example/{z}/{x}/{y}.png',
				'options' => [ 'maxZoom' => 18, 'evilOption' => 'x' ],
			],
		] );

		$this->assertSame(
			[ 'maxZoom' => 18 ],
			$definitions->getDefinitions( [ 'Historic' ] )['Historic']['options']
		);
	}

	public function testWmsOnlyOptionIsDroppedFromTileLayer() {
		$definitions = new LeafletLayerDefinitions( [
			'Tile' => [
				'url' => 'https://tiles.example/{z}/{x}/{y}.png',
				'options' => [ 'layers' => 'weather', 'maxZoom' => 18 ],
			],
		] );

		$this->assertSame(
			[ 'maxZoom' => 18 ],
			$definitions->getDefinitions( [ 'Tile' ] )['Tile']['options']
		);
	}

	public function testDefinitionWithNonHttpUrlIsSkipped() {
		$definitions = new LeafletLayerDefinitions( [
			'Ftp' => [ 'url' => 'ftp://tiles.example/{z}/{x}/{y}.png' ],
		] );

		$this->assertSame( [], $definitions->getLayerNames() );
	}

	public function testDefinitionWithProtocolRelativeUrlIsSkipped() {
		$definitions = new LeafletLayerDefinitions( [
			'Rel' => [ 'url' => '//tiles.example/{z}/{x}/{y}.png' ],
		] );

		$this->assertSame( [], $definitions->getLayerNames() );
	}

	public function testInvalidErrorTileUrlOptionIsDropped() {
		$definitions = new LeafletLayerDefinitions( [
			'Historic' => [
				'url' => 'https://tiles.example/{z}/{x}/{y}.png',
				'options' => [ 'errorTileUrl' => 'javascript:alert(1)' ],
			],
		] );

		$this->assertSame(
			[],
			$definitions->getDefinitions( [ 'Historic' ] )['Historic']['options']
		);
	}

	public function testValidErrorTileUrlOptionIsKept() {
		$definitions = new LeafletLayerDefinitions( [
			'Historic' => [
				'url' => 'https://tiles.example/{z}/{x}/{y}.png',
				'options' => [ 'errorTileUrl' => 'https://tiles.example/error.png' ],
			],
		] );

		$this->assertSame(
			[ 'errorTileUrl' => 'https://tiles.example/error.png' ],
			$definitions->getDefinitions( [ 'Historic' ] )['Historic']['options']
		);
	}

	public function testReservedLayerNameIsSkipped() {
		$definitions = new LeafletLayerDefinitions( [
			'Before' => [ 'url' => 'https://tiles.example/b/{z}/{x}/{y}.png' ],
			'__proto__' => [ 'url' => 'https://tiles.example/p/{z}/{x}/{y}.png' ],
			'After' => [ 'url' => 'https://tiles.example/a/{z}/{x}/{y}.png' ],
		] );

		$this->assertSame( [ 'Before', 'After' ], $definitions->getLayerNames() );
	}

	public function testTooLongLayerNameIsSkipped() {
		$definitions = new LeafletLayerDefinitions( [
			str_repeat( 'a', 201 ) => [ 'url' => 'https://tiles.example/{z}/{x}/{y}.png' ],
		] );

		$this->assertSame( [], $definitions->getLayerNames() );
	}

	public function testNumericLayerNameIsKept() {
		$definitions = new LeafletLayerDefinitions( [
			'1904' => [ 'url' => 'https://tiles.example/{z}/{x}/{y}.png' ],
		] );

		$this->assertSame( [ '1904' ], $definitions->getLayerNames() );
		$this->assertArrayHasKey( '1904', $definitions->getDefinitions( [ '1904' ] ) );
	}

	public function testUrlTemplateTokensArePreserved() {
		$definitions = new LeafletLayerDefinitions( [
			'Tokens' => [ 'url' => 'https://{s}.tiles.example/{z}/{x}/{y}.png' ],
		] );

		$this->assertSame(
			'https://{s}.tiles.example/{z}/{x}/{y}.png',
			$definitions->getDefinitions( [ 'Tokens' ] )['Tokens']['url']
		);
	}

}

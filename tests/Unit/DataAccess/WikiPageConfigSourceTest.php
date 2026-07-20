<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit\DataAccess;

use Maps\DataAccess\WikiPageConfigSource;
use Maps\Tests\TestDoubles\StubPageContentFetcher;
use MediaWiki\Content\Content;
use MediaWiki\Content\JsonContent;
use MediaWiki\Content\WikitextContent;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\DataAccess\WikiPageConfigSource
 */
class WikiPageConfigSourceTest extends TestCase {

	private function sourceFor( ?Content $content ): WikiPageConfigSource {
		return new WikiPageConfigSource( new StubPageContentFetcher( $content ), 'Maps' );
	}

	public function testReturnsTheWholeDecodedPage(): void {
		$source = $this->sourceFor( new JsonContent(
			'{ "leaflet": { "defaultLayers": [ "OpenStreetMap" ] }, "general": { "mapHeight": 400 } }'
		) );

		$this->assertSame(
			[
				'leaflet' => [ 'defaultLayers' => [ 'OpenStreetMap' ] ],
				'general' => [ 'mapHeight' => 400 ],
			],
			$source->getConfig()
		);
	}

	public function testReturnsNullWhenThereIsNoPage(): void {
		$this->assertNull( $this->sourceFor( null )->getConfig() );
	}

	public function testReturnsNullForNonJsonContent(): void {
		$this->assertNull( $this->sourceFor( new WikitextContent( '{ "general": {} }' ) )->getConfig() );
	}

	public function testReturnsNullForInvalidJson(): void {
		$this->assertNull( $this->sourceFor( new JsonContent( 'not json' ) )->getConfig() );
	}

	public function testReturnsNullWhenTheContentFetcherThrows(): void {
		$source = new WikiPageConfigSource(
			new class( null ) extends StubPageContentFetcher {
				public function getPageContent( string $pageTitle, int $defaultNamespace = NS_MAIN ): ?Content {
					throw new \RuntimeException( 'Database unavailable' );
				}
			},
			'Maps'
		);

		$this->assertNull( $source->getConfig() );
	}

}

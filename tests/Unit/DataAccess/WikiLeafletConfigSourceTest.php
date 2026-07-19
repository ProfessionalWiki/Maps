<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit\DataAccess;

use Maps\DataAccess\WikiLeafletConfigSource;
use Maps\Tests\TestDoubles\StubPageContentFetcher;
use MediaWiki\Content\Content;
use MediaWiki\Content\JsonContent;
use MediaWiki\Content\WikitextContent;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\DataAccess\WikiLeafletConfigSource
 */
class WikiLeafletConfigSourceTest extends TestCase {

	private function sourceFor( ?Content $content ): WikiLeafletConfigSource {
		return new WikiLeafletConfigSource( new StubPageContentFetcher( $content ), 'Maps' );
	}

	public function testReturnsLeafletSectionFromJsonContent() {
		$source = $this->sourceFor( new JsonContent( '{ "leaflet": { "defaultLayers": [ "OpenStreetMap" ] } }' ) );

		$this->assertSame( [ 'defaultLayers' => [ 'OpenStreetMap' ] ], $source->getLeafletConfig() );
	}

	public function testReturnsNullWhenThereIsNoPage() {
		$this->assertNull( $this->sourceFor( null )->getLeafletConfig() );
	}

	public function testReturnsNullForNonJsonContent() {
		$this->assertNull( $this->sourceFor( new WikitextContent( '{ "leaflet": {} }' ) )->getLeafletConfig() );
	}

	public function testReturnsNullWhenThereIsNoLeafletSection() {
		$this->assertNull( $this->sourceFor( new JsonContent( '{ "other": 1 }' ) )->getLeafletConfig() );
	}

	public function testReturnsNullForInvalidJson() {
		$this->assertNull( $this->sourceFor( new JsonContent( 'not json' ) )->getLeafletConfig() );
	}

	public function testReturnsNullWhenLeafletSectionIsNotAnObject() {
		$this->assertNull( $this->sourceFor( new JsonContent( '{ "leaflet": "nope" }' ) )->getLeafletConfig() );
	}

	public function testReturnsNullWhenTheContentFetcherThrows() {
		$source = new WikiLeafletConfigSource(
			new class( null ) extends StubPageContentFetcher {
				public function getPageContent( string $pageTitle, int $defaultNamespace = NS_MAIN ): ?Content {
					throw new \RuntimeException( 'Database unavailable' );
				}
			},
			'Maps'
		);

		$this->assertNull( $source->getLeafletConfig() );
	}

}

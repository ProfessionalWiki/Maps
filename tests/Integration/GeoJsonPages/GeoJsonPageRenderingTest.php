<?php

declare( strict_types = 1 );

namespace Maps\Tests\Integration\GeoJsonPages;

use Maps\GeoJsonPages\GeoJsonContent;
use Maps\Tests\Util\DeprecationWarnings;
use MediaWiki\MediaWikiServices;
use MediaWiki\Parser\ParserOutput;
use MediaWiki\Title\Title;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\GeoJsonPages\GeoJsonContentHandler
 * @covers \Maps\GeoJsonPages\GeoJsonMapPageUi
 * @covers \Maps\Presentation\OutputFacade
 */
class GeoJsonPageRenderingTest extends TestCase {

	private const PAGE_JSON = '{"type": "FeatureCollection", "features": [{"type": "Feature", '
		. '"properties": {"title": "Rijksmuseum"}, "geometry": {"type": "Point", "coordinates": [4.885, 52.36]}}]}';

	public function testPageShowsTheMap(): void {
		$this->assertStringContainsString( 'id="GeoJsonMap"', $this->renderPage()->getContentHolderText() );
	}

	private function renderPage(): ParserOutput {
		return MediaWikiServices::getInstance()->getContentRenderer()->getParserOutput(
			new GeoJsonContent( self::PAGE_JSON ),
			Title::makeTitle( NS_GEO_JSON, 'GeoJsonPageRenderingTest' )
		);
	}

	public function testPageLoadsTheMapModule(): void {
		$this->assertContains( 'ext.maps.geojson.page', $this->renderPage()->getModules() );
	}

	public function testPageRendersWithoutDeprecationWarnings(): void {
		$this->assertSame(
			[],
			DeprecationWarnings::during( fn () => $this->renderPage() )
		);
	}

}

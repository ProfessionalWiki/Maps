<?php

declare( strict_types = 1 );

namespace Maps\Tests\System;

use Maps\Tests\Util\PageCreator;
use Maps\Tests\Util\TestFactory;
use MediaWiki\Parser\ParserOptions;
use PHPUnit\Framework\TestCase;

/**
 * Needs the database: the queries need a page in the Semantic MediaWiki store. They do not ask for its coordinates, so
 * they find the page but no locations other than those they give.
 *
 * @covers \Maps\Map\SemanticFormat\MapPrinter
 * @covers \Maps\Presentation\ElementJsonSerializer
 */
class MapQueryEscapingTest extends TestCase {

	private const QUERY = '[[Coordinates::+]]';
	private const SCRIPT = '<script>alert(1)</script>';
	private const ESCAPED_SCRIPT = '&lt;script&gt;alert(1)&lt;/script&gt;';

	public static function setUpBeforeClass(): void {
		parent::setUpBeforeClass();

		if ( !defined( 'SMW_VERSION' ) ) {
			self::markTestSkipped( 'SMW is not available' );
		}

		PageCreator::instance()->createPage(
			'Property:Coordinates',
			'[[Has type::Geographic coordinate|geographic coordinate]]'
		);

		PageCreator::instance()->createPage(
			'MapQueryEscapingTest location',
			'[[Coordinates::52° 31\' 0", 13° 24\' 0"]]'
		);
	}

	public function testPageShowsScriptInDefaultAsText(): void {
		$this->assertStringContainsString( self::ESCAPED_SCRIPT, $this->renderPage( 'format=leaflet|default=' . self::SCRIPT ) );
	}

	private function renderPage( string $askParameters ): string {
		return TestFactory::newInstance()->renderPage(
			'{{#ask:' . self::QUERY . '|' . $askParameters . '}}',
			ParserOptions::newFromAnon()
		)->getContentHolderText();
	}

	public function testSpecialAskShowsScriptInDefaultAsText(): void {
		$this->assertStringContainsString(
			self::ESCAPED_SCRIPT,
			$this->renderOnSpecialAsk( [ 'format=leaflet', 'default=' . self::SCRIPT ], SMW_OUTPUT_HTML )
		);
	}

	/**
	 * @param string[] $askParameters
	 */
	private function renderOnSpecialAsk( array $askParameters, int $outputMode ): string {
		return TestFactory::newInstance()->renderOnSpecialAsk( [ self::QUERY, ...$askParameters ], $outputMode );
	}

	public function testPageRendersWikitextInDefault(): void {
		$this->assertStringContainsString( '<b>No locations</b>', $this->renderPage( "format=leaflet|default='''No locations'''" ) );
	}

	public function testSpecialAskShowsWikitextInDefaultAsText(): void {
		$this->assertStringContainsString(
			'[[No locations]]',
			$this->renderOnSpecialAsk( [ 'format=leaflet', 'default=[[No locations]]' ], SMW_OUTPUT_HTML )
		);
	}

	/**
	 * Special:Ask serves the raw output of a query as HTML.
	 */
	public function testRawResultShowsScriptInDefaultAsText(): void {
		$this->assertStringContainsString(
			self::ESCAPED_SCRIPT,
			$this->renderOnSpecialAsk( [ 'format=leaflet', 'default=' . self::SCRIPT ], SMW_OUTPUT_RAW )
		);
	}

	public function testSpecialAskShowsScriptInInlineLabelAsText(): void {
		$html = $this->renderOnSpecialAsk( [ 'format=googlemaps3', 'locations=1,1~Title~Text~~~' . self::SCRIPT ], SMW_OUTPUT_HTML );

		$this->assertStringContainsString( self::ESCAPED_SCRIPT, $this->mapDataOf( $html )['locations'][0]['inlineLabel'] );
	}

	private function mapDataOf( string $html ): array {
		$this->assertSame( 1, preg_match( '/data-mw-maps-mapdata="([^"]*)"/', $html, $matches ), 'The output holds no map' );

		return json_decode( htmlspecialchars_decode( $matches[1] ), true );
	}

	/**
	 * @dataProvider introAndOutroProvider
	 */
	public function testPageShowsScriptInIntroOrOutroAsText( string $parameter ): void {
		$html = $this->renderPage( 'format=leaflet|locations=1,1|' . $parameter . '=' . self::SCRIPT );

		$this->assertStringContainsString( self::ESCAPED_SCRIPT, $html );
		$this->assertStringNotContainsString( self::SCRIPT, $html );
	}

	public static function introAndOutroProvider(): iterable {
		yield 'intro' => [ 'intro' ];
		yield 'outro' => [ 'outro' ];
	}

	public function testPageRendersWikitextInIntro(): void {
		$this->assertStringContainsString(
			'<b>Some locations</b>',
			$this->renderPage( "format=leaflet|locations=1,1|intro='''Some locations'''" )
		);
	}

	public function testPageWrapsTheMapInIntroAndOutro(): void {
		$this->assertMatchesRegularExpression(
			'/<div class="map-box">\s*<div id="map_leaflet_/',
			$this->renderPage( 'format=leaflet|locations=1,1|intro=<div class="map-box">|outro=</div>' )
		);
	}

}

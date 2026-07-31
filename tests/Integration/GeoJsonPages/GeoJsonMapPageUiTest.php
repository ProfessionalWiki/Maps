<?php

declare( strict_types = 1 );

namespace Maps\Tests\Integration\GeoJsonPages;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Maps\GeoJsonPages\GeoJsonContent;
use Maps\GeoJsonPages\GeoJsonMapPageUi;
use Maps\Presentation\OutputFacade;
use MediaWiki\Parser\ParserOutput;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\GeoJsonPages\GeoJsonMapPageUi
 */
class GeoJsonMapPageUiTest extends TestCase {

	public function testValuesReachTheClientUnchanged() {
		// The combining long solidus overlay turns the preceding "=" into "≠".
		$title = "Tea & Coffee <b>Ltd</b>, open =\u{0338} closed";

		$this->assertSame( $title, $this->titleHandedToClient( $title ) );
	}

	private function titleHandedToClient( string $title ): string {
		$geoJson = json_decode( $this->geoJsonHandedToClient( $title ) );

		return $geoJson->features[0]->properties->title;
	}

	/**
	 * Returns the GeoJSON the way the browser hands it to the map JavaScript. The HTML parser
	 * decodes character references in attribute values, so this is the page JSON, unmodified.
	 */
	private function geoJsonHandedToClient( string $title ): string {
		return $this->mapElement( $this->renderPage( $title ) )->getAttribute( 'data-mw-maps-geojson' );
	}

	private function renderPage( string $title ): string {
		$parserOutput = new ParserOutput();

		GeoJsonMapPageUi::forExistingPage( $this->pageJson( $title ) )
			->addToOutput( OutputFacade::newFromParserOutput( $parserOutput ) );

		return $parserOutput->getRawText();
	}

	private function pageJson( string $title ): string {
		return GeoJsonContent::formatJson( (object)[
			'type' => 'FeatureCollection',
			'features' => [
				(object)[
					'type' => 'Feature',
					'properties' => (object)[ 'title' => $title ],
					'geometry' => (object)[ 'type' => 'Point', 'coordinates' => [ 4.35, 50.85 ] ]
				]
			]
		] );
	}

	private function mapElement( string $html ): DOMElement {
		$document = new DOMDocument();
		$document->loadHTML( '<meta charset="utf-8">' . $html, LIBXML_NOERROR );

		$element = ( new DOMXPath( $document ) )->query( '//*[@id="GeoJsonMap"]' )->item( 0 );

		$this->assertInstanceOf( DOMElement::class, $element, 'The page should contain the map element' );

		return $element;
	}

}

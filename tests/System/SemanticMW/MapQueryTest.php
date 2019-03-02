<?php

declare( strict_types = 1 );

namespace Maps\Tests\System\SemanticMW;

use Maps\DataAccess\PageContentFetcher;
use Maps\MapsFactory;
use Maps\Tests\Util\PageCreator;
use Maps\Tests\Util\TestFactory;
use PHPUnit\Framework\TestCase;
use Title;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MapQueryTest extends TestCase {

	/**
	 * @var PageCreator
	 */
	private $pageCreator;

	/**
	 * @var PageContentFetcher
	 */
	private $contentFetcher;

	public function setUp() {
		if ( !defined( 'SMW_VERSION' ) ) {
			$this->markTestSkipped( 'SMW is not available' );
		}

		$this->pageCreator = TestFactory::newInstance()->getPageCreator();
		$this->contentFetcher = MapsFactory::newDefault()->getPageContentFetcher();
	}

	public function testMapQuery() {
		$this->createPages();

		$this->pageCreator->createPage(
			'MapQuery',
			'{{#ask:[[Coordinates::+]]|?Coordinates|?Description|?URL|format=map}}'
		);

		// TODO: saner way
		$content = $this->contentFetcher->getPageContent( 'MapQuery' )->getParserOutput( Title::newFromText( 'MapQuery' ) )->getText();

		$this->assertContains(
			'<div id="map_',
			$content
		);

		$this->assertContains(
			'<div id="map_',
			$content
		);
	}

	private function createPages() {
		$this->pageCreator->createPage(
			'Property:Coordinates',
			'[[Has type::Geographic coordinate|geographic coordinate]]'
		);

		$this->pageCreator->createPage(
			'Property:Description',
			'[[Has type::Text]]'
		);

		$this->pageCreator->createPage(
			'Property:URL',
			'[[Has type::URL]]'
		);

		$this->pageCreator->createPage(
			'Berlin',
			'[[Coordinates::52° 31\' 0", 13° 24\' 0"]] [[Description::Capital of Germany]] [[URL::http://example.com/Berlin]]'
		);

		$this->pageCreator->createPage(
			'Brussels',
			'[[Coordinates::50° 51\' 1", 4° 21\' 6"]] [[Description::Capital of Belgium]]'
		);

		$this->pageCreator->createPage(
			'Hamburg',
			'[[Coordinates::53° 33\' 4", 9° 59\' 37"]]'
		);
	}

}

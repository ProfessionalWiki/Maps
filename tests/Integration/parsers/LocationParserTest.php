<?php

namespace Maps\Tests\Integration\parsers;

use DataValues\Geo\Values\LatLongValue;
use Jeroen\SimpleGeocoder\Geocoders\StubGeocoder;
use Maps\DataAccess\MediaWikiFileUrlFinder;
use Maps\Elements\Location;
use Maps\Presentation\WikitextParsers\LocationParser;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\Presentation\WikitextParsers\LocationParser
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class LocationParserTest extends TestCase {

	private $geocoder;
	private $fileUrlFinder;
	private $useAddressAsTitle;

	public function setUp() {
		$this->geocoder = new StubGeocoder( new LatLongValue( 1, 2 ) );
		$this->fileUrlFinder = new MediaWikiFileUrlFinder();
		$this->useAddressAsTitle = false;
	}

	private function newLocationParser() {
		return LocationParser::newInstance( $this->geocoder, $this->fileUrlFinder, $this->useAddressAsTitle );
	}

	/**
	 * @dataProvider titleProvider
	 */
	public function testGivenTitleThatIsNotLink_titleIsSetAndLinkIsNot( $title ) {
		$location = $this->newLocationParser()->parse( '4,2~' . $title );

		$this->assertTitleAndLinkAre( $location, $title, '' );
	}

	protected function assertTitleAndLinkAre( Location $location, $title, $link ) {
		$this->assertHasJsonKeyWithValue( $location, 'title', $title );
		$this->assertHasJsonKeyWithValue( $location, 'link', $link );
	}

	protected function assertHasJsonKeyWithValue( Location $polygon, $key, $value ) {
		$json = $polygon->getJSONObject();

		$this->assertArrayHasKey( $key, $json );
		$this->assertEquals( $value, $json[$key] );
	}

	public function titleProvider() {
		return [
			[ '' ],
			[ 'Title' ],
			[ 'Some title' ],
			[ 'link' ],
			[ 'links:foo' ],
		];
	}

	/**
	 * @dataProvider linkProvider
	 */
	public function testGivenTitleThatIsLink_linkIsSetAndTitleIsNot( $link ) {
		$location = $this->newLocationParser()->parse( '4,2~link:' . $link );

		$this->assertTitleAndLinkAre( $location, '', $link );
	}

	public function linkProvider() {
		return [
			[ 'https://www.semantic-mediawiki.org' ],
			[ 'irc://freenode.net' ],
		];
	}

//	/**
//	 * @dataProvider titleLinkProvider
//	 */
//	public function testGivenPageTitleAsLink_pageTitleIsTurnedIntoUrl( $link ) {
//		$parser = new LocationParser();
//		$location = $parser->parse( '4,2~link:' . $link );
//
//		$linkUrl = Title::newFromText( $link )->getFullURL();
//		$this->assertTitleAndLinkAre( $location, '', $linkUrl );
//	}
//
//	public function titleLinkProvider() {
//		return array(
//			array( 'Foo' ),
//			array( 'Some_Page' ),
//		);
//	}

	public function testGivenAddressAndNoTitle_addressIsSetAsTitle() {
		$this->useAddressAsTitle = true;
		$location = $this->newLocationParser()->parse( 'Tempelhofer Ufer 42' );

		$this->assertSame( 'Tempelhofer Ufer 42', $location->getTitle() );
	}

	public function testGivenAddressAndTitle_addressIsNotUsedAsTitle() {
		$this->useAddressAsTitle = true;
		$location = $this->newLocationParser()->parse( 'Tempelhofer Ufer 42~Great title of doom' );

		$this->assertSame( 'Great title of doom', $location->getTitle() );
	}

	public function testGivenCoordinatesAndNoTitle_noTitleIsSet() {
		$this->useAddressAsTitle = true;
		$location = $this->newLocationParser()->parse( '4,2' );

		$this->assertSame( '', $location->getTitle() );
	}

}

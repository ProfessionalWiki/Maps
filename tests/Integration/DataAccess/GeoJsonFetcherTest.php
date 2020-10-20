<?php

declare( strict_types = 1 );

namespace Maps\Tests\Integration\DataAccess;

use FileFetcher\FileFetcher;
use FileFetcher\NullFileFetcher;
use FileFetcher\SimpleFileFetcher;
use FileFetcher\StubFileFetcher;
use FileFetcher\ThrowingFileFetcher;
use Maps\DataAccess\GeoJsonFetcher;
use Maps\GeoJsonPages\GeoJsonContent;
use Maps\Tests\MapsTestFactory;
use Maps\Tests\Util\PageCreator;
use MediaWiki\MediaWikiServices;
use PHPUnit\Framework\TestCase;
use Title;

/**
 * @covers \Maps\DataAccess\GeoJsonFetcher
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class GeoJsonFetcherTest extends TestCase {

	private const VALID_FILE_JSON = [
		'type' => 'FeatureCollection',
		'features' => []
	];

	private const VALID_PAGE_JSON = [
		'type' => 'FeatureCollection',
		'features' => []
	];

	private const EXISTING_GEO_JSON_PAGE = 'Test Such';
	private const EXISTING_GEO_JSON_PAGE_WITH_PREFIX = 'GeoJson:Test Such';
	private const NON_EXISTING_GEO_JSON_PAGE = 'GeoJson:Test Nope';

	/**
	 * @var FileFetcher
	 */
	private $fileFetcher;

	public function setUp(): void {
		$this->fileFetcher = new StubFileFetcher( json_encode( self::VALID_FILE_JSON ) );

		$pageCreator = new PageCreator();
		$pageCreator->createPageWithContent(
			self::EXISTING_GEO_JSON_PAGE_WITH_PREFIX,
			new GeoJsonContent( json_encode( self::VALID_PAGE_JSON ) )
		);
	}

	private function newJsonFileParser(): GeoJsonFetcher {
		return MapsTestFactory::newTestInstance()->newGeoJsonFetcher( $this->fileFetcher );
	}

	public function testWhenFileRetrievalFails_emptyJsonIsReturned() {
		$this->fileFetcher = new ThrowingFileFetcher();

		$this->assertSame(
			[],
			$this->newJsonFileParser()->parse( 'http://such.a/file' )
		);
	}

	public function testWhenFileHasValidJson_jsonIsReturned() {
		$this->fileFetcher = new StubFileFetcher( json_encode( self::VALID_FILE_JSON ) );

		$this->assertEquals(
			self::VALID_FILE_JSON,
			$this->newJsonFileParser()->parse( 'http://such.a/file' )
		);
	}

	public function testWhenFileIsEmpty_emptyJsonIsReturned() {
		$this->fileFetcher = new NullFileFetcher();

		$this->assertSame(
			[],
			$this->newJsonFileParser()->parse( 'http://such.a/file' )
		);
	}

	public function testWhenFileLocationIsNotUrl_emptyJsonIsReturned() {
		$this->fileFetcher = new SimpleFileFetcher();

		$jsonFilePath = __DIR__ . '/../../../composer.json';
		$this->assertFileExists( $jsonFilePath );

		$this->assertSame( [], $this->newJsonFileParser()->parse( $jsonFilePath ) );
	}

	public function testWhenPageExists_itsContentsIsReturned() {
		$this->assertSame(
			self::VALID_PAGE_JSON,
			$this->newJsonFileParser()->parse( self::EXISTING_GEO_JSON_PAGE_WITH_PREFIX )
		);
	}

	public function testWhenPageDoesNotExist_emptyJsonIsReturned() {
		$this->assertSame(
			[],
			$this->newJsonFileParser()->parse( self::NON_EXISTING_GEO_JSON_PAGE )
		);
	}

	public function testWhenExistingPageIsSpecifiedWithoutPrefix_itsContentsIsReturned() {
		$this->assertSame(
			self::VALID_PAGE_JSON,
			$this->newJsonFileParser()->parse( self::EXISTING_GEO_JSON_PAGE )
		);
	}

	public function testPageIsReturnedAsSource() {
		$this->assertSame(
			self::EXISTING_GEO_JSON_PAGE,
			$this->newJsonFileParser()->fetch( self::EXISTING_GEO_JSON_PAGE_WITH_PREFIX )->getTitleValue()->getText()
		);
	}

}

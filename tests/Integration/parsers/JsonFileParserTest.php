<?php

namespace Maps\Tests\Integration\parsers;

use FileFetcher\FileFetcher;
use FileFetcher\NullFileFetcher;
use FileFetcher\SimpleFileFetcher;
use FileFetcher\StubFileFetcher;
use FileFetcher\ThrowingFileFetcher;
use Maps\DataAccess\JsonFileParser;
use Maps\MediaWiki\Content\GeoJsonContent;
use PHPUnit\Framework\TestCase;
use PHPUnit4And6Compat;
use Title;

/**
 * @covers \Maps\DataAccess\JsonFileParser
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class JsonFileParserTest extends TestCase {
	use PHPUnit4And6Compat;

	private const VALID_FILE_JSON = [
		'such' => 'string',
		42 => 13.37,
		'array' => [
			'~[,,_,,]:3'
		]
	];

	private const VALID_PAGE_JSON = [
		'foo' => 'bar',
		1 => 2.3,
		'array' => [
			'~[,,_,,]:3'
		]
	];

	private const EXISTING_GEO_JSON_PAGE = 'Such';
	private const EXISTING_GEO_JSON_PAGE_WITH_PREFIX = 'GeoJson:Such';
	private const NON_EXISTING_GEO_JSON_PAGE = 'GeoJson:Nope';

	/**
	 * @var FileFetcher
	 */
	private $fileFetcher;

	public function setUp() {
		$this->fileFetcher = new StubFileFetcher( json_encode( self::VALID_FILE_JSON ) );

		$page = new \WikiPage( Title::newFromText( self::EXISTING_GEO_JSON_PAGE_WITH_PREFIX ) );
		$page->doEditContent( new GeoJsonContent( json_encode( self::VALID_PAGE_JSON ) ), '' );
	}

	private function newJsonFileParser(): JsonFileParser {
		return new JsonFileParser(
			$this->fileFetcher,
			null
		);
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

}

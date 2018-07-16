<?php

namespace Maps\Test;

use FileFetcher\NullFileFetcher;
use FileFetcher\SimpleFileFetcher;
use FileFetcher\StubFileFetcher;
use FileFetcher\ThrowingFileFetcher;
use Maps\Parsers\JsonFileParser;
use PHPUnit\Framework\TestCase;
use PHPUnit4And6Compat;

/**
 * @covers \Maps\Parsers\JsonFileParser
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class JsonFileParserTest extends TestCase {
	use PHPUnit4And6Compat;

	/* private */ const VALID_JSON = [
		'such' => 'string',
		42 => 13.37,
		'array' => [
			'~[,,_,,]:3'
		]
	];

	public function testWhenFileRetrievalFails_emptyJsonIsReturned() {
		$parser = new JsonFileParser( new ThrowingFileFetcher() );

		$this->assertSame( [], $parser->parse( 'http://such.a/file' ) );
	}

	public function testWhenFileHasValidJson_jsonIsReturned() {
		$parser = new JsonFileParser( new StubFileFetcher( json_encode( self::VALID_JSON ) ) );

		$this->assertEquals( self::VALID_JSON, $parser->parse( 'http://such.a/file' ) );
	}

	public function testWhenFileIsEmpty_emptyJsonIsReturned() {
		$parser = new JsonFileParser( new NullFileFetcher() );

		$this->assertSame( [], $parser->parse( 'http://such.a/file' ) );
	}

	public function testWhenFileLocationIsNotUrl_emptyJsonIsReturned() {
		$parser = new JsonFileParser( new SimpleFileFetcher() );

		$jsonFilePath = __DIR__ . '/../../../composer.json';
		$this->assertFileExists( $jsonFilePath );

		$this->assertSame( [], $parser->parse( $jsonFilePath ) );
	}

}

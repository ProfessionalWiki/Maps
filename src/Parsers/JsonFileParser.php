<?php

namespace Maps\Parsers;

use FileFetcher\FileFetcher;
use FileFetcher\FileFetchingException;
use Maps\MapsFactory;
use ValueParsers\ParseException;
use ValueParsers\ValueParser;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class JsonFileParser implements ValueParser {

	private $fileFetcher;

	public function __construct( $fileFetcher = null ) {
		$this->fileFetcher = $fileFetcher instanceof FileFetcher ? $fileFetcher : MapsFactory::newDefault()->getFileFetcher();
	}

	/**
	 * @param string $fileLocation
	 *
	 * @return array
	 * @throws ParseException
	 */
	public function parse( $fileLocation ) {
		// Prevent reading JSON files on the server
		if( !filter_var( $fileLocation, FILTER_VALIDATE_URL) ) {
			return [];
		}

		try {
			$jsonString = $this->fileFetcher->fetchFile( $fileLocation );
		}
		catch ( FileFetchingException $ex ) {
			return [];
		}

		$json = json_decode( $jsonString, true );

		if ( $json === null ) {
			return [];
		}

		return $json;
	}


}

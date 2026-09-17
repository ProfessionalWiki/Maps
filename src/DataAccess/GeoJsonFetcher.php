<?php

declare( strict_types = 1 );

namespace Maps\DataAccess;

use FileFetcher\FileFetcher;
use FileFetcher\FileFetchingException;
use MediaWiki\Revision\RevisionLookup;

/**
 * Returns the content of the JSON file at the specified location as array.
 * Empty array is returned on failure.
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class GeoJsonFetcher {

	private FileFetcher $fileFetcher;
	private \TitleParser $titleParser;
	private RevisionLookup $revisionLookup;

	public function __construct( FileFetcher $fileFetcher, \TitleParser $titleParser, RevisionLookup $revisionLookup ) {
		$this->fileFetcher = $fileFetcher;
		$this->titleParser = $titleParser;
		$this->revisionLookup = $revisionLookup;
	}

	public function parse( string $fileLocation ): array {
		return $this->fetch( $fileLocation )->getContent();
	}

	public function fetch( string $fileLocation ): GeoJsonFetcherResult {
		try {
			$title = $this->titleParser->parseTitle( $fileLocation, NS_GEO_JSON );

			$revision = $this->revisionLookup->getRevisionByTitle( $title );

			if ( $revision !== null ) {
				$content = $revision->getContent( 'main' );

				if ( $content instanceof \JsonContent ) {
					return new GeoJsonFetcherResult(
						$this->normalizeJson( $content->getNativeData() ),
						$revision->getId(),
						$title
					);
				}
			}
		}
		catch ( \MalformedTitleException $e ) {
		}

		// Prevent reading JSON files on the server
		if ( !filter_var( $fileLocation, FILTER_VALIDATE_URL ) ) {
			return $this->newEmptyResult();
		}

		try {
			return new GeoJsonFetcherResult(
				$this->normalizeJson( $this->fileFetcher->fetchFile( $fileLocation ) ),
				null,
				null
			);
		}
		catch ( FileFetchingException $ex ) {
			return $this->newEmptyResult();
		}
	}

	private function newEmptyResult(): GeoJsonFetcherResult {
		return new GeoJsonFetcherResult(
			[],
			null,
			null
		);
	}

	private function normalizeJson( ?string $jsonString ): array {
		if ( $jsonString === null ) {
			return [];
		}

		$json = json_decode( $jsonString, true );

		if ( $json === null ) {
			return [];
		}

		return $json;
	}

}

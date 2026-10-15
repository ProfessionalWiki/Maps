<?php

declare( strict_types = 1 );

namespace Maps\DataAccess;

use FileFetcher\FileFetcher;
use FileFetcher\FileFetchingException;
use MediaWiki\Content\JsonContent;
use MediaWiki\Page\ExistingPageRecord;
use MediaWiki\Page\PageLookup;
use MediaWiki\Parser\Parser;
use MediaWiki\Revision\RevisionRecord;
use MediaWiki\Title\TitleValue;

/**
 * Returns the content of the JSON file at the specified location as array.
 * Empty array is returned on failure.
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class GeoJsonFetcher {

	private FileFetcher $fileFetcher;
	private PageLookup $pageLookup;
	private Parser $parser;
	private bool $allowExternalDataFiles;

	/**
	 * @param Parser $parser A parser in the middle of a parse, normally the one rendering the page. Pages are read
	 * through it, so its options decide which pages the render may include, and its output records the pages read.
	 */
	public function __construct(
		FileFetcher $fileFetcher,
		PageLookup $pageLookup,
		Parser $parser,
		bool $allowExternalDataFiles
	) {
		$this->fileFetcher = $fileFetcher;
		$this->pageLookup = $pageLookup;
		$this->parser = $parser;
		$this->allowExternalDataFiles = $allowExternalDataFiles;
	}

	public function parse( string $fileLocation ): array {
		return $this->fetch( $fileLocation )->getContent();
	}

	public function fetch( string $fileLocation ): GeoJsonFetcherResult {
		$page = $this->pageLookup->getExistingPageByText( $fileLocation, NS_GEO_JSON );

		if ( $page !== null ) {
			$revision = $this->fetchCurrentRevision( $page );

			if ( $revision !== null ) {
				$content = $revision->getContent( 'main' );

				if ( $content instanceof JsonContent ) {
					return new GeoJsonFetcherResult(
						$this->normalizeJson( $content->getText() ),
						$revision->getId(),
						TitleValue::newFromPage( $page )
					);
				}
			}
		}

		// Prevent reading JSON files on the server
		if ( !filter_var( $fileLocation, FILTER_VALIDATE_URL ) ) {
			return $this->newEmptyResult();
		}

		// External data files are only fetched when this wiki allows them
		if ( !$this->allowExternalDataFiles ) {
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

	/**
	 * Records the page as used even when the parser refuses it, so that an edit of the page also refreshes the
	 * renders that were allowed to show it.
	 */
	private function fetchCurrentRevision( ExistingPageRecord $page ): ?RevisionRecord {
		$title = TitleValue::newFromPage( $page );
		$revision = $this->parser->fetchCurrentRevisionRecordOfTitle( $title );

		$this->parser->getOutput()->addTemplate( $title, $page->getId(), $revision?->getId() );

		return $revision;
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

		// Not just null: JSON that is a bare number, string or boolean decodes to a non-array.
		if ( !is_array( $json ) ) {
			return [];
		}

		return $json;
	}

}

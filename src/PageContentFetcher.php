<?php

namespace Maps;

use MediaWiki\Storage\RevisionLookup;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PageContentFetcher {

	private $titleParser;
	private $revisionLookup;

	public function __construct( \TitleParser $titleParser, RevisionLookup $revisionLookup ) {
		$this->titleParser = $titleParser;
		$this->revisionLookup = $revisionLookup;
	}

	public function getPageContent( string $pageTitle, int $defaultNamespace ): ?\Content {
		try {
			$title = $this->titleParser->parseTitle( $pageTitle, $defaultNamespace );
		}
		catch ( \MalformedTitleException $e ) {
			return null;
		}

		$revision = $this->revisionLookup->getRevisionByTitle( $title );

		if ( $revision === null ) {
			return null;
		}

		return $revision->getContent( 'main' );
	}

}

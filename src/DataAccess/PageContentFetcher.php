<?php

declare( strict_types = 1 );

namespace Maps\DataAccess;

use MediaWiki\Revision\RevisionLookup;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PageContentFetcher {

	private \TitleParser $titleParser;
	private RevisionLookup $revisionLookup;

	public function __construct( \TitleParser $titleParser, RevisionLookup $revisionLookup ) {
		$this->titleParser = $titleParser;
		$this->revisionLookup = $revisionLookup;
	}

	public function getPageContent( string $pageTitle, int $defaultNamespace = NS_MAIN ): ?\Content {
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

		// $revision->getRevisionRecord()->getContent( 'main' );
		return $revision->getContent( 'main' );
	}

}

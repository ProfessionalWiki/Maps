<?php

declare( strict_types = 1 );

namespace Maps\Tests\Util;

use CommentStoreComment;
use Title;
use User;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PageCreator {

	public static function instance(): self {
		return new self();
	}

	public function createPage( string $title, string $content = null ) {
		$titleObject = Title::newFromText( $title );

		$this->createPageWithContent(
			$title,
			\ContentHandler::makeContent( $content ?? 'Content of ' . $title, $titleObject )
		);
	}

	public function createPageWithContent( string $title, \Content $content ) {
		$titleObject = Title::newFromText( $title );
		$page = new \WikiPage( $titleObject );

		$updater = $page->newPageUpdater( User::newSystemUser( 'TestUser' ) );
		$updater->setContent( 'main', $content );
		$updater->saveRevision( CommentStoreComment::newUnsavedComment( __CLASS__ . ' creating page ' . $title ) );
	}

}

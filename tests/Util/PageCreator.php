<?php

declare( strict_types = 1 );

namespace Maps\Tests\Util;

use Title;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PageCreator {

	public function createPage( string $title, string $content = null ) {
		$titleObject = Title::newFromText( $title );
		$page = new \WikiPage( $titleObject );

		$page->doEditContent(
			\ContentHandler::makeContent( $content ?? 'Content of ' . $title, $titleObject ),
			__CLASS__ . ' creating page ' . $title
		);
	}

}

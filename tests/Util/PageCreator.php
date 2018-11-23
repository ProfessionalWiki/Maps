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
		$page = new \WikiPage( Title::newFromText( $title ) );

		$page->doEditContent(
			new \WikitextContent( $content ?? 'Content of ' . $title ),
			__CLASS__ . ' creating page ' . $title
		);
	}

}

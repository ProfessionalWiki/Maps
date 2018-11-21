<?php

declare( strict_types = 1 );

namespace Maps\DataAccess;

use ImagePage;
use Maps\FileUrlFinder;
use Title;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MediaWikiFileUrlFinder implements FileUrlFinder {

	public function getUrlForFileName( string $fileName ): string {
		$colonPosition = strpos( $fileName, ':' );

		$titleWithoutPrefix = $colonPosition === false ? $fileName : substr( $fileName, $colonPosition + 1 );

		$title = Title::newFromText( trim( $titleWithoutPrefix ), NS_FILE );

		if ( $title !== null && $title->exists() ) {
			return ( new ImagePage( $title ) )->getDisplayedFile()->getURL();
		}

		return trim( $fileName );
	}

}

<?php

declare( strict_types = 1 );

namespace Maps\DataAccess;

use ImagePage;
use Maps\FileUrlFinder;
use RepoGroup;
use Title;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MediaWikiFileUrlFinder implements FileUrlFinder {

	public function getUrlForFileName( string $fileName ): string {
		$colonPosition = strpos( $fileName, ':' );

		$titleWithoutPrefix = $colonPosition === false ? $fileName : substr( $fileName, $colonPosition + 1 );

		$file = RepoGroup::singleton()->findFile( trim( $titleWithoutPrefix ) );

		if ( $file && $file->exists() ) {
			return $file->getURL();
		}

		return trim( $fileName );
	}
}

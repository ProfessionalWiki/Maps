<?php

declare( strict_types = 1 );

namespace Maps\DataAccess;

use Maps\FileUrlFinder;
use MediaWiki\MediaWikiServices;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MediaWikiFileUrlFinder implements FileUrlFinder {

	public function getUrlForFileName( string $fileName ): string {
		$colonPosition = strpos( $fileName, ':' );

		$titleWithoutPrefix = $colonPosition === false ? $fileName : substr( $fileName, $colonPosition + 1 );

		$file = MediaWikiServices::getInstance()->getRepoGroup()->findFile( trim( $titleWithoutPrefix ) );

		if ( $file && $file->exists() ) {
			return $file->getURL();
		}

		return trim( $fileName );
	}
}

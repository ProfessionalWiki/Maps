<?php

declare( strict_types = 1 );

namespace Maps\DataAccess;

use FileFetcher\FileFetcher;
use FileFetcher\FileFetchingException;
use MediaWiki\MediaWikiServices;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MapsFileFetcher implements FileFetcher {

	public function fetchFile( string $fileUrl ): string {
		$result = MediaWikiServices::getInstance()->getHttpRequestFactory()->get( $fileUrl );

		if ( !is_string( $result ) ) {
			throw new FileFetchingException( $fileUrl );
		}

		return $result;
	}

}

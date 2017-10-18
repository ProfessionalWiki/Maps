<?php

namespace Maps;

use FileFetcher\FileFetcher;
use FileFetcher\FileFetchingException;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MapsFileFetcher implements FileFetcher {

	/**
	 * Returns the contents of the specified file.
	 *
	 * @since 3.0
	 *
	 * @param string $fileUrl
	 *
	 * @return string
	 * @throws FileFetchingException
	 */
	public function fetchFile( string $fileUrl ): string {
		$result = \Http::get( $fileUrl );

		if ( !is_string( $result ) ) {
			throw new FileFetchingException( $fileUrl );
		}

		return $result;
	}

}

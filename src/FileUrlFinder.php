<?php

declare( strict_types = 1 );

namespace Maps;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface FileUrlFinder {

	/**
	 * Resolves the url of images provided as wiki page; leaves others alone.
	 */
	public function getUrlForFileName( string $fileName ): string;

	/**
	 * The url of the file page with this name, or null when this wiki has no such file.
	 */
	public function findFileUrl( string $fileName ): ?string;

}

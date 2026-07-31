<?php

declare( strict_types = 1 );

namespace Maps\Tests\TestDoubles;

use Maps\FileUrlFinder;

class InMemoryFileUrlFinder implements FileUrlFinder {

	/**
	 * @var array<string, string>
	 */
	private array $urls = [];

	public function addFile( string $fileName, string $url ): void {
		$this->urls[$fileName] = $url;
	}

	public function getUrlForFileName( string $fileName ): string {
		return $this->findFileUrl( $fileName ) ?? trim( $fileName );
	}

	public function findFileUrl( string $fileName ): ?string {
		return $this->urls[trim( $fileName )] ?? null;
	}

}

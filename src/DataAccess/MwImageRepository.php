<?php

declare( strict_types = 1 );

namespace Maps\DataAccess;

use RepoGroup;

class MwImageRepository implements ImageRepository {

	private RepoGroup $repoGroup;

	public function __construct( RepoGroup $repoGroup ) {
		$this->repoGroup = $repoGroup;
	}

	public function getByName( string $imageName ): ?Image {
		$file = $this->repoGroup->findFile( trim( $this->getNameWithoutPrefix( $imageName ) ) );

		if ( $file === false || !$file->exists() ) {
			return null;
		}

		return new MwImage( $file );
	}

	private function getNameWithoutPrefix( string $imageName ): string {
		$colonPosition = strpos( $imageName, ':' );

		return $colonPosition === false ? $imageName : substr( $imageName, $colonPosition + 1 );
	}

}

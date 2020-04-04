<?php

declare( strict_types = 1 );

namespace Maps\DataAccess;

interface ImageRepository {

	/**
	 * @param string $imageName Typically user input. Can be "File:FileName.png", "FileName.png" or "LocalizedFile:FileName.png"
	 */
	public function getByName( string $imageName ): ?Image;

}

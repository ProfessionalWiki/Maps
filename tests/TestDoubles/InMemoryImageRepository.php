<?php

declare( strict_types = 1 );

namespace Maps\Tests\TestDoubles;

use Maps\DataAccess\Image;
use Maps\DataAccess\ImageRepository;

class InMemoryImageRepository implements ImageRepository {

	private $images;

	public function getByName( string $imageName ): ?Image {
		return $this->images[$imageName] ?? null;
	}

	public function addImage( string $imageName, Image $image ) {
		$this->images[$imageName] = $image;
	}

}

<?php

declare( strict_types = 1 );

namespace Maps\Tests;

use Maps\DataAccess\ImageRepository;
use Maps\MapsFactory;
use Maps\Tests\TestDoubles\InMemoryImageRepository;

class MapsTestFactory extends MapsFactory {

	/**
	 * Can be exposed via getter type in PHP 7.4+
	 * @var InMemoryImageRepository
	 */
	public $imageRepo;

	/**
	 * Initializes a new test instance, updates the global instance used by production code and returns it.
	 */
	public static function newTestInstance(): self {
		self::$globalInstance = self::newDefault();

		self::$globalInstance->imageRepo = new InMemoryImageRepository();

		return self::$globalInstance;
	}

	public function getImageRepository(): ImageRepository {
		return $this->imageRepo;
	}

}

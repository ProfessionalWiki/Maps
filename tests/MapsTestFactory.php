<?php

declare( strict_types = 1 );

namespace Maps\Tests;

use Maps\DataAccess\ImageRepository;
use Maps\LeafletConfigSource;
use Maps\MapsFactory;
use Maps\Tests\TestDoubles\InMemoryImageRepository;
use Maps\Tests\TestDoubles\StubLeafletConfigSource;

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

	/**
	 * Keeps tests hermetic: the config lookup uses the PHP settings only, without reading the
	 * MediaWiki:Maps page from the database.
	 */
	protected function newWikiLeafletConfigSource(): LeafletConfigSource {
		return new StubLeafletConfigSource( null );
	}

}

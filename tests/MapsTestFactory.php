<?php

declare( strict_types = 1 );

namespace Maps\Tests;

use Maps\Config\WikiConfigSource;
use Maps\DataAccess\ImageRepository;
use Maps\MapsFactory;
use Maps\Tests\TestDoubles\InMemoryImageRepository;
use Maps\Tests\TestDoubles\StubWikiConfigSource;

class MapsTestFactory extends MapsFactory {

	/**
	 * Can be exposed via getter type in PHP 7.4+
	 * @var InMemoryImageRepository
	 */
	public $imageRepo;

	/**
	 * When set, the effective settings read this as the MediaWiki:Maps config instead of the
	 * default hermetic (empty) source. Reset to null in tearDown.
	 *
	 * @var array<string, mixed>|null
	 */
	public static ?array $wikiConfig = null;

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
	 * Keeps tests hermetic: the effective settings use the PHP settings only, unless a test sets
	 * self::$wikiConfig, and never read the MediaWiki:Maps page from the database.
	 */
	protected function newWikiConfigSource(): WikiConfigSource {
		return new StubWikiConfigSource( self::$wikiConfig );
	}

}

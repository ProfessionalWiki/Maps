<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit;

use Maps\Config\ConfigSchema;
use Maps\Config\EffectiveSettings;
use Maps\GoogleMapsService;
use Maps\Tests\TestDoubles\InMemoryFileUrlFinder;
use Maps\Tests\TestDoubles\StubWikiConfigSource;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\GoogleMapsService
 */
class GoogleMapsServiceTest extends TestCase {

	private const WIKI_FILE = 'Points.kml';
	private const WIKI_FILE_URL = 'https://wiki.example/images/Points.kml';
	private const EXTERNAL_URL = 'https://example.com/points.kml';

	public function testWikiFileIsResolvedToItsUrl(): void {
		$this->assertSame(
			[ self::WIKI_FILE_URL ],
			$this->kmlUrlsAllowingExternal( [ self::WIKI_FILE ] )
		);
	}

	public function testExternalUrlIsUsedAsIs(): void {
		$this->assertSame(
			[ self::EXTERNAL_URL ],
			$this->kmlUrlsAllowingExternal( [ self::EXTERNAL_URL ] )
		);
	}

	public function testOnlyWikiFilesRemainWhenExternalDataFilesAreNotAllowed(): void {
		$this->assertSame(
			[ self::WIKI_FILE_URL ],
			$this->kmlUrlsWithoutExternal( [ self::EXTERNAL_URL, self::WIKI_FILE, 'Missing.kml' ] )
		);
	}

	public function testExternalUrlIsDroppedWithTheDefaultSettings(): void {
		$this->assertSame(
			[ self::WIKI_FILE_URL ],
			$this->kmlUrls(
				$this->newServiceWithSettings( [] ),
				[ self::EXTERNAL_URL, self::WIKI_FILE ]
			)
		);
	}

	public function testMapDataSaysExternalDataFilesAreAllowed(): void {
		$this->assertTrue( $this->externalDataFilesInMapData( true ) );
	}

	public function testMapDataSaysExternalDataFilesAreNotAllowed(): void {
		$this->assertFalse( $this->externalDataFilesInMapData( false ) );
	}

	/**
	 * @param string[] $fileNames
	 * @return string[]
	 */
	private function kmlUrlsAllowingExternal( array $fileNames ): array {
		return $this->kmlUrls( $this->newService( true ), $fileNames );
	}

	/**
	 * @param string[] $fileNames
	 * @return string[]
	 */
	private function kmlUrlsWithoutExternal( array $fileNames ): array {
		return $this->kmlUrls( $this->newService( false ), $fileNames );
	}

	/**
	 * The urls the browser is told to fetch for the given values of the kml parameter.
	 *
	 * @param string[] $fileNames
	 * @return string[]
	 */
	private function kmlUrls( GoogleMapsService $service, array $fileNames ): array {
		$postFormat = $service->getParameterInfo()['kml']['post-format'];

		return $postFormat( $fileNames );
	}

	private function externalDataFilesInMapData( bool $allowExternalDataFiles ): bool {
		return $this->newService( $allowExternalDataFiles )
			->newMapDataFromParameters( [] )
			->getParameters()['allowexternaldatafiles'];
	}

	private function newService( bool $allowExternalDataFiles ): GoogleMapsService {
		return $this->newServiceWithSettings(
			[ 'egMapsAllowExternalDataFiles' => $allowExternalDataFiles ]
		);
	}

	/**
	 * @param array<string, mixed> $settings Settings overriding the shipped defaults
	 */
	private function newServiceWithSettings( array $settings ): GoogleMapsService {
		$fileUrlFinder = new InMemoryFileUrlFinder();
		$fileUrlFinder->addFile( self::WIKI_FILE, self::WIKI_FILE_URL );

		return new GoogleMapsService(
			new EffectiveSettings(
				array_merge( require __DIR__ . '/../../DefaultSettings.php', $settings ),
				ConfigSchema::newDefault(),
				new StubWikiConfigSource( null ),
				true
			),
			$fileUrlFinder
		);
	}

}

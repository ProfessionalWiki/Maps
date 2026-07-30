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

	public function testOnlyWikiFilesRemainWhenExternalKmlIsNotAllowed(): void {
		$this->assertSame(
			[ self::WIKI_FILE_URL ],
			$this->kmlUrlsWithoutExternal( [ self::EXTERNAL_URL, self::WIKI_FILE, 'Missing.kml' ] )
		);
	}

	public function testMapDataSaysExternalKmlIsAllowed(): void {
		$this->assertTrue( $this->externalKmlInMapData( true ) );
	}

	public function testMapDataSaysExternalKmlIsNotAllowed(): void {
		$this->assertFalse( $this->externalKmlInMapData( false ) );
	}

	/**
	 * @param string[] $fileNames
	 * @return string[]
	 */
	private function kmlUrlsAllowingExternal( array $fileNames ): array {
		return $this->kmlUrls( $fileNames, true );
	}

	/**
	 * @param string[] $fileNames
	 * @return string[]
	 */
	private function kmlUrlsWithoutExternal( array $fileNames ): array {
		return $this->kmlUrls( $fileNames, false );
	}

	/**
	 * The urls the browser is told to fetch for the given values of the kml parameter.
	 *
	 * @param string[] $fileNames
	 * @return string[]
	 */
	private function kmlUrls( array $fileNames, bool $allowExternalKml ): array {
		$postFormat = $this->newService( $allowExternalKml )->getParameterInfo()['kml']['post-format'];

		return $postFormat( $fileNames );
	}

	private function externalKmlInMapData( bool $allowExternalKml ): bool {
		return $this->newService( $allowExternalKml )
			->newMapDataFromParameters( [] )
			->getParameters()['allowexternalkml'];
	}

	private function newService( bool $allowExternalKml ): GoogleMapsService {
		$fileUrlFinder = new InMemoryFileUrlFinder();
		$fileUrlFinder->addFile( self::WIKI_FILE, self::WIKI_FILE_URL );

		return new GoogleMapsService(
			$this->newSettings( $allowExternalKml ),
			$fileUrlFinder
		);
	}

	private function newSettings( bool $allowExternalKml ): EffectiveSettings {
		return new EffectiveSettings(
			array_merge(
				require __DIR__ . '/../../DefaultSettings.php',
				[ 'egMapsAllowExternalKml' => $allowExternalKml ]
			),
			ConfigSchema::newDefault(),
			new StubWikiConfigSource( null ),
			true
		);
	}

}

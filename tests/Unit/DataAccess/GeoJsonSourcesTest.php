<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit\DataAccess;

use Maps\DataAccess\GeoJsonFetcherResult;
use Maps\DataAccess\GeoJsonSources;
use MediaWiki\Title\TitleValue;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\DataAccess\GeoJsonSources
 */
class GeoJsonSourcesTest extends TestCase {

	private const REVISION_ID = 42;

	public function testContentOfEverySourceIsIncluded() {
		$sources = new GeoJsonSources( [
			$this->newPageResult( 'First', 'FirstFeature' ),
			$this->newPageResult( 'Second', 'SecondFeature' ),
		] );

		$this->assertSame(
			[
				$this->newFeatureCollection( 'FirstFeature' ),
				$this->newFeatureCollection( 'SecondFeature' ),
			],
			$sources->getContents()
		);
	}

	public function testSourcesWithoutContentAreDropped() {
		$sources = new GeoJsonSources( [
			$this->newEmptyResult(),
			$this->newPageResult( 'Second', 'SecondFeature' ),
			$this->newEmptyResult(),
		] );

		$this->assertSame(
			[ $this->newFeatureCollection( 'SecondFeature' ) ],
			$sources->getContents()
		);
	}

	public function testWithoutSourcesThereIsNoContent() {
		$this->assertSame( [], ( new GeoJsonSources( [] ) )->getContents() );
	}

	public function testSinglePageSourceIsEditable() {
		$sources = new GeoJsonSources( [ $this->newPageResult( 'OnlySource', 'Feature' ) ] );

		$this->assertSame( 'OnlySource', $sources->getEditablePageName() );
		$this->assertSame( self::REVISION_ID, $sources->getEditableRevisionId() );
	}

	public function testSeveralPageSourcesAreNotEditable() {
		$sources = new GeoJsonSources( [
			$this->newPageResult( 'First', 'FirstFeature' ),
			$this->newPageResult( 'Second', 'SecondFeature' ),
		] );

		$this->assertNull( $sources->getEditablePageName() );
		$this->assertNull( $sources->getEditableRevisionId() );
	}

	public function testSourceThatIsNotAPageIsNotEditable() {
		$sources = new GeoJsonSources( [ $this->newUrlResult( 'Feature' ) ] );

		$this->assertNull( $sources->getEditablePageName() );
		$this->assertNull( $sources->getEditableRevisionId() );
	}

	public function testSourceOutsideTheGeoJsonNamespaceIsNotEditable() {
		$sources = new GeoJsonSources( [
			new GeoJsonFetcherResult(
				$this->newFeatureCollection( 'Feature' ),
				self::REVISION_ID,
				new TitleValue( NS_MEDIAWIKI, 'Maps' )
			),
		] );

		$this->assertNull( $sources->getEditablePageName() );
		$this->assertNull( $sources->getEditableRevisionId() );
	}

	public function testSecondSourceWithoutContentStillMakesTheFirstOneUneditable() {
		$sources = new GeoJsonSources( [
			$this->newPageResult( 'First', 'FirstFeature' ),
			$this->newEmptyResult(),
		] );

		$this->assertNull( $sources->getEditablePageName() );
		$this->assertNull( $sources->getEditableRevisionId() );
	}

	private function newPageResult( string $pageName, string $featureTitle ): GeoJsonFetcherResult {
		return new GeoJsonFetcherResult(
			$this->newFeatureCollection( $featureTitle ),
			self::REVISION_ID,
			new TitleValue( NS_GEO_JSON, $pageName )
		);
	}

	private function newUrlResult( string $featureTitle ): GeoJsonFetcherResult {
		return new GeoJsonFetcherResult( $this->newFeatureCollection( $featureTitle ), null, null );
	}

	private function newEmptyResult(): GeoJsonFetcherResult {
		return new GeoJsonFetcherResult( [], null, null );
	}

	private function newFeatureCollection( string $featureTitle ): array {
		return [
			'type' => 'FeatureCollection',
			'features' => [
				[
					'type' => 'Feature',
					'geometry' => [ 'type' => 'Point', 'coordinates' => [ 4.35, 50.85 ] ],
					'properties' => [ 'title' => $featureTitle ],
				],
			],
		];
	}

}

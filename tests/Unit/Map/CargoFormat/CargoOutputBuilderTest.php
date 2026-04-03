<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit\Map\CargoFormat;

use DataValues\Geo\Values\LatLongValue;
use Maps\Map\CargoFormat\CargoOutputBuilder;
use Maps\Map\Marker;
use Maps\LeafletService;
use Maps\Map\MapOutputBuilder;
use Maps\MappingServices;
use Maps\Tests\TestDoubles\ImageValueObject;
use Maps\Tests\TestDoubles\InMemoryImageRepository;
use ParamProcessor\ParamDefinitionFactory;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * @covers \Maps\Map\CargoFormat\CargoOutputBuilder
 */
class CargoOutputBuilderTest extends TestCase {

	public function testSetIconUrlWithMissingFileDoesNotCrash() {
		$marker = new Marker( new LatLongValue( 1, 2 ) );

		$this->callSetIconUrl( new InMemoryImageRepository(), [ $marker ], 'nonexistent.png' );

		$this->assertSame( '', $marker->getIconUrl() );
	}

	public function testSetIconUrlWithExistingFileSetsUrl() {
		$imageRepo = new InMemoryImageRepository();
		$imageRepo->addImage( 'icon.png', new ImageValueObject( 'http://example.com/icon.png', 32, 32 ) );

		$marker = new Marker( new LatLongValue( 1, 2 ) );

		$this->callSetIconUrl( $imageRepo, [ $marker ], 'icon.png' );

		$this->assertSame( 'http://example.com/icon.png', $marker->getIconUrl() );
	}

	public function testSetIconUrlWithEmptyParameterDoesNothing() {
		$imageRepo = new InMemoryImageRepository();
		$imageRepo->addImage( 'icon.png', new ImageValueObject( 'http://example.com/icon.png', 32, 32 ) );

		$marker = new Marker( new LatLongValue( 1, 2 ) );

		$this->callSetIconUrl( $imageRepo, [ $marker ], '' );

		$this->assertSame( '', $marker->getIconUrl() );
	}

	private function callSetIconUrl( InMemoryImageRepository $imageRepo, array $markers, string $iconParameter ): void {
		$leafletService = new LeafletService( new InMemoryImageRepository() );

		$builder = new CargoOutputBuilder(
			new MapOutputBuilder(),
			new MappingServices( [ 'leaflet' ], 'leaflet', $leafletService ),
			new ParamDefinitionFactory(),
			$imageRepo
		);

		$method = new ReflectionMethod( CargoOutputBuilder::class, 'setIconUrl' );
		$method->setAccessible( true );
		$method->invoke( $builder, $markers, $iconParameter );
	}

}

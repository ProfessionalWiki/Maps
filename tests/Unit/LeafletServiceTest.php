<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit;

use Maps\LeafletService;
use Maps\Tests\TestDoubles\ImageValueObject;
use Maps\Tests\TestDoubles\InMemoryImageRepository;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * @covers \Maps\LeafletService
 */
class LeafletServiceTest extends TestCase {

	public function testZeroWidthImageLayerIsSkipped() {
		$imageRepo = new InMemoryImageRepository();
		$imageRepo->addImage( 'zero.png', new ImageValueObject( 'http://example.com/zero.png', 0, 100 ) );

		$result = $this->getJsImageLayers( $imageRepo, [ 'zero.png' ] );

		$this->assertSame( [], $result );
	}

	public function testValidImageLayerIsIncluded() {
		$imageRepo = new InMemoryImageRepository();
		$imageRepo->addImage( 'valid.png', new ImageValueObject( 'http://example.com/valid.png', 200, 100 ) );

		$result = $this->getJsImageLayers( $imageRepo, [ 'valid.png' ] );

		$this->assertCount( 1, $result );
		$this->assertSame( 'valid.png', $result[0]['name'] );
		$this->assertSame( 'http://example.com/valid.png', $result[0]['url'] );
		$this->assertSame( 100, $result[0]['width'] );
		$this->assertSame( 50.0, $result[0]['height'] );
	}

	public function testNonExistentImageLayerIsSkipped() {
		$imageRepo = new InMemoryImageRepository();

		$result = $this->getJsImageLayers( $imageRepo, [ 'missing.png' ] );

		$this->assertSame( [], $result );
	}

	private function getJsImageLayers( InMemoryImageRepository $imageRepo, array $imageLayers ): array {
		$service = new LeafletService( $imageRepo );

		$method = new ReflectionMethod( LeafletService::class, 'getJsImageLayers' );
		$method->setAccessible( true );

		return $method->invoke( $service, $imageLayers );
	}

}

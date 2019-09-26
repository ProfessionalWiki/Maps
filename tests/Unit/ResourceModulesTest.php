<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit;

use PHPUnit\Framework\TestCase;

class ResourceModulesTest extends TestCase {

	/**
	 * @dataProvider mapsModuleNameProvider
	 */
	public function testDependenciesExist( array $module ) {
		foreach ( $module['dependencies'] ?? [] as $dependency ) {
			$this->assertArrayHasKey( $dependency, $GLOBALS['wgResourceModules'] );
		}

		$this->assertTrue( true );
	}

	public function mapsModuleNameProvider() {
		foreach ( $GLOBALS['wgResourceModules'] as $name => $module ) {
			if ( $this->isMapsModule( $name ) ) {
				yield $name => $module;
			}
		}
	}

	private function isMapsModule( string $name ): bool {
		$modulePrefix = 'ext.maps.';
		return substr( $name, 0, strlen( $modulePrefix ) ) === $modulePrefix;
	}

}

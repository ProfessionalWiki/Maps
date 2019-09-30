<?php

declare( strict_types = 1 );

namespace Maps\Tests\Integration;

use MediaWiki\MediaWikiServices;
use PHPUnit\Framework\TestCase;
use ResourceLoader;

class ResourceModulesTest extends TestCase {

	/**
	 * @dataProvider mapsModuleNameProvider
	 */
	public function testDependenciesExist( string $moduleName ) {
		$resourceLoader = $this->getResourceLoader();
		$module = $resourceLoader->getModule( $moduleName );

		if ( $module->getDependencies() === [] ) {
			$this->assertTrue( true );
		}
		else {
			foreach ( $module->getDependencies() as $dependency ) {
				$this->assertNotNull(
					$resourceLoader->getModule( $dependency ),
					'Dependency ' . $dependency . ' should exist'
				);
			}
		}
	}

	public function mapsModuleNameProvider() {
		foreach ( $this->getResourceLoader()->getModuleNames() as $name ) {
			if ( $this->isMapsModule( $name ) ) {
				yield $name => [ $name ];
			}
		}
	}

	private function isMapsModule( string $name ): bool {
		$modulePrefix = 'ext.maps.';
		return substr( $name, 0, strlen( $modulePrefix ) ) === $modulePrefix;
	}

	private function getResourceLoader(): ResourceLoader {
		$mwServices = MediaWikiServices::getInstance();

		if ( method_exists( $mwServices, 'getResourceLoader' ) ) {
			return $mwServices->getResourceLoader();
		}

		return new ResourceLoader();
	}

}

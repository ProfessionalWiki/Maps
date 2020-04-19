<?php

declare( strict_types = 1 );

namespace Maps\Tests\Integration;

use PHPUnit\Framework\TestCase;

class MapsSetupTest extends TestCase {

	public function testSpecialPageClassExists() {
		$this->assertTrue( class_exists( $GLOBALS['wgSpecialPages']['MapEditor'] ) );
	}

}

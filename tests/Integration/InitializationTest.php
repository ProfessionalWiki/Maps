<?php

declare( strict_types = 1 );

namespace Maps\Tests\Integration;

use PHPUnit\Framework\TestCase;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class InitializationTest extends TestCase {

	public function testVersionConstantIsDefined() {
		$this->assertIsInt( NS_GEO_JSON );
	}

}

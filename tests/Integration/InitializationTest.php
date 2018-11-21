<?php

namespace Maps\Tests\Integration;

use PHPUnit\Framework\TestCase;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class InitializationTest extends TestCase {

	public function testVersionConstantIsDefined() {
		$this->assertInternalType( 'string', Maps_VERSION );
		$this->assertInternalType( 'string', SM_VERSION );
		$this->assertSame( Maps_VERSION, SM_VERSION );
		$this->assertNotEmpty( Maps_VERSION );
	}

}

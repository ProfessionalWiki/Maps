<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit\Config;

use Maps\Config\ConfigExample;
use Maps\Config\ConfigSchema;
use Maps\Config\ConfigValidator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\Config\ConfigExample
 */
class ConfigExampleTest extends TestCase {

	public function testExampleIsSyntacticallyValidJson(): void {
		json_decode( ConfigExample::JSON, true );

		$this->assertSame( JSON_ERROR_NONE, json_last_error() );
	}

	public function testPreloadedExamplePassesTheValidator(): void {
		$errors = ( new ConfigValidator( ConfigSchema::newDefault() ) )->validate( ConfigExample::JSON );

		$this->assertSame( [], $errors );
	}

}

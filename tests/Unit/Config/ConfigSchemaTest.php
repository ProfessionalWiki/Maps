<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit\Config;

use Maps\Config\ConfigSchema;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\Config\ConfigSchema
 */
class ConfigSchemaTest extends TestCase {

	public function testKnownGroupsAndKeysResolveToTheirSetting(): void {
		$schema = ConfigSchema::newDefault();

		$this->assertTrue( $schema->hasGroup( 'general' ) );
		$this->assertSame( 'egMapsMapWidth', $schema->getSetting( 'general', 'mapWidth' )->settingName );
	}

	public function testUnknownGroupsAndKeysDoNotResolve(): void {
		$schema = ConfigSchema::newDefault();

		$this->assertFalse( $schema->hasGroup( 'bogus' ) );
		$this->assertNull( $schema->getSetting( 'general', 'bogus' ) );
		$this->assertNull( $schema->getSetting( 'bogus', 'mapWidth' ) );
	}

	public function testGroupAndKeyPairsAreUnique(): void {
		$seen = [];

		foreach ( ConfigSchema::newDefault()->getSettings() as $setting ) {
			$seen[] = $setting->group . '.' . $setting->key;
		}

		$this->assertSame( array_unique( $seen ), $seen );
	}

	public function testSettingNamesAreUnique(): void {
		$names = array_map(
			static fn ( $setting ) => $setting->settingName,
			ConfigSchema::newDefault()->getSettings()
		);

		$this->assertSame( array_unique( $names ), $names );
	}

	public function testEveryExposedSettingExistsInDefaultSettings(): void {
		$defaults = require __DIR__ . '/../../../DefaultSettings.php';

		foreach ( ConfigSchema::newDefault()->getSettings() as $setting ) {
			$this->assertArrayHasKey(
				$setting->settingName,
				$defaults,
				"Schema setting {$setting->group}.{$setting->key} maps to unknown PHP setting {$setting->settingName}"
			);
		}
	}

	public function testEverySettingTypeProvidesADescription(): void {
		foreach ( ConfigSchema::newDefault()->getSettings() as $setting ) {
			$description = $setting->type->describe();

			$this->assertNotSame( [], $description, "{$setting->group}.{$setting->key} has no type description" );
			$this->assertIsString( $description[0], "{$setting->group}.{$setting->key} description lacks a message key" );
		}
	}

}

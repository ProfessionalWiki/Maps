<?php

declare( strict_types = 1 );

namespace Maps\Tests\Integration\Config;

use Maps\Config\ConfigDocumentationBuilder;
use Maps\Config\ConfigSchema;
use MediaWiki\Context\RequestContext;
use MediaWiki\Title\Title;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\Config\ConfigDocumentationBuilder
 */
class ConfigDocumentationBuilderTest extends TestCase {

	private function newBuilder(): ConfigDocumentationBuilder {
		$context = new RequestContext();
		$context->setLanguage( 'en' );
		$context->setTitle( Title::makeTitle( NS_MEDIAWIKI, 'Maps' ) );

		return new ConfigDocumentationBuilder( ConfigSchema::newDefault(), $context );
	}

	public function testReferenceIncludesEverySchemaSetting(): void {
		$html = $this->newBuilder()->buildReference();

		foreach ( ConfigSchema::newDefault()->getSettings() as $setting ) {
			$this->assertStringContainsString( '>' . $setting->group . '<', $html, "group {$setting->group} missing" );
			$this->assertStringContainsString( '>' . $setting->key . '<', $html, "key {$setting->group}.{$setting->key} missing" );
			$this->assertStringContainsString( '$' . $setting->settingName, $html, "setting {$setting->settingName} missing" );
		}
	}

	public function testReferenceShowsEnumAllowedValuesAsCode(): void {
		// coordinates.notation is an enum of these notations.
		$this->assertStringContainsString(
			'<code>float</code>, <code>dms</code>, <code>dm</code>, <code>dd</code>',
			$this->newBuilder()->buildReference()
		);
	}

	public function testReferenceShowsBooleanValuesAsCode(): void {
		// general.resizableByDefault is a boolean.
		$this->assertStringContainsString(
			'<code>true</code> or <code>false</code>',
			$this->newBuilder()->buildReference()
		);
	}

	public function testReferenceShowsAnIntegerMinimumAsPlainText(): void {
		// general.distanceDecimals has a minimum of 0, which reads as prose rather than a code literal.
		$this->assertStringContainsString( '0 or greater', $this->newBuilder()->buildReference() );
	}

	public function testSettingAndLocalSettingsColumnsAreNotWrappedInCode(): void {
		// general.mapWidth overrides $egMapsMapWidth; both identifiers fill their whole cell, so they
		// render as plain cell text rather than inline code.
		$html = $this->newBuilder()->buildReference();

		$this->assertStringContainsString( '<td>mapWidth</td>', $html );
		$this->assertStringContainsString( '<td>$egMapsMapWidth</td>', $html );
		$this->assertStringNotContainsString( '<code>mapWidth</code>', $html );
		$this->assertStringNotContainsString( '<code>$egMapsMapWidth</code>', $html );
	}

	public function testReferenceCarriesTheAnchorThePointerLinksTo(): void {
		$this->assertStringContainsString(
			'id="' . ConfigDocumentationBuilder::ANCHOR . '"',
			$this->newBuilder()->buildReference()
		);
	}

	public function testPointerLinksToTheReferenceAndTheDocumentation(): void {
		$html = $this->newBuilder()->buildPointer();

		$this->assertStringContainsString( 'href="#' . ConfigDocumentationBuilder::ANCHOR . '"', $html );
		$this->assertStringContainsString( 'maps.extension.wiki/wiki/Configuration', $html );
	}

}

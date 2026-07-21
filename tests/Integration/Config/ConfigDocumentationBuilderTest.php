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

	public function testReferenceShowsEnumAllowedValues(): void {
		// coordinates.notation is an enum of these notations.
		$this->assertStringContainsString( 'float, dms, dm, dd', $this->newBuilder()->buildReference() );
	}

	public function testReferenceShowsAnIntegerMinimum(): void {
		// general.distanceDecimals has a minimum of 0.
		$this->assertStringContainsString( '0 or greater', $this->newBuilder()->buildReference() );
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

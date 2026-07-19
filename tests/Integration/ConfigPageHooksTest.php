<?php

declare( strict_types = 1 );

namespace Maps\Tests\Integration;

use Maps\MapsHooks;
use Maps\Tests\MapsTestFactory;
use MediaWiki\Title\Title;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\MapsHooks::onContentHandlerDefaultModelFor
 * @covers \Maps\MapsHooks::onEditFilter
 */
class ConfigPageHooksTest extends TestCase {

	private bool $originalEnabled;

	protected function setUp(): void {
		parent::setUp();

		$this->originalEnabled = $GLOBALS['egMapsEnableInWikiConfig'] ?? true;
		$this->setWikiConfigEnabled( true );
	}

	protected function tearDown(): void {
		$this->setWikiConfigEnabled( $this->originalEnabled );

		parent::tearDown();
	}

	private function setWikiConfigEnabled( bool $enabled ): void {
		$GLOBALS['egMapsEnableInWikiConfig'] = $enabled;
		MapsTestFactory::newTestInstance();
	}

	private function configTitle(): Title {
		return Title::makeTitle( NS_MEDIAWIKI, 'Maps' );
	}

	private function editorFor( Title $title ): object {
		return new class( $title ) {
			public function __construct( private Title $title ) {
			}

			public function getTitle(): Title {
				return $this->title;
			}
		};
	}

	public function testConfigPageGetsJsonContentModel() {
		$model = CONTENT_MODEL_WIKITEXT;

		MapsHooks::onContentHandlerDefaultModelFor( $this->configTitle(), $model );

		$this->assertSame( CONTENT_MODEL_JSON, $model );
	}

	public function testOtherMediaWikiPageKeepsItsContentModel() {
		$model = CONTENT_MODEL_WIKITEXT;

		MapsHooks::onContentHandlerDefaultModelFor( Title::makeTitle( NS_MEDIAWIKI, 'NotMaps' ), $model );

		$this->assertSame( CONTENT_MODEL_WIKITEXT, $model );
	}

	public function testContentModelIsNotForcedWhenWikiConfigDisabled() {
		$this->setWikiConfigEnabled( false );

		$model = CONTENT_MODEL_WIKITEXT;
		MapsHooks::onContentHandlerDefaultModelFor( $this->configTitle(), $model );

		$this->assertSame( CONTENT_MODEL_WIKITEXT, $model );
	}

	public function testValidConfigIsAccepted() {
		$error = '';

		MapsHooks::onEditFilter( $this->editorFor( $this->configTitle() ), '{"leaflet":{}}', '', $error, '' );

		$this->assertSame( '', $error );
	}

	public function testInvalidConfigIsRejected() {
		$error = '';

		MapsHooks::onEditFilter(
			$this->editorFor( $this->configTitle() ),
			'{"leaflet":{"layerDefinitions":{"Historic":{"url":"ftp://tiles.example"}}}}',
			'',
			$error,
			''
		);

		$this->assertNotSame( '', $error );
	}

	public function testEditsToOtherPagesAreNotValidated() {
		$error = '';

		MapsHooks::onEditFilter(
			$this->editorFor( Title::makeTitle( NS_MAIN, 'Some page' ) ),
			'this is not even json',
			'',
			$error,
			''
		);

		$this->assertSame( '', $error );
	}

	public function testConfigPageIsNotValidatedWhenWikiConfigDisabled() {
		$this->setWikiConfigEnabled( false );

		$error = '';
		MapsHooks::onEditFilter(
			$this->editorFor( $this->configTitle() ),
			'{"leaflet":{"layerDefinitions":{"Historic":{"url":"ftp://tiles.example"}}}}',
			'',
			$error,
			''
		);

		$this->assertSame( '', $error );
	}

}

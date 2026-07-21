<?php

declare( strict_types = 1 );

namespace Maps\Tests\Integration;

use Maps\Config\ConfigExample;
use Maps\MapsHooks;
use Maps\Tests\MapsTestFactory;
use MediaWiki\Context\IContextSource;
use MediaWiki\Context\RequestContext;
use MediaWiki\Request\FauxRequest;
use MediaWiki\Title\Title;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\MapsHooks::onAlternateEdit
 * @covers \Maps\MapsHooks::onEditFormPreloadText
 * @covers \Maps\MapsHooks::onBeforePageDisplay
 */
class ConfigDocumentationHooksTest extends TestCase {

	private const SEED_HTML = '<div id="intro">Default intro</div><table class="mw-json"><tr><td>data</td></tr></table>';

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

	private function englishContext( Title $title ): RequestContext {
		$context = new RequestContext();
		$context->setLanguage( 'en' );
		$context->setTitle( $title );

		return $context;
	}

	private function runAlternateEdit( Title $title ): object {
		$editPage = new class( $title, $this->englishContext( $title ) ) {
			public bool $suppressIntro = false;
			public string $editFormTextTop = '';
			public string $editFormTextBottom = '';

			public function __construct( private Title $title, private IContextSource $context ) {
			}

			public function getTitle(): Title {
				return $this->title;
			}

			public function getContext(): IContextSource {
				return $this->context;
			}
		};

		MapsHooks::onAlternateEdit( $editPage );

		return $editPage;
	}

	public function testConfigPageEditIsFramedWithDocumentation(): void {
		$editPage = $this->runAlternateEdit( $this->configTitle() );

		$this->assertTrue( $editPage->suppressIntro );
		$this->assertStringContainsString( 'maps.extension.wiki', $editPage->editFormTextTop );
		$this->assertStringContainsString( '$egMapsMapWidth', $editPage->editFormTextBottom );
	}

	public function testOtherMediaWikiPageEditIsNotFramed(): void {
		$editPage = $this->runAlternateEdit( Title::makeTitle( NS_MEDIAWIKI, 'NotMaps' ) );

		$this->assertFalse( $editPage->suppressIntro );
		$this->assertSame( '', $editPage->editFormTextTop );
		$this->assertSame( '', $editPage->editFormTextBottom );
	}

	public function testConfigPageEditIsNotFramedWhenWikiConfigDisabled(): void {
		$this->setWikiConfigEnabled( false );

		$editPage = $this->runAlternateEdit( $this->configTitle() );

		$this->assertFalse( $editPage->suppressIntro );
		$this->assertSame( '', $editPage->editFormTextBottom );
	}

	private function preloadFor( Title $title ): string {
		$text = '';
		MapsHooks::onEditFormPreloadText( $text, $title );

		return $text;
	}

	public function testConfigPageIsPreloadedWithTheExample(): void {
		$this->assertSame( ConfigExample::JSON, $this->preloadFor( $this->configTitle() ) );
	}

	public function testOtherMediaWikiPageIsNotPreloaded(): void {
		$this->assertSame( '', $this->preloadFor( Title::makeTitle( NS_MEDIAWIKI, 'NotMaps' ) ) );
	}

	public function testConfigPageIsNotPreloadedWhenWikiConfigDisabled(): void {
		$this->setWikiConfigEnabled( false );

		$this->assertSame( '', $this->preloadFor( $this->configTitle() ) );
	}

	private function renderView( Title $title, string $action ): string {
		$context = $this->englishContext( $title );
		$context->setRequest( new FauxRequest( [ 'action' => $action ] ) );

		$out = $context->getOutput();
		$out->addHTML( self::SEED_HTML );

		MapsHooks::onBeforePageDisplay( $out, $context->getSkin() );

		return $out->getHTML();
	}

	public function testConfigPageViewIsTrimmedToTheJsonTableAndFramed(): void {
		$html = $this->renderView( $this->configTitle(), 'view' );

		$this->assertStringNotContainsString( 'Default intro', $html );
		$this->assertStringContainsString( '<table class="mw-json">', $html );
		$this->assertStringContainsString( 'maps.extension.wiki', $html );
		$this->assertStringContainsString( '$egMapsMapWidth', $html );
	}

	public function testConfigPageIsUntouchedForNonViewActions(): void {
		$html = $this->renderView( $this->configTitle(), 'history' );

		$this->assertStringContainsString( 'Default intro', $html );
		$this->assertStringNotContainsString( '$egMapsMapWidth', $html );
	}

	public function testOtherMediaWikiPageViewIsUntouched(): void {
		$html = $this->renderView( Title::makeTitle( NS_MEDIAWIKI, 'NotMaps' ), 'view' );

		$this->assertStringContainsString( 'Default intro', $html );
		$this->assertStringNotContainsString( '$egMapsMapWidth', $html );
	}

	public function testConfigPageViewIsUntouchedWhenWikiConfigDisabled(): void {
		$this->setWikiConfigEnabled( false );

		$html = $this->renderView( $this->configTitle(), 'view' );

		$this->assertStringContainsString( 'Default intro', $html );
		$this->assertStringNotContainsString( '$egMapsMapWidth', $html );
	}

}

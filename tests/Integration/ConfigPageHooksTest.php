<?php

declare( strict_types = 1 );

namespace Maps\Tests\Integration;

use Maps\MapsHooks;
use Maps\Tests\MapsTestFactory;
use Maps\Tests\Util\PageCreator;
use MediaWiki\Content\JsonContent;
use MediaWiki\Content\ValidationParams;
use MediaWiki\Context\RequestContext;
use MediaWiki\MediaWikiServices;
use MediaWiki\Parser\Sanitizer;
use MediaWiki\Title\Title;
use MediaWikiTestCaseTrait;
use PHPUnit\Framework\TestCase;
use StatusValue;

/**
 * Needs the database to save MediaWiki:Maps, a save that the code under test refuses.
 *
 * @covers \Maps\MapsHooks::onContentHandlerDefaultModelFor
 * @covers \Maps\MapsHooks::onJsonValidateSave
 */
class ConfigPageHooksTest extends TestCase {

	use MediaWikiTestCaseTrait;

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

	public function testSavingInvalidConfigFailsWithEachError() {
		$status = $this->saveConfigPage( '{"leaflets":{},"googlemaps":{}}' );

		$this->assertStatusNotOK( $status );
		$this->assertStatusMessagesExactly(
			StatusValue::newFatal( 'maps-config-error-unknown-key', 'leaflets' )
				->fatal( 'maps-config-error-unknown-key', 'googlemaps' ),
			$status
		);
	}

	/**
	 * Saves the way maintenance scripts and extensions do, without the edit form.
	 */
	private function saveConfigPage( string $json ): StatusValue {
		return PageCreator::instance()->createPageWithContent( 'MediaWiki:Maps', new JsonContent( $json ) );
	}

	public function testValidConfigIsAccepted() {
		$this->assertStatusGood( $this->validateSave( $this->configTitle(), '{"leaflet":{}}' ) );
	}

	/**
	 * Runs the checks that every save of JSON content runs, without saving.
	 */
	private function validateSave( Title $title, string $json ): StatusValue {
		$content = new JsonContent( $json );

		return $content->getContentHandler()->validateSave( $content, new ValidationParams( $title, 0 ) );
	}

	/**
	 * MediaWiki 1.43 to 1.46 show the errors on the edit form as this HTML.
	 */
	public function testErrorsShowConfigKeysAsText() {
		$status = $this->validateSave( $this->configTitle(), '{"<!--":{},"later":{}}' );

		$html = MediaWikiServices::getInstance()->getFormatterFactory()
			->getStatusFormatter( RequestContext::getMain() )
			->getHTML( $status, [ 'lang' => 'qqx' ] );

		$text = Sanitizer::stripAllTags( $html );

		$this->assertStringContainsString( '(maps-config-error-unknown-key: <!--)', $text );
		$this->assertStringContainsString( '(maps-config-error-unknown-key: later)', $text );
	}

	public function testOtherJsonPagesAreNotValidated() {
		$this->assertStatusGood( $this->validateSave(
			Title::makeTitle( NS_GEO_JSON, 'Some area' ),
			'{"type":"FeatureCollection","features":[]}'
		) );
	}

	public function testOtherJsonPagesInTheMediaWikiNamespaceAreNotValidated() {
		$this->assertStatusGood( $this->validateSave( Title::makeTitle( NS_MEDIAWIKI, 'Maps.json' ), '{"leaflets":{}}' ) );
	}

	public function testConfigPageIsNotValidatedWhenWikiConfigDisabled() {
		$this->setWikiConfigEnabled( false );

		$this->assertStatusGood( $this->validateSave( $this->configTitle(), '{"leaflets":{}}' ) );
	}

}

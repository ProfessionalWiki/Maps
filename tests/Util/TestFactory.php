<?php

declare( strict_types = 1 );

namespace Maps\Tests\Util;

use MediaWiki\Content\WikitextContent;
use MediaWiki\MediaWikiServices;
use MediaWiki\Parser\ParserOptions;
use MediaWiki\Parser\ParserOutput;
use MediaWiki\Title\Title;
use User;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class TestFactory {

	public const PAGE_NAME = 'Maps test page';

	public static function newInstance(): self {
		return new self();
	}

	public function getPageCreator(): PageCreator {
		return new PageCreator();
	}

	public function parse( string $textToParse ): string {
		return MediaWikiServices::getInstance()->getParser()
			->parse(
				$textToParse,
				Title::newMainPage(),
				new \ParserOptions( User::newSystemUser( 'TestUser' ) )
			)
			->getContentHolderText();
	}

	public function renderPage( string $wikitext, ParserOptions $options ): ParserOutput {
		return MediaWikiServices::getInstance()->getContentRenderer()->getParserOutput(
			new WikitextContent( $wikitext ),
			Title::newFromText( self::PAGE_NAME ),
			null,
			$options
		);
	}

}

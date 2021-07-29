<?php

declare( strict_types = 1 );

namespace Maps\Tests\Util;

use MediaWiki\MediaWikiServices;
use User;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class TestFactory {

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
				\Title::newMainPage(),
				new \ParserOptions( User::newSystemUser( 'TestUser' ) )
			)->getText();
	}

}

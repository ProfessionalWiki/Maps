<?php

declare( strict_types = 1 );

namespace Maps\Tests\Util;

use LogicException;
use MediaWiki\MediaWikiServices;
use MediaWiki\Title\Title;
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
		$parserOutput = MediaWikiServices::getInstance()->getParser()
			->parse(
				$textToParse,
				Title::newMainPage(),
				new \ParserOptions( User::newSystemUser( 'TestUser' ) )
			);

		if ( method_exists( $parserOutput, 'getContentHolderText' ) ) {
			try {
				return $parserOutput->getContentHolderText();
			} catch ( LogicException $e ) {
				// Handle case where there is no body content
				return '';
			}
		} elseif ( method_exists( $parserOutput, 'getText' ) ) {
			return $parserOutput->getText();
		}

		return '';
	}

}

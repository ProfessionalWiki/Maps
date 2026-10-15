<?php

declare( strict_types = 1 );

namespace Maps\Tests\Util;

use MediaWiki\Linker\LinkTarget;
use MediaWiki\Parser\Parser;
use MediaWiki\Parser\ParserOptions;
use MediaWiki\Title\Title;

/**
 * Stands in for extensions that add to the output of a render, such as head items, config vars or extension data,
 * when it includes a page.
 */
class OutputAddingParserOptions {

	/**
	 * @param callable $addToOutput Called with the output of the parse and the name of the page it includes
	 */
	public static function addingOnInclude( callable $addToOutput ): ParserOptions {
		$options = ParserOptions::newFromAnon();
		$fetchTemplate = $options->getTemplateCallback();

		$options->setTemplateCallback(
			static function ( LinkTarget $page, $parser = false ) use ( $addToOutput, $fetchTemplate ): array {
				if ( $parser instanceof Parser ) {
					$addToOutput( $parser->getOutput(), Title::newFromLinkTarget( $page )->getPrefixedText() );
				}

				return $fetchTemplate( $page, $parser );
			}
		);

		return $options;
	}

}

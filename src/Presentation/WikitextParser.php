<?php

declare( strict_types = 1 );

namespace Maps\Presentation;

use LogicException;
use MediaWiki\Parser\Parser;
use ParserOptions;

class WikitextParser {

	private Parser $parser;

	public function __construct( Parser $parser ) {
		$this->parser = $parser;
	}

	public function wikitextToHtml( string $text ): string {
		if ( trim( $text ) === '' ) {
			return '';
		}

		$parserOutput = $this->parser->parse(
			$text,
			$this->parser->getTitle(),
			new ParserOptions( $this->parser->getUserIdentity() )
		);

		if ( method_exists( $parserOutput, 'getContentHolderText' ) ) {
			try {
				return $parserOutput->getContentHolderText();
			} catch ( LogicException $e ) {
				// Handle case where there is no body content
				return '';
			}
		} elseif ( method_exists( $parserOutput, 'getRawText' ) ) {
			return $parserOutput->getRawText();
		} elseif ( method_exists( $parserOutput, 'getText' ) ) {
			return $parserOutput->getText();
		}

		return '';
	}

}

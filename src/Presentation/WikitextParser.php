<?php

declare( strict_types = 1 );

namespace Maps\Presentation;

use Parser;
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

		return $this->parser->parse(
			$text,
			$this->parser->getTitle(),
			new ParserOptions( method_exists( $this->parser, 'getUserIdentity' ) ? $this->parser->getUserIdentity() : $this->parser->getUser() )
		)->getText();
	}

}

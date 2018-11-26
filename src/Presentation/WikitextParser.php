<?php

declare( strict_types = 1 );

namespace Maps\Presentation;

use Parser;
use ParserOptions;

class WikitextParser {

	private $parser;

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
			new ParserOptions()
		)->getText();
	}

}
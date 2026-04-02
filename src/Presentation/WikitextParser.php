<?php

declare( strict_types = 1 );

namespace Maps\Presentation;

use LogicException;
use MediaWiki\Context\RequestContext;
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

		$options = $this->parser->getOptions();
		if ( $options === null ) {
			$options = new ParserOptions( RequestContext::getMain()->getUser() );
		}

		$parserOutput = $this->parser->parse(
			$text,
			$this->parser->getTitle(),
			$options
		);

		try {
			return $parserOutput->getContentHolderText();
		} catch ( LogicException $e ) {
			return '';
		}
	}

}

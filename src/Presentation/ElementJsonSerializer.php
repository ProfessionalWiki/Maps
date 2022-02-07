<?php

declare( strict_types = 1 );

namespace Maps\Presentation;

use Maps\LegacyModel\BaseElement;

class ElementJsonSerializer {

	private WikitextParser $parser;

	public function __construct( WikitextParser $parser ) {
		$this->parser = $parser;
	}

	public function elementToJson( BaseElement $element ): array {
		$json = $element->getArrayValue();

		$this->titleAndText( $json );

		return $json;
	}

	public function titleAndText( array &$elementJson ): void {
		$elementJson['title'] = $this->parser->wikitextToHtml( $elementJson['title'] );
		$elementJson['text'] = $this->parser->wikitextToHtml( $elementJson['text'] );

		$hasTitleAndText = $elementJson['title'] !== '' && $elementJson['text'] !== '';
		$elementJson['text'] = ( $hasTitleAndText ? '<b>' . $elementJson['title'] . '</b>' : $elementJson['title'] ) . $elementJson['text'];
		$elementJson['title'] = strip_tags( $elementJson['title'] );
	}

}

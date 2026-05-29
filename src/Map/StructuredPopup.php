<?php

declare( strict_types = 1 );

namespace Maps\Map;

use Maps\Presentation\HtmlSanitizer;

class StructuredPopup {

	private string $titleHtml;
	private array $propertyValues;
	private HtmlSanitizer $sanitizer;

	/**
	 * @param string[] $propertyValues
	 */
	public function __construct( string $titleHtml, array $propertyValues ) {
		$this->titleHtml = $titleHtml;
		$this->propertyValues = $propertyValues;
		$this->sanitizer = new HtmlSanitizer();
	}

	public function getHtml(): string {
		$title = $this->sanitizer->sanitize( $this->titleHtml );
		$valueList = $this->getPropertyValueList();
		$separator = $title === '' || $valueList === '' ? '' : '<br>';

		return '<h3 style="padding-top: 0">' . $title . '</h3>' . $separator . $valueList;
	}

	private function getPropertyValueList(): string {
		$lines = [];

		foreach ( $this->propertyValues as $name => $value ) {
			$lines[] = $this->bold( $this->sanitizer->sanitize( (string)$name ) ) . ': ' . $this->sanitizer->sanitize( $value );
		}

		return implode( '<br>', $lines );
	}

	private function bold( string $html ): string {
		return '<strong>' . $html . '</strong>';
	}

}

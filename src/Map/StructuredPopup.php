<?php

declare( strict_types = 1 );

namespace Maps\Map;

use DOMDocument;
use DOMElement;

class StructuredPopup {

	private const SAFE_URL_SCHEMES = [ 'http', 'https', 'ftp', 'ftps', 'mailto', 'tel' ];

	private string $titleHtml;
	private array $propertyValues;

	/**
	 * @param string[] $propertyValues
	 */
	public function __construct( string $titleHtml, array $propertyValues ) {
		$this->titleHtml = $titleHtml;
		$this->propertyValues = $propertyValues;
	}

	public function getHtml(): string {
		$title = $this->sanitize( $this->titleHtml );
		$valueList = $this->getPropertyValueList();
		$separator = $title === '' || $valueList === '' ? '' : '<br>';

		return '<h3 style="padding-top: 0">' . $title . '</h3>' . $separator . $valueList;
	}

	private function getPropertyValueList(): string {
		$lines = [];

		foreach ( $this->propertyValues as $name => $value ) {
			$lines[] = $this->bold( $this->sanitize( (string)$name ) ) . ': ' . $this->sanitize( $value );
		}

		return implode( '<br>', $lines );
	}

	private function bold( string $html ): string {
		return '<strong>' . $html . '</strong>';
	}

	/**
	 * Keeps only <a> and <img> tags and strips event-handler attributes and unsafe URL schemes
	 * from them. Relative and http(s) URLs are preserved so links to wiki pages and images that
	 * the popup is meant to show keep working.
	 */
	private function sanitize( string $html ): string {
		$html = strip_tags( $html, '<a><img>' );

		if ( strpos( $html, '<' ) === false ) {
			return $html;
		}

		return $this->stripDangerousAttributes( $html );
	}

	private function stripDangerousAttributes( string $html ): string {
		$document = new DOMDocument();

		$previousErrorHandling = libxml_use_internal_errors( true );
		$loaded = $document->loadHTML(
			'<?xml encoding="UTF-8"?><div>' . $html . '</div>',
			LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
		);
		libxml_clear_errors();
		libxml_use_internal_errors( $previousErrorHandling );

		if ( !$loaded || $document->documentElement === null ) {
			return strip_tags( $html );
		}

		foreach ( $document->getElementsByTagName( '*' ) as $element ) {
			$this->stripDangerousAttributesFromElement( $element );
		}

		return $this->innerHtml( $document->documentElement );
	}

	private function stripDangerousAttributesFromElement( DOMElement $element ): void {
		foreach ( iterator_to_array( $element->attributes ) as $attribute ) {
			$name = strtolower( $attribute->name );

			if ( strpos( $name, 'on' ) === 0 ) {
				$element->removeAttribute( $attribute->name );
			} elseif ( ( $name === 'href' || $name === 'src' ) && $this->hasUnsafeScheme( $attribute->value ) ) {
				$element->removeAttribute( $attribute->name );
			}
		}
	}

	private function hasUnsafeScheme( string $url ): bool {
		$normalized = strtolower( (string)preg_replace( '/[\x00-\x20]+/', '', $url ) );

		if ( preg_match( '/^[a-z][a-z0-9+.\-]*:/', $normalized ) !== 1 ) {
			return false;
		}

		return !in_array( strstr( $normalized, ':', true ), self::SAFE_URL_SCHEMES, true );
	}

	private function innerHtml( DOMElement $element ): string {
		$html = '';

		foreach ( $element->childNodes as $child ) {
			$html .= $element->ownerDocument->saveHTML( $child );
		}

		return $html;
	}

}

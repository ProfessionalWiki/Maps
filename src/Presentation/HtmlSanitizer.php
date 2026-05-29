<?php

declare( strict_types = 1 );

namespace Maps\Presentation;

use DOMDocument;
use DOMElement;

/**
 * Restricts HTML to a safe subset: only <a> and <img> tags are kept, with their event-handler
 * attributes and unsafe URL schemes removed. Relative and http(s) URLs are preserved so that
 * links to wiki pages and images keep working.
 */
class HtmlSanitizer {

	private const SAFE_URL_SCHEMES = [ 'http', 'https', 'ftp', 'ftps', 'mailto', 'tel' ];

	public function sanitize( string $html ): string {
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

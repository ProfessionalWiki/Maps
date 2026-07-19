<?php

declare( strict_types = 1 );

namespace Maps;

use DOMDocument;
use DOMElement;

/**
 * Sanitizes a Leaflet layer attribution string so that it is safe to render as HTML.
 *
 * Leaflet inserts the attribution into the map via innerHTML, so untrusted markup here is a
 * stored-XSS vector (see advisory GHSA-4h7g-5542-v3fc). The allowlist is deliberately narrow:
 * plain text plus <a> links with an http(s) href and an optional title. Everything else, including
 * <img>, event handlers and javascript:/data:/vbscript: hrefs, is removed. The text content of
 * disallowed tags is kept as inert text.
 *
 * @licence GNU GPL v2+
 */
class AttributionSanitizer {

	public function sanitize( string $attribution ): string {
		if ( strpos( $attribution, '<' ) === false ) {
			return $attribution;
		}

		$onlyAnchors = strip_tags( $attribution, '<a>' );

		if ( strpos( $onlyAnchors, '<' ) === false ) {
			return $onlyAnchors;
		}

		return $this->cleanAnchors( $onlyAnchors );
	}

	private function cleanAnchors( string $html ): string {
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

		foreach ( $document->getElementsByTagName( 'a' ) as $anchor ) {
			$this->cleanAnchor( $anchor );
		}

		return $this->innerHtml( $document->documentElement );
	}

	private function cleanAnchor( DOMElement $anchor ): void {
		$href = $anchor->getAttribute( 'href' );
		$title = $anchor->getAttribute( 'title' );

		foreach ( iterator_to_array( $anchor->attributes ) as $attribute ) {
			$anchor->removeAttribute( $attribute->name );
		}

		if ( $this->isAllowedHref( $href ) ) {
			$anchor->setAttribute( 'href', $href );
		}

		if ( $title !== '' ) {
			$anchor->setAttribute( 'title', $title );
		}
	}

	private function isAllowedHref( string $href ): bool {
		$normalized = strtolower( (string)preg_replace( '/[\x00-\x20]+/', '', $href ) );

		return preg_match( '#^https?://#', $normalized ) === 1;
	}

	private function innerHtml( DOMElement $element ): string {
		$html = '';

		foreach ( $element->childNodes as $child ) {
			$html .= $element->ownerDocument->saveHTML( $child );
		}

		return $html;
	}

}

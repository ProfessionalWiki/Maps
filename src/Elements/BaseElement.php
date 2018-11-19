<?php

namespace Maps\Elements;

/**
 * @since 3.0
 *
 * @licence GNU GPL v2+
 * @author Kim Eik < kim@heldig.org >
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
abstract class BaseElement {

	private $title;
	private $text;
	private $link;

	public function setTitle( string $title ) {
		$this->title = trim( $title );
	}

	public function setText( string $text ) {
		$this->text = trim( $text );
	}

	public function setLink( string $link ) {
		$this->link = $link;
	}

	public function getArrayValue() {
		return $this->getJSONObject();
	}

	/**
	 * @deprecated
	 */
	public function getJSONObject( string $defText = '', string $defTitle = '' ): array {
		return [
			'text' => $this->text ?? $defText,
			'title' => $this->title ?? $defTitle,
			'link' => $this->link ?? '',
		];
	}

	public function getText(): string {
		return $this->text ?? '';
	}

	public function getTitle(): string {
		return $this->title ?? '';
	}

	public function getLink(): string {
		return $this->link ?? '';
	}

}

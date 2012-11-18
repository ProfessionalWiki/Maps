<?php

namespace Maps;

/**
 * @since 2.0
 */
abstract class BaseElement implements Element, \iBubbleMapElement, \iLinkableMapElement {

	protected $title;
	protected $text;
	protected $link;

	/**
	 * @since 3.0
	 * @var ElementOptions
	 */
	protected $options;

	public function getTitle() {
		return $this->title;
	}

	public function setTitle( $title ) {
		$this->title = trim($title);
	}

	public function getText() {
		return $this->text;
	}

	public function setText( $text ) {
		$this->text = trim($text);
	}

	public function getLink() {
		return $this->link;
	}

	public function setLink( $link ) {
		$this->link = trim($link);
	}

	public function hasTitle() {
		return !is_null($this->title) && $this->title !== '';
	}

	public function hasText() {
		return !is_null($this->text) && $this->text !== '';
	}

	/**
	 * @deprecated
	 * @param string $defText
	 * @param string $defTitle
	 * @return array
	 */
	public function getJSONObject( $defText = '' , $defTitle = '' ) {
		return array(
			'text' => $this->hasText() ? $this->getText() : $defText ,
			'title' => $this->hasTitle() ? $this->getTitle() : $defTitle ,
			'link' => $this->getLink() ,
		);
	}

	/**
	 * @see Element::getArrayValue
	 *
	 * @since 3.0
	 *
	 * @return mixed
	 */
	public function getArrayValue() {
		return $this->getJSONObject();
	}

	/**
	 * @see Element::getOptions
	 *
	 * @since 3.0
	 *
	 * @return ElementOptions
	 */
	public function getOptions() {
		return $this->options;
	}

	/**
	 * Sets the elements options.
	 *
	 * @since 3.0
	 *
	 * @param ElementOptions $options
	 */
	public function setOptions( ElementOptions $options ) {
		$this->options = $options;
	}

}

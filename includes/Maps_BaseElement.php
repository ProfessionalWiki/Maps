<?php
/**
 * @since 1.1
 */
abstract class MapsBaseElement implements iBubbleMapElement , iLinkableMapElement {

	protected $title;
	protected $text;
	protected $link;

	public function getTitle() {
		return $this->title;
	}

	public function setTitle( $title ) {
		$this->title = $title;
	}

	public function getText() {
		return $this->text;
	}

	public function setText( $text ) {
		$this->text = $text;
	}

	public function getLink() {
		return $this->link;
	}

	public function setLink( $link ) {
		$this->link = $link;
	}

	public function hasTitle() {
		return $this->title !== '';
	}

	public function hasText() {
		return $this->text !== '';
	}

	public function getJSONObject( $defText = '' , $defTitle = '' ) {
		return array(
			'text' => $this->hasText() ? $this->getText() : $defText ,
			'title' => $this->hasTitle() ? $this->getTitle() : $defTitle ,
			'link' => $this->getLink() ,
		);
	}
}

<?php

namespace Maps;

/**
 * Base class for objects implementing the @see Element interface.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @since 3.0
 *
 * @ingroup Maps
 *
 * @licence GNU GPL v2+
 * @author Kim Eik < kim@heldig.org >
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
abstract class BaseElement implements Element, \iBubbleMapElement, \iLinkableMapElement {

	/**
	 * @since 3.0
	 * @var ElementOptions
	 */
	protected $options;

	/**
	 * Constructor.
	 *
	 * @since 3.0
	 */
	public function __construct() {
		$this->options = new ElementOptions();
	}

	/**
	 * @since 3.0
	 * 
	 * @return string
	 */
	public function getTitle() {
		return $this->options->getOption( 'title' );
	}

	/**
	 * @since 3.0
	 *
	 * @param string $title
	 */
	public function setTitle( $title ) {
		$this->options->setOption( 'title', $title );
	}

	/**
	 * @since 3.0
	 *
	 * @return string
	 */
	public function getText() {
		return $this->options->getOption( 'text' );
	}

	/**
	 * @since 3.0
	 *
	 * @param string $text
	 */
	public function setText( $text ) {
		$this->options->setOption( 'text', $text );
	}

	/**
	 * @since 3.0
	 *
	 * @return string
	 */
	public function getLink() {
		return $this->options->getOption( 'link' );
	}

	/**
	 * @since 3.0
	 *
	 * @param string $link
	 */
	public function setLink( $link ) {
		$this->options->setOption( 'link', $link );
	}

	/**
	 * @deprecated
	 * @param string $defText
	 * @param string $defTitle
	 * @return array
	 */
	public function getJSONObject( $defText = '' , $defTitle = '' ) {
		$array = array();

		$array['text'] = $this->options->hasOption( 'text' ) ? $this->getText() : $defText;
		$array['title'] = $this->options->hasOption( 'title' ) ? $this->getTitle() : $defTitle;
		$array['link'] = $this->options->hasOption( 'link' ) ? $this->getLink() : '';

		return $array;
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

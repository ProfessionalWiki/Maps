<?php

namespace Maps;
use MWException;

/**
 * Interface for elements that can be places upon a map.
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
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface Element {

	/**
	 * Returns the value in array form.
	 *
	 * @since 3.0
	 *
	 * @return mixed
	 */
	public function getArrayValue();

	/**
	 * Returns the elements options.
	 * Modification of the elements options by mutating the obtained object is allowed.
	 *
	 * @since 3.0
	 *
	 * @return ElementOptions
	 */
	public function getOptions();

	/**
	 * Sets the elements options.
	 *
	 * @since 3.0
	 *
	 * @param ElementOptions $options
	 */
	public function setOptions( ElementOptions $options );

}

class OptionsObject {

	/**
	 * @since 3.0
	 *
	 * @var array
	 */
	protected $options;

	/**
	 * @since 3.0
	 *
	 * @param string $name
	 * @param mixed $value
	 *
	 * @throws MWException
	 */
	public function setOption( $name, $value ) {
		if ( !is_string( $name ) ) {
			throw new MWException( 'Option name should be a string' );
		}

		$this->options[$name] = $value;
	}

	/**
	 * @since 3.0
	 *
	 * @param string $name
	 *
	 * @throws MWException
	 */
	public function getOption( $name ) {
		if ( !is_string( $name ) ) {
			throw new MWException( 'Option name should be a string' );
		}

		if ( !array_key_exists( $name, $this->options ) ) {
			throw new MWException( 'Tried to obtain option "' . $name . '" while it has not been set' );
		}

		return $this->options[$name];
	}

}

class ElementOptions extends OptionsObject {



}
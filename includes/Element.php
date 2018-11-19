<?php

namespace Maps;

class ElementOptions {

	private $options = [];

	/**
	 * @since 3.0
	 *
	 * @param string $name
	 * @param mixed $value
	 *
	 * @throws \InvalidArgumentException
	 */
	public function setOption( $name, $value ) {
		if ( !is_string( $name ) ) {
			throw new \InvalidArgumentException( 'Option name should be a string' );
		}

		$this->options[$name] = $value;
	}

	/**
	 * @since 3.0
	 *
	 * @param string $name
	 *
	 * @throws \InvalidArgumentException
	 * @throws \OutOfBoundsException
	 */
	public function getOption( $name ) {
		if ( !is_string( $name ) ) {
			throw new \InvalidArgumentException( 'Option name should be a string' );
		}

		if ( !array_key_exists( $name, $this->options ) ) {
			throw new \OutOfBoundsException( 'Tried to obtain option "' . $name . '" while it has not been set' );
		}

		return $this->options[$name];
	}

	/**
	 * @since 3.0
	 *
	 * @param string $name
	 *
	 * @return boolean
	 * @throws \InvalidArgumentException
	 */
	public function hasOption( $name ) {
		if ( !is_string( $name ) ) {
			throw new \InvalidArgumentException( 'Option name should be a string' );
		}

		return array_key_exists( $name, $this->options );
	}

}
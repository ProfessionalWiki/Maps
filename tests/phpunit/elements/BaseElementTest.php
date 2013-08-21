<?php

namespace Maps\Tests\Elements;

use Maps\Element;

/**
 * Base class for unit tests classes for the Maps\BaseElement deriving objects.
 *
 * @since 3.0
 *
 * @ingroup MapsTest
 *
 * @group Maps
 * @group MapsElement
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
abstract class BaseElementTest extends \MediaWikiTestCase {

	/**
	 * Returns the name of the concrete class tested by this test.
	 *
	 * @since 3.0
	 *
	 * @return string
	 */
	public abstract function getClass();

	/**
	 * First element can be a boolean indication if the successive values are valid,
	 * or a string indicating the type of exception that should be thrown (ie not valid either).
	 *
	 * @since 3.0
	 *
	 * @return array
	 */
	public abstract function constructorProvider();

	/**
	 * Creates and returns a new instance of the concrete class.
	 *
	 * @since 3.0
	 *
	 * @return mixed
	 */
	public function newInstance() {
		$reflector = new \ReflectionClass( $this->getClass() );
		$args = func_get_args();
		$instance = $reflector->newInstanceArgs( $args );
		return $instance;
	}

	/**
	 * @since 3.0
	 *
	 * @return array [instance, constructor args]
	 */
	public function instanceProvider() {
		$phpFails = array( $this, 'newInstance' );

		return array_filter( array_map(
			function( array $args ) use ( $phpFails ) {
				$isValid = array_shift( $args ) === true;

				if ( $isValid ) {
					return array( call_user_func_array( $phpFails, $args ), $args );
				}
				else {
					return false;
				}
			},
			$this->constructorProvider()
		), 'is_array' );
	}

	/**
	 * @dataProvider constructorProvider
	 *
	 * @since 3.0
	 */
	public function testConstructor() {
		$args = func_get_args();

		$valid = array_shift( $args );
		$pokemons = null;

		try {
			$instance = call_user_func_array( array( $this, 'newInstance' ), $args );
			$this->assertInstanceOf( $this->getClass(), $instance );
		}
		catch ( \Exception $pokemons ) {
			if ( $valid === true ) {
				throw $pokemons;
			}

			if ( is_string( $valid ) ) {
				$this->assertEquals( $valid, get_class( $pokemons ) );
			}
			else {
				$this->assertFalse( $valid );
			}
		}
	}

	/**
	 * @dataProvider instanceProvider
	 * @param Element $element
	 */
	public function testGetOptions( Element $element ) {
		$this->assertInstanceOf( '\Maps\ElementOptions', $element->getOptions() );
	}

}
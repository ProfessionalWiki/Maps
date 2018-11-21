<?php

namespace Maps\Tests\Unit\Elements;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PHPUnit4And6Compat;

/**
 * Base class for unit tests classes for the Maps\BaseElement deriving objects.
 *
 * @since 3.0
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
abstract class BaseElementTest extends TestCase {
	use PHPUnit4And6Compat;

	public function invalidConstructorProvider() {
		return [];
	}

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
	 * Returns the name of the concrete class tested by this test.
	 *
	 * @since 3.0
	 *
	 * @return string
	 */
	public abstract function getClass();

	/**
	 * @since 3.0
	 *
	 * @return array [instance, constructor args]
	 */
	public function instanceProvider() {
		$phpFails = [ $this, 'newInstance' ];

		return array_map(
			function ( array $args ) use ( $phpFails ) {
				return [ call_user_func_array( $phpFails, $args ), $args ];
			},
			$this->validConstructorProvider()
		);
	}

	public abstract function validConstructorProvider();

	/**
	 * @dataProvider validConstructorProvider
	 *
	 * @since 3.0
	 */
	public function testGivenValidArguments_constructorDoesNotThrowException() {
		$instance = call_user_func_array( [ $this, 'newInstance' ], func_get_args() );
		$this->assertInstanceOf( $this->getClass(), $instance );
	}

	/**
	 * @dataProvider invalidConstructorProvider
	 *
	 * @since 3.0
	 */
	public function testGivenInvalidArguments_constructorThrowsException() {
		$this->expectException( InvalidArgumentException::class );
		call_user_func_array( [ $this, 'newInstance' ], func_get_args() );
	}

}

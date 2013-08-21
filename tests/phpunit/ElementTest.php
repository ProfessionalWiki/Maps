<?php

/**
 * Unit tests for the Maps\Element implementing classes.
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
use DataValues\LatLongValue;
use Maps\Element;

class ElementTest extends \MediaWikiTestCase {

	public function elementProvider() {
		$elements = array();

		$elements[] = new \Maps\Rectangle( new LatLongValue( 4, 2 ), new LatLongValue( 5, 6 ) );
		$elements[] = new \Maps\ImageOverlay( new LatLongValue( 4, 2 ), new LatLongValue( 5, 6 ), 'foo' );
		$elements[] = new \Maps\Circle( new LatLongValue( 4, 2 ), 42 );
		$elements[] = new \Maps\Line( array( new LatLongValue( 4, 2 ), new LatLongValue( 5, 6 ) ) );
		//$elements[] = new \Maps\Polygon( array( new LatLongValue( 4, 2 ), new LatLongValue( 5, 6 ) ) );
		// TODO: location

		return $this->arrayWrap( $elements );
	}

	/**
	 * @dataProvider elementProvider
	 * @param Element $element
	 */
	public function getArrayValue( Element $element ) {
		$this->assertEquals( $element->getArrayValue(), $element->getArrayValue() );
	}

	/**
	 * @dataProvider elementProvider
	 * @param Element $element
	 */
	public function testSetOptions( Element $element ) {
		$options = new \Maps\ElementOptions();
		$options->setOption( 'foo', 'bar' );
		$options->setOption( '~=[,,_,,]:3', 42 );

		$element->setOptions( $options );

		$this->assertEquals( $element->getOptions()->getOption( 'foo' ), 'bar' );
		$this->assertEquals( $element->getOptions()->getOption( '~=[,,_,,]:3' ), 42 );

		$options = clone $options;
		$options->setOption( 'foo', 'baz' );

		$element->setOptions( $options );

		$this->assertEquals( $element->getOptions()->getOption( 'foo' ), 'baz' );
	}

	/**
	 * @dataProvider elementProvider
	 * @param Element $element
	 */
	public function testGetOptions( Element $element ) {
		$this->assertInstanceOf( '\Maps\ElementOptions', $element->getOptions() );
	}

}

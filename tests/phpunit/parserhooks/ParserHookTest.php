<?php

namespace Maps\Test;

/**
 * Tests for the MapsCoordinates class.
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
 * @file
 * @since 2.0
 *
 * @ingroup Maps
 * @ingroup Test
 *
 * @group Maps
 * @group ParserHook
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
abstract class ParserHookTest extends \MediaWikiTestCase {

	/**
	 * @since 2.0
	 * @return \ParserHook
	 */
	protected abstract function getInstance();

	/**
	 * @since 2.0
	 * @return array
	 */
	public abstract function parametersProvider();

	/**
	 * Triggers the render process with different sets of parameters to see if
	 * no errors or notices are thrown and the result indeed is a string.
	 *
	 * @dataProvider parametersProvider
	 * @since 2.0
	 * @param array $parameters
	 * @param string|null $expected
	 */
	public function testRender( array $parameters, $expected = null ) {
		$parserHook = $this->getInstance();

		$parser = new \Parser();
		$parser->mOptions = new \ParserOptions();
		$parser->clearState();
		$parser->setTitle( \Title::newMainPage() );

		$renderResult = $parserHook->renderTag( null, $parameters, $parser );
		$this->assertInternalType( 'string', $renderResult );

		if ( $expected !== null ) {
			$this->assertEquals( $expected, $renderResult );
		}
	}

	public function processingProvider() {
		return array();
	}

	/**
	 * @dataProvider processingProvider
	 * @since 3.0
	 */
	public function testParamProcessing( array $parameters, array $expectedValues ) {
		$definitions = $this->getInstance()->getParamDefinitions();

		$processor = \ParamProcessor\Processor::newDefault();
		$processor->setParameters( $parameters, $definitions );

		$processor->validateParameters();

		if ( $processor->hasFatalError() ) {
			throw new \MWException( 'Fatal error occurred during the param processing: ' . $processor->hasFatalError()->getMessage() );
		}

		$actual = $processor->getParameterValues();

		$expectedValues = array_merge( $this->getDefaultValues(), $expectedValues );

		foreach ( $expectedValues as $name => $expected ) {
			$this->assertArrayHasKey( $name, $actual );

			$this->assertEquals(
				$expected,
				$actual[$name],
				'Expected ' . var_export( $expected, true ) . ' should match actual ' . var_export( $actual[$name], true )
			);
		}
	}

	/**
	 * Returns an array with the default values of the parameters.
	 *
	 * @since 3.0
	 *
	 * @return array
	 */
	protected function getDefaultValues() {
		$definitions = \ParamDefinition::getCleanDefinitions( $this->getInstance()->getParamDefinitions() );

		$defaults = array();

		foreach ( $definitions as $definition ) {
			if ( !$definition->isRequired() ) {
				$defaults[$definition->getName()] = $definition->getDefault();
			}
		}

		return $defaults;
	}

}
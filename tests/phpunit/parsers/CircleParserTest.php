<?php

namespace Maps\Test;

use Maps\CircleParser;

/**
 * @covers Maps\CircleParser
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class CircleParserTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {
		new CircleParser();
		$this->assertTrue( true );
	}



}
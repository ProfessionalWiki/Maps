<?php

declare( strict_types = 1 );

namespace Maps\Tests\System\SemanticMW;

use Maps\Tests\Util\TestFactory;
use PHPUnit\Framework\TestCase;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MapQueryTest extends TestCase {

	public function testMapQuery() {
		$pageCreator = TestFactory::newInstance()->getPageCreator();

		$pageCreator->createPage(
			'Property:Coordinates',
			'[[Has type::Geographic coordinate|geographic coordinate]]'
		);

		$pageCreator->createPage(
			'Property:Description',
			'[[Has type::Text]]'
		);

		$pageCreator->createPage(
			'Property:URL',
			'[[Has type::URL]]'
		);

		$pageCreator->createPage(
			'Berlin',
			'[[Coordinates::52° 31\' 0", 13° 24\' 0"]] [[Description::Capital of Germany]] [[URL::http://example.com/Berlin]]'
		);

		$pageCreator->createPage(
			'Brussels',
			'[[Coordinates::50° 51\' 1", 4° 21\' 6"]] [[Description::Capital of Belgium]]'
		);

		$pageCreator->createPage(
			'Hamburg',
			'[[Coordinates::53° 33\' 4", 9° 59\' 37"]]'
		);

		$pageCreator->createPage(
			'MapQuery',
			'{{#ask:[[Coordinates::+]]|?Coordinates|?Description|?URL|format=map}}'
		);

		$this->assertTrue( true ); // TODO
	}

}

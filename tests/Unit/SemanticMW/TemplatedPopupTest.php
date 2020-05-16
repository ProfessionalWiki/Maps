<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit\SemanticMW;

use DataValues\Geo\Values\LatLongValue;
use Maps\SemanticMW\TemplatedPopup;
use PHPUnit\Framework\TestCase;

class TemplatedPopupTest extends TestCase {

	public function testNamedParameters() {
		$popup = new TemplatedPopup( 'MyTemplate', '' );

		$this->assertSame(
			'{{MyTemplate|fulltitle=NS:MyTitle|title=MyTitle|latitude=4|longitude=2|userparam=|Has text=foo|foo|Has more=bar|bar}}',
			$popup->getWikiText(
				'NS:MyTitle',
				'MyTitle',
				new LatLongValue( 4, 2 ),
				[
					'Has text' => 'foo',
					'Has more' => 'bar'
				]
			)
		);
	}

}

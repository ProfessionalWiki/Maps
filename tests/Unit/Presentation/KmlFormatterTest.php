<?php

namespace Maps\Tests\Unit\Presentation;

use DataValues\Geo\Values\LatLongValue;
use Maps\Elements\Location;
use Maps\Presentation\KmlFormatter;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\Presentation\KmlFormatter
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class KmlFormatterTest extends TestCase {

	public function testEmptyList() {
		$this->assertSame(
			'<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://www.opengis.net/kml/2.2">
	<Document>

	</Document>
</kml>',
			( new KmlFormatter() )->formatLocationsAsKml()
		);
	}

	public function testSeveralLocations() {
		$this->assertSame(
			'<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://www.opengis.net/kml/2.2">
	<Document>
		<Placemark>
			<name><![CDATA[first title]]></name>
			<description><![CDATA[first text]]></description>
			<Point>
				<coordinates>23,42.42,0</coordinates>
			</Point>
		</Placemark>
		<Placemark>
			<name><![CDATA[second title]]></name>
			<description><![CDATA[second text]]></description>
			<Point>
				<coordinates>0,-1,0</coordinates>
			</Point>
		</Placemark>
	</Document>
</kml>',
			( new KmlFormatter() )->formatLocationsAsKml(
				new Location(
					new LatLongValue( 42.42,23 ),
					'first title',
					'first text'
				),
				new Location(
					new LatLongValue( -1,0 ),
					'second title',
					'second text'
				)
			)
		);
	}

	public function testLocationWithoutTitleAndText() {
		$this->assertSame(
			'<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://www.opengis.net/kml/2.2">
	<Document>
		<Placemark>
			<name><![CDATA[]]></name>
			<description><![CDATA[]]></description>
			<Point>
				<coordinates>23,42.42,0</coordinates>
			</Point>
		</Placemark>
	</Document>
</kml>',
			( new KmlFormatter() )->formatLocationsAsKml(
				new Location(
					new LatLongValue( 42.42,23 )
				)
			)
		);
	}

}

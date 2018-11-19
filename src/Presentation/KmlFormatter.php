<?php

namespace Maps\Presentation;

use Maps\Elements\Location;
use Xml;

/**
 * Class to format geographical data to KML.
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class KmlFormatter {

	/**
	 * @var array
	 */
	private $params;

	/**
	 * @var Location[]
	 */
	private $placemarks;

	public function __construct( array $params = [] ) {
		$this->params = $params;
		$this->clearElements();
	}

	/**
	 * Clears all set geographical objects.
	 */
	public function clearElements() {
		$this->clearPlacemarks();
	}

	/**
	 * Clears all set placemarks.
	 */
	public function clearPlacemarks() {
		$this->placemarks = [];
	}

	/**
	 * Builds and returns KML representing the set geographical objects.
	 */
	public function getKML(): string {
		$elements = $this->getKMLElements();

		// http://earth.google.com/kml/2.2
		return <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://www.opengis.net/kml/2.2">
	<Document>
		$elements
	</Document>
</kml>
EOT;
	}

	/**
	 * Returns the KML for all set geographical objects.
	 */
	private function getKMLElements(): string {
		$elements = [];

		$elements = array_merge( $elements, $this->getPlaceMarks() );

		return implode( "\n", $elements );
	}

	/**
	 * Returns KML for all set placemarks in a list, where each element is
	 * a KML node representing a placemark.
	 */
	private function getPlaceMarks(): array {
		$placeMarks = [];

		foreach ( $this->placemarks as $location ) {
			$placeMarks[] = $this->getKMLForLocation( $location );
		}

		return $placeMarks;
	}

	private function getKMLForLocation( Location $location ): string {
		$name = '<name><![CDATA[ ' . $location->getTitle() . ']]></name>';

		$description = '<description><![CDATA[ ' . $location->getText() . ']]></description>';

		$coordinates = $location->getCoordinates();

		// lon,lat[,alt]
		$coordinates = Xml::element(
			'coordinates',
			[],
			$coordinates->getLongitude() . ',' . $coordinates->getLatitude() . ',0'
		);

		return <<<EOT
		<Placemark>
			$name
			$description
			<Point>
				$coordinates
			</Point>
		</Placemark>
		
EOT;
	}

	/**
	 * @param Location[] $placemarks
	 */
	public function addPlacemarks( array $placemarks ) {
		foreach ( $placemarks as $placemark ) {
			$this->placemarks[] = $placemark;
		}
	}

}

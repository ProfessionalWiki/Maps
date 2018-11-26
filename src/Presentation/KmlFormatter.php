<?php

declare( strict_types = 1 );

namespace Maps\Presentation;

use Maps\Elements\Location;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class KmlFormatter {

	/**
	 * Builds and returns KML representing the set geographical objects.
	 */
	public function formatLocationsAsKml( Location ...$locations ): string {
		$elements = $this->getKmlForLocations( $locations );

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

	private function getKmlForLocations( array $locations ): string {
		return implode(
			"\n",
			array_map(
				function( Location $location ) {
					return $this->locationToKmlPlacemark( $location );
				},
				$locations
			)
		);
	}


	private function locationToKmlPlacemark( Location $location ): string {
		// TODO: escaping?
		$name = '<name><![CDATA[' . $location->getTitle() . ']]></name>';

		// TODO: escaping?
		$description = '<description><![CDATA[' . $location->getText() . ']]></description>';

		$coordinates = '<coordinates>'
			. $this->escapeValue( $this->getCoordinateString( $location ) )
			. '</coordinates>';

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

	private function getCoordinateString( Location $location ): string {
		// lon,lat[,alt]
		return $location->getCoordinates()->getLongitude()
			. ',' . $location->getCoordinates()->getLatitude()
			. ',0';
	}

	private function escapeValue( string $value ): string {
		return htmlspecialchars( $value, ENT_NOQUOTES );
	}

}

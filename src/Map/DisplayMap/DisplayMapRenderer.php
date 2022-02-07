<?php

declare( strict_types = 1 );

namespace Maps\Map\DisplayMap;

use Maps\DataAccess\MediaWikiFileUrlFinder;
use Maps\FileUrlFinder;
use Maps\LegacyModel\Location;
use Maps\Map\MapData;
use Maps\Map\MapOutput;
use Maps\Map\MapOutputBuilder;
use Maps\MappingService;
use Maps\Presentation\ElementJsonSerializer;
use Maps\Presentation\WikitextParser;
use Maps\WikitextParsers\LocationParser;
use Parser;

/**
 * Class handling the #display_map rendering.
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Kim Eik
 */
class DisplayMapRenderer {

	public ?MappingService $service;
	private LocationParser $locationParser;
	private FileUrlFinder $fileUrlFinder;
	private WikitextParser $wikitextParser;
	private ElementJsonSerializer $elementSerializer;

	public function __construct( MappingService $service = null ) {
		$this->service = $service;
	}

	/**
	 * Handles the request from the parser hook by doing the work that's common for all
	 * mapping services, calling the specific methods and finally returning the resulting output.
	 *
	 * @param MapData $mapData
	 * @param Parser $parser
	 *
	 * @return MapOutput
	 */
	final public function renderMap( MapData $mapData, Parser $parser ): MapOutput {
		$factory = \Maps\MapsFactory::globalInstance();

		$this->locationParser = $factory->newLocationParser();
		$this->fileUrlFinder = $factory->getFileUrlFinder();

		$this->wikitextParser = new WikitextParser( clone $parser );
		$this->elementSerializer = new ElementJsonSerializer( $this->wikitextParser );

		$mapData->setParameters( $this->handleMarkerData( $mapData->getParameters() ) );

		// TODO: inject
		$outputBuilder = new MapOutputBuilder();

		return $outputBuilder->buildOutput( $this->service, $mapData );
	}

	/**
	 * Converts the data in the coordinates parameter to JSON-ready objects.
	 * These get stored in the locations parameter, and the coordinates on gets deleted.
	 */
	private function handleMarkerData( array $params ) {
		$params['centre'] = $this->getCenter( $params['centre'] );

		// FIXME: this parameter is google maps service specific
		if ( array_key_exists( 'wmsoverlay', $params ) && is_object( $params['wmsoverlay'] ) ) {
			$params['wmsoverlay'] = $params['wmsoverlay']->getJSONObject();
		}

		$params['locations'] = $this->getLocationJson( $params );

		unset( $params['coordinates'] );

		$this->handleShapeData( $params );

		return $params;
	}

	private function getCenter( $coordinatesOrAddress ) {
		if ( $coordinatesOrAddress === false ) {
			return false;
		}

		try {
			// FIXME: a Location makes no sense here, since the non-coordinate data is not used
			$location = $this->locationParser->parse( $coordinatesOrAddress );
		}
		catch ( \Exception $ex ) {
			// TODO: somehow report this to the user
			return false;
		}

		return $location->getJSONObject();
	}

	private function getLocationJson( array $params ) {
		$iconUrl = $this->fileUrlFinder->getUrlForFileName( $params['icon'] );
		$visitedIconUrl = $this->fileUrlFinder->getUrlForFileName( $params['visitedicon'] ?? '' );

		$locationJsonObjects = [];

		foreach ( $params['coordinates'] as $coordinatesOrAddress ) {
			try {
				$location = $this->locationParser->parse( $coordinatesOrAddress );
			}
			catch ( \Exception $ex ) {
				// TODO: somehow report this to the user
				continue;
			}

			$locationJsonObjects[] = $this->getLocationJsonObject(
				$location,
				$params,
				$iconUrl,
				$visitedIconUrl
			);
		}

		return $locationJsonObjects;
	}

	private function getLocationJsonObject( Location $location, array $params, $iconUrl, $visitedIconUrl ) {
		$jsonObj = $location->getJSONObject( $params['title'], $params['label'], $iconUrl, '', '', $visitedIconUrl );

		$this->elementSerializer->titleAndText( $jsonObj );

		if ( isset( $jsonObj['inlineLabel'] ) ) {
			$jsonObj['inlineLabel'] = strip_tags(
				$this->wikitextParser->wikitextToHtml( $jsonObj['inlineLabel'] ),
				'<a><img>'
			);
		}

		return $jsonObj;
	}

	private function handleShapeData( array &$params ) {
		$textContainers = [
			&$params['lines'],
			&$params['polygons'],
			&$params['circles'],
			&$params['rectangles'],
			&$params['imageoverlays'], // FIXME: this is Google Maps specific!!
		];

		foreach ( $textContainers as &$textContainer ) {
			if ( is_array( $textContainer ) ) {
				foreach ( $textContainer as &$obj ) {
					$obj = $this->elementSerializer->elementToJson( $obj );
				}
			}
		}
	}

}

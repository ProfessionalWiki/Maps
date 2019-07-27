<?php

namespace Maps\SemanticMW\ValueDescriptions;

use DataValues\Geo\Values\LatLongValue;
use InvalidArgumentException;
use Maps\GeoFunctions;
use Maps\Presentation\MapsDistanceParser;
use SMW\DataValueFactory;
use SMW\DIProperty;
use SMW\Query\Language\ValueDescription;
use SMWDataItem;
use SMWDIGeoCoord;
use SMWThingDescription;
use Wikimedia\Rdbms\IDatabase;

/**
 * Description of a geographical area defined by a coordinates set and a distance to the bounds.
 * The bounds are a 'rectangle' (but bend due to the earths curvature), as the resulting query
 * would otherwise be to resource intensive.
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class AreaDescription extends ValueDescription {

	/**
	 * @var SMWDIGeoCoord
	 */
	private $center;

	private $radius;

	public function __construct( SMWDataItem $areaCenter, int $comparator, string $radius, DIProperty $property = null ) {
		if ( !( $areaCenter instanceof SMWDIGeoCoord ) ) {
			throw new InvalidArgumentException( '$areaCenter needs to be a SMWDIGeoCoord' );
		}

		parent::__construct( $areaCenter, $property, $comparator );

		$this->center = $areaCenter;
		$this->radius = $radius;
	}

	/**
	 * @see Description::prune
	 */
	public function prune( &$maxsize, &$maxdepth, &$log ) {
		if ( ( $maxsize < $this->getSize() ) || ( $maxdepth < $this->getDepth() ) ) {
			$log[] = $this->getQueryString();

			$result = new SMWThingDescription();
			$result->setPrintRequests( $this->getPrintRequests() );

			return $result;
		}

		$maxsize = $maxsize - $this->getSize();
		$maxdepth = $maxdepth - $this->getDepth();

		return $this;
	}

	public function getQueryString( $asValue = false ) {
		$centerString = DataValueFactory::getInstance()->newDataValueByItem(
			$this->center,
			$this->getProperty()
		)->getWikiValue();

		$queryString = "$centerString ({$this->radius})";

		return $asValue ? $queryString : "[[$queryString]]";
	}

	/**
	 * @see SomePropertyInterpreter::mapValueDescription
	 *
	 * FIXME: store specific code should be in the store component
	 *
	 * @param string $tableName
	 * @param string[] $fieldNames
	 * @param IDatabase $db
	 *
	 * @return string|false
	 */
	public function getSQLCondition( $tableName, array $fieldNames, IDatabase $db ) {
		if ( $this->center->getDIType() != SMWDataItem::TYPE_GEO ) {
			throw new \LogicException( 'Constructor should have prevented this' );
		}

		if ( !$this->comparatorIsSupported() ) {
			return false;
		}

		$bounds = $this->getBoundingBox();

		$north = $db->addQuotes( $bounds['north'] );
		$east = $db->addQuotes( $bounds['east'] );
		$south = $db->addQuotes( $bounds['south'] );
		$west = $db->addQuotes( $bounds['west'] );

		$isEq = $this->getComparator() == SMW_CMP_EQ;

		$smallerThen = $isEq ? '<' : '>=';
		$biggerThen = $isEq ? '>' : '<=';
		$joinCond = $isEq ? 'AND' : 'OR';

		$conditions = [];

		$conditions[] = "{$tableName}.$fieldNames[1] $smallerThen $north";
		$conditions[] = "{$tableName}.$fieldNames[1] $biggerThen $south";
		$conditions[] = "{$tableName}.$fieldNames[2] $smallerThen $east";
		$conditions[] = "{$tableName}.$fieldNames[2] $biggerThen $west";

		return implode( " $joinCond ", $conditions );
	}

	private function comparatorIsSupported(): bool {
		return $this->getComparator() === SMW_CMP_EQ || $this->getComparator() === SMW_CMP_NEQ;
	}

	/**
	 * @return float[] An associative array containing the limits with keys north, east, south and west.
	 */
	public function getBoundingBox(): array {
		$center = new LatLongValue(
			$this->center->getLatitude(),
			$this->center->getLongitude()
		);

		$radiusInMeters = MapsDistanceParser::parseDistance( $this->radius ); // TODO: this can return false

		$north = GeoFunctions::findDestination( $center, 0, $radiusInMeters );
		$east = GeoFunctions::findDestination( $center, 90, $radiusInMeters );
		$south = GeoFunctions::findDestination( $center, 180, $radiusInMeters );
		$west = GeoFunctions::findDestination( $center, 270, $radiusInMeters );

		return [
			'north' => $north['lat'],
			'east' => $east['lon'],
			'south' => $south['lat'],
			'west' => $west['lon'],
		];
	}

}

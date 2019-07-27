<?php

namespace Maps\SemanticMW\ValueDescriptions;

use SMW\DataValueFactory;
use SMW\Query\Language\ValueDescription;
use SMWDIGeoCoord;
use Wikimedia\Rdbms\IDatabase;

/**
 * Description of one data value of type Geographical Coordinates.
 *
 * @author Jeroen De Dauw
 */
class CoordinateDescription extends ValueDescription {

	public function getQueryString( $asValue = false ) {
		$queryString = DataValueFactory::getInstance()->newDataValueByItem(
			$this->getDataItem(),
			$this->getProperty()
		)->getWikiValue();

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
		$dataItem = $this->getDataItem();

		// Only execute the query when the description's type is geographical coordinates,
		// the description is valid, and the near comparator is used.
		if ( $dataItem instanceof SMWDIGeoCoord ) {
			switch ( $this->getComparator() ) {
				case SMW_CMP_EQ:
					$comparator = '=';
					break;
				case SMW_CMP_LEQ:
					$comparator = '<=';
					break;
				case SMW_CMP_GEQ:
					$comparator = '>=';
					break;
				case SMW_CMP_NEQ:
					$comparator = '!=';
					break;
				default:
					return false;
			}

			$lat = $db->addQuotes( $dataItem->getLatitude() );
			$lon = $db->addQuotes( $dataItem->getLongitude() );

			$conditions = [];

			$conditions[] = "{$tableName}.$fieldNames[1] $comparator $lat";
			$conditions[] = "{$tableName}.$fieldNames[2] $comparator $lon";

			return implode( ' AND ', $conditions );
		}

		return false;
	}

}

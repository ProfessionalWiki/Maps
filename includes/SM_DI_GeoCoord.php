<?php

/**
 * Implementation of dataitems that are geographic coordinates.
 *
 * @since 0.8
 *
 * @file SM_DI_GeoCoord.php
 * @ingroup SemanticMaps
 *
 * @licence GNU GPL v3
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SMDIGeoCoord extends SMWDataItem {

	protected $coordinateSet;
	protected $wikiValue;

	public function __construct( array $coords, $typeid = '_geo' ) {
		parent::__construct( $typeid );

        $this->coordinateSet = $coords;

        //throw new SMWDataItemException( "Initialisation value '$number' is not a number." );
	}

	public function getDIType() {
		return SMWDataItem::TYPE_GEO;
	}

	/**
	 * @since 0.8
	 *
	 * @return array
	 */
	public function getCoordinateSet() {
		return $this->coordinateSet;
	}

	public function getSortKey() {
		return $this->coordinateSet['lat']; // TODO
	}    

	public function getSerialization() {
        global $smgQPCoodFormat, $smgQPCoodDirectional;
        return MapsCoordinateParser::formatCoordinates( $this->coordinateSet, $smgQPCoodFormat, $smgQPCoodDirectional );
	}

	/**
	 * Create a data item from the provided serialization string and type
	 * ID.
	 * @note PHP can convert any string to some number, so we do not do
	 * validation here (because this would require less efficient parsing).
	 * @return SMWDINumber
	 */
	public static function doUnserialize( $serialization, $typeid ) {
        $parsedCoords = MapsCoordinateParser::parseCoordinates( $serialization );

        if ( $parsedCoords === false || !is_array( $parsedCoords ) ) {
            throw new Exception( 'Unserialization of coordinates failed' );
        }

		return new self( $parsedCoords, $typeid );
	}

}

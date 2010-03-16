<?php

/**
 * File holding the SMGeoCoordsValueDescription class.
 * 
 * @file SM_GeoCoordsValueDescription.php
 * @ingroup SemanticMaps
 * 
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * Description of one data value of type Goegraphical Coordinates.
 *
 * @author Jeroen De Dauw
 * 
 * @ingroup SemanticMaps
 */
class SMGeoCoordsValueDescription extends SMWValueDescription {

	protected $m_distance;
	
	public function __construct( SMGeoCoordsValue $datavalue, $distance, $comparator = SMW_CMP_EQ ) {
		parent::__construct( $datavalue, $comparator );
		$this->m_distance = $distance;
	}
	
}
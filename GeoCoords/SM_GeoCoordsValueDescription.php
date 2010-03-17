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
	
	public function getQueryString( $asvalue = false ) {
		if ( $this->m_datavalue !== null ) {
			switch ( $this->m_comparator ) {
				case SMW_CMP_LEQ:  $comparator = '<'; break;
				case SMW_CMP_GEQ:  $comparator = '>'; break;
				case SMW_CMP_NEQ:  $comparator = '!'; break;
				case SMW_CMP_LIKE: $comparator = '~'; break;
				default: case SMW_CMP_EQ:
					$comparator = '';
				break;
			}
			if ( $asvalue ) {
				return $comparator . $this->m_datavalue->getWikiValue();
			} else { // this only is possible for values of Type:Page
				return '[[' . $comparator . $this->m_datavalue->getWikiValue() . ']]';
			}
		} else {
			return $asvalue ? '+':''; // the else case may result in an error here (query without proper condition)
		}
	}	
	
}
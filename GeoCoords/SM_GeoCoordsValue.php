<?php
/**
 * File holding the SMGeoCoordsValue class.
 * 
 * @file SM_GeoCoordsValue.php
 * @ingroup SMWDataValues
 * @ingroup SemanticMaps
 * 
 * @author Jeroen De Dauw
 * @author Markus Krötzsch
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * Implementation of datavalues that are geographic coordinates.
 *
 * @author Jeroen De Dauw
 * @author Markus Krötzsch
 * 
 * @ingroup SemanticMaps
 */
class SMGeoCoordsValue extends SMWDataValue {

	protected $mCoordinateSet;
	protected $mWikivalue;

	/**
	 * Adds support for the geographical coordinate data type to Semantic MediaWiki.
	 * 
	 * TODO: i18n keys still need to be moved
	 */
	public static function initGeoCoordsType() {
		SMWDataValueFactory::registerDatatype( '_geo', __CLASS__, 'Geographic coordinate' );
		return true;
	}
	
	/**
	 * Defines the layout for the smw_coords table which is used to store value of the GeoCoords type.
	 * 
	 * @param array $propertyTables The property tables defined by SMW, passed by reference.
	 */
	public static function initGeoCoordsTable( array $propertyTables ) {
		$propertyTables['smw_coords'] = new SMWSQLStore2Table(
			'sm_coords',
			array( 'lat' => 'f', 'lon' => 'f' )
		);
		return true;
	}
	
	/**
	 * @see SMWDataValue::parseUserValue
	 */
	protected function parseUserValue( $value ) {
		$this->parseUserValueOrQuery( $value );
	}
	
	/**
	 * Overwrite SMWDataValue::getQueryDescription() to be able to process
	 * comparators between all values.
	 * 
	 * @param string $value
	 * 
	 * @return SMWDescription
	 */
	public function getQueryDescription( $value ) {
		return $this->parseUserValueOrQuery( $value, true );
	}	
	
	/**
	 * Parses the value into the coordinates and any meta data provided, such as distance.
	 */
	protected function parseUserValueOrQuery( $value, $asQuery = false ) {
		$this->mWikivalue = $value;
		
		$comparator = SMW_CMP_EQ;
		
		if ( $value == '' ) {
			$this->addError( wfMsg( 'smw_novalues' ) );
		} else {
			SMWDataValue::prepareValue( $value, $comparator );					

			$parts = explode( '(', $value );
			
			$coordinates = trim( array_shift( $parts ) );
			$distance = count( $parts ) > 0 ? trim( array_shift( $parts ) ) : false;

			if ( $distance !== false ) {
				if ( preg_match( '/^\d+(\.\d+)?(\s.+)\)$/', $distance ) ) {
					$distance = substr( $distance, 0, -1 );
				}
				else {
					$this->addError( wfMsgExt( 'semanticmaps-unrecognizeddistance', array( 'parsemag' ), $distance ) );
					$distance = false;						
				}
			}

			$parsedCoords = MapsCoordinateParser::parseCoordinates( $coordinates );
			if ( $parsedCoords ) {
				$this->mCoordinateSet = $parsedCoords;
				
				if ( $this->m_caption === false && !$asQuery ) {
					global $smgQPCoodFormat, $smgQPCoodDirectional;
					$this->m_caption = MapsCoordinateParser::formatCoordinates( $parsedCoords, $smgQPCoodFormat, $smgQPCoodDirectional );
        		}
			} else {
				$this->addError( wfMsgExt( 'maps_unrecognized_coords', array( 'parsemag' ), $coordinates, 1 ) );
			}
		}

		if ( $asQuery ) {
			$this->setUserValue( $value );
			
			switch ( true ) {
				case !$this->isValid() :
					return new SMWThingDescription();
					break;
				case $distance !== false :
					return new SMAreaValueDescription( $this, $distance );
					break;
				default :
					return new SMGeoCoordsValueDescription( $this, $comparator );
					break;										
			}			
		}
	}
	
	/**
	 * @see SMWDataValue::parseDBkeys
	 */
	protected function parseDBkeys( $args ) {
		global $smgQPCoodFormat, $smgQPCoodDirectional;
		
		$this->mCoordinateSet['lat'] = $args[0];
		$this->mCoordinateSet['lon'] = $args[1];
		
		$this->m_caption = MapsCoordinateParser::formatCoordinates(
			$this->mCoordinateSet,
			$smgQPCoodFormat,
			$smgQPCoodDirectional
		);
		
		$this->mWikivalue = $this->m_caption;
	}
	
	/**
	 * @see SMWDataValue::getDBkeys
	 */
	public function getDBkeys() {
		$this->unstub();
		
		return array(
			$this->mCoordinateSet['lat'],
			$this->mCoordinateSet['lon']
		);
	}
	
	/**
	 * @see SMWDataValue::getSignature
	 */	
	public function getSignature() {
		return 'ff';
	}	

	/**
	 * @see SMWDataValue::getShortWikiText
	 */
	public function getShortWikiText( $linked = null ) {
		if ( $this->isValid() && ( $linked !== null ) && ( $linked !== false ) ) {
			SMWOutputs::requireHeadItem( SMW_HEADER_TOOLTIP );
			return '<span class="smwttinline">' . htmlspecialchars( $this->m_caption ) . '<span class="smwttcontent">' .
		        htmlspecialchars ( wfMsgForContent( 'maps-latitude' ) . ' ' . $this->mCoordinateSet['lat'] ) . '<br />' .
		        htmlspecialchars ( wfMsgForContent( 'maps-longitude' ) . ' ' . $this->mCoordinateSet['lon'] ) .
		        '</span></span>';
		} else {
			return htmlspecialchars( $this->m_caption );
		}
	}
	
	/**
	 * @see SMWDataValue::getShortHTMLText
	 */
	public function getShortHTMLText( $linker = null ) {
		return $this->getShortWikiText( $linker );
	}
	
	/**
	 * @see SMWDataValue::getLongWikiText
	 */
	public function getLongWikiText( $linked = null ) {
		if ( !$this->isValid() ) {
			return $this->getErrorText();
		} else {
			global $smgQPCoodFormat, $smgQPCoodDirectional;
			return MapsCoordinateParser::formatCoordinates( $this->mCoordinateSet, $smgQPCoodFormat, $smgQPCoodDirectional );
		}
	}

	/**
	 * @see SMWDataValue::getLongHTMLText
	 */
	public function getLongHTMLText( $linker = null ) {
		return $this->getLongWikiText( $linker );
	}

	/**
	 * @see SMWDataValue::getWikiValue
	 */
	public function getWikiValue() {
		$this->unstub();
		return $this->mWikivalue;
	}

	/**
	 * @see SMWDataValue::getExportData
	 */
	public function getExportData() {
		if ( $this->isValid() ) {
			global $smgQPCoodFormat, $smgQPCoodDirectional;
			$lit = new SMWExpLiteral(
				MapsCoordinateParser::formatCoordinates( $this->mCoordinateSet, $smgQPCoodFormat, $smgQPCoodDirectional ),
				$this,
				'http://www.w3.org/2001/XMLSchema#string'
			);
			return new SMWExpData( $lit );
		} else {
			return null;
		}
	}

	/**
	 * Create links to mapping services based on a wiki-editable message. The parameters
	 * available to the message are:
	 * 
	 * @return array
	 */
	protected function getServiceLinkParams() {
		return array(  ); // TODO
	}
	
	/**
	 * @return array
	 */
	public function getCoordinateSet() {
		return $this->mCoordinateSet;
	}
	
	/**
	 * @see SMWDataValue::getValueIndexes
	 * 
	 * @return array
	 */	
	public function getValueIndexes() {
		return array( 0, 1 );
	}

	/**
	 * @see SMWDataValue::getLabelIndexes
	 * 
	 * @return array
	 */		
	public function getLabelIndexes() {
		return array( 0, 1 );
	}	

}
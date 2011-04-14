<?php

/**
 * Implementation of datavalues that are geographic coordinates.
 * 
 * @since 0.6
 * 
 * @file SM_GeoCoordsValue.php
 * @ingroup SemanticMaps
 * @ingroup SMWDataValues
 * 
 * @licence GNU GPL v3
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Markus Krötzsch
 */
class SMGeoCoordsValue extends SMWDataValue {

	protected $coordinateSet;
	protected $wikiValue;

	/**
	 * @see SMWDataValue::setDataItem()
	 * 
	 * @since 0.8
	 * 
	 * @param $dataitem SMWDataItem
	 * 
	 * @return boolean
	 */
	public function setDataItem( SMWDataItem $dataItem ) {
		if ( $dataItem->getDIType() == SMWDataItem::TYPE_GEO ) {
			$this->m_dataitem = $dataItem;
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * @see SMWDataValue::parseUserValue
	 * 
	 * @since 0.6
	 */
	protected function parseUserValue( $value ) {
		$this->parseUserValueOrQuery( $value );
	}
	
	/**
	 * Overwrite SMWDataValue::getQueryDescription() to be able to process
	 * comparators between all values.
	 * 
	 * @since 0.6
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
	 * 
	 * @since 0.6
	 * 
	 * @param $value String
	 * @param $asQuery Boolean
	 */
	protected function parseUserValueOrQuery( $value, $asQuery = false ) {
		$this->wikiValue = $value;
		
		$comparator = SMW_CMP_EQ;
		
		if ( $value == '' ) {
			$this->addError( wfMsg( 'smw_novalues' ) );
		} else {
			SMWDataValue::prepareValue( $value, $comparator );					

			$parts = explode( '(', $value );
			
			$coordinates = trim( array_shift( $parts ) );
			$distance = count( $parts ) > 0 ? trim( array_shift( $parts ) ) : false;

			if ( $distance !== false ) {
				$distance = substr( trim( $distance ), 0, -1 );
				
				if ( !MapsDistanceParser::isDistance( $distance ) ) {
					$this->addError( wfMsgExt( 'semanticmaps-unrecognizeddistance', array( 'parsemag' ), $distance ) );
					$distance = false;							
				}
			}

			$parsedCoords = MapsCoordinateParser::parseCoordinates( $coordinates );
			if ( $parsedCoords ) {
				$this->coordinateSet = $parsedCoords;
				
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
					return new SMAreaValueDescription( $this, $comparator, $distance );
					break;
				default :
					return new SMGeoCoordsValueDescription( $this, $comparator );
					break;										
			}			
		}
	}
	
	/**
	 * @see SMWDataValue::parseDBkeys
	 * 
	 * @since 0.6
	 */
	protected function parseDBkeys( $args ) {
		global $smgQPCoodFormat, $smgQPCoodDirectional;
		
		$this->coordinateSet['lat'] = (float)$args[0];
		$this->coordinateSet['lon'] = (float)$args[1];
		
		$this->m_caption = MapsCoordinateParser::formatCoordinates(
			$this->coordinateSet,
			$smgQPCoodFormat,
			$smgQPCoodDirectional
		);
		
		$this->wikiValue = $this->m_caption;
	}
	
	/**
	 * @see SMWDataValue::getDBkeys
	 * 
	 * @since 0.6
	 */
	public function getDBkeys() {
		$this->unstub();
		
		return array(
			$this->coordinateSet['lat'],
			$this->coordinateSet['lon']
		);
	}
	
	/**
	 * @see SMWDataValue::getSignature
	 * 
	 * @since 0.6
	 */	
	public function getSignature() {
		return 'ff';
	}	

	/**
	 * @see SMWDataValue::getShortWikiText
	 * 
	 * @since 0.6
	 */
	public function getShortWikiText( $linked = null ) {
		if ( $this->isValid() && ( $linked !== null ) && ( $linked !== false ) ) {
			SMWOutputs::requireHeadItem( SMW_HEADER_TOOLTIP );
			
			// TODO: fix lang keys so they include the space and coordinates.
			
			return '<span class="smwttinline">' . htmlspecialchars( $this->m_caption ) . '<span class="smwttcontent">' .
		        htmlspecialchars ( wfMsgForContent( 'maps-latitude' ) . ' ' . $this->coordinateSet['lat'] ) . '<br />' .
		        htmlspecialchars ( wfMsgForContent( 'maps-longitude' ) . ' ' . $this->coordinateSet['lon'] ) .
		        '</span></span>';
		}
		else {
			return htmlspecialchars( $this->m_caption );
		}
	}
	
	/**
	 * @see SMWDataValue::getShortHTMLText
	 * 
	 * @since 0.6
	 */
	public function getShortHTMLText( $linker = null ) {
		return $this->getShortWikiText( $linker );
	}
	
	/**
	 * @see SMWDataValue::getLongWikiText
	 * 
	 * @since 0.6
	 */
	public function getLongWikiText( $linked = null ) {
		if ( !$this->isValid() ) {
			return $this->getErrorText();
		}
		else {
			global $smgQPCoodFormat, $smgQPCoodDirectional;
			return MapsCoordinateParser::formatCoordinates( $this->coordinateSet, $smgQPCoodFormat, $smgQPCoodDirectional );
		}
	}

	/**
	 * @see SMWDataValue::getLongHTMLText
	 * 
	 * @since 0.6
	 */
	public function getLongHTMLText( $linker = null ) {
		return $this->getLongWikiText( $linker );
	}

	/**
	 * @see SMWDataValue::getWikiValue
	 * 
	 * @since 0.6
	 */
	public function getWikiValue() {
		$this->unstub();
		return $this->wikiValue;
	}

	/**
	 * @see SMWDataValue::getExportData
	 * 
	 * @since 0.6
	 */
	public function getExportData() {
		if ( $this->isValid() ) {
			global $smgQPCoodFormat, $smgQPCoodDirectional;
			$lit = new SMWExpLiteral(
				MapsCoordinateParser::formatCoordinates( $this->coordinateSet, $smgQPCoodFormat, $smgQPCoodDirectional ),
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
	 * $1: The location in non-directional float notation.
	 * $2: The location in directional DMS notation.
	 * $3: The latitude in non-directional float notation.
	 * $4 The longitude in non-directional float notation.
	 * 
	 * @since 0.6.4
	 * 
	 * @return array
	 */
	protected function getServiceLinkParams() {
		return array(
			MapsCoordinateParser::formatCoordinates( $this->coordinateSet, 'float', false ),
			MapsCoordinateParser::formatCoordinates( $this->coordinateSet, 'dms', true ),
			$this->coordinateSet['lat'],
			$this->coordinateSet['lon']
		);
	}
	
	/**
	 * @since 0.6
	 * 
	 * @return array
	 */
	public function getCoordinateSet() {
		return $this->coordinateSet;
	}
	
	/**
	 * @see SMWDataValue::getValueIndex
	 * 
	 * @since 0.6
	 * 
	 * @return integer
	 */	
	public function getValueIndex() {
		return 0;
	}

	/**
	 * @see SMWDataValue::getLabelIndex
	 * 
	 * @since 0.6
	 * 
	 * @return integer
	 */		
	public function getLabelIndex() {
		return 0;
	}	

}

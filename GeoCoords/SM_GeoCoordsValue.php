<?php
/**
 * File holding the SMGeoCoordsValue class.
 * 
 * @file SM_GeoCoordsValue.php
 * @ingroup SMWDataValues
 * @ingroup SemanticMaps
 * 
 * @author Markus Krötzsch
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/// Unicode symbols for coordinate minutes and seconds;
/// may not display in every font ...
define( 'SM_GEO_MIN', '′' );
define( 'SM_GEO_SEC', '″' );

/**
 * Implementation of datavalues that are geographic coordinates.
 *
 * @author Markus Krötzsch
 * @author Jeroen De Dauw
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
	public static function InitGeoCoordsType() {
		SMWDataValueFactory::registerDatatype( '_geo', __CLASS__, 'Geographic coordinate' );
		return true;
	}	
	
	/**
	 * @see SMWDataValue::parseUserValue
	 */
	protected function parseUserValue( $value ) {
		if ( $value == '' ) {
			$this->addError( wfMsg( 'smw_novalues' ) );
		} else {
			$coordinates = MapsCoordinateParser::formatCoordinates( $value );
			if ( $coordinates ) {
				$this->mCoordinateSet = $coordinates;
				
				if ( $this->m_caption === false ) {
					$this->m_caption = $value;
        		}				
			} else {
				$this->addError( wfMsgExt( 'maps_unrecognized_coords', array( 'parsemag' ), $value, 1 ) );
			}
		}
	}

	/**
	 * Overwrite SMWDataValue::getQueryDescription() to be able to process
	 * comparators between all values.
	 * 
	 * @return SMGeoCoordsValueDescription
	 */
	public function getQueryDescription( $value ) {
		// TODO
	}	
	
	/**
	 * @see SMWDataValue::parseDBkeys
	 */
	protected function parseDBkeys( $args ) {
		list( $this->mCoordinateSet['lat'], $this->mCoordinateSet['lon'] ) = explode( ',', $args[0] );
		
		$this->m_caption = $this->mCoordinateSet['lat'] . ', ' . $this->mCoordinateSet['lon'];
		$this->mWikivalue = $this->m_caption;
	}
	
	/**
	 * @see SMWDataValue::getDBkeys
	 */	
	public function getDBkeys() {
		$this->unstub();
		return array( $this->mCoordinateSet['lat'] . ',' . $this->mCoordinateSet['lon'] );
	}	

	/**
	 * @see SMWDataValue::getShortWikiText
	 */
	public function getShortWikiText( $linked = null ) {
		if ( $this->isValid() && ( $linked !== null ) && ( $linked !== false ) ) {
			SMWOutputs::requireHeadItem( SMW_HEADER_TOOLTIP );
			return '<span class="smwttinline">' . $this->m_caption . '<span class="smwttcontent">' .
			        wfMsgForContent( 'maps-latitude' ) . ' ' . $this->mCoordinateSet['lat'] . '<br />' .
			        wfMsgForContent( 'maps-longitude' ) . ' ' . $this->mCoordinateSet['lon'] .
			        '</span></span>';
		} else {
			return $this->m_caption;
		}
	}
	
	/**
	 * @see SMWDataValue::getShortHTMLText
	 */
	public function getShortHTMLText( $linker = null ) {
		// TODO: parse to HTML?
		return $this->getShortWikiText( $linker );
	}
	
	/**
	 * @see SMWDataValue::getLongWikiText
	 */		
	public function getLongWikiText( $linked = null ) {
		if ( !$this->isValid() ) {
			return $this->getErrorText();
		} else {
			return $this->mCoordinateSet['lat'] . ', ' . $this->mCoordinateSet['lon'];
		}
	}

	/**
	 * @see SMWDataValue::getLongHTMLText
	 */		
	public function getLongHTMLText( $linker = null ) {
		// TODO: parse to HTML?
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
			$lit = new SMWExpLiteral( $this->formatAngleValues( true, false ) . ', ' . $this->formatAngleValues( false, false ), $this, 'http://www.w3.org/2001/XMLSchema#string' );
			return new SMWExpData( $lit );
		} else {
			return null;
		}
	}

	/**
	 * Create links to mapping services based on a wiki-editable message. The parameters
	 * available to the message are:
	 * 
	 * 
	 */
	protected function getServiceLinkParams() {
		return array(  ); // TODO
	}

}

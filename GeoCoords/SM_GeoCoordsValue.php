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
		if ( $value == '' ) {
			$this->addError( wfMsg( 'smw_novalues' ) );
		} else {
			$coordinates = MapsCoordinateParser::parseCoordinates( $value );
			if ( $coordinates ) {
				$this->mCoordinateSet = $coordinates;
				
				if ( $this->m_caption === false ) {
					global $smgQPCoodFormat, $smgQPCoodDirectional;
					$this->m_caption = MapsCoordinateParser::formatCoordinates( $coordinates, $smgQPCoodFormat, $smgQPCoodDirectional );
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
	 * @return SMWDescription
	 */
	public function getQueryDescription( $value ) {
		return parent::getQueryDescription( $value );
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
	 * 
	 * TODO: make the output here more readible (and if possible informative)
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
			global $smgQPCoodFormat, $smgQPCoodDirectional;
			return MapsCoordinateParser::formatCoordinates( $this->mCoordinateSet, $smgQPCoodFormat, $smgQPCoodDirectional );
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

}

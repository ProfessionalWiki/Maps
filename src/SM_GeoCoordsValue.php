<?php

use DataValues\LatLongValue;
use ValueFormatters\GeoCoordinateFormatter;
use ValueParsers\ParseException;

/**
 * Implementation of datavalues that are geographic coordinates.
 * 
 * @since 0.6
 * 
 * @file SM_GeoCoordsValue.php
 * @ingroup SemanticMaps
 * @ingroup SMWDataValues
 * 
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Markus Krötzsch
 */
class SMGeoCoordsValue extends SMWDataValue {

	protected $wikiValue;

	/**
	 * @see SMWDataValue::setDataItem
	 * 
	 * @since 1.0
	 * 
	 * @param SMWDataItem $dataItem
	 * 
	 * @return boolean
	 */
	protected function loadDataItem( SMWDataItem $dataItem ) {
		if ( $dataItem instanceof SMWDIGeoCoord ) {
			 $formattedValue = $this->getFormattedCoord( $dataItem );

			if ( $formattedValue !== null ) {
				$this->wikiValue = $formattedValue;
				$this->m_dataitem = $dataItem;
				return true;
			}
		}

		return false;
	}

	/**
	 * @since 3.0
	 *
	 * @param SMWDIGeoCoord $dataItem
	 * @param string|null $format
	 *
	 * @return string|null
	 */
	protected function getFormattedCoord( SMWDIGeoCoord $dataItem, $format = null ) {
		global $smgQPCoodFormat;

		$options = new \ValueFormatters\FormatterOptions( array(
			GeoCoordinateFormatter::OPT_FORMAT => $format === null ? $smgQPCoodFormat : $format, // TODO
		) );

		// TODO: $smgQPCoodDirectional

		$coordinateFormatter = new GeoCoordinateFormatter( $options );

		$value = new LatLongValue(
			$dataItem->getLatitude(),
			$dataItem->getLongitude()
		);

		return $coordinateFormatter->format( $value );
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
	 * @throws InvalidArgumentException
	 */
	public function getQueryDescription( $value ) {
		if ( !is_string( $value ) ) {
			throw new InvalidArgumentException( '$value needs to be a string' );
		}

		list( $distance, $comparator ) = $this->parseUserValue( $value );
		$distance = $this->parserDistance( $distance );

		$this->setUserValue( $value );

		switch ( true ) {
			case !$this->isValid() :
				return new SMWThingDescription();
			case $distance !== false :
				return new SMAreaValueDescription( $this->getDataItem(), $comparator, $distance );
			default :
				return new SMGeoCoordsValueDescription( $this->getDataItem(), null, $comparator );
		}
	}

	protected function parserDistance( $distance ) {
		if ( $distance !== false ) {
			$distance = substr( trim( $distance ), 0, -1 );

			if ( !MapsDistanceParser::isDistance( $distance ) ) {
				$this->addError( wfMessage( 'semanticmaps-unrecognizeddistance', $distance )->text() );
				$distance = false;
			}
		}

		return $distance;
	}

	/**
	 * @see SMWDataValue::parseUserValue
	 *
	 * @since 0.6
	 */
	protected function parseUserValue( $value ) {
		if ( !is_string( $value ) ) {
			throw new InvalidArgumentException( '$value needs to be a string' );
		}

		$this->wikiValue = $value;

		$comparator = SMW_CMP_EQ;
		$distance = false;

		if ( $value === '' ) {
			$this->addError( wfMessage( 'smw_novalues' )->text() );
		} else {
			SMWDataValue::prepareValue( $value, $comparator );

			list( $coordinates, $distance ) = $this->findValueParts( $value );

			$this->tryParseAndSetDataItem( $coordinates );
		}

		return array( $distance, $comparator );
	}

	protected function findValueParts( $value ) {
		$parts = explode( '(', $value );

		$coordinates = trim( array_shift( $parts ) );
		$distance = count( $parts ) > 0 ? trim( array_shift( $parts ) ) : false;

		return array( $coordinates, $distance );
	}

	/**
	 * @param string $coordinates
	 */
	protected function tryParseAndSetDataItem( $coordinates ) {
		$options = new \ValueParsers\ParserOptions();
		$parser = new \ValueParsers\GeoCoordinateParser( $options );

		try {
			$value = $parser->parse( $coordinates );
			$this->m_dataitem = new SMWDIGeoCoord( $value->getLatitude(), $value->getLongitude() );
		}
		catch ( ParseException $parseException ) {
			$this->addError( wfMessage( 'maps_unrecognized_coords', $coordinates, 1 )->text() );

			// Make sure this is always set
			// TODO: Why is this needed?!
			$this->m_dataitem = new SMWDIGeoCoord( array( 'lat' => 0, 'lon' => 0 ) );
		}
	}

	/**
	 * @see SMWDataValue::getShortWikiText
	 * 
	 * @since 0.6
	 */
	public function getShortWikiText( $linked = null ) {
		if ( $this->isValid() ) {
			if ( $this->m_caption === false ) {
				return $this->getFormattedCoord( $this->m_dataitem );
			}
			else {
				return $this->m_caption; 
			}
		}
		else {
			return $this->getErrorText();
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
		if ( $this->isValid() ) {
			SMWOutputs::requireHeadItem( SMW_HEADER_TOOLTIP );

			// TODO: fix lang keys so they include the space and coordinates.
			$coordinateSet = $this->m_dataitem->getCoordinateSet();
			
			$text = $this->getFormattedCoord( $this->m_dataitem );

			$lines = array(
				wfMessage( 'semanticmaps-latitude', $coordinateSet['lat'] )->inContentLanguage()->escaped(),
				wfMessage( 'semanticmaps-longitude', $coordinateSet['lon'] )->inContentLanguage()->escaped(),
			);
			
			if ( array_key_exists( 'alt', $coordinateSet ) ) {
				$lines[] = wfMessage( 'semanticmaps-altitude', $coordinateSet['alt'] )->inContentLanguage()->escaped();
			}
			
			return 	'<span class="smwttinline">' . htmlspecialchars( $text ) . '<span class="smwttcontent">' .
		        	 	implode( '<br />', $lines ) .
		        	'</span></span>';
		} else {
			return $this->getErrorText();
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
		return $this->wikiValue;
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
		$coordinateSet = $this->m_dataitem->getCoordinateSet();
		return array(
			$this->getFormattedCoord( $this->m_dataitem, 'float' ), // TODO
			$this->getFormattedCoord( $this->m_dataitem, 'dms' ), // TODO
			$coordinateSet['lat'],
			$coordinateSet['lon']
		);
	}

}

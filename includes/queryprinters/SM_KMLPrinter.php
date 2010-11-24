<?php

/**
 * SMWResultPrinter class for printing a query result as KML.
 *
 * @since 0.7.3
 *
 * @file SM_KMLPrinter.php
 * @ingroup SemanticMaps
 *
 * @author Jeroen De Dauw
 */
class SMKMLPrinter extends SMWResultPrinter {
	
	/**
	 * Handler of the print request.
	 *
	 * @since 0.7.3
	 *
	 * @param SMWQueryResult $res
	 * @param $outputmode
	 * 
	 * @return array
	 */
	public function getResultText( /* SMWQueryResult */ $res, $outputmode ) {
		return $outputmode == SMW_OUTPUT_FILE ? $this->getKML( $res, $outputmode ) : $this->getLink( $res, $outputmode );
	}
	
	/**
	 * Returns the KML for the query result.
	 * 
	 * @since 0.7.3
	 * 
	 * @param SMWQueryResult $res
	 * @param integer $outputmode
	 * 
	 * @return string
	 */
	protected function getKML( SMWQueryResult $res, $outputmode ) {
		$queryHandler = new SMQueryHandler( $res, $outputmode );
		$locations = $queryHandler->getLocations();
		
		$formatter = new MapsKMLFormatter();
		$formatter->addPlacemarks( $locations );
		
		return $formatter->getKML();
	}
	
	/**
	 * Returns a link (HTML) pointing to a query that returns the actual KML file.
	 * 
	 * @since 0.7.3
	 * 
	 * @param SMWQueryResult $res
	 * @param integer $outputmode
	 * 
	 * @return string
	 */	
	protected function getLink( SMWQueryResult $res, $outputmode ) {
		$searchLabel = $this->getSearchLabel( $outputmode );
		$link = $res->getQueryLink( $searchLabel ? $searchLabel : wfMsgForContent( 'semanticmaps-kml-link' ) );
		$link->setParameter( 'kml', 'format' );
		
		/*
		if ( $this->m_title !== '' ) {
			$link->setParameter( $this->m_title, 'title' );
		}
		
		if ( $this->m_description !== '' ) {
			$link->setParameter( $this->m_description, 'description' );
		}
		*/
		if ( array_key_exists( 'limit', $this->m_params ) ) {
			$link->setParameter( $this->m_params['limit'], 'limit' );
		} else { // Use a reasonable default limit.
			$link->setParameter( 20, 'limit' );
		}

		$this->isHTML = ( $outputmode == SMW_OUTPUT_HTML );
		 
		return $link->getText( $outputmode, $this->mLinker );		
	}
	
	/**
	 * @see SMWResultPrinter::getMimeType()
	 */
	public function getMimeType( $res ) {
		return 'application/vnd.google-earth.kml+xml';
	}

	/**
	 * @see SMWResultPrinter::getFileName()
	 */	
	public function getFileName( $res ) {
		// TODO
		return 'kml.kml';
	}
	
	/**
	 * @see SMWResultPrinter::getName()
	 */
	public final function getName() {
		return wfMsg( 'semanticmaps-kml' );
	}
	
}

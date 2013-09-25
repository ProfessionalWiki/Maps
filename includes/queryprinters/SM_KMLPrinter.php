<?php

use SMW\FileExportPrinter;

/**
 * SMWResultPrinter class for printing a query result as KML.
 *
 * @since 0.7.3
 *
 * @file SM_KMLPrinter.php
 * @ingroup SemanticMaps
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SMKMLPrinter extends FileExportPrinter {

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
	public function getResultText( SMWQueryResult $res, $outputmode ) {
		if ( $outputmode == SMW_OUTPUT_FILE ) {
			return $this->getKML( $res, $outputmode );
		}
		else {
			return $this->getKMLLink( $res, $outputmode );
		}
	}

	/**
	 * @see SMWResultPrinter::handleParameters
	 *
	 * @since 1.0
	 *
	 * @param array $params
	 * @param $outputmode
	 */
	protected function handleParameters( array $params, $outputmode ) {
		$this->params = $params;
	}

	/**
	 * @see SMWResultPrinter::getParamDefinitions
	 *
	 * @since 3.0
	 *
	 * @param IParamDefinition[] $definitions
	 *
	 * @return array of IParamDefinition|array
	 */
	public function getParamDefinitions( array $definitions ) {
		global $egMapsDefaultLabel, $egMapsDefaultTitle;

		$params = parent::getParamDefinitions( $definitions );

		$params['text'] = array(
			'message' => 'semanticmaps-kml-text',
			'default' => $egMapsDefaultLabel,
		);

		$params['title'] = array(
			'message' => 'semanticmaps-kml-title',
			'default' => $egMapsDefaultTitle,
		);

		$params['linkabsolute'] = array(
			'message' => 'semanticmaps-kml-linkabsolute',
			'type' => 'boolean',
			'default' => true,
		);

		$params['pagelinktext'] = array(
			'message' => 'semanticmaps-kml-pagelinktext',
			'default' => wfMessage( 'semanticmaps-default-kml-pagelink' )->text(),
		);

		return $params;
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
		$queryHandler = new SMQueryHandler( $res, $outputmode, $this->params['linkabsolute'], $this->params['pagelinktext'], false );
		$queryHandler->setText( $this->params['text'] );
		$queryHandler->setTitle( $this->params['title'] );
		$queryHandler->setSubjectSeparator( '' );

		$formatter = new MapsKMLFormatter( $this->params );

		$shapes = $queryHandler->getShapes();
		$formatter->addPlacemarks( $shapes['locations'] );

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
	protected function getKMLLink( SMWQueryResult $res, $outputmode ) {
		$searchLabel = $this->getSearchLabel( $outputmode );
		$link = $res->getQueryLink( $searchLabel ? $searchLabel : wfMessage( 'semanticmaps-kml-link' )->inContentLanguage()->text() );
		$link->setParameter( 'kml', 'format' );
		$link->setParameter( $this->params['linkabsolute'] ? 'yes' : 'no', 'linkabsolute' );
		$link->setParameter( $this->params['pagelinktext'], 'pagelinktext' );

		if ( $this->params['title'] !== '' ) {
			$link->setParameter( $this->params['title'], 'title' );
		}

		if ( $this->params['text'] !== '' ) {
			$link->setParameter( $this->params['text'], 'text' );
		}

		// Fix for offset-error in getQueryLink()
		// (getQueryLink by default sets offset to point to the next 
		// result set, fix by setting it to 0 if now explicitly set)
		if ( array_key_exists( 'offset', $this->params ) ) {
			$link->setParameter( $this->params['offset'], 'offset' );
		} else {
			$link->setParameter( 0, 'offset' );
		}

		if ( array_key_exists( 'limit', $this->params ) ) {
			$link->setParameter( $this->params['limit'], 'limit' );
		} else { // Use a reasonable default limit.
			$link->setParameter( 20, 'limit' );
		}

		$this->isHTML = ( $outputmode == SMW_OUTPUT_HTML );

		return $link->getText( $outputmode, $this->mLinker );
	}

	/**
	 * @see SMWIExportPrinter::getMimeType
	 *
	 * @since 2.0
	 *
	 * @param SMWQueryResult $queryResult
	 *
	 * @return string
	 */
	public function getMimeType( SMWQueryResult $queryResult ) {
		return 'application/vnd.google-earth.kml+xml';
	}

	/**
	 * @see SMWIExportPrinter::getFileName
	 *
	 * @since 2.0
	 *
	 * @param SMWQueryResult $queryResult
	 *
	 * @return string|boolean
	 */
	public function getFileName( SMWQueryResult $queryResult ) {
		return 'kml.kml';
	}

	/**
	 * @see SMWResultPrinter::getName()
	 */
	public final function getName() {
		return wfMessage( 'semanticmaps-kml' )->text();
	}
}

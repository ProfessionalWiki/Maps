<?php

namespace Maps\SemanticMW\ResultPrinters;

use Maps\Presentation\KmlFormatter;
use ParamProcessor\ParamDefinition;
use SMW\Query\ResultPrinters\FileExportPrinter;
use SMWQueryResult;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class KmlPrinter extends FileExportPrinter {

	/**
	 * @param SMWQueryResult $res
	 * @param int $outputMode
	 *
	 * @return string
	 */
	public function getResultText( SMWQueryResult $res, $outputMode ) {
		if ( $outputMode == SMW_OUTPUT_FILE ) {
			return $this->getKML( $res, $outputMode );
		}

		return $this->getKMLLink( $res, $outputMode );
	}

	private function getKML( SMWQueryResult $res, int $outputMode ): string {
		$queryHandler = new QueryHandler( $res, $outputMode, $this->params['linkabsolute'] );
		$queryHandler->setText( $this->params['text'] );
		$queryHandler->setTitle( $this->params['title'] );
		$queryHandler->setSubjectSeparator( '' );

		$formatter = new KmlFormatter();
		return $formatter->formatLocationsAsKml( ...$queryHandler->getLocations() );
	}

	/**
	 * Returns a link (HTML) pointing to a query that returns the actual KML file.
	 */
	private function getKMLLink( SMWQueryResult $res, int $outputMode ): string {
		$searchLabel = $this->getSearchLabel( $outputMode );
		$link = $res->getQueryLink(
			$searchLabel ? $searchLabel : wfMessage( 'semanticmaps-kml-link' )->inContentLanguage()->text()
		);
		$link->setParameter( 'kml', 'format' );
		$link->setParameter( $this->params['linkabsolute'] ? 'yes' : 'no', 'linkabsolute' );

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

		$this->isHTML = ( $outputMode == SMW_OUTPUT_HTML );

		return $link->getText( $outputMode, $this->mLinker );
	}

	/**
	 * @see SMWResultPrinter::getParamDefinitions
	 *
	 * @param ParamDefinition[] $definitions
	 *
	 * @return array
	 */
	public function getParamDefinitions( array $definitions ) {
		global $egMapsDefaultLabel, $egMapsDefaultTitle;

		$definitions['text'] = [
			'message' => 'semanticmaps-kml-text',
			'default' => $egMapsDefaultLabel,
		];

		$definitions['title'] = [
			'message' => 'semanticmaps-kml-title',
			'default' => $egMapsDefaultTitle,
		];

		$definitions['linkabsolute'] = [
			'message' => 'semanticmaps-kml-linkabsolute',
			'type' => 'boolean',
			'default' => true,
		];

		return $definitions;
	}

	/**
	 * @see SMWIExportPrinter::getMimeType
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

	/**
	 * @see SMWResultPrinter::handleParameters
	 *
	 * @param array $params
	 * @param $outputMode
	 */
	protected function handleParameters( array $params, $outputMode ) {
		$this->params = $params;
	}
}

<?php

/**
 *
 * @file Maps_BasePointLineMap.php
 * @ingroup Maps
 *
 * @author Kim Eik
 */
class MapsBasePointLineMap extends MapsBasePointMap {

	protected function handleMarkerData( array &$params , Parser $parser ) {
		parent::handleMarkerData( $params , $parser );

		$parserClone = clone $parser;

		$textContainers = array(
			&$params['lines'] ,
			&$params['polygons'] ,
			&$params['circles'] ,
			&$params['rectangles'],
			&$params['imageoverlays'],
		);

		foreach ( $textContainers as &$textContainer ) {
			foreach ( $textContainer as &$obj ) {
				$obj['title'] = $parserClone->parse( $obj['title'] , $parserClone->getTitle() , new ParserOptions() )->getText();
				$obj['text'] = $parserClone->parse( $obj['text'] , $parserClone->getTitle() , new ParserOptions() )->getText();

				$hasTitleAndtext = $obj['title'] !== '' && $obj['text'] !== '';
				$obj['text'] = ( $hasTitleAndtext ? '<b>' . $obj['title'] . '</b><hr />' : $obj['title'] ) . $obj['text'];
				$obj['title'] = strip_tags( $obj['title'] );
			}
		}
	}


}

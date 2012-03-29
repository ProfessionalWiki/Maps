<?php

/**
 *
 * @file Maps_BasePointLineMap.php
 * @ingroup Maps
 *
 * @author Kim Eik
 */
class MapsBasePointLineMap extends MapsBasePointMap{

    protected function handleMarkerData(array &$params, Parser $parser)
    {
        parent::handleMarkerData($params, $parser);

        $parserClone = clone $parser;

        foreach($params['lines'] as &$line){

            $line['title'] = $parserClone->parse( $line['title'], $parserClone->getTitle(), new ParserOptions() )->getText();
            $line['text'] = $parserClone->parse( $line['text'], $parserClone->getTitle(), new ParserOptions() )->getText();

            $hasTitleAndtext = $line['title'] !== '' && $line['text'] !== '';
            $line['text'] = ( $hasTitleAndtext ? '<b>' . $line['title'] . '</b><hr />' : $line['title'] ) . $line['text'];
            $line['title'] = strip_tags( $line['title'] );
        }

        foreach($params['polygons'] as &$polygon){

            $polygon['title'] = $parserClone->parse( $polygon['title'], $parserClone->getTitle(), new ParserOptions() )->getText();
            $polygon['text'] = $parserClone->parse( $polygon['text'], $parserClone->getTitle(), new ParserOptions() )->getText();

            $hasTitleAndtext = $polygon['title'] !== '' && $polygon['text'] !== '';
            $polygon['text'] = ( $hasTitleAndtext ? '<b>' . $polygon['title'] . '</b><hr />' : $polygon['title'] ) . $polygon['text'];
            $polygon['title'] = strip_tags( $polygon['title'] );
        }
    }


}

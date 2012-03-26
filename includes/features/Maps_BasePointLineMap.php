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
    }


}

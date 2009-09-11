<?php

/**
 * This file holds the general information for the OpenLayers service
 *
 * @file Maps_OpenLayers.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

$egMapsServices['openlayers'] = array(
									'pf' => array('class' => 'MapsOpenLayersParserFunctions', 'file' => 'OpenLayers/Maps_OpenLayersParserFunctions.php', 'local' => true),
									'classes' => array(
											array('class' => 'MapsOpenLayersUtils', 'file' => 'OpenLayers/Maps_OpenLayersUtils.php', 'local' => true)
											),
									'aliases' => array('layers', 'openlayer'),
									'parameters' => array(
											'layers' => array(),
											'baselayer' => array()
											)
									);
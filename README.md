# Maps

Maps is a [MediaWiki](https://www.mediawiki.org) extension to work with and visualise geographical
information.

Features:

* Powerful `#display_map` parser hook for embedding highly customizable dynamic maps into wiki pages.
* Support for multiple mapping services: Google Maps, [OpenLayers](http://www.openlayers.org/),
[OpenStreetMap](www.openstreetmap.org/) and [Leaflet](http://leafletjs.com/).
* Integration with [Semantic MediaWiki](https://www.semantic-mediawiki.org) via a [coordinate datatype](https://www.semantic-mediawiki.org/wiki/Help:Type_Geographic_coordinate)
    * Query your stored coordinates and visualize them on dynamic maps, as tables or as lists
    * Export your coordinates as KML or RDF
    * Combine coordinates with other structured data stored in your wiki
* Integration with [Semantic Forms](https://www.mediawiki.org/wiki/Extension:Semantic_Forms): modify templates with coordinates via forms
* Coordinate formatting and format conversion via the `#coordinates` parser function.
* Geocoding via several supported services with the `#geocode` parser function.
* Geospatial operations
    * Calculating the distance between two points with `#geodistance`
    * Finding a destination given a starting point, bearing and distance with `#finddestination`
* Distance formatting and format conversion via the `#distance` parser function.
* Visual map editor (Special:MapEditor) to edit `#display_map` wikitext.

## User manual

* [Installation and configuration](INSTALL.md)
* [Release notes](RELEASE-NOTES.md)
* [Usage instructions](https://www.semantic-mediawiki.org/wiki/Maps)
* [Usage examples](https://www.semantic-mediawiki.org/wiki/Category:Maps_examples)
* [Semantic usage examples](https://www.semantic-mediawiki.org/wiki/Semantic_Maps_examples)

### Getting support

* Ask a question on [the mailing list](https://semantic-mediawiki.org/wiki/Mailing_list)
* Ask a question on the #mediawiki IRC channel on Freenode.

## Project status

[![Build Status](https://secure.travis-ci.org/JeroenDeDauw/Maps.png?branch=master)](http://travis-ci.org/JeroenDeDauw/Maps)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/JeroenDeDauw/Maps/badges/quality-score.png?s=3881a27e63cb64e7511d766bfec2e2db5d39bec3)](https://scrutinizer-ci.com/g/JeroenDeDauw/Maps/)
[![Dependency Status](https://www.versioneye.com/php/mediawiki:maps/dev-master/badge.png)](https://www.versioneye.com/php/mediawiki:maps/dev-master)

On [Packagist](https://packagist.org/packages/mediawiki/maps):
[![Latest Stable Version](https://poser.pugx.org/mediawiki/maps/version.png)](https://packagist.org/packages/mediawiki/maps)
[![Download count](https://poser.pugx.org/mediawiki/maps/d/total.png)](https://packagist.org/packages/mediawiki/maps)

* [Open bugs and feature requests](https://github.com/JeroenDeDauw/Maps/issues)
* [Maps on Ohloh](https://www.ohloh.net/p/maps/)
* [Blog posts about Maps](https://www.entropywins.wtf/blog/tag/maps/)

## Contributing

* [File an issue](https://github.com/JeroenDeDauw/Maps/issues)
* [Submit a pull request](https://github.com/JeroenDeDauw/Maps/pulls) ([tasks for newcomers](https://github.com/JeroenDeDauw/Maps/issues?q=is%3Aissue+is%3Aopen+label%3Anewcomer))

### Running the tests

To run the tests, execute this in the root directory of maps:

    composer ci

To run only the MediaWiki independent tests, execute this in the root directory of maps:

    phpunit

All tests in `tests/Unit` are MediaWiki independent, with those depending on MediaWiki reside in
`tests/Integration`.

## Credits to other projects

### jQuery

This extension uses code from the jQuery library.
jQuery is dual licensed under the
[MIT](http://www.opensource.org/licenses/mit-license.php)
and
[GPL](http://www.opensource.org/licenses/gpl-license.php)
licenses.

### OpenLayers

This extension includes code from the OpenLayers application.
OpenLayers is an open-source product released under a
[BSD-style license](http://svn.openlayers.org/trunk/openlayers/license.txt).

### geoxml3

This extension includes a copy of the geoxml3 KML processor.
geoxml3 is released under the
[Apache License 2.0 license](http://www.apache.org/licenses/LICENSE-2.0).

### google-maps-utility-library-v3

This extension includes code from the google-maps-utility-library-v3 (googleearth.js).
It is released under the
[Apache License 2.0 license](http://www.apache.org/licenses/LICENSE-2.0).

### OpenStreetMap.js

This extension includes the OpenStreetMap.js file which can be found
[here](http://www.openstreetmap.org/openlayers/OpenStreetMap.js).

## Links

* [Maps examples](https://www.semantic-mediawiki.org/wiki/Maps_examples)
* [Maps on Ohloh](https://www.ohloh.net/p/maps)
* [Maps on MediaWiki.org](https://www.mediawiki.org/wiki/Extension:Maps)
* [Maps on Packagist](https://packagist.org/packages/mediawiki/maps)
* [TravisCI build status](https://travis-ci.org/JeroenDeDauw/Maps)
* [Semantic Maps on MediaWiki.org](https://www.mediawiki.org/wiki/Extension:Semantic_Maps)

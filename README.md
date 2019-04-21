# Maps

Maps is a [MediaWiki](https://www.mediawiki.org) extension to work with and visualize geographical
information. 

Features:

* Powerful [`#display_map`](https://www.semantic-mediawiki.org/wiki/Extension:Maps/Displaying_maps) parser hook for embedding highly customizable dynamic maps into wiki pages.
* Support for multiple mapping services: [Leaflet](http://leafletjs.com/), Google Maps and [OpenStreetMap](www.openstreetmap.org/).
* Integration with [Semantic MediaWiki](https://www.semantic-mediawiki.org) via a [coordinate datatype](https://www.semantic-mediawiki.org/wiki/Help:Type_Geographic_coordinate)
    * Query your stored coordinates and visualize them on dynamic maps, as tables or as lists
    * Export your coordinates as KML or RDF
    * Combine coordinates with other structured data stored in your wiki
* Geocoding via several supported services with the [`#geocode`](https://www.semantic-mediawiki.org/wiki/Maps/Geocoding) parser function.
* Coordinate formatting and format conversion via the [`#coordinates`](https://www.semantic-mediawiki.org/wiki/Maps/Coordinates) parser function.
* Geospatial operations
    * Calculating the distance between two points with [`#geodistance`](https://www.semantic-mediawiki.org/wiki/Maps/Geodistance)
    * Finding a destination given a starting point, bearing and distance with [`#finddestination`](https://www.semantic-mediawiki.org/wiki/Maps/Finddestination)
* Distance formatting and format conversion via the [`#distance`](https://www.semantic-mediawiki.org/wiki/Maps/Distance) parser function.
* Visual map editor ([Special:MapEditor](https://www.semantic-mediawiki.org/wiki/Special:MapEditor)) to edit [`#display_map`](https://www.semantic-mediawiki.org/wiki/Extension:Maps/Displaying_maps) wikitext (requires Google Maps).

Maps has been maintained since 2009 and is installed on 1000+ wikis. [Professional.Wiki](https://professional.wiki/) provides professional support.

## User manual

### For administrators

* [Installation](https://www.semantic-mediawiki.org/wiki/Maps/Installation)
* [Configuration](https://www.semantic-mediawiki.org/wiki/Maps/Configuration)
* [Release notes](RELEASE-NOTES.md) - detailed list of changes per release
* [Platform compatibility](INSTALL.md#platform-compatibility-and-release-status) - overview of PHP and MediaWiki support per release

### For wiki users

* [Usage instructions](https://www.semantic-mediawiki.org/wiki/Extension:Maps)
* [Usage examples](https://www.semantic-mediawiki.org/wiki/Category:Maps_examples)
* [Semantic usage examples](https://www.semantic-mediawiki.org/wiki/Semantic_Maps_examples)

### Getting support

* Professional support and custom development: **[Professional.Wiki](https://professional.wiki/)**
* Ask a question on [the mailing list](https://www.semantic-mediawiki.org/wiki/Mailing_list)
* File an issue on [our issue tracker](https://github.com/JeroenDeDauw/Maps/issues)

## Project status

* Latest version &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; [![Latest Stable Version](https://poser.pugx.org/mediawiki/maps/version.png)](https://packagist.org/packages/mediawiki/maps)
* Downloads on [Packagist](https://packagist.org/packages/mediawiki/maps) [![Download count](https://poser.pugx.org/mediawiki/maps/d/total.png)](https://packagist.org/packages/mediawiki/maps)
* TravisCI &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; [![Build Status](https://secure.travis-ci.org/JeroenDeDauw/Maps.png?branch=master)](http://travis-ci.org/JeroenDeDauw/Maps)
* Code quality &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; [![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/JeroenDeDauw/Maps/badges/quality-score.png?s=3881a27e63cb64e7511d766bfec2e2db5d39bec3)](https://scrutinizer-ci.com/g/JeroenDeDauw/Maps/)
* [Open bugs and feature requests](https://github.com/JeroenDeDauw/Maps/issues)
* [Maps on OpenHub](https://www.ohloh.net/p/maps/)
* [Blog posts about Maps](https://www.entropywins.wtf/blog/tag/maps/)

## Contributing

* [File an issue](https://github.com/JeroenDeDauw/Maps/issues)
* [Submit a pull request](https://github.com/JeroenDeDauw/Maps/pulls) ([tasks for newcomers](https://github.com/JeroenDeDauw/Maps/issues?q=is%3Aissue+is%3Aopen+label%3Anewcomer))

### Project structure

The `src/` contains the PHP code and follows PSR-4 autoloading.

* `src/DataAccess` - implementations of services that use the network, read from disk or persistence services
* `src/Elements` - Value Objects representing geographical elements (should be devoid of MediaWiki binding)
* `src/MediaWiki` - MediaWiki hook points, including API modules, special pages and MediaWiki hooks
* `src/Presentation` - presentation layer code (all code dealing with HTML etc should be here)
* `src/SemanticMW` - Semantic MediaWiki hook points, including result printers and value descriptions

JavaScript, CSS and other web resources go into `resources/`.

Tests for PHP go into `tests/` where they are grouped by test type (ie unit, integration). Within those test type
directories the tests should mirror the directory structure in `src/`.

### Running the tests

As setup, run `composer install` inside of the Maps root directory.

You can run the MediaWiki independent tests by executing phpunit in the root directory of maps:

    phpunit

This is possible without having a MediaWiki installation or webserver. A clone of the Maps code suffices.

If you do not have PHPUnit installed, you can download the .phar into the root directory and execute it there:

	wget -O phpunit.phar https://phar.phpunit.de/phpunit-7.phar
	php phpunit.phar

To run the tests with MediaWiki, change into `tests/phpunit` of your MediaWiki installation and run

    php phpunit.php --wiki wiki -c ../../extensions/Maps/phpunit.xml.dist
    
Where you either update `wiki` to match your wikis name, or drop the parameter. The above command
works without modification if you are using the [MediaWiki Vagrant](https://www.mediawiki.org/wiki/MediaWiki-Vagrant).

Beware that due to technical debt, some tests access the network.

## Links

* [Maps on OpenHub](https://www.openhub.net/p/maps/)
* [Maps on Packagist](https://packagist.org/packages/mediawiki/maps)
* [Maps on WikiApiary](https://wikiapiary.com/wiki/Extension:Maps)
* [Maps on MediaWiki.org](https://www.mediawiki.org/wiki/Extension:Maps)
* [TravisCI build status](https://travis-ci.org/JeroenDeDauw/Maps)

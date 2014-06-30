# Semantic Maps

[![Build Status](https://secure.travis-ci.org/SemanticMediaWiki/SemanticMaps.png?branch=master)](http://travis-ci.org/SemanticMediaWiki/SemanticMaps)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/SemanticMediaWiki/SemanticMaps/badges/quality-score.png?s=2e92028475bc897409f8dbb40ce897d6cc88240e)](https://scrutinizer-ci.com/g/SemanticMediaWiki/SemanticMaps/)
[![Latest Stable Version](https://poser.pugx.org/mediawiki/semantic-maps/version.png)](https://packagist.org/packages/mediawiki/semantic-maps)
[![Download count](https://poser.pugx.org/mediawiki/semantic-maps/d/total.png)](https://packagist.org/packages/mediawiki/semantic-maps)

Semantic Maps is an extension that adds semantic capabilities to the [Maps extension]
(https://github.com/JeroenDeDauw/Maps). This
includes the ability to add, edit, aggregate and visualize coordinate data stored through
[Semantic MediaWiki](https://semantic-mediawiki.org/).

Since Semantic Maps uses the Maps API, you can use multiple mapping services. These include
Google Maps (with Google Earth support), Yahoo! Maps, OpenLayers and OpenStreetMap.

Both Semantic Maps and Maps are based on Semantic Google Maps and Semantic Layers, and are
meant to replace these extensions. Having Semantic MediaWiki and Maps installed is a
prerequisite for the Semantic Maps extension; the code will not work without it.

## Documentation

* [Usage examples](https://www.semantic-mediawiki.org/wiki/Semantic_Maps_examples)
* [Installation instructions](https://github.com/SemanticMediaWiki/SemanticMaps/blob/master/docs/INSTALL.md)
* [Release notes](https://github.com/SemanticMediaWiki/SemanticMaps/blob/master/docs/RELEASE-NOTES.md)

Note that the installation instructions might be out of date. Initiation now is done
the same way as that of the Maps extension.

## Contributing and support

* [File an issue](https://github.com/SemanticMediaWiki/SemanticMaps/issues)
* [Submit a pull request](https://github.com/SemanticMediaWiki/SemanticMaps/pulls)
* Ask a question on [the mailing list](https://semantic-mediawiki.org/wiki/Mailing_list)
* Ask a question on the #semantic-mediawiki IRC channel on Freenode.

You can run the PHPUnit tests by changing into the `tests/phpunit` directory of your MediaWiki
install and running

    php phpunit.php -c ../../extensions/SemanticMaps/
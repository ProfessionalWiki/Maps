# Semantic Maps

## Installation

These are the installation and configuration instructions for the [SemanticMaps extension](../README.md).

## Versions



### Database support

All current versions of Maps have full support for all databases that can be used with MediaWiki.

## Download and installation

The recommended way to download and install Semantic Maps is with [Composer](http://getcomposer.org) using
[MediaWiki 1.22 built-in support for Composer](https://www.mediawiki.org/wiki/Composer). MediaWiki
versions prior to 1.22 can use Composer via the
[Extension Installer](https://github.com/JeroenDeDauw/ExtensionInstaller/blob/master/README.md)
extension.

Note that [Semantic MediaWiki][https://semantic-mediawiki.org/wiki/Help:Installation) needs to be installed first for this extension to work.

#### Step 1

If you have MediaWiki 1.22 or later, go to the root directory of your MediaWiki installation,
and go to step 2. You do not need to install any extensions to support composer.

For MediaWiki 1.21.x and earlier you need to install the
[Extension Installer](https://github.com/JeroenDeDauw/ExtensionInstaller/blob/master/README.md) extension.

Once you are done installing the Extension Installer, go to its directory so composer.phar
is installed in the right place.

    cd extensions/ExtensionInstaller

#### Step 2

If you have previously installed Composer skip to step 3.

To install Composer:

    wget http://getcomposer.org/composer.phar

#### Step 3

Now using Composer, install Semantic Maps

    php composer.phar require mediawiki/semantic-maps "*"

#### Verify installation success

As final step, you can verify Semantic Maps got installed by looking at the "Special:Version" page on your wiki and verifying the Semantic Maps extension is listed.


## Configuration

See the [Semantic Maps settings file](../SM_Settings.php) for the available configuration options.

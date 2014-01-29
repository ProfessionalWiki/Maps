# Semantic Maps

## Installation

These are the installation and configuration instructions for the [SemanticMaps extension](../README.md).

## Versions

<table>
	<tr>
		<th></th>
		<th>Status</th>
		<th>Release date</th>
		<th>Git branch</th>
	</tr>
	<tr>
		<th><a href="https://github.com/JeroenDeDauw/SemanticMaps/blob/master/docs/RELEASE-NOTES.md">Semantic Maps 3.0.x</a></th>
		<td>Development version</td>
		<td>Future</td>
		<td><a href="https://github.com/JeroenDeDauw/SemanticMaps/tree/master">master</a></td>
	</tr>
	<tr>
		<th><a href="https://github.com/JeroenDeDauw/SemanticMaps/blob/2.0.x/RELEASE-NOTES">Semantic Maps 2.0.x</a></th>
		<td>Stable release</td>
		<td>2012-12-13</td>
		<td><a href="https://github.com/JeroenDeDauw/SemanticMaps/tree/2.0.x">2.0.x</a></td>
	</tr>
	<tr>
		<th><a href="https://github.com/JeroenDeDauw/SemanticMaps/blob/2.0.x/RELEASE-NOTES">Semantic Maps 1.0.5</a></th>
		<td>Legacy release</td>
		<td>2011-11-30</td>
		<td></td>
	</tr>
</table>

### Platform compatibility

<table>
	<tr>
		<th></th>
		<th>PHP</th>
		<th>MediaWiki</th>
		<th>Semantic<br />MediaWiki</th>
		<th>Maps</th>
		<th>Composer</th>
		<th>Validator</th>
	</tr>
	<tr>
		<th>Semantic Maps<br />3.0.x</th>
		<td>5.3.2 - 5.5.x</td>
		<td>1.18 - 1.23</td>
		<td>1.9.x</td>
		<td>3.0.x</td>
		<td>Required</td>
		<td>1.0.x (handled by Composer)</td>
	</tr>
	<tr>
		<th>Semantic Maps<br />2.0.x</th>
		<td>5.3.2 - 5.5.x</td>
		<td>1.18 - 1.23</td>
		<td>1.8.x</td>
		<td>2.0.x</td>
		<td>Not supported</td>
		<td>0.5.1</td>
	</tr>
	<tr>
		<th>Semantic Maps<br />1.0.5</th>
		<td>5.2.0 - 5.3.x</td>
		<td>1.17 - 1.19</td>
		<td>1.7.x</td>
		<td>1.0.5</td>
		<td>Not supported</td>
		<td>0.4.13 or 0.4.14</td>
	</tr>
</table>

When installing Semantic Maps 2.x, see the installation instructions that come bundled with it. Also
make use of Validator 0.5.x. More recent versions of Validator will not work.


### Database support

All current versions of Semantic Maps have full support for all databases that can be used with MediaWiki.

## Download and installation

The recommended way to download and install Semantic Maps is with [Composer](http://getcomposer.org) using
[MediaWiki 1.22 built-in support for Composer](https://www.mediawiki.org/wiki/Composer). MediaWiki
versions prior to 1.22 can use Composer via the
[Extension Installer](https://github.com/JeroenDeDauw/ExtensionInstaller/blob/master/README.md)
extension.

Note that [Semantic MediaWiki](https://semantic-mediawiki.org/wiki/Help:Installation) needs to be installed first for this extension to work.

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

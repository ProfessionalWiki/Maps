# Semantic Maps

## Installation

These are the installation and configuration instructions for the [SemanticMaps](README.md) extension.

## Versions

<table>
	<tr>
		<th></th>
		<th>Status</th>
		<th>Release date</th>
		<th>Git branch</th>
	</tr>
	<tr>
		<th><a href="RELEASE-NOTES.md">Semantic Maps 3.4.0</a></th>
		<td>Development version</td>
		<td>Future</td>
		<td><a href="https://github.com/SemanticMediaWiki/SemanticMaps/tree/master">master</a></td>
	</tr>
	<tr>
		<th><a href="RELEASE-NOTES.md">Semantic Maps 3.3.0</a></th>
		<td>Stable release</td>
		<td>2016-04-02</td>
		<td><a href="https://github.com/SemanticMediaWiki/SemanticMaps/tree/3.3.0">3.3.0</a></td>
	</tr>
	<tr>
		<th><a href="RELEASE-NOTES.md">Semantic Maps 3.2.0</a></th>
		<td>Legacy release</td>
		<td>2015-07-02</td>
		<td><a href="https://github.com/SemanticMediaWiki/SemanticMaps/tree/3.2.0">3.2.0</a></td>
	</tr>
	<tr>
		<th><a href="RELEASE-NOTES.md">Semantic Maps 3.1.x</a></th>
		<td>Legacy release</td>
		<td>2014-11-11</td>
		<td><a href="https://github.com/SemanticMediaWiki/SemanticMaps/tree/3.1.2">3.1.2</a></td>
	</tr>
	<tr>
		<th><a href="RELEASE-NOTES.md">Semantic Maps 3.0.x</a></th>
		<td>Legacy release</td>
		<td>2014-01-18</td>
		<td><a href="https://github.com/SemanticMediaWiki/SemanticMaps/tree/3.0">3.0</a></td>
	</tr>
	<tr>
		<th><a href="https://github.com/SemanticMediaWiki/SemanticMaps/blob/2.0.x/RELEASE-NOTES">Semantic Maps 2.0.x</a></th>
		<td>Legacy release</td>
		<td>2012-12-13</td>
		<td><a href="https://github.com/SemanticMediaWiki/SemanticMaps/tree/2.0.x">2.0.x</a></td>
	</tr>
	<tr>
		<th><a href="https://github.com/SemanticMediaWiki/SemanticMaps/blob/2.0.x/RELEASE-NOTES">Semantic Maps 1.0.5</a></th>
		<td>Legacy release</td>
		<td>2011-11-30</td>
		<td>-/-</td>
	</tr>
</table>

### Platform compatibility

<table>
	<tr>
		<th></th>
		<th>PHP</th>
		<th>MediaWiki</th>
		<th>SMW</th>
		<th>Maps</th>
		<th>Composer</th>
		<th>Validator</th>
	</tr>
	<tr>
		<th>S. Maps 3.4.x</th>
		<td><strong>5.5</strong> - 7.x</td>
		<td><strong>1.23</strong> - 1.27</td>
		<td>2.x >= 2.1</td>
		<td>3.x</td>
		<td>Required</td>
		<td>2.x (handled by Composer)</td>
	</tr>
	<tr>
		<th>S. Maps 3.3.x</th>
		<td>5.3.2 - 7.x</td>
		<td>1.19 - 1.27</td>
		<td>2.x >= 2.1</td>
		<td>3.x</td>
		<td>Required</td>
		<td>2.x (handled by Composer)</td>
	</tr>
	<tr>
		<th>S. Maps 3.2.x</th>
		<td>5.3.2 - 5.6.x</td>
		<td>1.19 - 1.25</td>
		<td>2.x >= 2.1</td>
		<td>3.x</td>
		<td>Required</td>
		<td>2.x (handled by Composer)</td>
	</tr>
	<tr>
		<th>S. Maps 3.1.x</th>
		<td>5.3.2 - 5.6.x</td>
		<td><strong>1.19</strong> - 1.23</td>
		<td>2.x</td>
		<td>3.x</td>
		<td>Required</td>
		<td>2.x (handled by Composer)</td>
	</tr>
	<tr>
		<th>S. Maps 3.0.x</th>
		<td>5.3.2 - 5.6.x</td>
		<td>1.18 - 1.23</td>
		<td>1.9.x</td>
		<td>3.x</td>
		<td>Required</td>
		<td>1.x (handled by Composer)</td>
	</tr>
	<tr>
		<th>S. Maps 2.0.x</th>
		<td><strong>5.3.2</strong> - 5.5.x</td>
		<td><strong>1.18</strong> - 1.23</td>
		<td>1.8.x</td>
		<td>2.0.x</td>
		<td>Not supported</td>
		<td>0.5.1</td>
	</tr>
	<tr>
		<th>S. Maps 1.0.5</th>
		<td>5.2.0 - 5.3.x</td>
		<td>1.17 - 1.19</td>
		<td>1.7.x</td>
		<td>1.0.5</td>
		<td>Not supported</td>
		<td>0.4.13 or 0.4.14</td>
	</tr>
</table>

When installing Semantic Maps 2.x, see the installation instructions that come bundled with it. Also
make use of Validator 0.5.x as stated above. More recent versions of Validator will not work.


### Database support

All current versions of Semantic Maps have full support for all databases that can be used with Semantic MediaWiki.

## Download and installation

The recommended way to download and install Semantic Maps is with [Composer](http://getcomposer.org) using
[MediaWiki 1.22 built-in support for Composer](https://www.mediawiki.org/wiki/Composer).

Note that the [Semantic MediaWiki](https://www.semantic-mediawiki.org/wiki/Help:Installation) extension as
well as the  [Maps](https://github.com/JeroenDeDauw/Maps/blob/master/docs/INSTALL.md) extension need
to be installed first for this extension to work.

#### Step 1

Go to the root directory of your MediaWiki installation.

If you have previously installed Composer skip to step 2.

To install Composer run:

    wget http://getcomposer.org/composer.phar

#### Step 2

Now using Composer, install Semantic Maps by running

    php composer.phar require mediawiki/semantic-maps "*"

#### Verify installation success

As final step, you can verify Semantic Maps got installed by looking at the "Special:Version" page on
your wiki and verifying the Semantic Maps extension is listed in the "Semantic extensions" section.


## Configuration

See the [Semantic Maps settings file](DefaultSettings.php) for the available configuration options.

# Maps installation

These are the installation and configuration instructions for the [Maps extension](README.md).

## Versions

<table>
	<tr>
		<th></th>
		<th>Status</th>
		<th>Release date</th>
		<th>Git branch</th>
	</tr>
	<tr>
		<th><a href="RELEASE-NOTES.md">Maps 3.6.x</a></th>
		<td>Development version</td>
		<td>Future</td>
		<td><a href="https://github.com/JeroenDeDauw/Maps/tree/master">master</a></td>
	</tr>
	<tr>
		<th><a href="RELEASE-NOTES.md">Maps 3.5.x</a></th>
		<td>Stable release</td>
		<td>2016-04-01</td>
		<td><a href="https://github.com/JeroenDeDauw/Maps/tree/3.5.0">3.5.0</a></td>
	</tr>
	<tr>
		<th><a href="RELEASE-NOTES.md#maps-341">Maps 3.4.1</a></th>
		<td>Obsolete release</td>
		<td>2016-01-30</td>
		<td><a href="https://github.com/JeroenDeDauw/Maps/tree/3.4.1">3.4.1</a></td>
	</tr>
	<tr>
		<th><a href="RELEASE-NOTES.md#maps-34">Maps 3.4</a></th>
		<td>Obsolete release</td>
		<td>2015-07-25</td>
		<td><a href="https://github.com/JeroenDeDauw/Maps/tree/3.4.0">3.4.0</a></td>
	</tr>
	<tr>
		<th><a href="RELEASE-NOTES.md#maps-33">Maps 3.3</a></th>
		<td>Obsolete release</td>
		<td>2015-06-29</td>
		<td><a href="https://github.com/JeroenDeDauw/Maps/tree/3.3.0">3.3.0</a></td>
	</tr>
	<tr>
		<th><a href="RELEASE-NOTES.md#maps-32">Maps 3.2</a></th>
		<td>Obsolete release</td>
		<td>2014-09-12</td>
		<td><a href="https://github.com/JeroenDeDauw/Maps/tree/3.2.0">3.2.0</a></td>
	</tr>
	<tr>
		<th><a href="RELEASE-NOTES.md#maps-31">Maps 3.1</a></th>
		<td>Obsolete release</td>
		<td>2014-06-30</td>
		<td><a href="https://github.com/JeroenDeDauw/Maps/tree/3.1">3.1.0</a></td>
	</tr>
	<tr>
		<th><a href="RELEASE-NOTES.md#maps-301">Maps 3.0.1</a></th>
		<td>Obsolete release</td>
		<td>2014-03-27</td>
		<td><a href="https://github.com/JeroenDeDauw/Maps/tree/3.0.1">3.0.1</a></td>
	</tr>
	<tr>
		<th><a href="RELEASE-NOTES.md#maps-20-2012-10-05">Maps 2.0.x</a></th>
		<td>Obsolete release</td>
		<td>2012-12-13</td>
		<td><a href="https://github.com/JeroenDeDauw/Maps/tree/2.0.x">2.0.x</a></td>
	</tr>
	<tr>
		<th><a href="RELEASE-NOTES.md#maps-105-2011-11-30">Maps 1.0.5</a></th>
		<td>Obsolete release</td>
		<td>2011-11-30</td>
		<td><a href="https://github.com/JeroenDeDauw/Maps/tree/1.0.5">1.0.5</a></td>
	</tr>
</table>

### Platform compatibility

<table>
	<tr>
		<th></th>
		<th>PHP</th>
		<th>MediaWiki</th>
		<th>Composer</th>
		<th>Validator</th>
	</tr>
	<tr>
		<th>Maps 3.6.x</th>
		<td><strong>5.5</strong> - 7.x</td>
		<td><strong>1.23</strong> - 1.27</td>
		<td>Required</td>
		<td>2.x (handled by Composer)</td>
	</tr>
	<tr>
		<th>Maps 3.5.x</th>
		<td>5.3.2 - 7.x</td>
		<td>1.18 - 1.27</td>
		<td>Required</td>
		<td>2.x (handled by Composer)</td>
	</tr>
	<tr>
		<th>Maps 3.4.x</th>
		<td>5.3.2 - 7.x</td>
		<td>1.18 - 1.27</td>
		<td>Required</td>
		<td>2.x (handled by Composer)</td>
	</tr>
	<tr>
		<th>Maps 3.3.x</th>
		<td>5.3.2 - 5.6.x</td>
		<td>1.18 - 1.25</td>
		<td>Required</td>
		<td>2.x (handled by Composer)</td>
	</tr>
	<tr>
		<th>Maps 3.1.x & 3.2.x</th>
		<td>5.3.2 - 5.6.x</td>
		<td>1.18 - 1.24</td>
		<td>Required</td>
		<td>2.x (handled by Composer)</td>
	</tr>
	<tr>
		<th>Maps 3.0.x</th>
		<td>5.3.2 - 5.6.x</td>
		<td>1.18 - 1.23</td>
		<td>Required</td>
		<td>1.x (handled by Composer)</td>
	</tr>
	<tr>
		<th>Maps 2.0.x</th>
		<td><strong>5.3.2</strong> - 5.5.x</td>
		<td><strong>1.18</strong> - 1.23</td>
		<td>Not supported</td>
		<td>0.5.1</td>
	</tr>
	<tr>
		<th>Maps 1.0.5</th>
		<td>5.2.0 - 5.3.x</td>
		<td>1.17 - 1.19</td>
		<td>Not supported</td>
		<td>0.4.13 or 0.4.14</td>
	</tr>
</table>

When installing Maps 2.x, see the installation instructions that come bundled with it. Also
make use of Validator 0.5.x. More recent versions of Validator will not work.

### Database support

All current versions of Maps have full support for all databases that can be used with MediaWiki.

## Download and installation

Go to the root directory of your MediaWiki installation.

If you have previously installed Composer skip to step 2.

To install Composer, just download http://getcomposer.org/composer.phar into your
current directory.

    wget http://getcomposer.org/composer.phar

#### Step 2

Now using Composer, install Maps

    php composer.phar require mediawiki/maps "*"

#### Verify installation success

As final step, you can verify Maps got installed by looking at the Special:Version page on your wiki and verifying the
Maps extension is listed.

#### Custom image layers support (experimental)

For support of the experimental custom image layers feature you have to run the MediaWiki update script.

    php maintenance/update.php

## Configuration

See the [Maps settings file](Maps_Settings.php) for the available configuration options.

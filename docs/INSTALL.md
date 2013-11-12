# Maps installation

These are the installation and configuration instructions for the [Maps extension](../README.md).

## Versions

<table>
	<tr>
		<th></th>
		<th>Status</th>
		<th>Release date</th>
		<th>PHP</th>
		<th>MediaWiki</th>
		<th>Git branch</th>
	</tr>
	<tr>
		<th><a href="https://github.com/JeroenDeDauw/Maps/blob/master/docs/RELEASE-NOTES.md">Maps 3.x</a></th>
		<td>Development version</td>
		<td>Future</td>
		<td>5.3.2+</td>
		<td>1.18+</td>
		<td><a href="https://github.com/JeroenDeDauw/Maps/tree/master">master</a></td>
	</tr>
	<tr>
		<th><a href="https://github.com/JeroenDeDauw/Maps/blob/master/docs/RELEASE-NOTES.md#maps-20-2012-10-05">Maps 2.0.x</a></th>
		<td>Stable release</td>
		<td>2012</td>
		<td>5.3.2+</td>
		<td>1.18+</td>
		<td><a href="https://github.com/JeroenDeDauw/Maps/tree/2.0.x">2.0.x</a></td>
	</tr>
	<tr>
		<th><a href="https://github.com/JeroenDeDauw/Maps/blob/master/docs/RELEASE-NOTES.md#maps-105-2011-11-30">Maps 1.0.5</a></th>
		<td>Legacy release</td>
		<td>2011-11-30</td>
		<td>5.2+</td>
		<td>1.17+</td>
		<td><a href="https://github.com/JeroenDeDauw/Maps/tree/1.0.5">1.0.5</a></td>
	</tr>
</table>



## Download and installation

### With Composer

The recommended way to install Maps is with [Composer](http://getcomposer.org).
See the [extension installation with Composer](https://www.mediawiki.org/wiki/Composer) instructions.

The package name is "mediawiki/maps", so your composer.json file should look as follows:

```javascript
{
	"require": {
		// ...
		"mediawiki/maps": ">=1.0"
	},
	"minimum-stability" : "dev"
}
```

The "minimum-stability" section needs to be added as well for now.
This need for this will be removed when Maps 3.0 is released.

### Manual installation

Alternatively you can obtain the Maps code and the code of all its dependencies yourself, and load them all.

The latest version of Maps requires:

* ParamProcessor 1.0 or later
* DataValuesInterfaces 0.1 or later
* DataValuesCommon 0.1 or later

You can get the Maps code itself:

* Via git: git clone https://github.com/JeroenDeDauw/Maps.git
* As Tarball: https://github.com/JeroenDeDauw/Maps/releases

The only remaining step is to include SubPageList in your LocalSettings.php file:

```php
require_once( "$IP/extensions/Maps/Maps.php" );
```

## Configuration

See the [Maps settings file](../Maps_Settings.php) for the available configuartion options.

# Maps installation

These are the installation and configuration instructions for the [Maps extension](README.md).

**Table of contents**

* [Download and installation](INSTALL.md#download-and-installation)
* [Configuration](INSTALL.md#configuration)
* [Platform compatibility and release status](INSTALL.md#platform-compatibility-and-release-status)

## Download and installation

Maps is installed and upgraded via Composer. For a detailed explanation see
[installing MediaWiki extensions with Composer](https://www.mediawiki.org/wiki/Composer/For_extensions).

In short:

* Edit `composer.local.json` (preferred) or `composer.json` by adding `mediawiki/maps` to the `require` section
* Choose the version constraint. Typically you want to pick `^x.y`, where `x.y` is the latest minor version of Maps receiving only backwards-compatible code changes
* Run `composer update` or `php composer.phar update` depending on how you installed Composer.

For upgrading, simply edit the `composer.local.json` or `composer.json` and update the version constraint. Then run `composer update`.

Example of a `require` section just with Maps:

```json
    "require": {
        "mediawiki/maps": "^5"
    }
```

If you would also like to make use of the semantic functionality Maps provides you also need to install Semantic MediaWiki. In this case the example `require` section with both Maps and Semantic MediaWiki looks like this:

```json
    "require": {
        "mediawiki/maps": "^5",
        "mediawiki/semantic-media-wiki": "^2.5"
    }
```

You will need a comma behind each version constraint except the last one.

**Verify installation success**

As final step, you can verify Maps got installed by looking at the Special:Version page on your wiki
and verifying the Maps extension is listed.

If you want to use the Semantic MediaWiki integration, you will also need to install Semantic MediaWiki.

## Configuration

Maps works out of the box without any configuration work being required. Below are some key configuration
options that you might want to change. See the [Maps settings file](Maps_Settings.php) for all available
configuration options.

Like in most MediaWiki extensions, configuration is done by placing small snippets of PHP code at the
bottom of your wikis LocalSettings.php file.

### Choosing the mapping service

The Maps extension supports displaying maps using multiple mapping services, including Google Maps,
Leaflet and OpenLayers. At present the default is Leaflet, while in older versions of the extension
it was Google Maps.

If you do not wish to use the default, use the `$GLOBALS['egMapsDefaultService']` setting. Example:

For OpenLayers:

`$GLOBALS['egMapsDefaultService'] = 'openlayers';`

For Leaflet:

`$GLOBALS['egMapsDefaultService'] = 'leaflet';`

For Google Maps:

`$GLOBALS['egMapsDefaultService'] = 'googlemaps3';`

### Required configuration for Google Maps

When using Google Maps, you will need to provide your
[Google API key](https://developers.google.com/maps/documentation/javascript/get-api-key).

`$GLOBALS['egMapsGMaps3ApiKey'] = 'your-api-key';`

### Choosing the geocoding service

The Maps extension supports [geocoding](https://www.semantic-mediawiki.org/wiki/Maps/Geocoding),
the conversion of human readable addresses to coordinates. This is done via a webservice used for each
map displayed on your wiki. By default Maps uses [Nominatim](https://wiki.openstreetmap.org/wiki/Nominatim).
To use a different geocoding service, use the `$GLOBALS['settingegMapsDefaultGeoService']`.

`$GLOBALS['egMapsDefaultGeoService'] = 'google';` 

## Platform compatibility and release status

The PHP and MediaWiki version ranges listed are those in which Maps is known to work. It might also
work with more recent versions of PHP and MediaWiki, though this is not guaranteed. Increases of
minimum requirements are indicated in bold. For a detailed list of changes, see the [release notes](RELEASE-NOTES.md).

<table>
	<tr>
		<th></th>
		<th>PHP</th>
		<th>MediaWiki</th>
		<th>Semantic MediaWiki</th>
		<th>Release status</th>
	</tr>
	<tr>
		<th>Maps 5.7.x</th>
		<td>7.0 - 7.3+</td>
		<td>1.27 - 1.31+</td>
		<td>2.1 - 2.5</td>
		<td>Planned Q3 2018</td>
	</tr>
	<tr>
		<th>Maps 5.6.x</th>
		<td>7.0 - 7.3</td>
		<td>1.27 - 1.31</td>
		<td>2.1 - 2.5</td>
		<td><strong>Stable release</strong></td>
	</tr>
	<tr>
		<th>Maps 5.5.x</th>
		<td>7.0 - 7.3</td>
		<td>1.27 - 1.31</td>
		<td>2.1 - 2.5</td>
		<td>Obsolete release, no support</td>
	</tr>
	<tr>
		<th>Maps 5.4.x</th>
		<td>7.0 - 7.3</td>
		<td>1.27 - 1.31</td>
		<td>2.1 - 2.5</td>
		<td>Obsolete release, no support</td>
	</tr>
	<tr>
		<th>Maps 5.3.x</th>
		<td>7.0 - 7.2</td>
		<td>1.27 - 1.30</td>
		<td>2.1 - 2.5</td>
		<td>Obsolete release, no support</td>
	</tr>
	<tr>
		<th>Maps 5.2.x</th>
		<td>7.0 - 7.2</td>
		<td>1.27 - 1.30</td>
		<td>2.1 - 2.5</td>
		<td>Obsolete release, no support</td>
	</tr>
	<tr>
		<th>Maps 5.1.x</th>
		<td><strong>7.0</strong> - 7.2</td>
		<td>1.27 - 1.30</td>
		<td>2.1 - 2.5</td>
		<td>Obsolete release, no support</td>
	</tr>
	<tr>
		<th>Maps 5.0.x</th>
		<td>5.6 - 7.1</td>
		<td>1.27 - 1.30</td>
		<td>2.1 - 2.5</td>
		<td>Obsolete release, no support</td>
	</tr>
	<tr>
		<th>Maps 4.4.x</th>
		<td>5.6 - 7.1</td>
		<td>1.27 - 1.29</td>
		<td>2.1 - 2.5</td>
		<td>Obsolete release, no support</td>
	</tr>
	<tr>
		<th>Maps 4.3.x</th>
		<td><strong>5.6</strong> - 7.1</td>
		<td><strong>1.27</strong> - 1.29</td>
		<td>2.1 - 2.5</td>
		<td>Obsolete release, no support</td>
	</tr>
	<tr>
		<th>Maps 4.2.x</th>
		<td>5.5 - 7.1</td>
		<td>1.23 - 1.29</td>
		<td>2.1 - 2.5</td>
		<td>Obsolete release, no support</td>
	</tr>
	<tr>
		<th>Maps 4.1.x</th>
		<td>5.5 - 7.1</td>
		<td>1.23 - 1.28</td>
		<td>2.1 - 2.5</td>
		<td>Obsolete release, no support</td>
	</tr>
	<tr>
		<th>Maps 4.0.x</th>
		<td>5.5 - 7.0</td>
		<td>1.23 - 1.28</td>
		<td>2.1 - 2.4</td>
		<td>Obsolete release, no support</td>
	</tr>
</table>

Older obsolete versions:

<table>
	<tr>
		<th></th>
		<th>PHP</th>
		<th>MediaWiki</th>
		<th>Composer</th>
		<th>Validator</th>
	</tr>
		<tr>
		<th>Maps 3.8.x</th>
		<td>5.5 - 7.0</td>
		<td>1.23 - 1.27</td>
		<td>Required</td>
		<td>Handled by Composer</td>
	</tr>
	<tr>
		<th>Maps 3.7.x</th>
		<td>5.5 - 7.0</td>
		<td>1.23 - 1.27</td>
		<td>Required</td>
		<td>Handled by Composer</td>
	</tr>
	<tr>
		<th>Maps 3.6.x</th>
		<td><strong>5.5</strong> - 7.0</td>
		<td><strong>1.23</strong> - 1.27</td>
		<td>Required</td>
		<td>Handled by Composer</td>
	</tr>
	<tr>
		<th>Maps 3.5.x</th>
		<td>5.3.2 - 7.0</td>
		<td>1.18 - 1.27</td>
		<td>Required</td>
		<td>Handled by Composer</td>
	</tr>
	<tr>
		<th>Maps 3.4.x</th>
		<td>5.3.2 - 7.0</td>
		<td>1.18 - 1.27</td>
		<td>Required</td>
		<td>Handled by Composer</td>
	</tr>
	<tr>
		<th>Maps 3.3.x</th>
		<td>5.3.2 - 5.6.x</td>
		<td>1.18 - 1.25</td>
		<td>Required</td>
		<td>Handled by Composer</td>
	</tr>
	<tr>
		<th>Maps 3.2.x</th>
		<td>5.3.2 - 5.6.x</td>
		<td>1.18 - 1.24</td>
		<td>Required</td>
		<td>Handled by Composer</td>
	</tr>
	<tr>
		<th>Maps 3.1.x</th>
		<td>5.3.2 - 5.6.x</td>
		<td>1.18 - 1.24</td>
		<td>Required</td>
		<td>Handled by Composer</td>
	</tr>
	<tr>
		<th>Maps 3.0.x</th>
		<td>5.3.2 - 5.6.x</td>
		<td>1.18 - 1.23</td>
		<td>Required</td>
		<td>Handled by Composer</td>
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

### Database support

All current versions of Maps have full support for all databases that can be used with MediaWiki.

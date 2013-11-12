# Maps installation

These are the installation and configuration instructions for the [Maps extension](../README.md).

## Dependencies

<table>
	<tr>
		<th></th>
		<th>Status</th>
		<th>PHP</th>
		<th>MediaWiki</th>
		<th>ParamProcessor (Validator)</th>
		<th>DataValues</th>
	</tr>
	<tr>
		<th>Maps 3.x</th>
		<td>Development version</td>
		<td>5.3.2+</td>
		<td>1.18+</td>
		<td>1.0+</td>
		<td>0.1+</td>
	</tr>
	<tr>
		<th>Maps 2.x</th>
		<td>Stable release</td>
		<td>5.3.2+</td>
		<td>1.18+</td>
		<td>0.5.x</td>
		<td>Not needed</td>
	</tr>
	<tr>
		<th>Maps 1.0.5</th>
		<td>Legacy release</td>
		<td>5.2+</td>
		<td>1.17+</td>
		<td>0.4.12+ &lt;1.0</td>
		<td>Not needed</td>
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
}
```

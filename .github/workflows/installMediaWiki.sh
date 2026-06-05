#! /bin/bash

set -ex

MW_BRANCH=$1
EXTENSION_NAME=$2
SMW_VERSION=$3

wget "https://github.com/wikimedia/mediawiki/archive/refs/heads/$MW_BRANCH.tar.gz" -nv

tar -zxf $MW_BRANCH.tar.gz
mv mediawiki-$MW_BRANCH mediawiki

cd mediawiki

composer install --no-progress --no-interaction --prefer-dist --optimize-autoloader

php maintenance/install.php \
	--dbtype sqlite \
	--dbuser root \
	--dbname mw \
	--dbpath "$(pwd)" \
	--scriptpath="" \
	--pass AdminPassword WikiName AdminUser

echo 'error_reporting(E_ALL| E_STRICT);' >> LocalSettings.php
echo 'ini_set("display_errors", 1);' >> LocalSettings.php
echo '$wgShowExceptionDetails = true;' >> LocalSettings.php
echo '$wgShowDBErrorBacktrace = true;' >> LocalSettings.php
echo '$wgDevelopmentWarnings = true;' >> LocalSettings.php

# Optionally load Semantic MediaWiki before the extension under test, so that the
# extension's Semantic MediaWiki integration registers and the tests that exercise
# it actually run instead of self-skipping.
SMW_REQUIRE=""
if [ -n "$SMW_VERSION" ]; then
	echo 'wfLoadExtension( "SemanticMediaWiki" );' >> LocalSettings.php
	SMW_REQUIRE="\"mediawiki/semantic-media-wiki\": \"$SMW_VERSION\""
fi

echo 'wfLoadExtension( "'$EXTENSION_NAME'" );' >> LocalSettings.php

cat <<EOT >> composer.local.json
{
  	"require": { $SMW_REQUIRE },
	"extra": {
		"merge-plugin": {
			"merge-dev": true,
			"include": [
				"extensions/$EXTENSION_NAME/composer.json"
			]
		}
	}
}
EOT

#! /bin/bash

set -x

originalDirectory=$(pwd)

cd ..

wget https://github.com/wikimedia/mediawiki-core/archive/$MW.tar.gz
tar -zxf $MW.tar.gz
mv mediawiki-$MW phase3

cd phase3

composer install --prefer-source

if [ "$DB" == "postgres" ]
then
	psql -c 'create database its_a_mw;' -U postgres
	php maintenance/install.php --dbtype $DBTYPE --dbuser postgres --dbname its_a_mw --pass nyan TravisWiki admin --scriptpath /TravisWiki
else
	mysql -e 'create database its_a_mw;'
	php maintenance/install.php --dbtype $DBTYPE --dbuser root --dbname its_a_mw --dbpath $(pwd) --pass nyan TravisWiki admin --scriptpath /TravisWiki
fi

cd extensions
cp -r $originalDirectory Maps
cd Maps
composer install --prefer-source
cd ..
cd ..

if [ ! -z $SMW ]
then
	composer require "mediawiki/semantic-media-wiki=$SMW" --prefer-source
fi

cat <<EOT >> composer.local.json
{
	"extra": {
		"merge-plugin": {
			"merge-dev": true,
			"include": [
				"extensions/*/composer.json"
			]
		}
	}
}
EOT

composer install --prefer-source

echo 'wfLoadExtension( "Maps" );' >> LocalSettings.php

if [ ! -z $SMW ]
then
	echo 'wfLoadExtension( "SemanticMediaWiki" );' >> LocalSettings.php
fi

echo 'error_reporting(E_ALL| E_STRICT);' >> LocalSettings.php
echo 'ini_set("display_errors", 1);' >> LocalSettings.php
echo '$wgShowExceptionDetails = true;' >> LocalSettings.php
echo '$wgShowDBErrorBacktrace = true;' >> LocalSettings.php
echo '$wgDevelopmentWarnings = true;' >> LocalSettings.php
echo "putenv( 'MW_INSTALL_PATH=$(pwd)' );" >> LocalSettings.php

php maintenance/update.php --quick

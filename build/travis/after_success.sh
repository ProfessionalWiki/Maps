#! /bin/bash

set -x

if [ "$MW-$DBTYPE" == "master-mysql" ]
then
	cd ../../extensions/SemanticMaps
	composer require satooshi/php-coveralls:dev-master
	php vendor/bin/coveralls -v
fi
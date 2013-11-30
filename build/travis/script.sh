#! /bin/bash

set -x

cd ../phase3/extensions/SemanticMaps

if [ "$MW-$DBTYPE" == "master-mysql" ]
then
	phpunit --coverage-clover ../../extensions/SemanticMaps/build/logs/clover.xml
else
	phpunit
fi
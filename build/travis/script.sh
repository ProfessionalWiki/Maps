#! /bin/bash

set -x

cd ../phase3/extensions/Maps

if [ "$TYPE" == "coverage" ]
then
	phpunit --coverage-clover ../../extensions/Maps/build/logs/clover.clover
else
	phpunit
fi
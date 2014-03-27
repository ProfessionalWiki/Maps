#! /bin/bash

set -x

originalDirectory=$(pwd)

if [ "$TYPE" == "coverage" ]
then
	wget https://scrutinizer-ci.com/ocular.phar
	echo $originalDirectory/build/coverage.clover
	du -hs $originalDirectory/build/coverage.clover
	php ocular.phar code-coverage:upload --format=php-clover $originalDirectory/build/coverage.clover
fi
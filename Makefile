.PHONY: ci test cs phpunit phpcs stan stan-baseline psalm psalm-baseline baseline

ci: test cs
test: phpunit stan
cs: phpcs

phpunit:
	php ../../tests/phpunit/phpunit.php -c phpunit.xml.dist

phpcs:
	cd ../.. && vendor/bin/phpcs -p -s --standard=$(shell pwd)/phpcs.xml

stan:
	../../vendor/bin/phpstan analyse --configuration=phpstan.neon --memory-limit=2G

stan-baseline:
	../../vendor/bin/phpstan analyse --configuration=phpstan.neon --memory-limit=2G --generate-baseline

psalm:
	../../vendor/bin/psalm --config=psalm.xml

psalm-baseline:
	../../vendor/bin/psalm --set-baseline=psalm-baseline.xml

baseline: stan-baseline psalm-baseline

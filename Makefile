.PHONY: ci test cs phpunit phpcs stan stan-baseline psalm psalm-baseline baseline

ci: test cs
test: phpunit
cs: phpcs stan

phpunit:
	php ../../tests/phpunit/phpunit.php -c phpunit.xml.dist

phpcs:
	vendor/bin/phpcs -p -s

stan:
	vendor/bin/phpstan analyse --configuration=phpstan.neon --memory-limit=2G

stan-baseline:
	vendor/bin/phpstan analyse --configuration=phpstan.neon --memory-limit=2G --generate-baseline

psalm:
	vendor/bin/psalm

psalm-baseline:
	vendor/bin/psalm --set-baseline=psalm-baseline.xml

baseline: stan-baseline psalm-baseline

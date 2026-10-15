.PHONY: cs-fix

cs-fix:
	PHP_CS_FIXER_IGNORE_ENV=1 ./vendor/bin/php-cs-fixer fix

test:
	./vendor/bin/phpcs --standard=ruleset.xml
	./vendor/bin/php-cs-fixer fix --dry-run -v --using-cache=no --allow-risky=yes

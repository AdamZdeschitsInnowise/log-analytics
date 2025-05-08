start:
	cd ./docker && docker compose up -d

stop:
	cd ./docker && docker compose down

install:
	cd ./docker && docker compose exec php-cli composer install
	cd ./docker && docker compose exec php-cli bin/console doctrine:migrations:migrate --no-interaction
	cd ./docker && docker compose exec php-cli bin/console doctrine:migrations:migrate --no-interaction --env=test

copy-env:
	cp ./docker/.env.example ./docker/.env
	cp ./app/.env.example ./app/.env

sh:
	cd ./docker && docker compose exec -it -u 1000:1000 php-cli bash -l

import:
	cd ./docker && docker compose exec -u 1000:1000 php-cli bin/console app:import-logs /app/import/logs.log

consume:
	cd ./docker && docker compose exec -u 1000:1000 php-cli bin/console messenger:consume

test:
	cd ./docker && docker compose exec -u 1000:1000 php-cli bin/phpunit

analyze:
	cd ./docker && docker compose exec -it -u 1000:1000 php-cli ./vendor/bin/phpstan analyze

fixer:
	cd ./docker && docker compose exec -it -u 1000:1000 -e PHP_CS_FIXER_IGNORE_ENV=1 php-cli ./vendor/bin/php-cs-fixer fix --show-progress=dots --config=.php-cs-fixer.php
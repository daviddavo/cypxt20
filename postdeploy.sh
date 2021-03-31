#!/usr/bin/bash

set -eux

yarn install --no-dev
composer install --no-dev --optimize-autoloader --no-interaction --no-plugins --no-scripts --no-suggest

bin/console doctrine:migrations:diff --allow-empty-diff
bin/console doctrine:migrations:migrate -n --allow-no-migration
bin/console cache:clear
bin/console cache:warmup

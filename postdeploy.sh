#!/usr/bin/bash

set -eux

# Not really needed
# yarn install --no-dev
composer install --no-dev --optimize-autoloader --no-interaction

bin/console doctrine:migrations:status
bin/console doctrine:migrations:diff --allow-empty-diff
bin/console doctrine:migrations:migrate -n --allow-no-migration
bin/console cache:warmup

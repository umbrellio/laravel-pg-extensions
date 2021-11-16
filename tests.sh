#!/usr/bin/env bash

psql postgres -U postgres -tc "SELECT 1 FROM pg_database WHERE datname = 'testing'" | grep -q 1 || psql postgres -U postgres -c "CREATE DATABASE testing"
sed -e "s/\${USERNAME}/postgres/" \
    -e "s/\${PASSWORD}//" \
    -e "s/\${DATABASE}/testing/" \
    -e "s/\${HOST}/127.0.0.1/" \
    phpunit.xml.dist > phpunit.xml
COMPOSER_MEMORY_LIMIT=-1 composer update
composer lint
if [ "x$EXCLUDE_GROUP" != "x" ]; then
    php -d pcov.directory='.' vendor/bin/phpunit \
      --exclude-group $EXCLUDE_GROUP \
      --coverage-html build \
      --coverage-text
else
    php -d pcov.directory='.' vendor/bin/phpunit \
      --exclude-group WithoutSchema,forPHP7 \
      --coverage-html build \
      --coverage-text
fi

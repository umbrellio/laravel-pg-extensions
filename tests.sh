#!/usr/bin/env bash

psql postgres -U postgres -tc "SELECT 1 FROM pg_database WHERE datname = 'testing'" | grep -q 1 || psql postgres -U postgres -c "CREATE DATABASE testing"
sed -e "s/\${USERNAME}/postgres/" \
    -e "s/\${PASSWORD}//" \
    -e "s/\${DATABASE}/testing/" \
    -e "s/\${HOST}/127.0.0.1/" \
    phpunit.xml.dist > phpunit.xml
COMPOSER_MEMORY_LIMIT=-1 composer update
composer lint
php -d xdebug.mode=coverage -d memory_limit=-1 vendor/bin/phpunit --coverage-html build --coverage-text

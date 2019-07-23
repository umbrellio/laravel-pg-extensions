#!/usr/bin/env bash

psql postgres -U user -tc "SELECT 1 FROM pg_database WHERE datname = 'testing'" | grep -q 1 || psql postgres -U user -c "CREATE DATABASE testing"
composer lint
php -d pcov.directory='.' vendor/bin/phpunit --coverage-html build

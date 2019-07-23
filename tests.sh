#!/usr/bin/env bash

#psql -c 'CREATE DATABASE testing;' -U postgres
php -d pcov.directory='.' vendor/bin/phpunit --coverage-html build

#!/bin/sh
set -e

cp .env.example .env
php artisan key:generate --ansi

php artisan migrate --force --seed --ansi

exec php-fpm

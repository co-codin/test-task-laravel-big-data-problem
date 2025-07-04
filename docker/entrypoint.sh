#!/bin/sh
set -e

cp .env.example .env
php artisan key:generate --ansi

php artisan migrate --ansi

exec php-fpm

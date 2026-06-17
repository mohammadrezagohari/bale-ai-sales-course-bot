#!/usr/bin/env bash
set -euo pipefail

APP_DIR=${APP_DIR:-/var/www/course-bale-bot/current}

cd "$APP_DIR"

composer install --no-dev --prefer-dist --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

sudo systemctl reload php8.3-fpm
sudo systemctl reload nginx

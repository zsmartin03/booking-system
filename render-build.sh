#!/usr/bin/env bash
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
composer install
php artisan key:generate
npm install && npm run build
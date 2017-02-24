#!/bin/sh
php artisan migrate:refresh
composer dump-autoload
php artisan db:seed


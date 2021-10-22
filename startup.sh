#!/bin/bash

composer update
php artisan key:generate
php artisan jwt:secret
php artisan migrate:fresh --seed

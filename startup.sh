#!/bin/bash

composer update
php artisan key:generate
php artisan migrate:fresh --seed

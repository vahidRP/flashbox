<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    $user = \App\Models\User::where('email', 'admin@flashbox.com')->first();
    \Illuminate\Support\Facades\Auth::login($user);
    dd(\Illuminate\Support\Facades\Gate::has('super-admin'));
    return $router->app->version();
});

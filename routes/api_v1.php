<?php

/**
 * |--------------------------------------------------------------------------
 * | Application Routes
 * |--------------------------------------------------------------------------
 * |
 * | Here is where you can register all of the routes for an application.
 * | It is a breeze. Simply tell Lumen the URIs it should respond to
 * | and give it the Closure to call when that URI is requested.
 * |
 *
 * @var Laravel\Lumen\Routing\Router $router
 */

use Laravel\Lumen\Routing\Router;

$router->group(['middleware' => ['auth:api', 'throttleWithRedis:120,1']], function(Router $router){
    $router->group(['prefix' => 'auth'], function(Router $router){
        $router->post('logout', 'AuthController@logout');
        $router->get('me', 'AuthController@me');
    });

});

$router->group(['middleware' => ['guest']], function(Router $router){
    $router->group(['middleware' => 'throttleWithRedis:20,1'], function(Router $router){
        $router->post('auth/login', 'AuthController@login');
    });
});

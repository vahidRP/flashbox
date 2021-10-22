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

    $models = [
        \App\Models\Address::class,
        \App\Models\Permission::class,
        \App\Models\Product::class,
        \App\Models\Receipt::class,
        \App\Models\Role::class,
        \App\Models\Store::class,
        \App\Models\User::class,
    ];
    foreach($models as $model){
        $baseModel = class_basename($model);
        $router->group(['prefix' => route_slug($baseModel)], function(Router $router) use ($model, $baseModel){
            $createRoles = ['admin'];
            $readRoles = ['admin'];
            $updateRoles = ['admin'];
            $deleteRoles = ['admin'];
            $attachRoles = ['admin'];
            $detachRoles = ['admin'];

            switch($model){
                case \App\Models\Address::class:
                case \App\Models\Product::class:
                case \App\Models\Receipt::class:
                case \App\Models\Store::class:
                    $createRoles[] = 'seller';
                    $readRoles[] = 'seller';
                    $updateRoles[] = 'seller';
                    $deleteRoles[] = 'seller';
                    $attachRoles[] = 'seller';
                    $detachRoles[] = 'seller';
                    break;
            }

            switch($model){
                case \App\Models\Address::class:
                    $createRoles[] = 'customer';
                    $readRoles[] = 'customer';
                    $updateRoles[] = 'customer';
                    $deleteRoles[] = 'customer';
                    $attachRoles[] = 'customer';
                    $detachRoles[] = 'customer';
                    break;
                case \App\Models\Receipt::class:
                    $readRoles[] = 'customer';
                    $attachRoles[] = 'customer';
                    $detachRoles[] = 'customer';
                    break;
                case \App\Models\Product::class:
                case \App\Models\Store::class:
                    $readRoles[] = 'customer';
                    break;
            }

            $createRoles = array_unique($createRoles);
            $readRoles = array_unique($readRoles);
            $updateRoles = array_unique($updateRoles);
            $deleteRoles = array_unique($deleteRoles);
            $attachRoles = array_unique($attachRoles);
            $detachRoles = array_unique($detachRoles);

            $router->get(' / ', [
                'uses'       => "{$baseModel}Controller@index",
                'middleware' => ('role:' . implode(',', $readRoles))
            ]);
            $router->post(' / ', [
                'uses'       => "{$baseModel}Controller@store",
                'middleware' => ('role:' . implode(',', $createRoles))
            ]);
            $router->get('{id}', [
                'uses'       => "{$baseModel}Controller@show",
                'middleware' => ('role:' . implode(',', $readRoles))
            ]);
            $router->post('{id}/attach', [
                'uses'       => "{$baseModel}Controller@attach",
                'middleware' => ('role:' . implode(',', $attachRoles))
            ]);
            $router->post('{id}/detach', [
                'uses'       => "{$baseModel}Controller@detach",
                'middleware' => ('role:' . implode(',', $detachRoles))
            ]);
            $router->post('{id}', [
                'uses'       => "{$baseModel}Controller@update",
                'middleware' => ('role:' . implode(',', $updateRoles))
            ]);
            $router->delete('{id}', [
                'uses'       => "{$baseModel}Controller@destroy",
                'middleware' => ('role:' . implode(',', $deleteRoles))
            ]);
        });
    }

    $router->group(['middleware' => 'role:customer'], function($router){
        $baseModel = class_basename(\App\Models\Receipt::class);
        $router->post(route_slug($baseModel) . '/pay/{id}', "{$baseModel}Controller@pay");
    });
});

$router->group(['middleware' => ['guest']], function(Router $router){
    $router->group(['middleware' => 'throttleWithRedis:20,1'], function(Router $router){
        $router->post('auth / login', 'AuthController@login');
    });

});

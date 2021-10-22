<?php

require_once __DIR__ . '/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(dirname(__DIR__)))->bootstrap();

date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(dirname(__DIR__));

$app->withFacades();

$app->withEloquent();

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(Illuminate\Contracts\Debug\ExceptionHandler::class, App\Exceptions\Handler::class);

$app->singleton(Illuminate\Contracts\Console\Kernel::class, App\Console\Kernel::class);

/*
|--------------------------------------------------------------------------
| Register Config Files
|--------------------------------------------------------------------------
|
| Now we will register the "app" configuration file. If the file exists in
| your configuration directory it will be loaded; otherwise, we'll load
| the default version. You may register other files below as needed.
|
*/

$app->configure('app');
$app->configure('jwt');
$app->configure('repositories');

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

// $app->middleware([
//     App\Http\Middleware\ExampleMiddleware::class
// ]);

$app->routeMiddleware([
    'auth' => App\Http\Middleware\Authenticate::class,
    'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
    'throttle' => App\Http\Middleware\ThrottleRequests::class,
    'throttleWithRedis' => App\Http\Middleware\ThrottleRequestsWithRedis::class,
    'role' => \App\Http\Middleware\RoleMiddleware::class,
]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

$app->register(App\Providers\AppServiceProvider::class);
$app->register(App\Providers\AuthServiceProvider::class);
$app->register(App\Providers\EventServiceProvider::class);

$app->register(\Illuminate\Redis\RedisServiceProvider::class);
$app->register(Tymon\JWTAuth\Providers\LumenServiceProvider::class);
$app->register(\App\Support\Repository\RepositoryServiceProvider::class);

if(env('APP_ENV') === 'local'){
    $app->register(Flipbox\LumenGenerator\LumenGeneratorServiceProvider::class);
    $app->register(Irazasyed\Larasupport\Providers\ArtisanServiceProvider::class);
}

$app->bind(\App\Repositories\Interfaces\AddressRepositoryInterface::class, \App\Repositories\Eloquent\AddressRepository::class);
$app->bind(\App\Repositories\Interfaces\PermissionRepositoryInterface::class, \App\Repositories\Eloquent\PermissionRepository::class);
$app->bind(\App\Repositories\Interfaces\ProductRepositoryInterface::class, \App\Repositories\Eloquent\ProductRepository::class);
$app->bind(\App\Repositories\Interfaces\ReceiptRepositoryInterface::class, \App\Repositories\Eloquent\ReceiptRepository::class);
$app->bind(\App\Repositories\Interfaces\RoleRepositoryInterface::class, \App\Repositories\Eloquent\RoleRepository::class);
$app->bind(\App\Repositories\Interfaces\StoreRepositoryInterface::class, \App\Repositories\Eloquent\StoreRepository::class);
$app->bind(\App\Repositories\Interfaces\UserRepositoryInterface::class, \App\Repositories\Eloquent\UserRepository::class);

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    $router->group([
        'namespace' => 'Api',
        'prefix' => 'api'
    ], function ($router) {
        $router->group([
            'namespace' => 'V1',
            'prefix' => 'v1',
        ], function ($router) {
            require __DIR__ . '/../routes/api_v1.php';
        });
    });

    require __DIR__ . '/../routes/web.php';
});


return $app;

<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        $this->bootPolicies();
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function($request){
            if($request->input('email')){
                return User::where('email', $request->input('email'))->first();
            }
        });
    }

    protected function bootPolicies()
    {
        $finder = new \Symfony\Component\Finder\Finder();
        $finder->files()->in(app_path('Policies'));
        foreach($finder as $file){
            if(empty($file->getRelativePath())){
                $baseName = $file->getBasename('.php');
                $modelName = str_replace('Policy', '', $baseName);

                Gate::policy("App\\Models\\{$modelName}", "App\\Policies\\{$baseName}");
            }
        }
    }
}

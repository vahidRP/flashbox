<?php

namespace App\Support\Repository;

use App\Support\Repository\Commands\MakeCriteriaCommand;
use App\Support\Repository\Commands\MakeRepositoryCommand;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    protected $commandPath = 'app/Support/Commands';

    /**
     * Register any application services.
     */
    public function register()
    {
        // merge default config
        $this->mergeConfigFrom(config_path('repositories.php'), 'repositories');

        // Bind the repositories.
        $this->autoBindRepositories();

        // And generators.
        $this->registerRepositoryGenerator();
        $this->registerCriteriaGenerator();
    }

    /**
     * Automatically Bind Repositories.
     */
    private function autoBindRepositories()
    {
        // Load config parameters needed.
        $repositoriesBasePath = config('repositories.repositories_path');
        $baseNamespace = rtrim(config('repositories.repository_interfaces_namespace'), '\\') . '\\';
        $skipRepositories = config('repositories.skip');
        $implementationBindings = config('repositories.bindings');
        $defaultImplementation = $this->findDefaultImplementation($implementationBindings);

        if (File::exists($repositoriesBasePath)) {
            $allRepos = File::files($repositoriesBasePath);

            foreach ($allRepos as $repo) {
                $implementation = $defaultImplementation;
                $interfaceName = pathinfo($repo, PATHINFO_FILENAME);

                if (in_array($interfaceName, $skipRepositories)) {
                    continue;
                } else {
                    $commonName = str_replace('Interface', '', $interfaceName);
                    $interfaceFullClassName = $baseNamespace . $interfaceName;

                    foreach ($implementationBindings as $engine => $bindRepositories) {
                        if ('default' === $bindRepositories) {
                            continue;
                        } else {
                            if (in_array($interfaceName, $bindRepositories)) {
                                $implementation = $engine;
                                break;
                            }
                        }
                    }

                    $fullClassName = $baseNamespace . ucfirst(camel_case($implementation)) . '\\' . $commonName;

                    if (class_exists($fullClassName)) {
                        $this->app->bind(
                            $interfaceFullClassName,
                            function (Application $app) use ($fullClassName) {
                                return $app->make($fullClassName);
                            }
                        );
                    }
                }
            }
        }
    }

    /**
     * @param $implementations
     *
     * @return array|mixed|string
     */
    private function findDefaultImplementation($implementations)
    {
        $filtered = array_filter(
            $implementations,
            function ($k) {
                return 'default' === $k;
            }
        );

        $default = array_keys($filtered);
        $default = is_array($default) ? $default[0] : $default;

        return $default ? $default : 'eloquent';
    }

    /**
     * Register Repository Command Generator.
     */
    private function registerRepositoryGenerator()
    {
        $this->app->singleton(
            'command.repository',
            function ($app) {
                return $app[MakeRepositoryCommand::class];
            }
        );

        $this->commands('command.repository');
    }

    /**
     * Register Criteria Command Generator.
     */
    private function registerCriteriaGenerator()
    {
        $this->app->singleton(
            'command.criteria',
            function ($app) {
                return $app[MakeCriteriaCommand::class];
            }
        );

        $this->commands('command.criteria');
    }
}

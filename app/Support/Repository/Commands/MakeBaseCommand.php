<?php

namespace App\Support\Repository\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeBaseCommand extends Command
{
    /**
     * @var string
     */
    protected $modelNamespace;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * @var mixed
     */
    protected $composer;

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();

        $this->modelNamespace = rtrim(config('repositories.model_namespace', app()->getNamespace()), '\\') . '\\';
        $this->filesystem = $filesystem;
        $this->composer = app()['composer'];
    }

    /**
     * @param $path
     */
    protected function makeDirectory($path)
    {
        if (! $this->filesystem->isDirectory($path)) {
            $this->filesystem->makeDirectory($path, 0775, true, true);
        }
    }

    /**
     * @return array|mixed|string
     */
    protected function findDefaultImplementation()
    {
        $implementationBindings = config('repositories.bindings');

        $filtered = array_filter(
            $implementationBindings,
            function ($k) {
                return 'default' === $k;
            }
        );

        $default = array_keys($filtered);
        $default = is_array($default) ? $default[0] : $default;

        return $default ? $default : 'eloquent';
    }

    /**
     * @param string $file
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     *
     * @return string
     */
    protected function getStubContent(string $file)
    {
        return $this->filesystem->get(__DIR__ . '/stubs/' . $file);
    }
}

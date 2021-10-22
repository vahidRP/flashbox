<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionException;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws ReflectionException
     */
    public function run()
    {
        $permissions = [];

        $permissions[] = [
            'title'    => "Super Admin",
            'identity' => 'super-admin'
        ];

        $this->createModelsPermissions($permissions);

        if(count($permissions)){
            $identities = \App\Models\Permission::pluck('identity')->toArray();
            $permissions = array_filter($permissions, fn($permission) => !in_array($permission['identity'], $identities));
            $now = \Illuminate\Support\Carbon::now();
            $permissions = array_map(function($p) use ($now){
                $p['created_at'] = $now;
                $p['updated_at'] = $now;
                return $p;
            }, $permissions);
            usort($permissions, fn($a, $b) => strcmp($a['identity'], $b['identity']));
            \App\Models\Permission::insert($permissions);
        }
    }

    /**
     * @param array $permissions
     * @throws ReflectionException
     */
    protected function createModelsPermissions(array &$permissions)
    {
        $namespace = 'App\\Models';
        $path = 'app/Models';

        $finder = new \Symfony\Component\Finder\Finder();
        $finder->files()->in(base_path() . '/' . $path);

        foreach($finder as $file){
            $ns = $namespace;
            if($relativePath = $file->getRelativePath()){
                $ns .= '\\' . strtr($relativePath, '/', '\\');
            }
            $baseName = $file->getBasename('.php');
            $class = $ns . '\\' . $baseName;

            if((new ReflectionClass($class))->isInstantiable()){
                $actions = [
                    'create',
                    'read',
                    'update',
                    'delete',
                ];

                foreach($actions as $action){
                    $permissions[] = [
                        'title'    => ucfirst($action) . " {$baseName}",
                        'identity' => (Str::snake(Str::pluralStudly($baseName)) . ".{$action}")
                    ];
                }
            }
        }
    }

}

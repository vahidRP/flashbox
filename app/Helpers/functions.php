<?php

use Illuminate\Support\Str;

if (! function_exists('_dd')) {
    /**
     * Dump the passed variables and end the script.
     *
     * @param  mixed  $args
     * @return void
     */
    function _dd(...$args)
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: *');
        header('Access-Control-Allow-Headers: *');
        http_response_code(500);

        foreach ($args as $x) {
            \Symfony\Component\VarDumper\VarDumper::dump($x);
        }

        die(1);
    }
}

if(!function_exists('config_path')){
    /**
     * Get the configuration path.
     *
     * @param string $path
     * @return string
     */
    function config_path($path = '')
    {
        return base_path('config') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

/**
 * Helper functions for the helper functions, that can still be used standalone
 */
if(!function_exists('studly')){
    /**
     * Convert a value to studly caps case.
     *
     * @param string $value
     * @return string
     */
    function studly($value)
    {
        static $studlyCache = [];
        $key = $value;

        if(isset($studlyCache[$key])){
            return $studlyCache[$key];
        }

        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return $studlyCache[$key] = str_replace(' ', '', $value);
    }
}

if(!function_exists('camel_case')){
    /**
     * Convert a value to camel case.
     *
     * @param string $value
     * @return string
     */
    function camel_case($value)
    {
        static $camelCache = [];

        if(isset($camelCache[$value])){
            return $camelCache[$value];
        }

        return $camelCache[$value] = lcfirst(studly($value));
    }
}

if(!function_exists('route_slug')){
    function route_slug($string): string
    {
        $string = Str::snake($string);
        $explode = explode('_', $string);
        $keys = array_keys($explode);
        $lastKey = end($keys);
        $explode[$lastKey] = Str::plural($explode[$lastKey]);
        return implode('-', $explode);
    }
}

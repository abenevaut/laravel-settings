<?php

require dirname(__DIR__) . '/vendor/autoload.php';

// phpcs:disable
if (!function_exists('storage_path')) {
    function storage_path($filename)
    {
        return dirname(__DIR__) . '/tests/' . $filename;
    }
}

if (!function_exists('base_path')) {
    function base_path($filename)
    {
        return dirname(__DIR__) . '/tests/' . $filename;
    }
}

if (!function_exists('config_path')) {
    function config_path($arg)
    {
        return $arg;
    }
}
// phpcs:enable

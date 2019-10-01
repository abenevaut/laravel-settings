<?php

require dirname(__DIR__) . '/vendor/autoload.php';

function storage_path($filename)
{
    return dirname(__DIR__) . '/tests/' . $filename;
}

function base_path($filename)
{
    return dirname(__DIR__) . '/tests/' . $filename;
}

function config_path($arg)
{
    return $arg;
}

<?php

use abenevaut\Settings\App\Facades\SettingsFacade;

if (!function_exists('settings')) {
    /**
     * @param      $key
     * @param null $default
     *
     * @return mixed|null
     */
    function settings($key, $default = null)
    {
        return SettingsFacade::get($key, $default);
    }
}

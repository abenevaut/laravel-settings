<?php

use ABENEVAUT\Settings\Facades\Settings;

if (!function_exists('settings'))
{
	/**
	 * @param      $key
	 * @param null $default
	 *
	 * @return mixed|null
	 */
	function settings($key, $default = null)
	{
		return Settings::get($key, $default);
	}
}

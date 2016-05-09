<?php namespace CVEPDB\Settings\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Settings
 * @package CVEPDB\Settings\Facades
 */
class Settings extends Facade
{

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return 'settings';
	}

}

<?php namespace ABENEVAUT\Settings\App\Providers;

use Illuminate\Support\ServiceProvider;
use ABENEVAUT\Settings\Domain\Settings\Cache\Repositories\CacheRepository;
use ABENEVAUT\Settings\Domain\Settings\Settings\Repositories\SettingsRepository;

class SettingsServiceProvider extends ServiceProvider
{

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->publishes([
			__DIR__ . '/../../config/settings.php' => config_path('settings.php')
		]);
		$this->publishes([
			__DIR__ . '/../../database/migrations/' => base_path('/database/migrations')
		]);
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->mergeConfigFrom(
			__DIR__ . '/../../config/settings.php', 'settings'
		);
		$this->app['settings'] = $this->app->share(function ($app)
		{

			$config = $app->config->get('settings', [
				'cache_file' => storage_path('settings.json'),
				'db_table'   => 'settings'
			]);

			return new SettingsRepository(
				$app['db'],
				new CacheRepository($config['cache_file']),
				$config
			);
		});
	}


	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return ['settings'];
	}
}

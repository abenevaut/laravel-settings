<?php

namespace Tests;

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use abenevaut\Settings\Domain\Settings\Cache\Repositories\CacheRepository;
use abenevaut\Settings\Domain\Settings\Settings\Repositories\SettingsRepository;

trait SettingsTrait
{

    /**
     * @var
     */
    protected $settings;

    /**
     * @var
     */
    protected $db;

    /**
     * @var
     */
    protected $config;

    /**
     *
     */
    protected function setUp(): void
    {
        $this->db = $this->initDb();

        $this->config = [
            'db_table'   => 'settings',
            'cache_file' => storage_path('settings.json'),
            'fallback'   => false
        ];

        $this->initSettingsRepository();
    }

    /**
     * @return \Illuminate\Database\DatabaseManager
     */
    private function initDb()
    {
        $capsule = new Capsule();

        $capsule->addConnection([
            'driver'   => 'sqlite',
            'host'     => 'localhost',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $capsule->setEventDispatcher(new Dispatcher(new Container()));
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        Capsule::schema()->create('settings', function ($table) {
            $table->string('setting_key', 100)->index()->unique('key');
            $table->text('setting_value', 65535)->nullable();
        });

        return $capsule->getDatabaseManager();
    }

    private function initSettingsRepository()
    {
        $this->settings = new SettingsRepository(
            $this->db,
            new CacheRepository($this->config['cache_file']),
            $this->config
        );
    }
}

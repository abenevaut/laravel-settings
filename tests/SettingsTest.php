<?php

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use CVEPDB\Settings\Settings;
use CVEPDB\Settings\Cache;

/**
 * Class SettingsTest
 */
class SettingsTest extends PHPUnit_Framework_TestCase
{

	const SETTINGS_TABLE = 'settings';
	const SETTINGS_COL_KEY = 'setting_key';
	const SETTINGS_COL_VAL = 'setting_value';

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
	protected function setUp()
	{
		$this->db = $this->initDb();

		$this->config = [
			'db_table'   => self::SETTINGS_TABLE,
			'cache_file' => storage_path('settings.json'),
			'fallback'   => false
		];
		$this->settings = new Settings($this->db, new Cache($this->config['cache_file']), $this->config);
	}

	/**
	 *
	 */
	public function testSet()
	{
		$set = 'value';
		$this->settings->set('key', $set);

		$setting = $this->db->table(self::SETTINGS_TABLE)->where(self::SETTINGS_COL_KEY, 'key')->first([self::SETTINGS_COL_VAL]);

		$this->assertEquals($set, unserialize($setting->{self::SETTINGS_COL_VAL}));
		$this->assertEquals($set, $this->settings->get('key'));
	}

	/**
	 *
	 */
	public function testSetArray()
	{
		$set = ['key' => 'value'];
		$this->settings->set('key', $set);

		$setting = $this->db->table(self::SETTINGS_TABLE)->where(self::SETTINGS_COL_KEY, 'key')->first([self::SETTINGS_COL_VAL]);

		$this->assertEquals($set, unserialize($setting->{self::SETTINGS_COL_VAL}));
		$this->assertEquals($set, $this->settings->get('key'));
	}

	/**
	 *
	 */
	public function testGet()
	{
		$set = 'value';
		$this->settings->set('key', $set);

		$this->assertEquals($set, $this->settings->get('key'));
	}

	/**
	 *
	 */
	public function testGetAll()
	{
		$this->settings->set('key', 'value');
		$this->settings->set('key2', 'value2');

		$this->assertEquals('value', $this->settings->get('key'));
		$this->assertEquals('value2', $this->settings->get('key2'));

		$this->assertEquals(['key' => 'value', 'key2' => 'value2'], $this->settings->getAll());
	}

	/**
	 *
	 */
	public function testFlush()
	{
		$this->settings->set('key', 'value');
		$this->settings->flush();
		$this->assertEquals([], $this->settings->getAll());
	}

	/**
	 *
	 */
	public function testHasKey()
	{
		$this->settings->set('key', 'value');

		$this->assertTrue($this->settings->hasKey('key'));
		$this->assertFalse($this->settings->hasKey('key2'));
	}

	/**
	 *
	 */
	public function testHasKeyWithoutCache()
	{
		$this->settings->set('key', 'value');

		$this->assertTrue($this->settings->hasKey('key'));
		$this->assertFalse($this->settings->hasKey('key2'));

		@unlink(storage_path('settings.json'));

		$this->assertTrue($this->settings->hasKey('key'));
		$this->assertFalse($this->settings->hasKey('key2'));

	}

	/**
	 *
	 */
	public function testForget()
	{
		$this->settings->set('key', 'value');
		$this->settings->forget('key');
		$this->assertNull($this->settings->get('key'));
	}

	/**
	 *
	 */
	protected function tearDown()
	{
		Capsule::schema()->drop('settings');
		@unlink(storage_path('settings.json'));
	}

	/**
	 * @return \Illuminate\Database\DatabaseManager
	 */
	private function initDb()
	{
		$capsule = new Capsule;

		$capsule->addConnection([
			'driver'   => 'sqlite',
			'host'     => 'localhost',
			'database' => ':memory:',
			'prefix'   => '',
		]);

		$capsule->setEventDispatcher(new Dispatcher(new Container));
		$capsule->setAsGlobal();
		$capsule->bootEloquent();

		Capsule::schema()->create('settings', function ($table)
		{
			$table->string(self::SETTINGS_COL_KEY, 100)->index()->unique('key');
			$table->text(self::SETTINGS_COL_VAL, 65535)->nullable();
		});

		return $capsule->getDatabaseManager();
	}

}

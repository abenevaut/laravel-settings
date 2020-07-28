<?php

namespace Tests;

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\TestCase;

class SettingsTest extends TestCase
{
    use SettingsTrait;

    public const SETTINGS_TABLE = 'settings';
    public const SETTINGS_COL_KEY = 'setting_key';
    public const SETTINGS_COL_VAL = 'setting_value';

    /**
     *
     */
    public function testSet()
    {
        /*
         * Add setting
         */
        $set = 'value';
        $this->settings->set('key', $set);

        $setting = $this
            ->db
            ->table(self::SETTINGS_TABLE)
            ->where(self::SETTINGS_COL_KEY, 'key')
            ->first([self::SETTINGS_COL_VAL]);

        $this->assertEquals($set, unserialize($setting->{self::SETTINGS_COL_VAL}));
        $this->assertEquals($set, $this->settings->get('key'));
        /*
         * Update setting
         */
        $set = 'value2';
        $this->settings->set('key', $set);

        $setting = $this
            ->db
            ->table(self::SETTINGS_TABLE)
            ->where(self::SETTINGS_COL_KEY, 'key')
            ->first([self::SETTINGS_COL_VAL]);

        $this->assertEquals($set, unserialize($setting->{self::SETTINGS_COL_VAL}));
        $this->assertEquals($set, $this->settings->get('key'));
    }

    /**
     *
     */
    public function testSetArray()
    {
        /*
         * Add setting
         */
        $set = ['key' => 'value'];
        $this->settings->set('key', $set);

        $setting = $this
            ->db
            ->table(self::SETTINGS_TABLE)
            ->where(self::SETTINGS_COL_KEY, 'key')
            ->first([self::SETTINGS_COL_VAL]);

        $this->assertEquals($set, unserialize($setting->{self::SETTINGS_COL_VAL}));
        $this->assertEquals($set, $this->settings->get('key'));
        /*
         * Update setting
         */
        $set = ['key' => 'value2'];
        $this->settings->set('key', $set);

        $setting = $this
            ->db
            ->table(self::SETTINGS_TABLE)
            ->where(self::SETTINGS_COL_KEY, 'key')
            ->first([self::SETTINGS_COL_VAL]);

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

        /*
         * Mock `$this->settings->get('key')` to use the configuration fallback value.
         */

        $this->config = [
            'db_table' => self::SETTINGS_TABLE,
            'cache_file' => storage_path('settings.json'),
            'fallback' => true,
        ];
        $this->initSettingsRepository();

        Config::shouldReceive('get')
            ->once()
            ->with('unknownKey', null)
            ->andReturn('fallbackValue');

        $this->assertEquals('fallbackValue', $this->settings->get('unknownKey'));

        /*
         * Then reset config.
         */

        $this->config = [
            'db_table' => self::SETTINGS_TABLE,
            'cache_file' => storage_path('settings.json'),
            'fallback' => false,
        ];
        self::setUp();
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

        $this->assertEquals(
            ['key' => 'value', 'key2' => 'value2'],
            $this->settings->getAll()
        );
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
    protected function tearDown(): void
    {
        Capsule::schema()->drop('settings');
        @unlink(storage_path('settings.json'));
    }
}

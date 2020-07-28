<?php

namespace Tests;

use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;
use abenevaut\Settings\App\Providers\SettingsServiceProvider;

class ServiceProviderTest extends TestCase
{
    use SettingsTrait;

    public function testSettingsServiceProvider()
    {
        $app = \Mockery::mock(Container::class, function ($mock) {
            $mock->shouldReceive('configurationIsCached')->once()->andReturn(true);
            $mock->shouldReceive('singleton')->once()->andReturn($this->settings);
            $mock->shouldReceive('offsetGet')->once()->andReturn(new FakeConfig());
        });

        $provider = new SettingsServiceProvider($app);
        $provider->boot();
        $provider->register();
        $provides = $provider->provides();
        $this->assertIsArray($provides);
        $this->assertEquals(['settings'], $provides);
    }
}

// phpcs:disable
class FakeConfig
{
    public function get()
    {
        return [];
    }

    public function set()
    {
        return $this;
    }
}
// phpcs:enable

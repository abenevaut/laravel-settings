<?php namespace abenevaut\Tests;

use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;
use abenevaut\Settings\App\Providers\SettingsServiceProvider;
use abenevaut\Tests\SettingsTrait;

class ServiceProviderTest extends TestCase
{

    use SettingsTrait;

	public function testSettingsServiceProvider()
	{
        $app = \Mockery::mock(Container::class, function ($mock) {
            $mock->shouldReceive('configurationIsCached')->once()->andReturn(true);
            $mock->shouldReceive('singleton')->once()->andReturn($this->settings);
        });

        $provider = new SettingsServiceProvider($app);
        $provider->boot();
        $provider->register();
        $provides = $provider->provides();
        $this->assertIsArray($provides);
        $this->assertEquals(['settings'], $provides);
	}
}

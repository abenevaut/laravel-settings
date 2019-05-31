<?php

use abenevaut\Settings\App\Facades\SettingsFacade;
use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
	public function testGetFromHelper()
	{
        SettingsFacade::shouldReceive('get')
            ->once()
            ->with('key', null)
            ->andReturn('value');

        $this->assertEquals('value', settings('key'));
	}
}

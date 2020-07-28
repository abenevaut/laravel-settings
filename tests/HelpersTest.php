<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use abenevaut\Settings\App\Facades\SettingsFacade;

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

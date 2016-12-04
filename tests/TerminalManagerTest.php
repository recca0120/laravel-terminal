<?php

use Mockery as m;
use Recca0120\Terminal\TerminalManager;

class TerminalManagerTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_handle()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $kernel = m::spy('Recca0120\Terminal\Kernel');
        $helpInfo = 'helpinfo';
        $config = [
            'username' => 'LARAVEL',
            'hostname' => php_uname('n'),
            'os' => PHP_OS,
            'helpInfo' => $helpInfo,
        ];

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $kernel->shouldReceive('output')->andReturn($helpInfo);

        $terminalManager = new TerminalManager($kernel, $config);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame($config, $terminalManager->getOptions());
        $this->assertSame($kernel, $terminalManager->getKernel());

        $kernel->shouldHaveReceived('call')->with('--ansi')->once();
        $kernel->shouldHaveReceived('output')->once();
    }
}

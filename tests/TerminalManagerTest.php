<?php

namespace Recca0120\Terminal\Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\Terminal\TerminalManager;

class TerminalManagerTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testGetOptions()
    {
        $terminalManager = new TerminalManager(
            $kernel = m::mock('Recca0120\Terminal\Kernel'),
            $config = ['enabled' => true, 'whitelists' => [], 'route' => [], 'commands' => []]
        );
        $kernel->shouldReceive('call')->once()->with('--ansi');
        $kernel->shouldReceive('output')->once()->andReturn($output = 'foo');
        $this->assertSame([
            'username' => 'LARAVEL',
            'hostname' => php_uname('n'),
            'os' => PHP_OS,
            'helpInfo' => $output
        ], $terminalManager->getOptions());
    }
}

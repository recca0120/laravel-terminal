<?php

namespace Recca0120\Terminal\Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\Terminal\TerminalManager;

class TerminalManagerTest extends TestCase
{
    protected function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testGetConfig()
    {
        $terminalManager = new TerminalManager(
            $kernel = m::mock('Recca0120\Terminal\Kernel'),
            $config = ['enabled' => true, 'whitelists' => [], 'route' => [], 'commands' => []]
        );
        $this->assertSame([
            'username' => 'LARAVEL',
            'hostname' => php_uname('n'),
            'os' => PHP_OS,
        ], $terminalManager->getConfig());
    }
}

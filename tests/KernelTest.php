<?php

namespace Recca0120\Terminal\Tests;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\Terminal\Kernel;

class KernelTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testHandle()
    {
        $kernel = new Kernel(
            $artisan = m::mock('Recca0120\Terminal\Application')
        );
        $artisan->shouldReceive('run')->once()->with(
            $input = m::mock('Symfony\Component\Console\Input\InputInterface'),
            $output = m::mock('Symfony\Component\Console\Output\OutputInterface')
        )->andReturn($code = 'foo');
        $this->assertSame($code, $kernel->handle($input, $output));
    }

    public function testCall()
    {
        $kernel = new Kernel(
            $artisan = m::mock('Recca0120\Terminal\Application')
        );
        $artisan->shouldReceive('call')->once()->with(
            $command = 'foo',
            $parameters = ['foo' => 'bar'],
            $outputBuffer = null
        )->andReturn($code = 'foo');
        $this->assertSame($code, $kernel->call($command, $parameters, $outputBuffer));
    }

    public function testQueue()
    {
        $kernel = new Kernel(
            $artisan = m::mock('Recca0120\Terminal\Application')
        );
        $artisan->shouldReceive('getLaravel')->once()->andReturn(
            $app = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess')
        );
        $app->shouldReceive('offsetGet')->once()->with('Illuminate\Contracts\Queue\Queue')->andReturn(
            $queue = m::mock('Illuminate\Contracts\Queue\Queue')
        );
        $queue->shouldReceive('push')->once()->with('Illuminate\Foundation\Console\QueuedJob', [
            $command = 'foo',
            $parameters = ['foo' => 'bar'],
        ]);
        $this->assertNull($kernel->queue($command, $parameters));
    }

    public function testAll()
    {
        $kernel = new Kernel(
            $artisan = m::mock('Recca0120\Terminal\Application')
        );
        $artisan->shouldReceive('all')->once()->andReturn(
            $commands = ['foo']
        );
        $this->assertSame($commands, $kernel->all());
    }

    public function testOutput()
    {
        $kernel = new Kernel(
            $artisan = m::mock('Recca0120\Terminal\Application')
        );
        $artisan->shouldReceive('output')->once()->andReturn(
            $output = 'foo'
        );
        $this->assertSame($output, $kernel->output());
    }

    public function testTerminate()
    {
        $kernel = new Kernel(
            $artisan = m::spy('Recca0120\Terminal\Application')
        );
        $input = m::mock('Symfony\Component\Console\Input\InputInterface');

        $kernel->terminate($input, 0);

        $artisan->shouldHaveReceived('terminate');
    }
}

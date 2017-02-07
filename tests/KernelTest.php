<?php

namespace Recca0120\Terminal\Tests;

use Mockery as m;
use Recca0120\Terminal\Kernel;
use PHPUnit\Framework\TestCase;
use Illuminate\Contracts\Queue\Queue;

class KernelTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

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
            $parameters = ['foo' => 'bar']
        )->andReturn($code = 'foo');
        $this->assertSame($code, $kernel->call($command, $parameters));
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
        $kernel->queue($command, $parameters);
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
}

<?php

use Mockery as m;
use Recca0120\Terminal\Kernel;
use Illuminate\Contracts\Queue\Queue;

class KernelTest extends PHPUnit_Framework_TestCase
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

        $artisan = m::spy('Recca0120\Terminal\Application');
        $input = m::spy('Symfony\Component\Console\Input\InputInterface');
        $output = m::spy('Symfony\Component\Console\Output\OutputInterface');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $artisan
            ->shouldReceive('run')->with($input, $output)->andReturn(1);

        $kernel = new Kernel($artisan);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame(1, $kernel->handle($input, $output));
        $artisan->shouldHaveReceived('run')->with($input, $output)->once();
    }

    public function test_call()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $artisan = m::spy('Recca0120\Terminal\Application');
        $command = 'foo.command';
        $parameters = [];

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $artisan
            ->shouldReceive('call')->with($command, $parameters)->andReturn(1);

        $kernel = new Kernel($artisan);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame(1, $kernel->call($command, $parameters));
        $artisan->shouldHaveReceived('call')->with($command, $parameters)->once();
    }

    public function test_queue()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $artisan = m::spy('Recca0120\Terminal\Application');
        $command = 'foo.command';
        $parameters = [];
        $app = m::spy('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $queue = m::spy('Illuminate\Contracts\Queue\Queue');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $artisan
            ->shouldReceive('getLaravel')->andReturn($app);

        $app
            ->shouldReceive('offsetGet')->with('Illuminate\Contracts\Queue\Queue')->andReturn($queue);

        $kernel = new Kernel($artisan);
        $kernel->queue($command, $parameters);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $artisan->shouldHaveReceived('getLaravel')->once();
        $queue->shouldHaveReceived('push')->with('Illuminate\Foundation\Console\QueuedJob', [$command, $parameters]);
    }

    public function test_all()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $artisan = m::spy('Recca0120\Terminal\Application');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $kernel = new Kernel($artisan);
        $kernel->all();

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $artisan->shouldHaveReceived('all')->once();
    }

    public function test_output()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $artisan = m::spy('Recca0120\Terminal\Application');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $kernel = new Kernel($artisan);
        $kernel->output();

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $artisan->shouldHaveReceived('output')->once();
    }
}

<?php

use Illuminate\Contracts\Queue\Queue;
use Mockery as m;
use Recca0120\Terminal\Kernel;

class KernelTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_kernel()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $input = m::mock('Symfony\Component\Console\Input\InputInterface');
        $output = m::mock('Symfony\Component\Console\Output\OutputInterface');
        $app = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $artisan = m::mock('Recca0120\Terminal\Application');
        $kernel = new Kernel($artisan);
        $queue = m::mock('Illuminate\Contracts\Queue\Queue');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $artisan
            ->shouldReceive('call')->with('list', [])->once()
            ->shouldReceive('output')->once()
            ->shouldReceive('all')->once()
            ->shouldReceive('run')->with($input, $output)->once()
            ->shouldReceive('getLaravel')->andReturn($app)->once();

        $app->shouldReceive('offsetGet')->with('Illuminate\Contracts\Queue\Queue')->once()->andReturn($queue);

        $queue->shouldReceive('push')->with('Illuminate\Foundation\Console\QueuedJob', ['foo', ['bar']])->once();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $kernel->call('list');
        $kernel->output();
        $kernel->all();
        $kernel->queue('foo', ['bar']);
        $kernel->handle($input, $output);
    }
}

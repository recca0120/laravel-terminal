<?php

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Mockery as m;
use Recca0120\Terminal\Kernel;
use Recca0120\Terminal\Application as Artisan;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Foundation\Console\QueuedJob;
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

        $input = m::mock(InputInterface::class);
        $output = m::mock(OutputInterface::class);
        $app = m::mock(Application::class.','.ArrayAccess::class);
        $artisan = m::mock(Artisan::class);
        $kernel = new Kernel($artisan);
        $queue = m::mock(Queue::class);

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

        $app->shouldReceive('offsetGet')->with(Queue::class)->once()->andReturn($queue);

        $queue->shouldReceive('push')->with(QueuedJob::class, ['foo', ['bar']])->once();

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

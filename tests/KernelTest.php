<?php

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Http\Request;
use Mockery as m;
use Recca0120\Terminal\Console\Kernel;
use Symfony\Component\Console\Input\InputInterface;

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

        $events = m::mock(DispatcherContract::class);
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);
        $request = m::mock(Request::class);
        $input = m::mock(InputInterface::class);
        $kernel = new Kernel($app, $events);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $request->shouldReceive('ajax')->andReturn(false);

        $input
            ->shouldReceive('hasParameterOption')->andReturn(false)
            ->shouldReceive('getParameterOption')->andReturn([])
            ->shouldReceive('getFirstArgument')->andReturnNull();

        $events
            ->shouldReceive('fire')
            ->shouldReceive('firing');

        $app
            ->shouldReceive('offsetGet')->with('request')->andReturn($request)
            ->shouldReceive('offsetGet')->with('events')->andReturn($events)
            ->shouldReceive('version')->andReturn('testing')
            ->shouldReceive('make')->andReturnUsing(function ($class) {
                return new $class();
            });

        $kernel = new Kernel($app, $events);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $kernel->call('list');
        $output = $kernel->output();
        $kernel->all();
        $kernel->queue('foo');
        $kernel->handle($input);
    }
}

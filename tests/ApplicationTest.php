<?php

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Http\Request;
use Mockery as m;
use Recca0120\Terminal\Application;

class ApplicationTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_artisan()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $events = m::mock(DispatcherContract::class);
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $events->shouldReceive('fire');
        $app
            ->shouldReceive('offsetGet')->with('request')->andReturn(m::mock(Request::class))
            ->shouldReceive('basePath')->andReturn(__DIR__)
            ->shouldReceive('storagePath')->andReturn(__DIR__);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $artisan = new Application($app, $events, 'testing');
    }
}

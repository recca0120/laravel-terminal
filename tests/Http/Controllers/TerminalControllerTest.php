<?php

use Mockery as m;
use Recca0120\Terminal\Http\Controllers\TerminalController;

class TerminalControllerTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_index()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $kernel = m::mock('Recca0120\Terminal\Kernel');
        $app = m::mock('Illuminate\Contracts\Foundation\Application');
        $request = m::mock('Illuminate\Http\Request');
        $responseFactory = m::mock('Illuminate\Contracts\Routing\ResponseFactory');
        $urlGenerator = m::mock('Illuminate\Contracts\Routing\UrlGenerator');
        $session = m::mock('stdClass');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $kernel
            ->shouldReceive('call')->with('--ansi')->once()
            ->shouldReceive('output')->once();

        $app
            ->shouldReceive('basePath')->once()->andReturn(__DIR__)
            ->shouldReceive('environment')->once()->andReturn('testing')
            ->shouldReceive('version')->once()->andReturn('testing');

        $request
            ->shouldReceive('hasSession')->once()->andReturn(true)
            ->shouldReceive('session')->once()->andReturn($session);

        $session->shouldReceive('token')->andReturn('fooToken');

        $urlGenerator->shouldReceive('action')->with('\Recca0120\Terminal\Http\Controllers\TerminalController@endpoint')->once();

        $responseFactory->shouldReceive('view')->once();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $controller = new TerminalController();
        $controller->index($app, $kernel, $request, $responseFactory, $urlGenerator);
    }

    public function test_endpoint()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $kernel = m::mock('Recca0120\Terminal\Kernel');
        $app = m::mock('Illuminate\Contracts\Foundation\Application');
        $request = m::mock('Illuminate\Http\Request');
        $responseFactory = m::mock('Illuminate\Contracts\Routing\ResponseFactory');
        $urlGenerator = m::mock('Illuminate\Contracts\Routing\UrlGenerator');
        $session = m::mock('stdClass');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $kernel
            ->shouldReceive('call')->once()->andReturn(1)
            ->shouldReceive('output')->once();

        $request
            ->shouldReceive('hasSession')->once()->andReturn(true)
            ->shouldReceive('session')->once()->andReturn($session)
            ->shouldReceive('get')->with('command')->andReturn('command')
            ->shouldReceive('get')->with('jsonrpc')
            ->shouldReceive('get')->with('id');

        $session
            ->shouldReceive('isStarted')->andReturn(true)
            ->shouldReceive('save');

        $responseFactory->shouldReceive('json')->once();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $controller = new TerminalController();
        $controller->endpoint($kernel, $request, $responseFactory);
    }
}

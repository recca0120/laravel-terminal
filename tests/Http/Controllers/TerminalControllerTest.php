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

        $consoleKernel = m::mock('Recca0120\Terminal\Kernel');
        $app = m::mock('Illuminate\Contracts\Foundation\Application');
        $sessionManager = m::mock('Illuminate\Session\SessionManager');
        $request = m::mock('Illuminate\Http\Request');
        $responseFactory = m::mock('Illuminate\Contracts\Routing\ResponseFactory');
        $urlGenerator = m::mock('Illuminate\Contracts\Routing\UrlGenerator');
        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $consoleKernel
            ->shouldReceive('call')->with('--ansi')->once()
            ->shouldReceive('output')->once();

        $sessionManager
            ->shouldReceive('driver')->andReturnSelf()
            ->shouldReceive('token')->andReturn('fooToken');

        $app
            ->shouldReceive('basePath')->andReturn(__DIR__)
            ->shouldReceive('environment')->andReturn('testing')
            ->shouldReceive('version')->andReturn('testing');

        $urlGenerator->shouldReceive('action')->with('\Recca0120\Terminal\Http\Controllers\TerminalController@endpoint')->once();

        $responseFactory->shouldReceive('view')->once();
        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $controller = new TerminalController($consoleKernel, $app, $sessionManager, $request);
        $controller->index($responseFactory, $urlGenerator);
    }

    public function test_endpoint()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $consoleKernel = m::mock('Recca0120\Terminal\Kernel');
        $app = m::mock('Illuminate\Contracts\Foundation\Application');
        $sessionManager = m::mock('Illuminate\Session\SessionManager');
        $request = m::mock('Illuminate\Http\Request');
        $responseFactory = m::mock('Illuminate\Contracts\Routing\ResponseFactory');
        $urlGenerator = m::mock('Illuminate\Contracts\Routing\UrlGenerator');
        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $consoleKernel
            ->shouldReceive('call')->once()->andReturn(1)
            ->shouldReceive('output')->once();

        $sessionManager
            ->shouldReceive('driver')->andReturnSelf();

        $request
            ->shouldReceive('get')->with('command')->andReturn('command')
            ->shouldReceive('get')->with('jsonrpc')
            ->shouldReceive('get')->with('id');

        $responseFactory->shouldReceive('json')->once();
        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $controller = new TerminalController($consoleKernel, $app, $sessionManager, $request);
        $controller->endpoint($responseFactory);
    }
}

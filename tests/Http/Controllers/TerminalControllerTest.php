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
        | Arrange
        |------------------------------------------------------------
        */

        $request = m::spy('Illuminate\Http\Request');
        $responseFactory = m::spy('Illuminate\Contracts\Routing\ResponseFactory');
        $terminalManager = m::spy('Recca0120\Terminal\TerminalManager');
        $view = 'index';
        $kernel = m::spy('Recca0120\Terminal\Kernel');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $request
            ->shouldReceive('hasSession')->andReturn(true)
            ->shouldReceive('session->token')->andReturn('foo.token');

        $terminalManager
            ->shouldReceive('getOptions')->andReturn([]);

        $controller = new TerminalController($request, $responseFactory);
        $controller->index($terminalManager, $view);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $request->shouldHaveReceived('hasSession')->once();
        $request->shouldHaveReceived('session')->once();
        $terminalManager->shouldHaveReceived('getOptions')->once();
        $responseFactory->shouldHaveReceived('view')->once();
    }

    public function test_endpoint()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $request = m::spy('Illuminate\Http\Request');
        $responseFactory = m::spy('Illuminate\Contracts\Routing\ResponseFactory');
        $terminalManager = m::spy('Recca0120\Terminal\TerminalManager');
        $session = m::spy('Illuminate\Session\SessionManager');
        $kernel = m::spy('Recca0120\Terminal\Kernel');
        $command = 'foo.command';

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $request
            ->shouldReceive('hasSession')->andReturn(true)
            ->shouldReceive('session')->andReturn($session)
            ->shouldReceive('get')->with('command')->andReturn($command);

        $session
            ->shouldReceive('isStarted')->andReturn(true)
            ->shouldReceive('save');

        $terminalManager
            ->shouldReceive('getKernel')->andReturn($kernel);

        $controller = new TerminalController($request, $responseFactory);
        $controller->endpoint($terminalManager);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $request->shouldHaveReceived('hasSession')->once();
        $request->shouldHaveReceived('session')->once();
        $session->shouldHaveReceived('isStarted')->once();
        $session->shouldHaveReceived('save')->once();
        $terminalManager->shouldHaveReceived('getKernel')->once();
        $request->shouldHaveReceived('get')->with('command')->once();
        $kernel->shouldHaveReceived('call')->with($command)->once();
        $request->shouldHaveReceived('get')->with('jsonrpc')->once();
        $request->shouldHaveReceived('get')->with('id')->once();
        $kernel->shouldHaveReceived('output')->once();
        $responseFactory->shouldHaveReceived('json')->once();
    }
}

<?php

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;
use Mockery as m;
use Recca0120\Terminal\Kernel;
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

        $consoleKernel = m::mock(Kernel::class);
        $app = m::mock(Application::class);
        $sessionManager = m::mock(SessionManager::class);
        $request = m::mock(Request::class);
        $responseFactory = m::mock(ResponseFactory::class);
        $urlGenerator = m::mock(UrlGenerator::class);
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

        $urlGenerator->shouldReceive('action')->with('\\'.TerminalController::class.'@endpoint')->once();

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

        $consoleKernel = m::mock(Kernel::class);
        $app = m::mock(Application::class);
        $sessionManager = m::mock(SessionManager::class);
        $request = m::mock(Request::class);
        $responseFactory = m::mock(ResponseFactory::class);
        $urlGenerator = m::mock(UrlGenerator::class);
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

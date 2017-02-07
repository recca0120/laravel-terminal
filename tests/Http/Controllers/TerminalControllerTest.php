<?php

namespace Recca0120\Terminal\Tests\Http\Controllers;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\Terminal\Http\Controllers\TerminalController;

class TerminalControllerTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testIndex()
    {
        $controller = new TerminalController(
            $request = m::mock('Illuminate\Http\Request'),
            $responseFactory = m::mock('Illuminate\Contracts\Routing\ResponseFactory')
        );
        $request->shouldReceive('hasSession')->once()->andReturn(true);
        $request->shouldReceive('session->token')->once()->andReturn($token = uniqid());
        $terminalManager = m::mock('Recca0120\Terminal\TerminalManager');
        $terminalManager->shouldReceive('getOptions')->once()->andReturn($options = ['foo' => 'bar']);
        $responseFactory->shouldReceive('view')->once()->with('terminal::index', [
            'options' => json_encode(array_merge($options, ['csrfToken' => $token])),
            'id' => null,
        ]);
        $controller->index($terminalManager, 'index');
    }

    public function testEndpoint()
    {
        $controller = new TerminalController(
            $request = m::mock('Illuminate\Http\Request'),
            $responseFactory = m::mock('Illuminate\Contracts\Routing\ResponseFactory')
        );

        $request->shouldReceive('hasSession')->once()->andReturn(true);
        $request->shouldReceive('session')->once()->andReturn(
            $session = m::mock('Illuminate\Session\SessionManager')
        );
        $session->shouldReceive('isStarted')->once()->andReturn(true);
        $session->shouldReceive('save')->once();

        $request->shouldReceive('get')->once()->with('command')->andReturn($command = 'foo');

        $terminalManager = m::mock('Recca0120\Terminal\TerminalManager');
        $terminalManager->shouldReceive('call')->once()->with($command)->andReturn($error = 0);
        $request->shouldReceive('get')->once()->with('jsonrpc')->andReturn($jsonrpc = 'foo');
        $request->shouldReceive('get')->once()->with('id')->andReturn($id = 'foo');
        $terminalManager->shouldReceive('output')->once()->andReturn($output = 'foo');

        $responseFactory->shouldReceive('json')->once()->with([
            'jsonrpc' => $jsonrpc,
            'id' => $id,
            'result' => $output,
            'error' => $error,
        ])->andReturn($result = 'foo');

        $this->assertSame($result, $controller->endpoint($terminalManager));
    }
}

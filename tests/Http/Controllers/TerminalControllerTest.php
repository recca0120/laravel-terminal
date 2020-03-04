<?php

namespace Recca0120\Terminal\Tests\Http\Controllers;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\Terminal\Http\Controllers\TerminalController;

class TerminalControllerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testIndex()
    {
        $controller = new TerminalController(
            $request = m::mock('Illuminate\Http\Request'),
            $responseFactory = m::mock('Illuminate\Contracts\Routing\ResponseFactory')
        );

        $request->shouldReceive('hasSession')->once()->andReturn(true);
        $request->shouldReceive('session->token')->once()->andReturn($token = 'foo');

        $kernel = m::mock('Recca0120\Terminal\Kernel');
        $kernel->shouldReceive('call')->once()->with('list --ansi');
        $kernel->shouldReceive('output')->once()->andReturn($output = 'foo');
        $kernel->shouldReceive('getConfig')->once()->andReturn($config = ['foo' => 'bar']);
        $responseFactory->shouldReceive('view')->once()->with('terminal::index', [
            'options' => json_encode(array_merge($config, [
                'csrfToken' => $token,
                'helpInfo' => $output,
            ])),
            'id' => null,
        ])->andReturn(
            $view = m::mock('Illuminate\Contracts\View\View')
        );
        $this->assertSame($view, $controller->index($kernel, 'index'));
    }

    public function testEndpoint()
    {
        $controller = new TerminalController(
            $request = m::mock('Illuminate\Http\Request'),
            $responseFactory = m::mock('Illuminate\Contracts\Routing\ResponseFactory')
        );

        $request->shouldReceive('get')->once()->with('method')->andReturn($command = 'foo');
        $request->shouldReceive('get')->once()->with('params', [])->andReturn($parameters = ['foo' => 'bar']);

        $kernel = m::mock('Recca0120\Terminal\Kernel');
        $kernel->shouldReceive('call')->once()->with($command, $parameters)->andReturn($error = 0);
        $request->shouldReceive('get')->once()->with('jsonrpc')->andReturn($jsonrpc = 'foo');
        $request->shouldReceive('get')->once()->with('id')->andReturn($id = 'foo');
        $kernel->shouldReceive('output')->once()->andReturn($output = 'foo');

        $responseFactory->shouldReceive('json')->once()->with([
            'jsonrpc' => $jsonrpc,
            'id' => $id,
            'result' => $output,
        ])->andReturn($result = 'foo');

        $this->assertSame($result, $controller->endpoint($kernel));
    }

    public function testEndpointError()
    {
        $controller = new TerminalController(
            $request = m::mock('Illuminate\Http\Request'),
            $responseFactory = m::mock('Illuminate\Contracts\Routing\ResponseFactory')
        );

        $request->shouldReceive('get')->once()->with('method')->andReturn($command = 'foo');
        $request->shouldReceive('get')->once()->with('params', [])->andReturn($parameters = ['foo' => 'bar']);

        $kernel = m::mock('Recca0120\Terminal\Kernel');
        $kernel->shouldReceive('call')->once()->with($command, $parameters)->andReturn($error = 1);
        $request->shouldReceive('get')->once()->with('jsonrpc')->andReturn($jsonrpc = 'foo');
        $kernel->shouldReceive('output')->once()->andReturn($output = 'foo');

        $responseFactory->shouldReceive('json')->once()->with([
            'jsonrpc' => $jsonrpc,
            'id' => null,
            'error' => [
                'code' => -32600,
                'message' => 'Invalid Request',
                'data' => $output,
            ],
        ])->andReturn($result = 'foo');

        $this->assertSame($result, $controller->endpoint($kernel));
    }
}

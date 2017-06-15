<?php

namespace Recca0120\Terminal\Tests\Http\Controllers;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\Terminal\Http\Controllers\TerminalController;

class TerminalControllerTest extends TestCase
{
    protected function tearDown()
    {
        parent::tearDown();
        m::close();
    }

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

        $request->shouldReceive('get')->once()->with('command')->andReturn($command = 'foo');

        $kernel = m::mock('Recca0120\Terminal\Kernel');
        $kernel->shouldReceive('call')->once()->with($command)->andReturn($error = 0);
        $request->shouldReceive('get')->once()->with('jsonrpc')->andReturn($jsonrpc = 'foo');
        $request->shouldReceive('get')->once()->with('id')->andReturn($id = 'foo');
        $kernel->shouldReceive('output')->once()->andReturn($output = 'foo');

        $responseFactory->shouldReceive('json')->once()->with([
            'jsonrpc' => $jsonrpc,
            'id' => $id,
            'result' => $output,
            'error' => $error,
        ])->andReturn($result = 'foo');

        $this->assertSame($result, $controller->endpoint($kernel));
    }
}

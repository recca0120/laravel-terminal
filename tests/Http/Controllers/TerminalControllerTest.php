<?php

namespace Recca0120\Terminal\Tests\Http\Controllers;

use Exception;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;
use Illuminate\Session\Store;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\Terminal\Application;
use Recca0120\Terminal\Http\Controllers\TerminalController;
use Recca0120\Terminal\Kernel;

class TerminalControllerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function test_index()
    {
        $container = new Container();
        $request = Request::capture();
        $container->instance('request', $request);
        $container->instance('config', new Repository());

        /** @var Store $session */
        $session = (new SessionManager($container))->driver('array');
        $session->regenerateToken();
        $request->setLaravelSession($session);
        $artisan = new Application($container, new Dispatcher(), 'testing');
        $kernel = new Kernel($artisan);
        $responseFactory = m::mock(ResponseFactory::class);
        $responseFactory->shouldReceive('view')
            ->once()
            ->with('terminal::index', m::on(function ($viewData) use ($session) {
                $data = json_decode($viewData['options'], true);

                return $data['csrfToken'] === $session->token();
            }))
            ->andReturn($view = m::mock(View::class));

        $controller = $container->make(TerminalController::class);

        self::assertSame($view, $controller->index($kernel, $request, $responseFactory));
    }

    /**
     * @throws Exception
     */
    public function test_endpoint()
    {
        $controller = new TerminalController();
        $request = m::mock(Request::class);
        $responseFactory = m::mock(ResponseFactory::class);

        $request->shouldReceive('get')->once()->with('method')->andReturn($command = 'foo');
        $request->shouldReceive('get')->once()->with('params', [])->andReturn($parameters = ['foo' => 'bar']);

        $kernel = m::mock(Kernel::class);
        $kernel->shouldReceive('call')->once()->with($command, $parameters)->andReturn($error = 0);
        $request->shouldReceive('get')->once()->with('jsonrpc')->andReturn($jsonrpc = 'foo');
        $request->shouldReceive('get')->once()->with('id')->andReturn($id = 'foo');
        $kernel->shouldReceive('output')->once()->andReturn($output = 'foo');

        $responseFactory->shouldReceive('json')->once()->with([
            'jsonrpc' => $jsonrpc, 'id' => $id, 'result' => $output,
        ])->andReturn($result = 'foo');

        self::assertSame($result, $controller->endpoint($kernel, $request, $responseFactory));
    }

    /**
     * @throws Exception
     */
    public function test_endpoint_error()
    {
        $controller = new TerminalController();
        $request = m::mock(Request::class);
        $responseFactory = m::mock(ResponseFactory::class);

        $request->shouldReceive('get')->once()->with('method')->andReturn($command = 'foo');
        $request->shouldReceive('get')->once()->with('params', [])->andReturn($parameters = ['foo' => 'bar']);

        $kernel = m::mock(Kernel::class);
        $kernel->shouldReceive('call')->once()->with($command, $parameters)->andReturn($error = 1);
        $request->shouldReceive('get')->once()->with('jsonrpc')->andReturn($jsonrpc = 'foo');
        $kernel->shouldReceive('output')->once()->andReturn($output = 'foo');

        $responseFactory->shouldReceive('json')->once()->with([
            'jsonrpc' => $jsonrpc,
            'id' => null,
            'error' => ['code' => -32600, 'message' => 'Invalid Request', 'data' => $output],
        ])->andReturn($result = 'foo');

        self::assertSame($result, $controller->endpoint($kernel, $request, $responseFactory));
    }
}

<?php

use Illuminate\Contracts\Config\Repository as ConfigContract;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Mockery as m;
use Recca0120\Terminal\ServiceProvider;
use Recca0120\Terminal\Kernel;
use Recca0120\Terminal\Application;

class ServiceProviderTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_boot()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);
        $config = m::mock(ConfigContract::class);
        $request = m::mock(Request::class);
        $router = m::mock(Router::class);
        $view = m::mock(stdClass::class);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $router->shouldReceive('group')->with(m::any(), m::type(Closure::class))->andReturnUsing(function ($options, $closure) use ($router) {
            // $closure($router);
        });

        $view->shouldReceive('addNamespace')->with('terminal', m::any());

        $config
            ->shouldReceive('get')->with('app.debug', false)->once()->andReturn(false)
            ->shouldReceive('get')->with('terminal', [])->twice()->andReturn([
                'whitelists' => ['127.0.0.1'],
            ])
            ->shouldReceive('set')->with('terminal', m::any())->once();

        $request
            ->shouldReceive('getClientIp')->once()->andReturn('127.0.0.1');

        $app
            ->shouldReceive('offsetGet')->with('config')->andReturn($config)
            ->shouldReceive('configPath')
            ->shouldReceive('basePath')
            ->shouldReceive('publicPath')
            ->shouldReceive('offsetGet')->with('view')->andReturn($view)
            ->shouldReceive('routesAreCached')->andReturn(false)
            ->shouldReceive('singleton')->with(Kernel::class, Kernel::class)
            ->shouldReceive('singleton')->with(Application::class, m::type(Closure::class));

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $serviceProvider = new ServiceProvider($app);
        $serviceProvider->register();
        $serviceProvider->boot($request, $router, $config);
    }
}

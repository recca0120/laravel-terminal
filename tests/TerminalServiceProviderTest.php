<?php

use Mockery as m;
use Recca0120\Terminal\TerminalServiceProvider;

class TerminalServiceProviderTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_register()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $app = m::spy('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $config = m::spy('Illuminate\Contracts\Config\Repository, ArrayAccess');
        $events = m::spy('Illuminate\Contracts\Events\Dispatcher');
        $urlGenerator = m::spy('Illuminate\Contracts\Routing\UrlGenerator');
        $kernel = m::spy('Recca0120\Terminal\Kernel');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $app
            ->shouldReceive('offsetGet')->with('config')->andReturn($config)
            ->shouldReceive('offsetGet')->with('events')->andReturn($events)
            ->shouldReceive('offsetGet')->with('url')->andReturn($urlGenerator)
            ->shouldReceive('singleton')->with('Recca0120\Terminal\Application', m::type('Closure'))->andReturnUsing(function ($className, $closure) use ($app) {
                $closure($app);
            })
            ->shouldReceive('singleton')->with('Recca0120\Terminal\TerminalManager', m::type('Closure'))->andReturnUsing(function ($className, $closure) use ($app) {
                $closure($app);
            })
            ->shouldReceive('make')->with('Recca0120\Terminal\Kernel')->andReturn($kernel);

        $config
            ->shouldReceive('get')->andReturn([])
            ->shouldReceive('offsetGet')->with('terminal')->andReturn(['commands' => []]);

        $serviceProvider = new TerminalServiceProvider($app);
        $serviceProvider->register();

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $app->shouldHaveReceived('singleton')->with('Recca0120\Terminal\Application', m::type('Closure'))->once();
        $app->shouldHaveReceived('singleton')->with('Recca0120\Terminal\Kernel', 'Recca0120\Terminal\Kernel')->once();
        $app->shouldHaveReceived('singleton')->with('Recca0120\Terminal\TerminalManager', m::type('Closure'))->once();
        $app->shouldHaveReceived('make')->with('Recca0120\Terminal\Kernel')->once();
        $app->shouldHaveReceived('version')->twice();
        $app->shouldHaveReceived('basePath')->once();
        $app->shouldHaveReceived('environment')->once();
    }

    public function test_boot()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $app = m::spy('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $request = m::spy('Illuminate\Http\Request');
        $router = m::spy('Illuminate\Routing\Router');
        $view = m::spy('Illuminate\Contracts\View\Factory');
        $clientIp = '127.0.0.1';
        $config = [
            'terminal' => [
                'whitelists' => [$clientIp],
            ],
        ];

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $app
            ->shouldReceive('runningInConsole')->andReturn(true)
            ->shouldReceive('offsetGet')->with('config')->andReturn($config)
            ->shouldReceive('offsetGet')->with('view')->andReturn($view)
            ->shouldReceive('routesAreCached')->andReturn(false);

        $request
            ->shouldReceive('getClientIp')->andReturn($clientIp);

        $router->shouldReceive('group')->with(['namespace' => 'Recca0120\Terminal\Http\Controllers'], m::type('Closure'))->andReturnUsing(function ($config, $closure) use ($router) {
            $closure($router);
        });

        $serviceProvider = new TerminalServiceProvider($app);
        $serviceProvider->boot($request, $router);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $app->shouldHaveReceived('routesAreCached')->once();
        $router->shouldHaveReceived('group')->with(['namespace' => 'Recca0120\Terminal\Http\Controllers'], m::type('Closure'))->once();
        $app->shouldHaveReceived('runningInConsole')->once();
        $app->shouldHaveReceived('configPath');
        $app->shouldHaveReceived('basePath');
        $app->shouldHaveReceived('publicPath');
    }
}

if (function_exists('env') === false) {
    function env($env)
    {
        switch ($env) {
            case 'APP_ENV':
                return 'local';
                break;

            case 'APP_DEBUG':
                return true;
                break;
        }
    }
}

if (class_exists('Route') === false) {
    class Route
    {
        public static function __callStatic($method, $arguments)
        {
            return new static;
        }

        public function __call($method, $arguments)
        {
        }
    }
}

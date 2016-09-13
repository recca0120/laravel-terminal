<?php

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Mockery as m;
use Recca0120\Terminal\ServiceProvider;
use Recca0120\Terminal\Kernel;
use Recca0120\Terminal\Application as Artisan;
use Symfony\Component\Console\Command\Command;
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

        $app = m::mock(Application::class.','.ArrayAccess::class);
        $config = m::mock(Repository::class);
        $request = m::mock(Request::class);
        $router = m::mock(Router::class);
        $view = m::mock(stdClass::class);
        $events = m::mock(Dispatcher::class);


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
            ->shouldReceive('offsetGet')->with('view')->once()->andReturn($view)
            ->shouldReceive('routesAreCached')->once()->andReturn(false)
            ->shouldReceive('offsetGet')->with('events')->times(3)->andReturn($events)
            ->shouldReceive('version')->andReturn('testing')->once()
            ->shouldReceive('singleton')->with(Kernel::class, Kernel::class)
            ->shouldReceive('singleton')->with(Artisan::class, m::type(Closure::class))->andReturnUsing(function($className, $closure) use ($app) {
                return $closure($app);
            })
            ->shouldReceive('make')->andReturnUsing(function() {
                $command = m::mock(Command::class);

                $command->shouldReceive('setApplication')
                    ->shouldReceive('isEnabled')->andReturn(false);
                return $command;
            });

        $events
            ->shouldReceive('fire')->once()
            ->shouldReceive('firing')->once();

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

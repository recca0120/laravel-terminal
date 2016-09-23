<?php

use Mockery as m;
use Recca0120\Terminal\ServiceProvider;

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

        $app = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $config = m::mock('Illuminate\Contracts\Config\Repository');
        $request = m::mock('Illuminate\Http\Request');
        $router = m::mock('Illuminate\Routing\Router');
        $view = m::mock('stdClass');
        $events = m::mock('Illuminate\Contracts\Events\Dispatcher');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $router->shouldReceive('group')->with(m::any(), m::type('Closure'))->andReturnUsing(function ($options, $closure) use ($router) {
            // $closure($router);
        });

        $view->shouldReceive('addNamespace')->with('terminal', m::any());

        $config
            ->shouldReceive('get')->with('app.debug', false)->once()->andReturn(false)
            ->shouldReceive('get')->with('terminal', [])->twice()->andReturn([
                'whitelists' => ['127.0.0.1'],
            ])
            ->shouldReceive('set')->with('terminal', m::any())->once()
            ->shouldReceive('get')->with('terminal.commands')->once()->andReturn([]);

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
            ->shouldReceive('version')->andReturn(5.0)->twice()
            ->shouldReceive('singleton')->with('Recca0120\Terminal\Kernel', 'Recca0120\Terminal\Kernel')
            ->shouldReceive('singleton')->with('Recca0120\Terminal\Application', m::type('Closure'))->andReturnUsing(function ($className, $closure) use ($app) {
                return $closure($app);
            })
            ->shouldReceive('make')->andReturnUsing(function () {
                $command = m::mock('Symfony\Component\Console\Command\Command');

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

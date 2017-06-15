<?php

namespace Recca0120\Terminal\Tests;

use Mockery as m;
use Recca0120\Terminal\Kernel;
use PHPUnit\Framework\TestCase;
use Illuminate\Container\Container;
use Recca0120\Terminal\TerminalServiceProvider;

class TerminalServiceProviderTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $container = m::mock(new Container);
        $container->instance('path.public', __DIR__);
        $container->instance('path.config', __DIR__);
        $container->shouldReceive('basePath')->andReturn(__DIR__);
        $container->shouldReceive('resourcePath')->andReturn(__DIR__);
        Container::setInstance($container);
    }

    protected function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testRegister()
    {
        $serviceProvider = new TerminalServiceProvider(
            $app = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess')
        );
        $app->shouldReceive('offsetGet')->twice()->with('config')->andReturn(
            $config = m::mock('Illuminate\Contracts\Config\Repository, ArrayAccess')
        );
        $config->shouldReceive('get')->once()->with('terminal', [])->andReturn([]);
        $config->shouldReceive('set')->once()->with('terminal', m::type('array'));

        $app->shouldReceive('singleton')->once()->with(
            'Recca0120\Terminal\Application', m::on(function ($closure) use ($app) {
                $app->shouldReceive('offsetGet')->once()->with('config')->andReturn(
                    $config = ['terminal' => ['commands' => []]]
                );
                $app->shouldReceive('offsetGet')->once()->with('events')->andReturn(
                    $events = m::mock('Illuminate\Contracts\Events\Dispatcher')
                );
                $app->shouldReceive('version')->once()->andReturn('testing');
                $events->shouldReceive('fire');
                $events->shouldReceive('dispatch');
                $this->assertInstanceOf('Recca0120\Terminal\Application', $closure($app));

                return true;
            })
        );

        $app->shouldReceive('singleton')->once()->with(
            'Recca0120\Terminal\Kernel', m::on(function ($closure) use ($app) {
                $app->shouldReceive('offsetGet')->once()->with('config')->andReturn(
                    $config = ['terminal' => ['route' => ['as' => 'foo.']]]
                );
                $app->shouldReceive('offsetGet')->once()->with('Recca0120\Terminal\Application')->andReturn(
                    $artisan = m::mock('Recca0120\Terminal\Application')
                );
                $app->shouldReceive('offsetGet')->once()->with('url')->andReturn(
                    $url = m::mock('Illuminate\Contracts\Routing\UrlGenerator')
                );
                $app->shouldReceive('basePath')->andReturn($basePath = 'foo');
                $app->shouldReceive('environment')->once()->andReturn($environment = 'foo');
                $app->shouldReceive('version')->once()->andReturn($version = 'foo');
                $url->shouldReceive('route')->once()->with('foo.endpoint')->andReturn('foo');

                return $closure($app) instanceof Kernel;
            })
        );

        $serviceProvider->register();
    }

    public function testBoot()
    {
        $serviceProvider = new TerminalServiceProvider(
            $app = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess')
        );
        $app->shouldReceive('offsetGet')->once()->with('config')->andReturn(
            $config = [
                'terminal' => [
                    'route' => [
                        'prefix' => 'terminal',
                        'as' => 'terminal.',
                    ],
                    'whitelists' => [
                        $ip = '127.0.0.1',
                    ],
                ],
            ]
        );
        $request = m::mock('Illuminate\Http\Request');
        $router = m::mock('Illuminate\Routing\Router');

        $request->shouldReceive('getClientIp')->once()->andReturn($ip);
        $app->shouldReceive('offsetGet')->once()->with('view')->andReturn(
            $view = m::mock('Illuminate\Contracts\View\Factory')
        );
        $view->shouldReceive('addNamespace')->once();

        $app->shouldReceive('routesAreCached')->once()->andReturn(false);
        $router->shouldReceive('group')->once()->with([
            'namespace' => 'Recca0120\Terminal\Http\Controllers',
            'prefix' => 'terminal',
            'as' => 'terminal.',
        ], m::on(function ($closure) use ($router) {
            return true;
        }));

        $app->shouldReceive('runningInConsole')->once()->andReturn(true);
        $app->shouldReceive('basePath');
        $app->shouldReceive('resourcePath');

        $this->assertNull($serviceProvider->boot($request, $router));
    }
}

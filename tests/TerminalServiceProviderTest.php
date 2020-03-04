<?php

namespace Recca0120\Terminal\Tests;

use Illuminate\Config\Repository as Config;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\Terminal\Application;
use Recca0120\Terminal\Kernel;
use Recca0120\Terminal\TerminalServiceProvider;

class TerminalServiceProviderTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testBoot()
    {
        $app = m::mock(implode(',', [
            Container::class,
            \Illuminate\Contracts\Foundation\Application::class,
        ]))->makePartial();
        Container::setInstance($app);

        $events = new Dispatcher($app);
        $url = m::mock(UrlGenerator::class);
        $url->shouldReceive('route');

        $app->instance('path.public', __DIR__);
        $app->instance('path.config', __DIR__);
        $app->shouldReceive('basePath')->andReturn(__DIR__);
        $app->shouldReceive('resourcePath')->andReturn(__DIR__);
        $app->shouldReceive('version')->andReturn('7.0');
        $app->shouldReceive('environment')->andReturn('local');
        $app['events'] = $events;
        $app['url'] = $url;

        $app['config'] = new Config([
            'terminal' => array_merge(require __DIR__.'/../config/terminal.php', [
                'whitelists' => ['127.0.0.1'],
                'commands' => [],
            ]),
        ]);

        $app->shouldReceive('runningInConsole')->andReturn(true);
        $app->shouldReceive('routesAreCached')->once()->andReturn(false);

        $request = m::mock('Illuminate\Http\Request');
        $request->shouldReceive('getClientIp')->andReturn('127.0.0.1');

        $router = m::mock('Illuminate\Routing\Router');
        $router->shouldReceive('group');

        $serviceProvider = new TerminalServiceProvider($app);
        $serviceProvider->register();
        $serviceProvider->boot($request, $router);

        $this->assertInstanceOf(Application::class, $app->make(Application::class));
        $this->assertInstanceOf(Kernel::class, $app->make(Kernel::class));
    }
}

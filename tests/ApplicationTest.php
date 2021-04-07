<?php

namespace Recca0120\Terminal\Tests;

use Exception;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\Terminal\Application;
use Recca0120\Terminal\Console\Commands\Artisan;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

class ApplicationTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @throws Exception
     */
    public function test_call_method()
    {
        $laravel = new Container();
        $request = Request::capture();
        $events = new Dispatcher();
        $laravel->instance('request', $request);

        $application = new Application($laravel, $events, $version = 'testing');

        self::assertSame(0, $application->call('help', ['list']));
    }

    /**
     * @throws Exception
     */
    public function test_call_method_when_request_is_ajax()
    {
        $laravel = new Container();
        $request = m::mock(Request::capture());
        $request->shouldReceive('ajax')->andReturn(true);
        $events = new Dispatcher();
        $laravel->instance('request', $request);

        $application = new Application($laravel, $events, $version = 'testing');

        self::assertSame(0, $application->call('help', ['list']));
    }

    public function test_resolve_commands()
    {
        $laravel = m::mock(new Container());
        $request = Request::capture();
        $events = new Dispatcher();
        $laravel->instance('request', $request);

        $application = new Application($laravel, $events, $version = 'testing');

        $command = Artisan::class;
        $laravel->shouldReceive('make')->once()->with($command)->andReturn(new HelpCommand);
        self::assertSame($application, $application->resolveCommands([$command]));
    }

    /**
     * @throws Exception
     */
    public function test_run_method()
    {
        $laravel = new Container();
        $request = Request::capture();
        $events = new Dispatcher();
        $laravel->instance('request', $request);

        $application = new Application($laravel, $events, $version = 'testing');

        self::assertSame(0, $application->run(new StringInput('help'), new BufferedOutput()));
    }
}

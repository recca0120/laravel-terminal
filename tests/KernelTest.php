<?php

namespace Recca0120\Terminal\Tests;

use Carbon\CarbonInterval;
use Exception;
use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\Terminal\Application;
use Recca0120\Terminal\Kernel;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class KernelTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @throws Exception
     */
    public function test_handle_method()
    {
        $container = new Container();
        $request = Request::capture();
        $container->instance('request', $request);
        $artisan = new Application($container, new Dispatcher(), 'testing');
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $kernel = new Kernel($artisan);

        self::assertSame(0, $kernel->handle($input, $output));
    }

    /**
     * @throws Exception
     */
    public function test_call_method()
    {
        $container = new Container();
        $request = Request::capture();
        $container->instance('request', $request);
        $artisan = new Application($container, new Dispatcher(), 'testing');
        $output = new BufferedOutput();

        $kernel = new Kernel($artisan);

        self::assertSame(0, $kernel->call('help', ['list'], $output));

        return [$kernel];
    }

    /**
     * @throws Exception
     */
    public function test_output_method()
    {
        $container = new Container();
        $request = Request::capture();
        $container->instance('request', $request);
        $artisan = new Application($container, new Dispatcher(), 'testing');
        $kernel = new Kernel($artisan);
        $kernel->call('help', ['list'], new BufferedOutput());

        self::assertStringContainsString('--raw', $kernel->output());
    }

    public function test_queue_method_and_laravel_version_less_then_54()
    {
        $container = new Container();
        $request = Request::capture();
        $container->instance('request', $request);
        $queue = m::spy(Queue::class);
        $container->instance(Queue::class, $queue);
        $artisan = new Application($container, new Dispatcher(), '5.3.9');

        $kernel = new Kernel($artisan);
        $command = 'help';
        $parameters = ['list'];

        $kernel->queue($command, $parameters);

        $queue->shouldHaveReceived('push')
            ->with('Illuminate\Foundation\Console\QueuedJob', m::on(function ($args) use ($command, $parameters) {
                return [$command, $parameters] === $args;
            }));
    }

    public function test_all_method()
    {
        $artisan = new Application(new Container(), new Dispatcher(), 'testing');

        $kernel = new Kernel($artisan);

        self::assertArrayHasKey('help', $kernel->all());
    }

    public function test_terminate_method()
    {
        $artisan = m::spy(new Application(new Container(), new Dispatcher(), 'testing'));
        $kernel = new Kernel($artisan);
        $input = new ArrayInput([]);

        $kernel->terminate($input, 0);

        $artisan->shouldHaveReceived('terminate');
    }

    public function test_call_when_command_lifecycle_is_longer_than()
    {
        $artisan = m::spy(new Application(new Container(), new Dispatcher(), 'testing'));
        $kernel = new Kernel($artisan);

        $carbonInterval = CarbonInterval::seconds(1);
        $closure = function () {
        };
        $kernel->whenCommandLifecycleIsLongerThan($carbonInterval, $closure);

        $artisan->shouldHaveReceived('whenCommandLifecycleIsLongerThan')
            ->with($carbonInterval, $closure)
            ->once();
    }
}

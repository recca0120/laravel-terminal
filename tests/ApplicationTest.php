<?php

namespace Recca0120\Terminal\Tests;

use Mockery as m;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use Recca0120\Terminal\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Output\BufferedOutput;

class ApplicationTest extends TestCase
{
    protected function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testCall()
    {
        $laravel = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $laravel->shouldReceive('offsetGet')->once()->with('request')->andReturn(
            $request = m::mock('Illuminate\Http\Request')
        );
        $request->shouldReceive('ajax')->once()->andReturn(false);
        $events = m::mock('Illuminate\Contracts\Events\Dispatcher');
        $events->shouldReceive('fire');
        $events->shouldReceive('dispatch');

        $application = new Application(
            $laravel,
            $events,
            $version = 'testing'
        );
        $command = 'help';
        $parameters = ['--foo'];
        $this->assertSame(0, $application->call($command, $parameters));
    }

    public function testCallAndRequestIsAjax()
    {
        $laravel = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $laravel->shouldReceive('offsetGet')->once()->with('request')->andReturn(
            $request = m::mock('Illuminate\Http\Request')
        );
        $request->shouldReceive('ajax')->once()->andReturn(true);
        $events = m::mock('Illuminate\Contracts\Events\Dispatcher');
        $events->shouldReceive('fire');
        $events->shouldReceive('dispatch');

        $application = new Application(
            $laravel,
            $events,
            $version = 'testing'
        );
        $command = 'help';
        $parameters = ['--foo'];
        $this->assertSame(0, $application->call($command, $parameters));
    }

    public function testResolveCommands()
    {
        $laravel = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $events = m::mock('Illuminate\Contracts\Events\Dispatcher');
        $events->shouldReceive('fire');
        $events->shouldReceive('dispatch');

        $application = new Application(
            $laravel,
            $events,
            $version = 'testing'
        );

        $laravel->shouldReceive('make')->once()->with(
            $command = 'Symfony\Component\Console\Command\HelpCommand'
        )->andReturn(new HelpCommand);
        $this->assertSame($application, $application->resolveCommands($command, true));
    }

    public function testRun()
    {
        $laravel = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess');

        $events = m::mock('Illuminate\Contracts\Events\Dispatcher');
        $events->shouldReceive('fire');
        $events->shouldReceive('dispatch');

        $application = new Application(
            $laravel,
            $events,
            $version = 'testing'
        );

        $this->assertSame(0, $application->run(
            new StringInput('help'),
            new BufferedOutput()
        ));
    }
}

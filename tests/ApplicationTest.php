<?php

namespace Recca0120\Terminal\Tests;

use Mockery as m;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use Recca0120\Terminal\Application;
use Symfony\Component\Console\Command\HelpCommand;

class ApplicationTest extends TestCase
{
    protected function tearDown()
    {
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
        $application->call($command, $parameters);
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
        $application->call($command, $parameters);
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
        $application->resolveCommands($command, true);
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

        $application->run(
            $input = m::spy('Symfony\Component\Console\Input\InputInterface'),
            $output = m::spy('Symfony\Component\Console\Output\OutputInterface')
        );
    }
}

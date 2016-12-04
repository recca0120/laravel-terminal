<?php

use Mockery as m;
use Illuminate\Http\Request;
use Recca0120\Terminal\Application as Artisan;
use Recca0120\Terminal\Console\Commands\Artisan as ArtisanCommand;

class ApplicationTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_run()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $app = m::spy('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $events = m::spy('Illuminate\Contracts\Events\Dispatcher');
        $version = 'testing';
        $input = m::spy('Symfony\Component\Console\Input\InputInterface');
        $output = m::spy('Symfony\Component\Console\Output\OutputInterface');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $artisan = new Artisan($app, $events, $version);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame(0, $artisan->run($input, $output));
    }

    /**
     * @expectedException Exception
     */
    public function test_run_when_throw_exception()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $app = m::spy('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $events = m::spy('Illuminate\Contracts\Events\Dispatcher');
        $version = 'testing';
        $input = m::spy('Symfony\Component\Console\Input\InputInterface');
        $output = m::spy('Symfony\Component\Console\Output\OutputInterface');
        $request = m::spy('Illuminate\Http\Request');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $input->shouldReceive('hasParameterOption')->andThrow('Exception');

        $app
            ->shouldReceive('offsetGet')->with('request')->andReturn($request);

        $request
            ->shouldReceive('ajax')->andReturn(false);

        $artisan = new Artisan($app, $events, $version);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $artisan->run($input, $output);

        $request->shouldHaveReceived('ajax')->once();
    }

    public function test_run_when_throw_exception_and_request_is_ajax()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $app = m::spy('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $events = m::spy('Illuminate\Contracts\Events\Dispatcher');
        $version = 'testing';
        $input = m::spy('Symfony\Component\Console\Input\InputInterface');
        $output = m::spy('Symfony\Component\Console\Output\OutputInterface');
        $request = m::spy('Illuminate\Http\Request');
        $formatter = m::spy('Symfony\Component\Console\Formatter\OutputFormatterInterface');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $input->shouldReceive('hasParameterOption')->andThrow(new Exception('test', 0, new Exception));

        $app
            ->shouldReceive('offsetGet')->with('request')->andReturn($request);

        $request
            ->shouldReceive('ajax')->andReturn(true);

        $output->shouldReceive('getFormatter')->andReturn($formatter);

        $artisan = new Artisan($app, $events, $version);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $artisan->run($input, $output);

        $request->shouldHaveReceived('ajax')->once();
    }

    /**
     * @requires PHP 7
     */
    public function test_run_when_throw_throwable()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $app = m::spy('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $events = m::spy('Illuminate\Contracts\Events\Dispatcher');
        $version = 'testing';
        $input = m::spy('Symfony\Component\Console\Input\InputInterface');
        $output = m::spy('Symfony\Component\Console\Output\OutputInterface');
        $request = m::spy('Illuminate\Http\Request');
        $formatter = m::spy('Symfony\Component\Console\Formatter\OutputFormatterInterface');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $input->shouldReceive('hasParameterOption')->andReturnUsing(function () {
            function_not_exists();
        });

        $app
            ->shouldReceive('offsetGet')->with('request')->andReturn($request);

        $request
            ->shouldReceive('ajax')->andReturn(false);

        $output->shouldReceive('getFormatter')->andReturn($formatter);

        $artisan = new Artisan($app, $events, $version);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        try {
            $artisan->run($input, $output);
        } catch (Throwable $e) {
        }

        $this->assertInstanceOf('Throwable', $e);

        $request->shouldHaveReceived('ajax')->once();
    }

    /**
     * @requires PHP 7
     */
    public function test_run_when_throw_throwable_and_request_is_ajax()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $app = m::spy('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $events = m::spy('Illuminate\Contracts\Events\Dispatcher');
        $version = 'testing';
        $input = m::spy('Symfony\Component\Console\Input\InputInterface');
        $output = m::spy('Symfony\Component\Console\Output\OutputInterface');
        $request = m::spy('Illuminate\Http\Request');
        $formatter = m::spy('Symfony\Component\Console\Formatter\OutputFormatterInterface');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $input->shouldReceive('hasParameterOption')->andReturnUsing(function () {
            function_not_exists();
        });

        $app
            ->shouldReceive('offsetGet')->with('request')->andReturn($request);

        $request
            ->shouldReceive('ajax')->andReturn(true);

        $output->shouldReceive('getFormatter')->andReturn($formatter);

        $artisan = new Artisan($app, $events, $version);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame(1, $artisan->run($input, $output));

        $request->shouldHaveReceived('ajax')->once();
    }

    public function test_resolve_commands()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $app = m::spy('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $events = m::spy('Illuminate\Contracts\Events\Dispatcher');
        $version = 'testing';
        $eventString = $this->getArtisanEventString();

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $artisan = new Artisan($app, $events, $version);
        $artisan->resolveCommands([]);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */
    }

    public function test_resolve_commands_and_receive_artisan_event_string()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $app = m::spy('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $events = m::spy('Illuminate\Contracts\Events\Dispatcher');
        $version = 'testing';
        $eventString = $this->getArtisanEventString();

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $app
            ->shouldReceive('offsetGet')->with('events')->andReturn($events);

        $events
            ->shouldReceive('firing')->andReturn($eventString);

        $artisan = new Artisan($app, $events, $version);
        $artisan->resolveCommands([]);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $events->shouldHaveReceived('firing')->once();
    }

    public function test_call()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $app = m::spy('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $events = m::spy('Illuminate\Contracts\Events\Dispatcher');
        $version = 'testing';
        $kernel = m::spy('Illuminate\Contracts\Console\Kernel');
        $command = new ArtisanCommand($kernel);
        $commandString = 'artisan';
        $parameters = [];

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $artisan = new Artisan($app, $events, $version);
        $artisan->add($command);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame(0, $artisan->call($commandString));
    }

    public function test_call_when_request_is_ajax()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $app = m::spy('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $events = m::spy('Illuminate\Contracts\Events\Dispatcher');
        $version = 'testing';
        $request = m::spy('Illuminate\Http\Request');
        $kernel = m::spy('Illuminate\Contracts\Console\Kernel');
        $command = new ArtisanCommand($kernel);
        $commandString = 'artisan';
        $parameters = [];

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $app
            ->shouldReceive('offsetGet')->with('request')->andReturn($request);

        $request
            ->shouldReceive('ajax')->andReturn(true);

        $artisan = new Artisan($app, $events, $version);
        $artisan->add($command);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame(0, $artisan->call($commandString));

        $request->shouldHaveReceived('ajax')->once();
    }

    protected function getArtisanEventString()
    {
        return class_exists('Illuminate\Console\Events\ArtisanStarting') === false ? 'artisan.start' : 'Illuminate\Console\Events\ArtisanStarting';
    }
}

<?php

use Illuminate\Http\Request;
use Mockery as m;
use Recca0120\Terminal\Application as Artisan;
use Recca0120\Terminal\Console\Commands\Artisan as ArtisanCommand;

class ApplicationTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_call()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $events = m::mock('Illuminate\Contracts\Events\Dispatcher');
        $app = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $request = m::mock('Illuminate\Http\Request');
        $kernel = m::mock('Illuminate\Contracts\Console\Kernel');
        $command = m::mock(new ArtisanCommand($kernel));

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $events->shouldReceive('fire')->once();

        $app
            ->shouldReceive('offsetGet')->with('request')->once()->andReturn(null)
            ->shouldReceive('offsetGet')->with('events')->once()->andReturn(null)
            ->shouldReceive('basePath')->andReturn(__DIR__)
            ->shouldReceive('storagePath')->andReturn(__DIR__)
            ->shouldReceive('version')->andReturn('testing')
            ->shouldReceive('call');

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $application = new Artisan($app, $events, 'testing');
        $application->resolveCommands([]);
        $application->add($command);
        $application->call('artisan');
    }

    public function test_artisan_string()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $events = m::mock('Illuminate\Contracts\Events\Dispatcher');
        $app = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $request = m::mock('Illuminate\Http\Request');
        $kernel = m::mock('Illuminate\Contracts\Console\Kernel');
        $command = m::mock(new ArtisanCommand($kernel));

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $events
            ->shouldReceive('fire')->once()
            ->shouldReceive('firing')->andReturn($this->getArtisanString());

        $app
            ->shouldReceive('offsetGet')->with('request')->twice()->andReturn($request)
            ->shouldReceive('offsetGet')->with('events')->twice()->andReturn($events)
            ->shouldReceive('basePath')->andReturn(__DIR__)
            ->shouldReceive('storagePath')->andReturn(__DIR__)
            ->shouldReceive('version')->andReturn('testing')
            ->shouldReceive('call');

        $request->shouldReceive('ajax')->andReturn(true);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $application = new Artisan($app, $events, 'testing');
        $application->resolveCommands([]);
        $application->add($command);
        $application->call('artisan');
    }

    /**
     * @expectedException Exception
     */
    public function test_exception()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $events = m::mock('Illuminate\Contracts\Events\Dispatcher');
        $app = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $request = m::mock('Illuminate\Http\Request');
        $input = m::mock('Symfony\Component\Console\Input\InputInterface');
        $output = m::mock('Symfony\Component\Console\Output\OutputInterface');
        $exception = m::mock('Exception');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $events
            ->shouldReceive('fire')->once()
            ->shouldReceive('firing')->andReturn('Illuminate\Console\Events\ArtisanStarting');

        $app
            ->shouldReceive('offsetGet')->with('request')->twice()->andReturn($request)
            // ->shouldReceive('offsetGet')->with('events')->twice()->andReturn($events)
            ->shouldReceive('basePath')->andReturn(__DIR__)
            ->shouldReceive('storagePath')->andReturn(__DIR__);

        $request->shouldReceive('ajax')->andReturn(false);

        $output->shouldReceive('writeln')->andReturnUsing(function () use ($exception) {
            throw $exception;
        });

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $application = new Artisan($app, $events, 'testing');
        $application->run($input, $output);
    }

    /**
     * @expectedException Exception
     */
    public function test_exception_with_ajax()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $events = m::mock('Illuminate\Contracts\Events\Dispatcher');
        $app = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $request = m::mock('Illuminate\Http\Request');
        $input = m::mock('Symfony\Component\Console\Input\InputInterface');
        $output = m::mock('Symfony\Component\Console\Output\OutputInterface');
        $exception = m::mock('Exception');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $events
            ->shouldReceive('fire')->once()
            ->shouldReceive('firing')->andReturn('Illuminate\Console\Events\ArtisanStarting');

        $app
            ->shouldReceive('offsetGet')->with('request')->twice()->andReturn($request)
            // ->shouldReceive('offsetGet')->with('events')->twice()->andReturn($events)
            ->shouldReceive('basePath')->andReturn(__DIR__)
            ->shouldReceive('storagePath')->andReturn(__DIR__);

        $request->shouldReceive('ajax')->once()->andReturn(true);

        $output->shouldReceive('writeln')->andReturnUsing(function () use ($exception) {
            throw $exception;
        });

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $application = new Artisan($app, $events, 'testing');
        $application->run($input, $output);
    }

    protected function getArtisanString() {
        return class_exists('Illuminate\Console\Events\ArtisanStarting') === false?'artisan.start':'Illuminate\Console\Events\ArtisanStarting';
    }
}

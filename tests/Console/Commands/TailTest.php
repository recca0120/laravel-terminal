<?php

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Mockery as m;
use Recca0120\Terminal\Application;
use Recca0120\Terminal\Console\Commands\Tail;

class TailTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    protected function getArtisan()
    {
        $events = m::mock(DispatcherContract::class);
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);
        $request = m::mock(Request::class);

        $request->shouldReceive('ajax')->andReturn(true);
        $events->shouldReceive('fire');

        $app
            ->shouldReceive('offsetGet')->with('request')->andReturn($request)
            ->shouldReceive('basePath')->andReturn(__DIR__)
            ->shouldReceive('storagePath')->andReturn(__DIR__);

        return new Application($app, $events, 'testing');
    }

    public function test_handle()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $artisan = $this->getArtisan();
        $filesystem = m::mock(Filesystem::class);
        $command = new Tail();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $artisan->add($command);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $artisan->getLaravel()->shouldReceive('call')->andReturnUsing(function () use ($command, $filesystem) {
            $command->handle($filesystem);
        })->once();

        $artisan->call('tail TailTest.php --lines 5');
    }

    public function test_handle_glob()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $artisan = $this->getArtisan();
        $filesystem = m::mock(Filesystem::class);
        $command = new Tail();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $artisan->add($command);
        $filesystem->shouldReceive('glob')->once()->andReturn([
            __FILE__,
        ]);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $artisan->getLaravel()->shouldReceive('call')->andReturnUsing(function () use ($command, $filesystem) {
            $command->handle($filesystem);
        })->once();

        $artisan->call('tail --lines 5');
    }

    public function test_handle_file_not_found()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $artisan = $this->getArtisan();
        $filesystem = m::mock(Filesystem::class);
        $command = new Tail();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $artisan->add($command);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $artisan->getLaravel()->shouldReceive('call')->andReturnUsing(function () use ($command, $filesystem) {
            $command->handle($filesystem);
        })->once();

        $artisan->call('tail TailTest1.php --lines 5');
    }
}

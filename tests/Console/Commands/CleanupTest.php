<?php

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Mockery as m;
use Recca0120\Terminal\Application as Artisan;
use Recca0120\Terminal\Console\Commands\Cleanup;

class CleanupTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    protected function getArtisan()
    {
        $events = m::mock(Dispatcher::class);
        $app = m::mock(Application::class.','.ArrayAccess::class);
        $request = m::mock(Request::class);

        $request->shouldReceive('ajax')->andReturn(true);
        $events->shouldReceive('fire');

        $app
            ->shouldReceive('offsetGet')->with('request')->andReturn($request)
            ->shouldReceive('basePath')->andReturn(__DIR__)
            ->shouldReceive('storagePath')->andReturn(__DIR__);

        return new Artisan($app, $events, 'testing');
    }

    public function test_handle_cleanup_directory()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $artisan = $this->getArtisan();
        $filesystem = m::mock(Filesystem::class);
        $command = new Cleanup();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $artisan->add($command);
        $filesystem
            ->shouldReceive('glob')->andReturn([
                'Foo.php',
                'Bar.php',
            ])
            ->shouldReceive('isDirectory')->andReturn(true)
            ->shouldReceive('deleteDirectory');

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $artisan->getLaravel()->shouldReceive('call')->andReturnUsing(function () use ($command, $filesystem) {
            $command->handle($filesystem);
        })->once();
        $artisan->call('cleanup');
    }

    public function test_handle_cleanup_file()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $artisan = $this->getArtisan();
        $filesystem = m::mock(Filesystem::class);
        $command = new Cleanup();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $artisan->add($command);
        $filesystem
            ->shouldReceive('glob')->andReturn([
                'Foo.php',
            ])
            ->shouldReceive('isDirectory')->andReturn(false)
            ->shouldReceive('deleteDirectory');

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $artisan->getLaravel()->shouldReceive('call')->andReturnUsing(function () use ($command, $filesystem) {
            $command->handle($filesystem);
        })->once();
        $artisan->call('cleanup');
    }

    public function test_handle_file_not_exists()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $artisan = $this->getArtisan();
        $filesystem = m::mock(Filesystem::class);
        $command = new Cleanup();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $artisan->add($command);
        $filesystem
            ->shouldReceive('glob')->andReturn([])
            ->shouldReceive('isDirectory')->andReturn(false)
            ->shouldReceive('deleteDirectory');

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $artisan->getLaravel()->shouldReceive('call')->andReturnUsing(function () use ($command, $filesystem) {
            $command->handle($filesystem);
        })->once();
        $artisan->call('cleanup');
    }
}

<?php

use Mockery as m;
use Recca0120\Terminal\Console\Commands\Cleanup;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class CleanupTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_handle_cleanup_directory()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $filesystem = m::mock('Illuminate\Filesystem\Filesystem');
        $command = new Cleanup($filesystem);
        $laravel = m::mock('Illuminate\Contracts\Foundation\Application');
        $command->setLaravel($laravel);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $filesystem
            ->shouldReceive('glob')->andReturn([
                'Foo.php',
                'Bar.php',
            ])
            ->shouldReceive('isDirectory')->andReturn(true)
            ->shouldReceive('deleteDirectory');

        $laravel
            ->shouldReceive('basePath')->once()->andReturn(__DIR__)
            ->shouldReceive('call')->once()->andReturnUsing(function ($command) {
                call_user_func($command);
            });

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $command->run(new StringInput(''), new NullOutput);
    }

    public function test_handle_cleanup_file()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $filesystem = m::mock('Illuminate\Filesystem\Filesystem');
        $command = new Cleanup($filesystem);
        $laravel = m::mock('Illuminate\Contracts\Foundation\Application');
        $command->setLaravel($laravel);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $filesystem
            ->shouldReceive('glob')->andReturn([
                'Foo.php',
            ])
            ->shouldReceive('isDirectory')->andReturn(false)
            ->shouldReceive('deleteDirectory');

        $laravel
            ->shouldReceive('basePath')->once()->andReturn(__DIR__)
            ->shouldReceive('call')->once()->andReturnUsing(function ($command) {
                call_user_func($command);
            });

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $command->run(new StringInput(''), new NullOutput);
    }

    public function test_handle_file_not_exists()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $filesystem = m::mock('Illuminate\Filesystem\Filesystem');
        $command = new Cleanup($filesystem);
        $laravel = m::mock('Illuminate\Contracts\Foundation\Application');
        $command->setLaravel($laravel);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $filesystem
            ->shouldReceive('glob')->andReturn([])
            ->shouldReceive('isDirectory')->andReturn(false)
            ->shouldReceive('deleteDirectory');

        $laravel
            ->shouldReceive('basePath')->once()->andReturn(__DIR__)
            ->shouldReceive('call')->once()->andReturnUsing(function ($command) {
                call_user_func($command);
            });

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $command->run(new StringInput(''), new NullOutput);
    }
}

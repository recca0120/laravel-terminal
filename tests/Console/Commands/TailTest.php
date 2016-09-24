<?php

use Mockery as m;
use Recca0120\Terminal\Console\Commands\Tail;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class TailTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_handle()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $filesystem = m::mock('Illuminate\Filesystem\Filesystem');
        $command = new Tail($filesystem);
        $laravel = m::mock('Illuminate\Contracts\Foundation\Application');
        $command->setLaravel($laravel);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

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

        $command->run(new StringInput('TailTest.php --lines 5'), new NullOutput);
    }

    public function test_handle_glob()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $filesystem = m::mock('Illuminate\Filesystem\Filesystem');
        $command = new Tail($filesystem);
        $laravel = m::mock('Illuminate\Contracts\Foundation\Application');
        $command->setLaravel($laravel);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $filesystem->shouldReceive('glob')->once()->andReturn([
            __FILE__,
        ]);

        $laravel
            ->shouldReceive('storagePath')->once()->andReturn(__DIR__)
            ->shouldReceive('call')->once()->andReturnUsing(function ($command) {
                call_user_func($command);
            });

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $command->run(new StringInput('--lines 5'), new NullOutput);
    }

    public function test_handle_file_not_found()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $filesystem = m::mock('Illuminate\Filesystem\Filesystem');
        $command = new Tail($filesystem);
        $laravel = m::mock('Illuminate\Contracts\Foundation\Application');
        $command->setLaravel($laravel);
        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

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

        $command->run(new StringInput('TailTest1.php --lines 5'), new NullOutput);
    }
}

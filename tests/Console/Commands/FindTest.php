<?php

use Mockery as m;
use Recca0120\Terminal\Console\Commands\Find;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class FindTest extends PHPUnit_Framework_TestCase
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

        $finder = m::mock('Symfony\Component\Finder\Finder');
        $filesystem = m::mock('Illuminate\Filesystem\Filesystem');
        $command = new Find($finder, $filesystem);
        $laravel = m::mock('Illuminate\Contracts\Foundation\Application');
        $command->setLaravel($laravel);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $finder
            ->shouldReceive('in')->with(__DIR__)
            ->shouldReceive('name')->with('*')
            ->shouldReceive('depth')->with('<1')
            ->shouldReceive('files')
            ->shouldReceive('getIterator')->andReturn(new AppendIterator());

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

        $command->run(new StringInput('./ -name * -type f -maxdepth 1 -delete'), new NullOutput);
    }

    public function test_handle_directory()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $finder = m::mock('Symfony\Component\Finder\Finder');
        $filesystem = m::mock('Illuminate\Filesystem\Filesystem');
        $command = new Find($finder, $filesystem);
        $laravel = m::mock('Illuminate\Contracts\Foundation\Application');
        $command->setLaravel($laravel);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $finder
            ->shouldReceive('in')->with(__DIR__)
            ->shouldReceive('name')->with('*')
            ->shouldReceive('depth')->with('<1')
            ->shouldReceive('directories')
            ->shouldReceive('getIterator')->andReturn(new AppendIterator());

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

        $command->run(new StringInput('./ -name * -type d -maxdepth 0 -delete'), new NullOutput);
    }
}

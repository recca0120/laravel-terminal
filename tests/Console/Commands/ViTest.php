<?php

use Mockery as m;
use Recca0120\Terminal\Console\Commands\Vi;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class ViTest extends PHPUnit_Framework_TestCase
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
        $command = new Vi($filesystem);
        $laravel = m::mock('Illuminate\Contracts\Foundation\Application');
        $command->setLaravel($laravel);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $filesystem
            ->shouldReceive('get')->with(realpath(__DIR__.'/ViTest.php'))
            ->mock();

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

        $command->run(new StringInput('ViTest.php'), new NullOutput);
    }

    public function test_handle_write()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $filesystem = m::mock('Illuminate\Filesystem\Filesystem');
        $command = new Vi($filesystem);
        $laravel = m::mock('Illuminate\Contracts\Foundation\Application');
        $command->setLaravel($laravel);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $filesystem
            ->shouldReceive('put')->with(realpath(__DIR__.'/ViTest.php'), 'abc')
            ->mock();

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

        $command->run(new StringInput('ViTest.php --text="abc"'), new NullOutput);
    }
}

<?php

use Mockery as m;
use Recca0120\Terminal\Console\Commands\Vi;

class ViTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_handle_read()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $filesystem = m::spy('Illuminate\Filesystem\Filesystem');
        $input = m::spy('Symfony\Component\Console\Input\InputInterface');
        $output = m::spy('Symfony\Component\Console\Output\OutputInterface');
        $formatter = m::spy('Symfony\Component\Console\Formatter\OutputFormatterInterface');
        $laravel = m::spy('Illuminate\Contracts\Foundation\Application');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */
        $output
            ->shouldReceive('getFormatter')->andReturn($formatter);

        $input
            ->shouldReceive('getArgument')->with('path')->andReturn('foo.path')
            ->shouldReceive('getOption')->with('text')->andReturn(null);

        $laravel
            ->shouldReceive('basePath')->andReturn('foo.basepath');

        $command = new Vi($filesystem);
        $command->setLaravel($laravel);
        $command->run($input, $output);
        $command->fire();

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $laravel->shouldHaveReceived('basePath')->once();
        $filesystem->shouldHaveReceived('get')->with('foo.basepath/foo.path')->once();
    }

    public function test_handle_write()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $filesystem = m::spy('Illuminate\Filesystem\Filesystem');
        $input = m::spy('Symfony\Component\Console\Input\InputInterface');
        $output = m::spy('Symfony\Component\Console\Output\OutputInterface');
        $formatter = m::spy('Symfony\Component\Console\Formatter\OutputFormatterInterface');
        $laravel = m::spy('Illuminate\Contracts\Foundation\Application');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */
        $output
            ->shouldReceive('getFormatter')->andReturn($formatter);

        $input
            ->shouldReceive('getArgument')->with('path')->andReturn('foo.path')
            ->shouldReceive('getOption')->with('text')->andReturn('foo.text');

        $laravel
            ->shouldReceive('basePath')->andReturn('foo.basepath');

        $command = new Vi($filesystem);
        $command->setLaravel($laravel);
        $command->run($input, $output);
        $command->fire();

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $laravel->shouldHaveReceived('basePath')->once();
        $filesystem->shouldHaveReceived('put')->with('foo.basepath/foo.path', 'foo.text')->once();
    }
}

<?php

use Mockery as m;
use Recca0120\Terminal\Console\Commands\Cleanup;

class CleanupTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_handler()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $filesystem = m::spy('Illuminate\Filesystem\Filesystem');
        // $filesystem = new Illuminate\Filesystem\Filesystem();
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
            ->shouldReceive('basePath')->andReturn(__DIR__);

        $filesystem
            ->shouldReceive('glob')->with(m::type('string'), GLOB_ONLYDIR)->andReturn([
                'foo.directory',
            ]);

        $command = new Cleanup($filesystem);
        $command->setLaravel($laravel);
        $command->run($input, $output);
        $command->fire();

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $laravel->shouldHaveReceived('basePath')->once();
        $filesystem->shouldHaveReceived('glob')->with(m::type('string'), GLOB_ONLYDIR);
        $filesystem->shouldHaveReceived('deleteDirectory')->with(m::type('string'));
    }
}

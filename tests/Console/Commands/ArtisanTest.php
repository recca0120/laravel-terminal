<?php

use Mockery as m;
use Recca0120\Terminal\Console\Commands\Artisan;

class ArtisanTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_handle()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $kernel = m::spy('Illuminate\Contracts\Console\Kernel');
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
            ->shouldReceive('getOption')->with('command')->andReturn('db:seed');

        $command = new Artisan($kernel);
        $command->setLaravel($laravel);
        $command->run($input, $output);
        $command->fire();

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $input->shouldHaveReceived('getOption')->with('command')->once();
        $kernel->shouldHaveReceived('handle')->with(m::on(function ($input) {
            return (bool) preg_match('/[\'"]db:seed[\'"] --force/', (string) $input);
        }), m::type('Symfony\Component\Console\Output\OutputInterface'))->once();
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_handle_command_no_support()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $kernel = m::spy('Illuminate\Contracts\Console\Kernel');
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
            ->shouldReceive('getOption')->with('command')->once()->andReturn('down');

        $command = new Artisan($kernel);
        $command->setLaravel($laravel);
        $command->run($input, $output);
        $command->fire();

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $input->shouldHaveReceived('getOption')->with('command')->once();
    }
}

<?php

use Mockery as m;
use Recca0120\Terminal\Console\Commands\ArtisanTinker;

class ArtisanTinkerTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_handle_echo()
    {
        $input = m::spy('Symfony\Component\Console\Input\InputInterface');
        $command = $this->getCommand($input);

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $input
            ->shouldReceive('getOption')->with('command')->andReturn('echo 123;');

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $command->fire();
    }

    public function test_handle_number()
    {
        $input = m::spy('Symfony\Component\Console\Input\InputInterface');
        $command = $this->getCommand($input);

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $input
            ->shouldReceive('getOption')->with('command')->andReturn('123;');

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $command->fire();
    }

    public function test_handle_array()
    {
        $input = m::spy('Symfony\Component\Console\Input\InputInterface');
        $command = $this->getCommand($input);

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $input
            ->shouldReceive('getOption')->with('command')->andReturn('[];');

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $command->fire();
    }

    public function test_handle_string()
    {
        $input = m::spy('Symfony\Component\Console\Input\InputInterface');
        $command = $this->getCommand($input);

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $input
            ->shouldReceive('getOption')->with('command')->andReturn('\'abc\';');

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $command->fire();
    }

    protected function getCommand($input)
    {
        $kernel = m::spy('Illuminate\Contracts\Console\Kernel');
        $output = m::spy('Symfony\Component\Console\Output\OutputInterface');
        $formatter = m::spy('Symfony\Component\Console\Formatter\OutputFormatterInterface');
        $laravel = m::spy('Illuminate\Contracts\Foundation\Application');

        $output
            ->shouldReceive('getFormatter')->andReturn($formatter);

        $command = new ArtisanTinker($kernel);
        $command->setLaravel($laravel);
        $command->run($input, $output);

        return $command;
    }
}

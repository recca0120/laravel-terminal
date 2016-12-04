<?php

use Mockery as m;
use Recca0120\Terminal\Console\Commands\Mysql;

class MysqlTest extends PHPUnit_Framework_TestCase
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

        $databaseManager = m::spy('Illuminate\Database\DatabaseManager');
        $connection = m::spy('Illuminate\Database\ConnectionInterface');
        $input = m::spy('Symfony\Component\Console\Input\InputInterface');
        $output = m::spy('Symfony\Component\Console\Output\OutputInterface');
        $formatter = m::spy('Symfony\Component\Console\Formatter\OutputFormatterInterface');
        $laravel = m::spy('Illuminate\Contracts\Foundation\Application');
        $query = 'SELECT * FROM users;';

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $output
            ->shouldReceive('getFormatter')->andReturn($formatter);

        $input
            ->shouldReceive('getOption')->with('command')->andReturn($query);

        $databaseManager
            ->shouldReceive('connection')->andReturn($connection);

        $connection
            ->shouldReceive('select')->andReturn([]);

        $command = new Mysql($databaseManager);
        $command->setLaravel($laravel);
        $command->run($input, $output);
        $command->fire();

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $databaseManager->shouldHaveReceived('connection')->once();
        $connection->shouldHaveReceived('setFetchMode')->with(PDO::FETCH_ASSOC)->once();
        $connection->shouldHaveReceived('select')->with($query)->once();
    }
}

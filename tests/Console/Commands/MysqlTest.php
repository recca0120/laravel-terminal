<?php

namespace Recca0120\Terminal\Tests\Console\Commands;

use PDO;
use stdClass;
use Mockery as m;
use MockingHelpers;
use PHPUnit\Framework\TestCase;
use Recca0120\Terminal\Console\Commands\Mysql;
use Symfony\Component\Console\Output\BufferedOutput;

class MysqlTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testFire()
    {
        $command = new Mysql(
            $databaseManager = m::mock('Illuminate\Database\DatabaseManager')
        );
        MockingHelpers::mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        MockingHelpers::mockProperty($command, 'output', $output = new BufferedOutput);

        $input->shouldReceive('getOption')->once()->with('command')->andReturn($query = 'SELECT * FROM users;');
        $databaseManager->shouldReceive('connection')->once()->andReturn(
            $connection = m::mock('Illuminate\Database\ConnectionInterface')
        );
        $connection->shouldReceive('select')->once()->with($query)->andReturn($rows = [
            new stdClass
        ]);

        $command->fire();
    }
}

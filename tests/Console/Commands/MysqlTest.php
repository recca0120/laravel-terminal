<?php

namespace Recca0120\Terminal\Tests\Console\Commands;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\Terminal\Console\Commands\Mysql;
use Symfony\Component\Console\Output\BufferedOutput;

class MysqlTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testHandle()
    {
        $command = new Mysql(
            $databaseManager = m::mock('Illuminate\Database\DatabaseManager')
        );
        $this->mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        $this->mockProperty($command, 'output', $output = new BufferedOutput);

        $input->shouldReceive('getOption')->once()->with('command')->andReturn($sql = 'SELECT * FROM users;');
        $input->shouldReceive('getOption')->once()->with('connection')->andReturn($connection = 'mysql');
        $databaseManager->shouldReceive('connection')->once()->with($connection)->andReturn(
            $connection = m::mock('Illuminate\Database\ConnectionInterface')
        );
        $connection->shouldReceive('select')->once()->with($sql, [], true)->andReturn($rows = [
            ['name' => 'name', 'email' => 'email'],
        ]);

        $command->handle();
        $this->assertStringContainsString('email', $output->fetch());
    }

    protected function mockProperty($object, $propertyName, $value)
    {
        $reflectionClass = new \ReflectionClass($object);

        $property = $reflectionClass->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
        $property->setAccessible(false);
    }
}

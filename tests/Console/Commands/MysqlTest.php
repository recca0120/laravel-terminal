<?php

namespace Recca0120\Terminal\Tests\Console\Commands;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\Terminal\Console\Commands\Mysql;
use Symfony\Component\Console\Output\BufferedOutput;

class MysqlTest extends TestCase
{
    protected function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testHandle()
    {
        $command = new Mysql(
            $databaseManager = m::mock('Illuminate\Database\DatabaseManager')
        );
        $this->mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        $this->mockProperty($command, 'output', $output = new BufferedOutput);

        $input->shouldReceive('getOption')->once()->with('command')->andReturn($query = 'SELECT * FROM users;');
        $databaseManager->shouldReceive('connection')->once()->andReturn(
            $connection = m::mock('Illuminate\Database\ConnectionInterface')
        );
        $connection->shouldReceive('select')->once()->with($query)->andReturn($rows = [
            ['name' => 'name', 'email' => 'email'],
        ]);

        $command->handle();
        $this->assertContains('email', $output->fetch());
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

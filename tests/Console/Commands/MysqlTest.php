<?php

namespace Recca0120\Terminal\Tests\Console\Commands;

use Illuminate\Container\Container;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\DatabaseManager;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\Terminal\Console\Commands\Mysql;
use Symfony\Component\Console\Tester\CommandTester;

class MysqlTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testHandle()
    {
        $container = m::mock(new Container);
        Container::setInstance($container);
        $sql = 'SELECT * FROM users;';
        $databaseManager = m::mock(DatabaseManager::class);
        $connection = m::mock(ConnectionInterface::class);
        $databaseManager->shouldReceive('connection')->once()->with('mysql')->andReturn($connection);
        $connection->shouldReceive('select')->once()->with($sql, [], true)->andReturn($rows = [
            ['name' => 'recca0120', 'email' => 'recca0120@gmail.com'],
        ]);

        $command = new Mysql($databaseManager);
        $command->setLaravel($container);

        $commandTester = new CommandTester($command);
        $commandTester->execute(['--command' => $sql, '--connection' => 'mysql']);

        self::assertStringContainsString('recca0120@gmail.com', $commandTester->getDisplay());
    }
}

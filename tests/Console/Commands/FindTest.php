<?php

namespace Recca0120\Terminal\Tests\Console\Commands;

use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as m;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Recca0120\Terminal\Console\Commands\Find;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Finder\Finder;

class FindTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private $structure = [
        'foo' => [
            'foo' => 'foo',
            'foo1' => 'foo1',
            'bar' => [
                'bar' => 'bar',
                'bar1' => 'bar1',
            ],
        ],
    ];

    public function test_find_name_by_file()
    {
        $commandTester = new CommandTester($this->giveCommand());
        $commandTester->execute(['path' => '/', '-type' => 'f', '-name' => 'foo1']);

        self::assertStringContainsString('foo1', $commandTester->getDisplay());
    }

    public function test_find_name_by_directory()
    {
        $commandTester = new CommandTester($this->giveCommand());
        $commandTester->execute(['path' => '/', '-type' => 'd']);

        self::assertStringContainsString('bar', $commandTester->getDisplay());
    }

    public function test_max_depth_is_zero()
    {
        $commandTester = new CommandTester($this->giveCommand());
        $commandTester->execute(['path' => '/', '-name' => 'foo1', '-maxdepth' => '0']);

        self::assertStringContainsString('vfs://root//', $commandTester->getDisplay());
    }

    public function test_max_depth_bigger_then_zero()
    {
        $commandTester = new CommandTester($this->giveCommand());
        $commandTester->execute(['path' => '/', '-name' => 'foo1', '-maxdepth' => '1']);

        self::assertEmpty('', $commandTester->getDisplay());
    }

    public function test_delete()
    {
        $commandTester = new CommandTester($this->giveCommand());
        $commandTester->execute(['path' => 'foo', '-name' => 'foo', '-delete' => '']);

        self::assertStringContainsString('removed vfs://root/foo/foo', $commandTester->getDisplay());
    }

    /**
     * @return Find
     */
    protected function giveCommand()
    {
        $root = vfsStream::setup('root', null, $this->structure);
        $container = m::mock(new Container);
        $container->shouldReceive('basePath')->andReturn($root->url());
        Container::setInstance($container);

        $command = new Find(new Finder, new Filesystem);
        $command->setLaravel($container);

        return $command;
    }
}

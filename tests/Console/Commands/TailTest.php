<?php

namespace Recca0120\Terminal\Tests\Console\Commands;

use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as m;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Recca0120\Terminal\Console\Commands\Tail;
use Symfony\Component\Console\Tester\CommandTester;
use Webmozart\Glob\Glob;

class TailTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private $structure = [
        'logs' => [
            '1.log' => '
                    1.log
                    1.log
                    1.log
                    1.log
                    1.log
                    1.log
                    1.log
                    1.log
                    1.log
                    1.log
                ',
            '2.log' => '
                    2.log
                    2.log
                    2.log
                    2.log
                    2.log
                    2.log
                    2.log
                    2.log
                    2.log
                    2.log
                ',
            '3.log' => '
                    3.log
                    3.log
                    3.log
                    3.log
                    3.log
                    3.log
                    3.log
                    3.log
                    3.log
                    3.log
                ',
            '4.log' => '
                    4.log
                    4.log
                    4.log
                    4.log
                    4.log
                    4.log
                    4.log
                    4.log
                    4.log
                    4.log
                ',
            '5.log' => '
                    5.log
                    5.log
                    5.log
                    5.log
                    5.log
                    5.log
                    5.log
                    5.log
                    5.log
                    5.log
                ',
        ],
    ];

    public function test_tail_default_file()
    {
        $commandTester = new CommandTester($this->getCommand());

        $commandTester->execute([]);

        self::assertStringContainsString('5.log', $commandTester->getDisplay());
    }

    public function test_tail_file()
    {
        $commandTester = new CommandTester($this->getCommand());

        $commandTester->execute(['path' => 'logs/1.log']);

        self::assertStringContainsString('1.log', $commandTester->getDisplay());
    }

    protected function giveRoot()
    {
        $root = vfsStream::setup('root', null, $this->structure);
        $i = 0;
        foreach ($this->structure as $directory => $files) {
            foreach ($files as $file => $content) {
                $root->getChild($directory.'/'.$file)->lastAttributeModified(time() + $i);
                $i++;
            }
        }

        return $root;
    }

    /**
     * @return Tail
     */
    private function getCommand()
    {
        $root = $this->giveRoot();
        $container = m::mock(new Container);
        $container->shouldReceive('basePath')->andReturn($root->url());
        $container->instance('path.storage', $root->url());
        Container::setInstance($container);

        $command = new Tail($this->getFile());
        $command->setLaravel($container);

        return $command;
    }

    /**
     * @return Filesystem
     */
    private function getFile()
    {
        $files = m::mock(new Filesystem());
        $files->shouldReceive('glob')->andReturnUsing(function ($path) {
            return Glob::glob($path);
        });

        return $files;
    }
}

<?php

namespace Recca0120\Terminal\Tests\Console\Commands;

use Mockery as m;
use Webmozart\Glob\Glob;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Recca0120\Terminal\Console\Commands\Tail;
use Symfony\Component\Console\Output\BufferedOutput;

class TailTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $structure = [
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
        $this->root = vfsStream::setup('root', null, $structure);
        $i = 0;
        foreach ($structure as $directory => $files) {
            foreach ($files as $file => $content) {
                $this->root->getChild($directory.'/'.$file)->lastAttributeModified(time() + $i);
                $i++;
            }
        }

        $container = m::mock(new Container);
        $container->shouldReceive('basePath')->andReturn($this->root->url());
        $container->instance('path.storage', $this->root->url());
        Container::setInstance($container);
    }

    protected function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testFire()
    {
        $command = new Tail(
            $files = m::mock(new Filesystem)
        );
        $this->mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        $this->mockProperty($command, 'output', $output = new BufferedOutput);

        $input->shouldReceive('getArgument')->once()->with('path')->andReturn(null);
        $input->shouldReceive('getOption')->once()->with('lines')->andReturn($lines = 5);
        $command->setLaravel(
            $laravel = m::mock('Illuminate\Contracts\Foundation\Application')
        );
        $storagePath = $this->root->url();
        $files->shouldReceive('glob')->once()->with($storagePath.'/logs/*.log')->andReturnUsing(function ($path) {
            return Glob::glob($path);
        });

        $command->fire();

        $this->assertContains('5.log', $output->fetch());
    }

    public function testFirePath()
    {
        $command = new Tail(
            $files = m::mock('Illuminate\Filesystem\Filesystem')
        );
        $this->mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        $this->mockProperty($command, 'output', $output = new BufferedOutput);

        $command->setLaravel(
            $laravel = m::mock('Illuminate\Contracts\Foundation\Application')
        );

        $input->shouldReceive('getArgument')->once()->with('path')->andReturn($path = 'logs/1.log');
        $input->shouldReceive('getOption')->once()->with('lines')->andReturn($lines = 5);

        $command->fire();

        $this->assertContains('1.log', $output->fetch());
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

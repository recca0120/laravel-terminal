<?php

namespace Recca0120\Terminal\Tests\Console\Commands;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Illuminate\Container\Container;
use Recca0120\Terminal\Console\Commands\Vi;
use Symfony\Component\Console\Output\BufferedOutput;

class ViTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $container = m::mock(new Container);
        $container->shouldReceive('basePath')->andReturn('foo/');
        Container::setInstance($container);
    }

    protected function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testFireRead()
    {
        $command = new Vi(
            $files = m::mock('Illuminate\Filesystem\Filesystem')
        );
        $this->mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        $this->mockProperty($command, 'output', $output = new BufferedOutput);

        $input->shouldReceive('getArgument')->once()->with('path')->andReturn($path = 'foo');
        $input->shouldReceive('getOption')->once()->with('text')->andReturn($text = null);

        $command->setLaravel(
            $laravel = m::mock('Illuminate\Contracts\Foundation\Application')
        );
        $basePath = 'foo/';

        $files->shouldReceive('get')->with($basePath.$path);

        $this->assertNull($command->fire());
    }

    public function testFireWrite()
    {
        $command = new Vi(
            $files = m::mock('Illuminate\Filesystem\Filesystem')
        );
        $this->mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        $this->mockProperty($command, 'output', $output = new BufferedOutput);

        $input->shouldReceive('getArgument')->once()->with('path')->andReturn($path = 'foo');
        $input->shouldReceive('getOption')->once()->with('text')->andReturn($text = 'foo');

        $command->setLaravel(
            $laravel = m::mock('Illuminate\Contracts\Foundation\Application')
        );
        $basePath = 'foo/';

        $files->shouldReceive('put')->with($basePath.$path, $text);

        $this->assertNull($command->fire());
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

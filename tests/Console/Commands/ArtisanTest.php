<?php

namespace Recca0120\Terminal\Tests\Console\Commands;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\Terminal\Console\Commands\Artisan;
use Symfony\Component\Console\Output\BufferedOutput;

class ArtisanTest extends TestCase
{
    protected function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testHandle()
    {
        $command = new Artisan(
            $kernel = m::mock('Illuminate\Contracts\Console\Kernel')
        );
        $this->mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        $this->mockProperty($command, 'output', $output = new BufferedOutput);

        $input->shouldReceive('getOption')->once()->with('command')->andReturn($cmd = 'foo');
        $kernel->shouldReceive('handle')->with(m::on(function ($input) use ($cmd) {
            $this->assertSame((string) $input, $cmd);

            return (string) $input === $cmd;
        }), $output);

        $command->handle();
    }

    public function testHandleForceCommand()
    {
        $command = new Artisan(
            $kernel = m::mock('Illuminate\Contracts\Console\Kernel')
        );
        $this->mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        $this->mockProperty($command, 'output', $output = new BufferedOutput);

        $input->shouldReceive('getOption')->once()->with('command')->andReturn($cmd = 'migrate');
        $kernel->shouldReceive('handle')->with(m::on(function ($input) use ($cmd) {
            $this->assertSame($cmd.' --force', (string) $input);

            return (string) $input === $cmd.' --force';
        }), $output);

        $command->handle();
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testHandleNotSupportCommand()
    {
        $command = new Artisan(
            $kernel = m::mock('Illuminate\Contracts\Console\Kernel')
        );
        $this->mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        $this->mockProperty($command, 'output', $output = new BufferedOutput);

        $input->shouldReceive('getOption')->once()->with('command')->andReturn($cmd = 'down');

        $command->handle();
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

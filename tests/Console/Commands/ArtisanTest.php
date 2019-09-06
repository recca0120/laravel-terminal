<?php

namespace Recca0120\Terminal\Tests\Console\Commands;

use Mockery as m;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Recca0120\Terminal\ProcessUtils;
use Recca0120\Terminal\Console\Commands\Artisan;
use Symfony\Component\Console\Output\BufferedOutput;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class ArtisanTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testHandle()
    {
        $command = new Artisan(
            $kernel = m::mock('Illuminate\Contracts\Console\Kernel')
        );
        $this->mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        $this->mockProperty($command, 'output', $output = new BufferedOutput);

        $input->shouldReceive('getOption')->once()->with('command')->andReturn($cmd = 'foo');
        $kernel->shouldReceive('handle')->with(m::on(function ($input) use ($cmd) {
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

        $input->shouldReceive('getOption')->once()->with('command')->andReturn($cmd = 'migrate:fresh');
        $kernel->shouldReceive('handle')->with(m::on(function ($input) use ($cmd) {
            return ProcessUtils::escapeArgument($cmd).' --force' === (string) $input;
        }), $output);

        $command->handle();
    }

    public function testHandleVendorPublishCommand()
    {
        $command = new Artisan(
            $kernel = m::mock('Illuminate\Contracts\Console\Kernel')
        );
        $this->mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        $this->mockProperty($command, 'output', $output = new BufferedOutput);

        $command->setLaravel(
            $application = m::mock('Illuminate\Contracts\Foundation\Application')
        );

        $application->shouldReceive('version')->once()->andReturn('5.5.0');

        $input->shouldReceive('getOption')->once()->with('command')->andReturn($cmd = 'vendor:publish');
        $kernel->shouldReceive('handle')->with(m::on(function ($input) use ($cmd) {
            return ProcessUtils::escapeArgument($cmd).' --all' === (string) $input;
        }), $output);

        $command->handle();
    }

    public function testHandleNotSupportCommand()
    {
        $this->expectException(InvalidArgumentException::class);

        $command = new Artisan(
            m::mock('Illuminate\Contracts\Console\Kernel')
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

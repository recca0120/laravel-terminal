<?php

namespace Recca0120\Terminal\Tests\Console\Commands;

use Mockery as m;
use MockingHelpers;
use PHPUnit\Framework\TestCase;
use Recca0120\Terminal\Console\Commands\Artisan;
use Symfony\Component\Console\Output\BufferedOutput;

class ArtisanTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testFire()
    {
        $command = new Artisan(
            $kernel = m::mock('Illuminate\Contracts\Console\Kernel')
        );
        MockingHelpers::mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        MockingHelpers::mockProperty($command, 'output', $output = new BufferedOutput);

        $input->shouldReceive('getOption')->once()->with('command')->andReturn($cmd = 'foo');
        $kernel->shouldReceive('handle')->with(m::on(function ($input) use ($cmd) {
            return (string) $input === $cmd;
        }), $output);

        $command->fire();
    }

    public function testFireForceCommand()
    {
        $command = new Artisan(
            $kernel = m::mock('Illuminate\Contracts\Console\Kernel')
        );
        MockingHelpers::mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        MockingHelpers::mockProperty($command, 'output', $output = new BufferedOutput);

        $input->shouldReceive('getOption')->once()->with('command')->andReturn($cmd = 'migrate');
        $kernel->shouldReceive('handle')->with(m::on(function ($input) use ($cmd) {
            return (string) $input === $cmd.' --force';
        }), $output);

        $command->fire();
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testFireNotSupportCommand()
    {
        $command = new Artisan(
            $kernel = m::mock('Illuminate\Contracts\Console\Kernel')
        );
        MockingHelpers::mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        MockingHelpers::mockProperty($command, 'output', $output = new BufferedOutput);

        $input->shouldReceive('getOption')->once()->with('command')->andReturn($cmd = 'down');

        $command->fire();
    }
}

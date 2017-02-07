<?php

namespace Recca0120\Terminal\Tests\Console\Commands;

use Mockery as m;
use MockingHelpers;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;
use Recca0120\Terminal\Console\Commands\ArtisanTinker;

class ArtisanTinkerTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testFireEcho()
    {
        $command = new ArtisanTinker();
        MockingHelpers::mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        MockingHelpers::mockProperty($command, 'output', $output = new BufferedOutput);

        $input->shouldReceive('getOption')->once()->with('command')->andReturn($cmd = 'echo 123');
        $command->fire();
        $this->assertSame("123\n=> ", $output->fetch());
    }

    public function testFireVarDump()
    {
        $command = new ArtisanTinker();
        MockingHelpers::mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        MockingHelpers::mockProperty($command, 'output', $output = new BufferedOutput);

        $input->shouldReceive('getOption')->once()->with('command')->andReturn($cmd = 'var_dump(123)');
        $command->fire();
        // $this->assertSame("int(123)\n\n=> ", $output->fetch());
    }

    public function testFireObject()
    {
        $command = new ArtisanTinker();
        MockingHelpers::mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        MockingHelpers::mockProperty($command, 'output', $output = new BufferedOutput);

        $input->shouldReceive('getOption')->once()->with('command')->andReturn($cmd = 'new stdClass;');
        $command->fire();

        $this->assertSame("=> stdClass::__set_state(array(\n))\n", $output->fetch());
    }

    public function testFireArray()
    {
        $command = new ArtisanTinker();
        MockingHelpers::mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        MockingHelpers::mockProperty($command, 'output', $output = new BufferedOutput);

        $input->shouldReceive('getOption')->once()->with('command')->andReturn($cmd = "['foo' => 'bar'];");
        $command->fire();

        $this->assertSame("=> array (\n  'foo' => 'bar',\n)\n", $output->fetch());
    }

    public function testFireString()
    {
        $command = new ArtisanTinker();
        MockingHelpers::mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        MockingHelpers::mockProperty($command, 'output', $output = new BufferedOutput);

        $input->shouldReceive('getOption')->once()->with('command')->andReturn($cmd = "'abc'");
        $command->fire();

        $this->assertSame("=> abc\n", $output->fetch());
    }

    public function testNumeric()
    {
        $command = new ArtisanTinker();
        MockingHelpers::mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        MockingHelpers::mockProperty($command, 'output', $output = new BufferedOutput);

        $input->shouldReceive('getOption')->once()->with('command')->andReturn($cmd = '123');
        $command->fire();

        $this->assertSame("=> 123\n", $output->fetch());
    }
}

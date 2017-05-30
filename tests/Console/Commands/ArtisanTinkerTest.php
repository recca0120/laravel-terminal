<?php

namespace Recca0120\Terminal\Tests\Console\Commands;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;
use Recca0120\Terminal\Console\Commands\ArtisanTinker;

class ArtisanTinkerTest extends TestCase
{
    protected function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testFireEcho()
    {
        $command = new ArtisanTinker();
        $this->mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        $this->mockProperty($command, 'output', $output = new BufferedOutput);

        $input->shouldReceive('getOption')->once()->with('command')->andReturn($cmd = 'echo 123');
        $command->fire();

        $this->assertContains('123', $this->lf($output->fetch()));
    }

    public function testFireVarDump()
    {
        $command = new ArtisanTinker();
        $this->mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        $this->mockProperty($command, 'output', $output = new BufferedOutput);

        $input->shouldReceive('getOption')->once()->with('command')->andReturn($cmd = 'var_dump(123)');
        $this->assertNull($command->fire());

        $this->assertContains('int(123)', $this->lf($output->fetch()));
    }

    public function testFireObject()
    {
        $command = new ArtisanTinker();
        $this->mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        $this->mockProperty($command, 'output', $output = new BufferedOutput);

        $input->shouldReceive('getOption')->once()->with('command')->andReturn($cmd = 'new stdClass;');
        $command->fire();

        $this->assertSame("=> stdClass::__set_state(array(\n))\n", $this->lf($output->fetch()));
    }

    public function testFireArray()
    {
        $command = new ArtisanTinker();
        $this->mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        $this->mockProperty($command, 'output', $output = new BufferedOutput);

        $input->shouldReceive('getOption')->once()->with('command')->andReturn($cmd = "['foo' => 'bar'];");
        $command->fire();

        $this->assertSame("=> array (\n  'foo' => 'bar',\n)\n", $this->lf($output->fetch()));
    }

    public function testFireString()
    {
        $command = new ArtisanTinker();
        $this->mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        $this->mockProperty($command, 'output', $output = new BufferedOutput);

        $input->shouldReceive('getOption')->once()->with('command')->andReturn($cmd = "'abc'");
        $command->fire();

        $this->assertSame("=> abc\n", $this->lf($output->fetch()));
    }

    public function testNumeric()
    {
        $command = new ArtisanTinker();
        $this->mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        $this->mockProperty($command, 'output', $output = new BufferedOutput);

        $input->shouldReceive('getOption')->once()->with('command')->andReturn($cmd = '123');
        $command->fire();

        $this->assertSame("=> 123\n", $this->lf($output->fetch()));
    }

    protected function mockProperty($object, $propertyName, $value)
    {
        $reflectionClass = new \ReflectionClass($object);

        $property = $reflectionClass->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
        $property->setAccessible(false);
    }

    protected function lf($content)
    {
        return str_replace("\r\n", "\n", $content);
    }
}

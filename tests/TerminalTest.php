<?php

use Illuminate\Contracts\Console\Kernel as KernelContract;
use Mockery as m;
use Recca0120\Terminal\Console\Kernel;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;

class TerminalTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $app = App::getInstance();
        $app[KernelContract::class] = $kernel = new Kernel($app, $app['events']);
    }

    public function tearDown()
    {
        m::close();
    }

    public function command($cmd)
    {
        $app = App::getInstance();
        $kernel = $app[KernelContract::class];
        $argv = array_merge(['artisan', array_get($cmd, 'name')], array_get($cmd, 'args', []));
        $input = new ArgvInput($argv);
        $output = new BufferedOutput(BufferedOutput::VERBOSITY_NORMAL, true, new OutputFormatter(true));
        $status = $kernel->handle($input, $output);

        return $output->fetch();
    }

    public function test_artisan_list_command()
    {
        $this->assertTrue(strpos($this->command([
            'reset' => 'artisan list',
        ]), 'Laravel') !== false);
    }

    public function test_find_command()
    {
        $this->assertTrue(strpos($this->command([
            'name' => 'find',
        ]), 'TerminalTest.php') !== false);
    }
}

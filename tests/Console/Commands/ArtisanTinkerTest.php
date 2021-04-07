<?php

namespace Recca0120\Terminal\Tests\Console\Commands;

use Illuminate\Container\Container;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Recca0120\Terminal\Console\Commands\ArtisanTinker;
use Symfony\Component\Console\Tester\CommandTester;

class ArtisanTinkerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_echo()
    {
        $commandTester = $this->executeCommand('echo 123');

        self::assertStringContainsString('123', $this->lf($commandTester->getDisplay()));
    }

    public function test_var_dump()
    {
        $commandTester = $this->executeCommand('var_dump(123)');

        self::assertStringContainsString('int(123)', $this->lf($commandTester->getDisplay()));
    }

    public function test_show_object()
    {
        $commandTester = $this->executeCommand('new stdClass;');

        if (PHP_VERSION_ID >= 70300) {
            self::assertStringContainsString("=> (object) array(\n)\n", $this->lf($commandTester->getDisplay()));
        } else {
            self::assertStringContainsString("=> stdClass::__set_state(array(\n))\n", $this->lf($commandTester->getDisplay()));
        }
    }

    public function test_show_array()
    {
        $commandTester = $this->executeCommand("['foo' => 'bar'];");

        self::assertSame("=> array (\n  'foo' => 'bar',\n)\n", $this->lf($commandTester->getDisplay()));
    }

    public function testHandleString()
    {
        $commandTester = $this->executeCommand("'abc'");

        self::assertSame("=> abc\n", $this->lf($commandTester->getDisplay()));
    }

    public function testNumeric()
    {
        $commandTester = $this->executeCommand('123');

        self::assertSame("=> 123\n", $this->lf($commandTester->getDisplay()));
    }

    protected function lf($content)
    {
        return str_replace("\r\n", "\n", $content);
    }

    /**
     * @param string $cmd
     * @return CommandTester
     */
    private function executeCommand($cmd)
    {
        $container = new Container;
        $command = new ArtisanTinker();
        $command->setLaravel($container);

        $commandTester = new CommandTester($command);
        $commandTester->execute(['--command' => $cmd]);

        return $commandTester;
    }
}

<?php

namespace Recca0120\Terminal\Tests\Console\Commands;

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use InvalidArgumentException;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\Terminal\Application;
use Recca0120\Terminal\Console\Commands\Artisan;
use Recca0120\Terminal\Kernel;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Tester\CommandTester;

class ArtisanTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_artisan_list_command()
    {
        $laravel = $this->getLaravel();
        $command = new Artisan($this->getKernel($laravel));
        $command->setLaravel($laravel);
        $commandTester = new CommandTester($command);

        $commandTester->execute(['--command' => 'list -h'], []);

        self::assertStringContainsString('--raw', $commandTester->getDisplay());
    }

    public function test_artisan_migrate_refresh_command()
    {
        $laravel = $this->getLaravel();
        $kernel = m::mock($this->getKernel($laravel));

        $kernel->shouldReceive('handle')->with(m::on(function (InputInterface $input) {
            return strpos((string) $input, '--force') !== -1;
        }), m::any());

        $command = new Artisan($kernel);
        $command->setLaravel($laravel);
        $commandTester = new CommandTester($command);

        $commandTester->execute(['--command' => 'migrate:fresh'], []);
        $commandTester->assertCommandIsSuccessful();
    }

    public function test_artisan_vendor_publish_command()
    {
        $laravel = $this->getLaravel('5.5.0');
        $kernel = m::mock($this->getKernel($laravel));
        $kernel->shouldReceive('handle')->with(m::on(function (InputInterface $input) {
            return strpos((string) $input, ' --all') !== -1;
        }), m::any());

        $command = new Artisan($kernel);
        $command->setLaravel($laravel);
        $commandTester = new CommandTester($command);

        $commandTester->execute(['--command' => 'vendor:publish'], []);
        $commandTester->assertCommandIsSuccessful();
    }

    public function test_not_supported_command()
    {
        $this->expectException(InvalidArgumentException::class);

        $laravel = $this->getLaravel();
        $kernel = m::mock($this->getKernel($laravel));
        $kernel->shouldReceive('handle');

        $command = new Artisan($kernel);
        $command->setLaravel($laravel);
        $commandTester = new CommandTester($command);

        $commandTester->execute(['--command' => 'down'], []);
        $commandTester->assertCommandIsSuccessful();
    }

    /**
     * @return Kernel
     */
    private function getKernel(Container $laravel)
    {
        return new Kernel(new Application($laravel, new Dispatcher(), $laravel->version()));
    }

    /**
     * @param  string  $version
     * @return Container|m\LegacyMockInterface|m\MockInterface
     */
    private function getLaravel($version = 'testing')
    {
        $laravel = m::mock(new Container());
        $laravel->shouldReceive('version')->andReturn($version);
        $laravel->shouldReceive('runningUnitTests')->andReturn(false);

        return $laravel;
    }
}

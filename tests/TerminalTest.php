<?php

use Illuminate\Http\Request;
use Illuminate\Session\SessionInterface;
use Illuminate\Session\SessionManager;
use Mockery as m;
use Recca0120\Terminal\Console\Commands\Artisan;
use Recca0120\Terminal\Console\Commands\ArtisanTinker;
use Recca0120\Terminal\Console\Commands\Find;
use Recca0120\Terminal\Console\Commands\Mysql;
use Recca0120\Terminal\Http\Controllers\TerminalController;

class TerminalTest extends PHPUnit_Framework_TestCase
{
    use Laravel;

    public function tearDown()
    {
        m::close();
    }

    public function test_kernel_call()
    {
        $app = $this->createApplication();
        $app['request']->shouldReceive('ajax')->andReturn(false)->mock();
        $app['events']
            ->shouldReceive('fire')->andReturn(true)
            ->shouldReceive('firing')->andReturn('ArtisanStarting')
            ->mock();
        $kernel = new Recca0120\Terminal\Console\Kernel($app, $app['events']);
        $kernel->call('list');

        return [$kernel, $app];
    }

    /**
     * @depends test_kernel_call
     */
    public function test_controller($arguments)
    {
        $kernel = $arguments[0];
        $app = $arguments[1];

        $controller = m::mock(new TerminalController($kernel))
            ->makePartial();

        $session = m::mock(SessionInterface::class)
            ->shouldReceive('isStarted')->andReturn(true)
            ->shouldReceive('token')->andReturn('token')
            ->mock();

        $sessionManager = m::mock(SessionManager::class)
            ->makePartial()
            ->shouldReceive('driver')->andReturn($session)
            ->mock();

        $request = m::mock(Request::class)
            ->makePartial();

        $controller->index($app, $sessionManager, $request);

        $request = m::mock(Request::class)
            ->makePartial()
            ->shouldReceive('get')->andReturn(['command' => 'list'])
            ->mock();
        $controller->endPoint($request);

        return [$kernel, $app];
    }

    /**
     * @depends test_kernel_call
     */
    public function test_application_call($arguments)
    {
        $kernel = $arguments[0];
        $app = $arguments[1];

        $artisan = new Recca0120\Terminal\Application($app, $app['events'], 'testing');
        $artisan->call('list');
        $this->assertRegExp('/list/', $artisan->output());

        return [$kernel, $app, $artisan];
    }

    /**
     * @depends test_application_call
     */
    public function test_artisan($arguments)
    {
        $kernel = $arguments[0];
        $app = $arguments[1];
        $artisan = $arguments[2];

        $app['Illuminate\Contracts\Console\Kernel'] = m::mock('Illuminate\Contracts\Console\Kernel')
            ->shouldReceive('handle')->andReturnUsing(function ($input) {
                $this->assertEquals((string) $input, 'list');
            })
            ->mock();

        $command = new Artisan();
        $artisan->add($command);
        $exitCode = $artisan->call('artisan list');
        $this->assertEquals($exitCode, 0);
    }

    /**
     * @depends test_application_call
     */
    public function test_artisan_tinker($arguments)
    {
        $kernel = $arguments[0];
        $app = $arguments[1];
        $artisan = $arguments[2];

        $command = new ArtisanTinker();
        $artisan->add($command);
        $exitCode = $artisan->call('tinker echo 123;');
        $this->assertEquals($exitCode, 0);
        $this->assertRegExp('/123/', $artisan->output());
    }

    /**
     * @depends test_application_call
     */
    public function test_find($arguments)
    {
        $kernel = $arguments[0];
        $app = $arguments[1];
        $artisan = $arguments[2];

        $app['Symfony\Component\Finder\Finder'] = m::mock('Symfony\Component\Finder\Finder')
            ->makePartial()
            ->shouldReceive('in')->once()->passthru()
            ->shouldReceive('depth')->with(m::mustBe('<2'))->once()->passthru()
            ->mock();

        $command = new Find();
        $artisan->add($command);
        $exitCode = $artisan->call('find ../src -name -maxdepth 2');
        $this->assertEquals($exitCode, 0);
        $this->assertRegExp('/ServiceProvider\.php/', $artisan->output());
    }

    /**
     * @depends test_application_call
     */
    public function test_mysql($arguments)
    {
        $kernel = $arguments[0];
        $app = $arguments[1];
        $artisan = $arguments[2];

        $connection = m::mock('Illuminate\Database\Connection')
            ->makePartial()
            ->shouldReceive('select')->with('select * from users;')->andReturn([
                ['recca0120@gmail.com'],
            ])
            ->mock();

        $app['Illuminate\Database\ConnectionInterface'] = $connection;

        $command = new Mysql();
        $artisan->add($command);
        $exitCode = $artisan->call('mysql select * from users;');
        $this->assertEquals($exitCode, 0);
        $this->assertRegExp('/recca0120@gmail\.com/', $artisan->output());
    }
}

function action()
{
}

function view()
{
}

function response()
{
    return m::mock(['json' => null]);
}

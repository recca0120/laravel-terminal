<?php

use Illuminate\Contracts\Console\Kernel as ArtisanContract;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Mockery as m;
use Recca0120\Terminal\Application;
use Recca0120\Terminal\Console\Commands\Artisan;
use Recca0120\Terminal\Console\Commands\ArtisanTinker;
use Recca0120\Terminal\Console\Commands\Cleanup;
use Recca0120\Terminal\Console\Commands\Find;
use Recca0120\Terminal\Console\Commands\Mysql;
use Recca0120\Terminal\Console\Commands\Tail;
use Recca0120\Terminal\Console\Commands\Vi;
use Recca0120\Terminal\Console\Kernel;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class TerminalTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testKernel()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $events = m::mock(DispatcherContract::class);
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);
        $kernel = new Kernel($app, $events);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $events
            ->shouldReceive('fire')
            ->shouldReceive('firing');

        $app
            ->shouldReceive('offsetGet')->with('request')->andReturn(m::mock(Request::class))
            ->shouldReceive('offsetGet')->with('events')->andReturn($events)
            ->shouldReceive('version')->andReturn('testing')
            ->shouldReceive('make')->andReturnUsing(function ($class) {
                return new $class();
            });

        $kernel = new Kernel($app, $events);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $kernel->call('list');
    }

    public function testArtisan()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $events = m::mock(DispatcherContract::class);
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $events->shouldReceive('fire');
        $app
            ->shouldReceive('offsetGet')->with('request')->andReturn(m::mock(Request::class))
            ->shouldReceive('basePath')->andReturn(__DIR__)
            ->shouldReceive('storagePath')->andReturn(__DIR__);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        return new Application($app, $events, 'testing');
    }

    /**
     * @depends testArtisan
     */
    public function testArtisanCommand($artisan)
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $command = new Artisan();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $artisan->add($command);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $artisan->getLaravel()->shouldReceive('call')->andReturnUsing(function () use ($command) {
            $artisan = m::mock(ArtisanContract::class);
            $artisan->shouldReceive('handle')->with(m::on(function ($input) {
                return (string) $input === 'migrate --force';
            }), m::type(OutputInterface::class))->once();
            $command->handle($artisan);
        })->once();
        $artisan->call('artisan --command=migrate');
    }

    /**
     * @depends testArtisan
     *
     * @expectedException InvalidArgumentException
     */
    public function testArtisanCommandException($artisan)
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $command = new Artisan();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $artisan->add($command);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $artisan->getLaravel()->shouldReceive('call')->andReturnUsing(function () use ($command) {
            $artisan = m::mock(ArtisanContract::class);
            $command->handle($artisan);
        })->once();

        $artisan->call('artisan --command=down');
    }

    /**
     * @depends testArtisan
     */
    public function testArtisanTinkerCommand($artisan)
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $command = new ArtisanTinker();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $artisan->add($command);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $artisan->getLaravel()->shouldReceive('call')->andReturnUsing(function () use ($command) {
            $command->handle();
        })->times(4);
        $artisan->call('tinker --command="echo 123;"');
        $artisan->call('tinker --command="123;"');
        $artisan->call('tinker --command="[];"');
        $artisan->call('tinker --command="\'abc\'"');
    }

    /**
     * @depends testArtisan
     */
    public function testCleanUpCommand($artisan)
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $command = new Cleanup();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $artisan->add($command);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $artisan->getLaravel()->shouldReceive('call')->andReturnUsing(function () use ($command) {
            $filesystem = m::mock(Filesystem::class)
                ->shouldReceive('glob')
                ->shouldReceive('isDirectory')
                ->shouldReceive('deleteDirectory')
                ->mock();
            $command->handle($filesystem);
        })->once();
        $artisan->call('cleanup');
    }

    /**
     * @depends testArtisan
     */
    public function testFindCommand($artisan)
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $command = new Find();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $artisan->add($command);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $artisan->getLaravel()->shouldReceive('call')->andReturnUsing(function () use ($command) {
            $finder = m::mock(Finder::class)
                ->shouldReceive('in')->with(__DIR__)
                ->shouldReceive('name')->with('*')
                ->shouldReceive('depth')->with('<1')
                ->shouldReceive('files')
                ->shouldReceive('getIterator')->andReturn(new AppendIterator())
                ->mock();
            $filesystem = m::mock(Filesystem::class);
            $command->handle($finder, $filesystem);
        })->once();

        $artisan->call('find ./ -name * -type f -maxdepth 1 -delete');
    }

    /**
     * @depends testArtisan
     */
    public function testMysql($artisan)
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $command = new Mysql();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $artisan->add($command);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $artisan->getLaravel()->shouldReceive('call')->andReturnUsing(function () use ($command) {
            $connection = m::mock(ConnectionInterface::class)
                ->shouldReceive('setFetchMode')->once()
                ->shouldReceive('select')->with('select * from users;')->andReturn([])->once()
                ->mock();
            $command->handle($connection);
        })->once();

        $artisan->call('mysql --command="select * from users;"');
    }

    /**
     * @depends testArtisan
     */
    public function test_tail_command($artisan)
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $command = new Tail();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $artisan->add($command);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $artisan->getLaravel()->shouldReceive('call')->andReturnUsing(function () use ($command) {
            $filesystem = m::mock(Filesystem::class);
            $command->handle($filesystem);
        })->once();

        $artisan->call('tail TerminalTest.php --lines 5');
    }

    /**
     * @depends testArtisan
     */
    public function testTailCommandGlob($artisan)
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $command = new Tail();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $artisan->add($command);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $artisan->getLaravel()->shouldReceive('call')->andReturnUsing(function () use ($command) {
            $filesystem = m::mock(Filesystem::class)
                ->shouldReceive('glob')->once()->andReturn([
                    __FILE__,
                ])
                ->mock();
            $command->handle($filesystem);
        })->once();

        $artisan->call('tail --lines 5');
    }

    /**
     * @depends testArtisan
     */
    public function testViCommandRead($artisan)
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $command = new Vi();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $artisan->add($command);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $artisan->getLaravel()->shouldReceive('call')->andReturnUsing(function () use ($command) {
            $filesystem = m::mock(Filesystem::class)
                ->shouldReceive('get')->with(realpath(__DIR__.'/TerminalTest.php'))
                ->mock();
            $command->handle($filesystem);
        })->once();

        $artisan->call('vi TerminalTest.php');
    }

    /**
     * @depends testArtisan
     */
    public function testViCommandWrite($artisan)
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $command = new Vi();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $artisan->add($command);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $artisan->getLaravel()->shouldReceive('call')->andReturnUsing(function () use ($command) {
            $filesystem = m::mock(Filesystem::class)
                ->shouldReceive('put')->with(realpath(__DIR__.'/TerminalTest.php'), 'abc')
                ->mock();
            $command->handle($filesystem);
        })->once();

        $artisan->call('vi TerminalTest.php --text="abc"');
    }
}

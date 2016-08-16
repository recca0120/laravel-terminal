<?php

use Illuminate\Contracts\Console\Kernel as ArtisanContract;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Http\Request;
use Mockery as m;
use Recca0120\Terminal\Application;
use Recca0120\Terminal\Console\Commands\Artisan;
use Symfony\Component\Console\Output\OutputInterface;

class ArtisanTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    protected function getArtisan()
    {
        $events = m::mock(DispatcherContract::class);
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);
        $request = m::mock(Request::class);

        $request->shouldReceive('ajax')->andReturn(true);
        $events->shouldReceive('fire');

        $app
            ->shouldReceive('offsetGet')->with('request')->andReturn($request)
            ->shouldReceive('basePath')->andReturn(__DIR__)
            ->shouldReceive('storagePath')->andReturn(__DIR__);

        return new Application($app, $events, 'testing');
    }

    public function test_handle()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $artisan = $this->getArtisan();
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

    public function test_down()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $artisan = $this->getArtisan();
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
}

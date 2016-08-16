<?php

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Http\Request;
use Mockery as m;
use Recca0120\Terminal\Application;
use Recca0120\Terminal\Console\Commands\ArtisanTinker;

class ArtisanTinkerTest extends PHPUnit_Framework_TestCase
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
}

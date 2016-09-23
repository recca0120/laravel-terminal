<?php

use Mockery as m;
use Recca0120\Terminal\Console\Commands\ArtisanTinker;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class ArtisanTinkerTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_handle()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $command = new ArtisanTinker();
        $laravel = m::mock('Illuminate\Contracts\Foundation\Application');
        $command->setLaravel($laravel);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $laravel
            ->shouldReceive('call')->times(4)->andReturnUsing(function ($command) {
                call_user_func($command);
            });

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $command->run(new StringInput('--command="echo 123;"'), new NullOutput);
        $command->run(new StringInput('--command="123;"'), new NullOutput);
        $command->run(new StringInput('--command="[];"'), new NullOutput);
        $command->run(new StringInput('--command="\'abc\'"'), new NullOutput);
    }
}

<?php

use Mockery as m;
use Recca0120\Terminal\Console\Commands\Artisan;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class ArtisanTest extends PHPUnit_Framework_TestCase
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

        $kernel = m::mock('Illuminate\Contracts\Console\Kernel');
        $command = new Artisan($kernel);
        $laravel = m::mock('Illuminate\Contracts\Foundation\Application');
        $command->setLaravel($laravel);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $kernel->shouldReceive('handle')->with(m::on(function ($input) {
            return (string) $input === 'migrate --force';
        }), m::type('Symfony\Component\Console\Output\OutputInterface'))->once();

        $laravel
            ->shouldReceive('call')->once()->andReturnUsing(function ($command) {
                call_user_func($command);
            });

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $command->run(new StringInput('--command=migrate'), new NullOutput);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_down()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $kernel = m::mock('Illuminate\Contracts\Console\Kernel');
        $command = new Artisan($kernel);
        $laravel = m::mock('Illuminate\Contracts\Foundation\Application');
        $command->setLaravel($laravel);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $laravel
            ->shouldReceive('call')->once()->andReturnUsing(function ($command) {
                call_user_func($command);
            });

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $command->run(new StringInput('--command=down'), new NullOutput);
    }
}

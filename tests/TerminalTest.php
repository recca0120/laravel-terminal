<?php

use Illuminate\Contracts\Console\Kernel as KernelContract;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Http\Request;
use Mockery as m;
use Recca0120\Terminal\Console\Commands\Artisan;
use Recca0120\Terminal\Console\Commands\Mysql;
use Recca0120\Terminal\Console\Kernel;

class TerminalTest extends PHPUnit_Framework_TestCase
{
    use Laravel;

    public function tearDown()
    {
        m::close();
    }

    public function test_kernel()
    {
        $app = $this->createApplication();
        $app['request']->shouldReceive('ajax')->andReturn(false)->mock();
        $app['events']
           ->shouldReceive('fire')->andReturn(true)
           ->shouldReceive('firing')->andReturn('ArtisanStarting')
           ->mock();

        $kernel = new Kernel($app, $app['events']);
        $exitCode = $kernel->call('list');
        $this->assertEquals($exitCode, 0);

        $output = $kernel->output();
        $this->assertRegExp('/'.$app->version().'/', $output);

        return [$kernel, $app];
    }

    /**
     * @depends test_kernel
     */
    public function test_artisan($arguments)
    {
        $kernel = $arguments[0];
        $app = $arguments[1];

        $app[KernelContract::class] = m::mock(KernelContract::class)
            ->shouldReceive('handle')->with(m::on(function ($input) {
                return (string) $input === 'list';
            }), m::any())
            ->mock();

        $exitCode = $kernel->call('artisan list');
        $this->assertEquals($exitCode, 0);
    }

    /**
     * @depends test_kernel
     */
    public function test_artisan_tinker($arguments)
    {
        $kernel = $arguments[0];
        $app = $arguments[1];

        $exitCode = $kernel->call('tinker echo 123;');
        $this->assertEquals($exitCode, 0);

        $output = $kernel->output();
        $this->assertRegExp('/=> 123/', $output);
    }

    /**
     * @depends test_kernel
     */
    public function test_cleanup($arguments)
    {
        $kernel = $arguments[0];
        $app = $arguments[1];

        $exitCode = $kernel->call('cleanup');
        $this->assertEquals($exitCode, 0);
    }

    /**
     * @depends test_kernel
     */
    public function test_find($arguments)
    {
        $kernel = $arguments[0];
        $app = $arguments[1];

        $exitCode = $kernel->call('find ../src -name -maxdepth 2');
        $this->assertEquals($exitCode, 0);

        $output = $kernel->output();
        $this->assertRegExp('/ServiceProvider\.php/', $output);
    }

    /**
     * @depends test_kernel
     */
    public function test_mysql($arguments)
    {
        $kernel = $arguments[0];
        $app = $arguments[1];

        $results = [[
            'id'    => 1,
            'email' => 'test01@test.com',
        ], [
            'id'    => 2,
            'email' => 'test02@test.com',
        ]];

        $app[ConnectionInterface::class] = m::mock(ConnectionInterface::class)
            ->shouldReceive('setFetchMode')
            ->shouldReceive('select')->with('select * from users;')->andReturn($results)
            ->mock();

        $exitCode = $kernel->call('mysql select * from users;');
        $this->assertEquals($exitCode, 0);

        $output = $kernel->output();
        $this->assertRegExp('/test01@test.com/', $output);
    }

    /**
     * @depends test_kernel
     */
    public function test_tail($arguments)
    {
        $kernel = $arguments[0];
        $app = $arguments[1];

        $file = __DIR__.'/test.log';
        touch($file);
        $fp = fopen($file, 'w');
        for ($i = 0; $i < 100; $i++) {
            fwrite($fp, $i."\n");
        }
        fclose($fp);

        $exitCode = $kernel->call('tail test.log --lines 5');
        $this->assertEquals($exitCode, 0);

        $output = $kernel->output();
        $this->assertRegExp('/4/', $output);
        unlink($file);
    }

    /**
     * @depends test_kernel
     */
    public function test_vi($arguments)
    {
        $kernel = $arguments[0];
        $app = $arguments[1];

        $file = __DIR__.'/test.txt';
        touch($file);
        $exitCode = $kernel->call('vi test.txt --text=test2');
        $this->assertEquals($exitCode, 0);

        $exitCode = $kernel->call('vi test.txt');
        $this->assertEquals($exitCode, 0);

        $output = $kernel->output();
        $this->assertRegExp('/test2/', $output);

        unlink($file);
    }
}

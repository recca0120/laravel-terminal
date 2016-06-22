<?php

use \Illuminate\Hashing\BcryptHasher;
use Illuminate\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\DatabaseServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Mockery as m;

trait Laravel
{
    public $app;

    public $aliases = [
        Facade::class  => 'Facade',
        App::class     => 'App',
        Schema::class  => 'Schema',
    ];

    public function createApplication()
    {
        if (is_null($this->app) === false) {
            return $this->app;
        }

        $app = m::mock(Container::class.', Illuminate\Contracts\Foundation\Application')
            ->makePartial()
            ->shouldReceive('basePath')->andReturn(realpath(__DIR__.'/../').'/')
            ->shouldReceive('version')->andReturn('5.x.testing')
            ->shouldReceive('environment')->andReturn('testing')
            ->mock();

        $app->setInstance($app);

        $app['request'] = m::mock(Request::class);

        $app['events'] = m::mock(Dispatcher::class);

        foreach ($this->aliases as $className => $alias) {
            if (class_exists() === false) {
                class_alias($className, $alias);
            }
        }

        Facade::setFacadeApplication($app);

        return $this->app = $app;
    }

    public function createDatabase()
    {
        $app = $this->createApplication();

        if (isset($app['db']) === true) {
            return $app['db'];
        }

        $app['config'] = [
            'database.fetch'       => PDO::FETCH_CLASS,
            'database.default'     => 'sqlite',
            'database.connections' => [
                'sqlite' => [
                    'driver'   => 'sqlite',
                    'database' => ':memory:',
                ],
            ],
        ];

        $databaseServiceProvider = m::mock(DatabaseServiceProvider::class, [$app])
            ->makePartial();

        $databaseServiceProvider->register();
        $databaseServiceProvider->boot();

        return $app['db'];
    }

    public function migrate($method)
    {
        $app = $this->createApplication();
        $this->createDatabase();
        foreach (glob($app->basePath().'../database/migrations/*.php') as $file) {
            if (preg_match('/\d+_\d+_\d+_\d+_(.*)\.php/', $file, $m)) {
                $className = Str::studly($m[1]);
                if (class_exists($className) === false) {
                    include_once $file;
                }
                $migration = new $className();
                call_user_func_array([$migration, $method], []);
            }
        }
    }

    public function destroyApplication()
    {
        $this->app = null;
        unset($this->app);
    }
}

if (function_exists('bcrypt') === false) {
    /**
     * Hash the given value.
     *
     * @param string $value
     * @param array  $options
     *
     * @return string
     */
    function bcrypt($value, $options = [])
    {
        return (new BcryptHasher())->make($value, $options);
    }
}
if (function_exists('app') === false) {
    function app()
    {
        return Container::getInstance();
    }
}

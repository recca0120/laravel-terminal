<?php

require __DIR__.'/../vendor/autoload.php';

use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Str;

class Application extends Container
{
    public $aliases = [
        \Illuminate\Support\Facades\Facade::class  => 'Facade',
        \Illuminate\Support\Facades\App::class     => 'App',
        \Illuminate\Support\Facades\Schema::class  => 'Schema',
    ];

    public function __construct()
    {
        date_default_timezone_set('UTC');

        if (class_exists('\Carbon\Carbon') === true) {
            \Carbon\Carbon::setTestNow(\Carbon\Carbon::now());
        }

        $this['app'] = $this;
        $this->setupAliases();
        $this->setupDispatcher();
        $this->setupConnection();
        Facade::setFacadeApplication($this);
        Container::setInstance($this);
    }

    public function setupDispatcher()
    {
        if (class_exists('\Illuminate\Events\Dispatcher') === false) {
            return;
        }
        $this['events'] = new \Illuminate\Events\Dispatcher($this);
    }

    public function setupAliases()
    {
        foreach ($this->aliases as $className => $alias) {
            class_alias($className, $alias);
        }
    }

    public function setupConnection()
    {
        if (class_exists('\Illuminate\Database\Capsule\Manager') === false) {
            return;
        }

        $connection = new \Illuminate\Database\Capsule\Manager();
        $connection->addConnection([
            'driver'   => 'sqlite',
            'database' => ':memory:',
        ]);
        if (isset($this['events']) === true) {
            $connection->setEventDispatcher($this['events']);
        }
        $connection->bootEloquent();
        $connection->setAsGlobal();

        $this['db'] = $connection;
    }

    public function migrate($method)
    {
        if (class_exists('\Illuminate\Database\Capsule\Manager') === false) {
            return;
        }

        foreach (glob(__DIR__.'/../database/migrations/*.php') as $file) {
            include_once $file;
            if (preg_match('/\d+_\d+_\d+_\d+_(.*)\.php/', $file, $m)) {
                $className = Str::studly($m[1]);
                $migration = new $className();
                call_user_func_array([$migration, $method], []);
            }
        }
    }

    public function environment()
    {
        return 'testing';
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
        return (new \Illuminate\Hashing\BcryptHasher())->make($value, $options);
    }
}

if (function_exists('app') === false) {
    function app()
    {
        return App::getInstance();
    }
}

if (Application::getInstance() == null) {
    new Application();
}

<?php

require __DIR__.'/../vendor/autoload.php';

use Illuminate\Container\Container;
use Illuminate\Contracts\Container\Container as ContainerContract;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Str;

class Application extends Container implements ApplicationContract
{
    /**
     * Get the version number of the application.
     *
     * @return string
     */
    public function version()
    {
        return 'testing';
    }

    /**
     * Get the base path of the Laravel installation.
     *
     * @return string
     */
    public function basePath()
    {
        return realpath(__DIR__.'/../src').'/';
    }

    /**
     * Get or check the current application environment.
     *
     * @param  mixed
     * @return string
     */
    public function environment()
    {
        return 'testing';
    }

    /**
     * Determine if the application is currently down for maintenance.
     *
     * @return bool
     */
    public function isDownForMaintenance()
    {
        return false;
    }

    /**
     * Register all of the configured providers.
     *
     * @return void
     */
    public function registerConfiguredProviders()
    {
    }

    /**
     * Register a service provider with the application.
     *
     * @param  \Illuminate\Support\ServiceProvider|string  $provider
     * @param  array  $options
     * @param  bool   $force
     * @return \Illuminate\Support\ServiceProvider
     */
    public function register($provider, $options = [], $force = false)
    {
    }

    /**
     * Register a deferred provider and service.
     *
     * @param  string  $provider
     * @param  string  $service
     * @return void
     */
    public function registerDeferredProvider($provider, $service = null)
    {
    }

    /**
     * Boot the application's service providers.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register a new boot listener.
     *
     * @param  mixed  $callback
     * @return void
     */
    public function booting($callback)
    {
    }

    /**
     * Register a new "booted" listener.
     *
     * @param  mixed  $callback
     * @return void
     */
    public function booted($callback)
    {
    }

    /**
     * Get the path to the cached "compiled.php" file.
     *
     * @return string
     */
    public function getCachedCompilePath()
    {
    }

    /**
     * Get the path to the cached services.json file.
     *
     * @return string
     */
    public function getCachedServicesPath()
    {
    }

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
        $this[ContainerContract::class] = $this;
        $this[ApplicationContract::class] = $this;

        $this->registerAliases();
        $this->registerDispatcher();
        $this->registerConnection();

        Facade::setFacadeApplication($this);
        Container::setInstance($this);
    }

    public function registerAliases()
    {
        foreach ($this->aliases as $className => $alias) {
            class_alias($className, $alias);
        }
    }

    public function registerDispatcher()
    {
        if (class_exists('\Illuminate\Events\Dispatcher') === false) {
            return;
        }
        $this['events'] = new \Illuminate\Events\Dispatcher($this);
        $this[\Illuminate\Contracts\Events\Dispatcher::class] = $this['events'];
    }

    public function registerConnection()
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
    Application::setInstance(new Application());
}

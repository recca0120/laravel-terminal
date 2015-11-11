<?php

namespace Recca0120\Terminal;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    protected $namespace = 'Recca0120\Terminal\Http\Controllers';

    public function boot(Router $router)
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'terminal');
        $this->map($router);
        $this->publish();
    }

    protected function map($router)
    {
        if ($this->app->routesAreCached() === false) {
            $prefix = 'terminal';
            $group = $router->group([
                'namespace' => $this->namespace,
                'as'        => 'terminal::',
                'prefix'    => $prefix,
            ], function () {
                require __DIR__.'/Http/routes.php';
            });
        }
    }

    protected function publish()
    {
        $this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/terminal'),
        ], 'views');

        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/terminal'),
        ], 'public');
    }

    public function register()
    {
    }
}

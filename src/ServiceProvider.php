<?php

namespace Recca0120\Terminal;

use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * namespace.
     *
     * @var string
     */
    protected $namespace = 'Recca0120\Terminal\Http\Controllers';

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Router $router, Request $request)
    {
        $this->handlePublishes();

        if (in_array($request->getClientIp(), config('terminal.whitelists', [])) === true ||
            config('app.debug') === true
        ) {
            $this->loadViewsFrom(__DIR__.'/../resources/views', 'terminal');
            $this->handleRoutes($router);
        }
    }

    /**
     * handle routes.
     *
     * @param  \Illuminate\Routing\Router $router
     * @return void
     */
    protected function handleRoutes(Router $router)
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

    /**
     * handle publishes.
     *
     * @return void
     */
    protected function handlePublishes()
    {
        $this->publishes([
            __DIR__.'/../config/terminal.php' => config_path('terminal.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/terminal'),
        ], 'views');

        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/terminal'),
        ], 'public');

        $this->mergeConfigFrom(__DIR__.'/../config/terminal.php', 'terminal');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }
}

<?php

namespace Recca0120\Terminal;

use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Arr;

class TerminalServiceProvider extends ServiceProvider
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
     * @param \Illuminate\Http\Request   $Request
     * @param \Illuminate\Routing\Router $router
     */
    public function boot(Request $request, Router $router)
    {
        $config = $this->app['config']['terminal'];
        if (in_array($request->getClientIp(), Arr::get($config, 'whitelists', [])) === true || Arr::get($config, 'enabled') === true) {
            $this->loadViewsFrom(__DIR__.'/../resources/views', 'terminal');
            $this->handleRoutes($router, $config);
        }

        if ($this->app->runningInConsole() === true) {
            $this->handlePublishes();
        }
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/terminal.php', 'terminal');
        $this->app->singleton(Application::class, function ($app) {
            $config = $app['config'];
            $commands = $config['terminal.commands'];
            $artisan = new Application($app, $app['events'], $app->version());
            $artisan->resolveCommands($commands);

            return $artisan;
        });

        $this->app->singleton(Kernel::class, Kernel::class);
    }

    /**
     * register routes.
     *
     * @param Illuminate\Routing\Router $router
     * @param array                     $config
     */
    protected function handleRoutes(Router $router, $config = [])
    {
        if ($this->app->routesAreCached() === false) {
            $router->group(array_merge([
                'namespace' => $this->namespace,
            ], Arr::get($config, 'route', [])), function (Router $router) {
                require __DIR__.'/Http/routes.php';
            });
        }
    }

    /**
     * handle publishes.
     */
    protected function handlePublishes()
    {
        $this->publishes([
            __DIR__.'/../config/terminal.php' => $this->app->configPath().'/terminal.php',
        ], 'config');

        $this->publishes([
            __DIR__.'/../resources/views' => $this->app->basePath().'/resources/views/vendor/terminal',
        ], 'views');

        $this->publishes([
            __DIR__.'/../public' => $this->app->publicPath().'/vendor/terminal',
        ], 'public');
    }
}

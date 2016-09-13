<?php

namespace Recca0120\Terminal;

use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
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
     * @param \Illuminate\Http\Request   $Request
     * @param \Illuminate\Routing\Router $router
     *
     * @return void
     */
    public function boot(Request $request, Router $router)
    {
        $this->handlePublishes();
        $config = $this->app['config']->get('terminal', []);
        $enabled = $this->app['config']->get('app.debug', false);
        $enabled = $enabled === false ? Arr::get($config, 'enabled', false) : $enabled;
        if ($enabled === true || in_array($request->getClientIp(), Arr::get($config, 'whitelists', [])) === true) {
            $this->loadViewsFrom(__DIR__.'/../resources/views', 'terminal');
            $this->handleRoutes($router, $config);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/terminal.php', 'terminal');
        $this->app->singleton(Application::class, function ($app) {
            $config = $app['config'];
            $commands = Arr::get($config, 'terminal.commands');
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
     *
     * @return void
     */
    public function handleRoutes(Router $router, $config = [])
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
     *
     * @return void
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

<?php

namespace Recca0120\Terminal;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

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
     * @param \Illuminate\Http\Request $request
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
            $config = $app['config']['terminal'];
            $artisan = new Application($app, $app['events'], $app->version());
            $artisan->resolveCommands($config['commands']);

            return $artisan;
        });

        $this->app->singleton(Kernel::class, function ($app) {
            $config = $app['config']['terminal'];

            return new Kernel($app[Application::class], array_merge($config, [
                'basePath' => $app->basePath(),
                'environment' => $app->environment(),
                'version' => $app->version(),
                'endpoint' => $app['url']->route(Arr::get($config, 'route.as').'endpoint'),
            ]));
        });
    }

    /**
     * register routes.
     *
     * @param \Illuminate\Routing\Router $router
     * @param array $config
     */
    protected function handleRoutes(Router $router, $config = [])
    {
        if ($this->app->routesAreCached() === false) {
            $router->group(array_merge([
                'namespace' => $this->namespace,
            ], Arr::get($config, 'route', [])), function (Router $router) {
                require __DIR__.'/../routes/web.php';
            });
        }
    }

    /**
     * handle publishes.
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
    }
}
